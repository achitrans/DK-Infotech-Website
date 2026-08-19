<?php

namespace App\Http\Controllers;

use App\Models\ProjectMilestone;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectMilestoneController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
            'completed_date' => 'nullable|date',
            'status' => 'required',
            // 'order_no' => 'nullable|integer',
            'project_id' => 'required|exists:projects,id',
        ]);
        ProjectMilestone::create($data);
        return redirect()->back()->with('success', 'Milestone added successfully');
    }
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
            'completed_date' => 'nullable|date',
            'status' => 'required',
            // 'order_no' => 'required|integer',
        ]);
        $milestone = ProjectMilestone::findOrFail($id);
        $milestone->update($data);
        return redirect()->route('projects.show', $milestone->project_id)->with('success', 'Milestone updated successfully');
        // return redirect()->back()->with('success', 'Milestone updated successfully');
    }
    public function show($id)
    {
        $milestone = ProjectMilestone::with('remarks.user')->findOrFail($id);

        return view('project-milestones.show', compact('milestone'));
    }

    public function edit($id)
    {
        $milestone = ProjectMilestone::findOrFail($id);
        return view('project-milestones.edit', compact('milestone'));
    }

    public function destroy($id)
    {
        if (Auth::user()->isAdmin()) {
            $milestone = ProjectMilestone::findOrFail($id);
            $milestone->delete();
            return redirect()->back()->with('success', 'Milestone deleted successfully');
        } else {
            return redirect()->back()->with('error', 'Invalid Request');
        }
    }
}
