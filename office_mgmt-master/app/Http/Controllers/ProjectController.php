<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Events\ProjectCreated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProjectController extends Controller
{

    public function index(Request $request)
    {
        $user = Auth::user();
        $search = trim($request->query('search', ''));

        $model = Project::query();
        $model->with('user', 'client');

        if ($search !== '') {
            $like = '%' . $search . '%';
            $model->where(function ($query) use ($like) {
                $query->where('name', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhereHas('client', function ($clientQuery) use ($like) {
                        $clientQuery->where('name', 'like', $like)
                            ->orWhere('mobile', 'like', $like);
                    })
                    ->orWhereHas('user', function ($userQuery) use ($like) {
                        $userQuery->where('name', 'like', $like)
                            ->orWhere('mobile', 'like', $like);
                    });
            });
            $projects = $model->get();
        } else {
            if ($user->isAdmin()) {
                $projects = $model->get();
            } elseif ($user->isClient()) {
                $projects = $model->where('client_id', $user->id)->get();
            } elseif ($user->type == 'associate') {
                $projects = $model->where('created_by', $user->id)
                    ->orWhere('associate_id', $user->id)->get();
            } else {
                $projects = $model->where('user_id', $user->id)->get();
            }
        }

        return view('projects.index', compact('projects'));
    }
    public function create()
    {
        if(Auth::user()->isClient()) {
            abort(403, 'You do not have permission.');
        }
        $query = User::query();
        $query->where('type', 'client');

        if (Auth::user()->isAssociate()){
            $query->where('created_by',Auth::id());
        }

        $clients = $query->get();

        return view('projects.create', compact('clients'));
    }
    public function store(Request $request)
    {
        $user = Auth::user();
        $rules = [
            'name' => 'required',
            'client_id' => 'required|exists:users,id',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'required',
            'budget' => 'numeric',
            'tawk_code' => 'nullable|string|max:100',
        ];
        if ($user->isAdmin()) {
            $rules['user_id'] = 'nullable|exists:users,id';
        }
        $data = $request->validate($rules);

        if ($user->isAdmin()) {
            $selectedUser = \App\Models\User::find($data['user_id']);
        }else{
            $selectedUser = \App\Models\User::find(Auth::id());
        }

        if (!$user->isAdmin()) {
            $data['user_id'] = $user->id;
        }
        if ($user->isAssociate()) {
            $data['associate_id'] = $user->id;
        }

        $data['created_by'] = $user->id;
        $data['tawk_code'] = $request->tawk_code;

        $data['department'] = $selectedUser ? $selectedUser->department : null;
        $data['branch_id'] = $this->branchContext->currentBranchId();
        $project = Project::create($data);
        if ($project){
            event(new ProjectCreated($project, User::find($data['client_id']), $selectedUser));
        }
        return redirect()->route('projects.index')->with('success', 'Project created successfully');
    }
    public function edit($id)
    {
        if(Auth::user()->isClient()) {
            abort(403, 'You do not have permission.');
        }
        $clients = User::where('type', 'client')->get();
        $project = Project::findOrFail($id);
        if (!Auth::user()->isAdmin() && ($project->user_id !== Auth::id() || $project->associate_id !== Auth::id())) {
            abort(403);
        }
        return view('projects.edit', compact('project', 'clients'));
    }
    public function update(Request $request, $id)
    {
        if(Auth::user()->isClient()) {
            abort(403, 'You do not have permission.');
        }
        $user = Auth::user();
        $rules = [
            'name' => 'required',
            'client_id' => 'required|exists:users,id',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'required',
            'budget' => 'nullable|numeric',
            'tawk_code' => 'nullable|string|max:100',
        ];
        if ($user->isAdmin()) {
            $rules['user_id'] = 'nullable|exists:users,id';
        }
        $data = $request->validate($rules);
        $project = Project::findOrFail($id);
        if (!Auth::user()->isAdmin() && ($project->user_id !== Auth::id() || $project->associate_id !== Auth::id())) {
            abort(403);
        }
        if (!$user->isAdmin()) {
            unset($data['user_id']);
        }
        // Set department from selected user
        $selectedUser = \App\Models\User::find($data['user_id'] ?? $project->user_id);
        $data['department'] = $selectedUser ? $selectedUser->department : null;
        $data['tawk_code'] = $request->tawk_code;
        $project->update($data);
        return redirect()->route('projects.index')->with('success', 'Project updated successfully');
    }
    public function destroy($id)
    {
        if(Auth::user()->isClient()) {
            abort(403, 'You do not have permission.');
        }
        $user = Auth::user();
        $project = Project::findOrFail($id);
        if (!$user->isAdmin() && $project->user_id !== $user->id) {
            abort(403);
        }
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Project deleted successfully');
    }
    public function show(Request $request, $id)
    {
        $user = Auth::user();
        $project = Project::with([
            'milestones' => function ($query) use ($request) {
                $milestoneStatus = $request->query('milestone_status', 'active');
                if ($milestoneStatus == 'active') {
                    $query->whereIn('status', ['pending', 'in progress', 'on hold']);
                } elseif ($milestoneStatus != 'all') {
                    $query->where('status', $milestoneStatus);
                }
                $query->orderByRaw("CASE
                    WHEN status = 'pending' THEN 1
                    WHEN status = 'in progress' THEN 2
                    WHEN status = 'on hold' THEN 3
                    WHEN status = 'completed' THEN 4
                    ELSE 5 END");
            },
            'milestones.remarks',
            'remarks',
            'tasks' => function ($query) use ($request) {
                $taskStatus = $request->query('task_status', 'active');
                if ($taskStatus == 'active') {
                    $query->whereIn('status', ['pending', 'in progress', 'on hold']);
                } elseif ($taskStatus != 'all') {
                    $query->where('status', $taskStatus);
                }
                $query->orderByRaw("CASE
                    WHEN status = 'pending' THEN 1
                    WHEN status = 'in progress' THEN 2
                    WHEN status = 'on hold' THEN 3
                    WHEN status = 'cancelled' THEN 4
                    WHEN status = 'closed' THEN 5
                    WHEN status = 'completed' THEN 6
                    ELSE 7 END");
            }
        ])->findOrFail($id);
        // if ($user->isEmployee()  && $project->user_id !== $user->id) {
        //     abort(403);
        // }
        if ($user->isClient() && $project->client_id !== $user->id) {
            abort(403);
        }

        $tawkUrlSuffix = "";
        if ($user->isClient()) {
            if (strlen($project->tawk_code)>6){
                $tawkUrlSuffix=$project->tawk_code;
            }elseif (strlen($project->user?->tawk_code)>6){
                $tawkUrlSuffix=$project->user->tawk_code;
            }
        }

        return view('projects.show', compact('project','tawkUrlSuffix'));
    }

    public function initiateCall($data)
    {
        try {
            $data = json_decode(Crypt::decrypt($data), true);
            if(env('IVR_ENABLE') && env('IVR_USER') && env('IVR_ID') && strlen($data['to'])==10 && strlen($data['from'])==10){
                $url = "https://callme.wemonde.com/clicktocallapi/initiateclicktocall?user_id=".env('IVR_USER')."&dnid=".env('IVR_ID')."&caller_num=".$data['to']."&agent_num=".$data['from'];
                $response = Http::get($url);
//                Log::info('IVR log', ['url'=>$url,'response'=>$response->json(),'code'=>$response->getStatusCode(),'headers'=>$response->headers()]);
                return back()->with('success','Call initiated. You will receive call shortly.');
            }else{
                return back()->with('error','Setting issue/Invalid mobile number. Please try again');
            }

        }catch (\Exception $exception){
            return back()->with('error','Invalid Request. Please try again');
        }
    }
}
