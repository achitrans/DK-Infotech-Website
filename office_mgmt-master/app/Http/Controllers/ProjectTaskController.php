<?php

namespace App\Http\Controllers;

use App\Models\ProjectTask;
use App\Models\Project;
use App\Models\User;
use App\Services\WhatsappService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectTaskController extends Controller
{

    public function searchResults(Request $request, $slug)
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

        return view('projects.search_results', compact('projects', 'slug'));
    }

    public function searchResultsShow(Request $request, $slug, $id,)
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
        };

        return view('projects.search_results_show', compact('project','tawkUrlSuffix','slug'));
    }


    public function index($project_id)
    {
        $tasks = ProjectTask::with(['project', 'assignedTo', 'createdBy', 'updatedBy', 'closedBy'])
            ->where('project_id', $project_id)
            ->simplePaginate(20);
        // return view('project_tasks.index', compact('tasks', 'project_id'));
    }

    public function create($project_id)
    {
        $project = Project::findOrFail($project_id);
        //        $users = User::where('department', $project->department)->get();
        return view('project_tasks.create', compact('project', 'project_id'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            //            'assigned_to' => 'nullable|exists:users,id',
            'task_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'status' => 'required|string|max:50',
            'doc_path' => 'nullable|file',
        ]);
        $validated['created_by'] = Auth::id();
        if ($request->hasFile('doc_path')) {
            $validated['doc_path'] = $request->file('doc_path')->store('project_tasks');
        }
        $task = ProjectTask::create($validated);
        $ws = new WhatsappService();

        $message = "*Task Added*\n";
        $message .= "*Project*: " . $task->project->name . "\n\n";
        $message .= "*Task*: " . $task->task_name . "\n\n";
        $message .= "*Description*: " . $task->description . "\n\n";
        $message .= "Team " . env('COMPANY_NAME');
        $ws->sendMessage($task->project->user->mobile, $message);
        $ws->sendMessage($task->project->client->mobile, $message);
        // return redirect()->route('project_tasks.show', $task->id)->with('success', 'Task created successfully.');
        return redirect()->route('projects.show', $request->project_id)->with('success', 'Task created successfully.');
    }

    public function show($id)
    {
        $task = ProjectTask::with(['project', 'assignedTo', 'createdBy', 'updatedBy', 'closedBy'])->findOrFail($id);
        return view('project_tasks.show', compact('task'));
    }

    public function edit($id)
    {
        $task = ProjectTask::findOrFail($id);
        //        $users = User::where('department', $task->project->department)->get();
        return view('project_tasks.edit', compact('task'));
    }

    public function update(Request $request, $id)
    {
        $task = ProjectTask::findOrFail($id);
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            //            'assigned_to' => 'nullable|exists:users,id',
            'task_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'status' => 'required|string|max:50',
            'doc_path' => 'nullable|file',
        ]);
        $validated['updated_by'] = Auth::id();
        if ($request->hasFile('doc_path')) {
            $validated['doc_path'] = $request->file('doc_path')->store('project_tasks');
        } else {
            unset($validated['doc_path']); // Don't update doc_path if no file
        }
        $task->update($validated);

        $ws = new WhatsappService();

        $message = "*Task Updated*\n\n";
        $message .= "*Project*: " . $task->project->name . "\n\n";
        $message .= "*Task*: " . $task->task_name . "\n\n";
        $message .= "*Description*: " . $task->description . "\n\n";
        $message .= "*Status*: " . $task->status . "\n\n";
        $message .= "Team " . env('COMPANY_NAME');
        $ws->sendMessage($task->project->user->mobile, $message);
        $ws->sendMessage($task->project->client->mobile, $message);

        return redirect()->route('project_tasks.show', $task->id)->with('success', 'Task updated successfully.');
    }

    public function destroy($id)
    {
        $task = ProjectTask::findOrFail($id);
        $task->delete();
        return redirect()->route('project_tasks.index')->with('success', 'Task deleted successfully.');
    }
}
