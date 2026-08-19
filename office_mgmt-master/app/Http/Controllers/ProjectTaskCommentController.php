<?php
namespace App\Http\Controllers;

use App\Models\ProjectTask;
use App\Models\ProjectTaskComment;
use App\Services\WhatsappService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectTaskCommentController extends Controller
{
    public function store(Request $request, $taskId)
    {
        $task = ProjectTask::findOrFail($taskId);
        $data = $request->validate([
            'comment' => 'required|string',
            'is_internal' => 'sometimes|boolean',
            'parent_id' => 'nullable|exists:project_task_comments,id'
        ]);
        // Ensure parent belongs to same task if provided
        if (!empty($data['parent_id'])) {
            $parent = ProjectTaskComment::where('id', $data['parent_id'])
                ->where('project_task_id', $task->id)->first();
            if (!$parent) {
                return back()->withErrors(['parent_id' => 'Invalid parent comment selected.'])->withInput();
            }
        }
        $data['project_task_id'] = $task->id;
        $data['user_id'] = Auth::id();
        $data['is_internal'] = (bool)($data['is_internal'] ?? false);
        $projectTaskComment = ProjectTaskComment::create($data);
        if ($projectTaskComment->is_internal != true){
            $ws = new WhatsappService();
            $message = "*Project Task Comment*\n";
            $message .= "*Project*: ".$projectTaskComment->task->project->name."\n\n";
            $message .= "*Task*: ".$projectTaskComment->task->task_name."\n\n";
            $message .= "*Comment*: ".$projectTaskComment->comment."\n\n";
            $message .= "Team ".env('COMPANY_NAME');
            $ws->sendMessage($task->project->user->mobile,$message);
            $ws->sendMessage($task->project->client->mobile,$message);
        }
        return back()->with('success', 'Comment added.');
    }

    public function edit($id)
    {
        $comment = ProjectTaskComment::with('task')->findOrFail($id);
        return view('project_task_comments.edit', compact('comment'));
    }

    public function update(Request $request, $id)
    {
        $comment = ProjectTaskComment::findOrFail($id);
        $data = $request->validate([
            'comment' => 'required|string',
            'is_internal' => 'sometimes|boolean',
        ]);
        $data['is_internal'] = (bool)($data['is_internal'] ?? false);
        $comment->update($data);
        return back()->with('success', 'Comment updated.');
    }
}
