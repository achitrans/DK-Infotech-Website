<?php
namespace App\Http\Controllers;
use App\Models\ProjectMilestoneRemark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectMilestoneRemarkController extends Controller
{
    public function store(Request $request) {
        $data = $request->validate([
            'milestone_id' => 'required|exists:project_milestones,id',
            'remark_text' => 'required|string',
            'remark_type' => 'required',
        ]);
        $data['user_id'] = Auth::id();
        ProjectMilestoneRemark::create($data);
        return redirect()->back()->with('success', 'Milestone remark added successfully');
    }
    public function destroy($id) {
        $remark = ProjectMilestoneRemark::findOrFail($id);
        $remark->delete();
        return redirect()->back()->with('success', 'Milestone remark deleted successfully');
    }
}
