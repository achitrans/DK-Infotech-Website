<?php
namespace App\Http\Controllers;
use App\Models\ProjectRemark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectRemarkController extends Controller
{
    public function store(Request $request) {
        $data = $request->validate([
            'remark_text' => 'required|string',
            'remark_type' => 'required',
            'project_id' => 'required|exists:projects,id',
        ]);
        $data['user_id'] = Auth::id();
        ProjectRemark::create($data);
        return redirect()->back()->with('success', 'Remark added successfully');
    }
    public function edit($id) {
        $remark = ProjectRemark::findOrFail($id);
        return view('project-remarks.edit', compact('remark'));
    }

    public function update(Request $request, $id) {
        $remark = ProjectRemark::findOrFail($id);
        $data = $request->validate([
            'remark_text' => 'required|string',
            'remark_type' => 'required',
        ]);
        $remark->update($data);
        return redirect()->route('projects.show', $remark->project_id)->with('success', 'Remark updated successfully');
    }

    public function destroy($id) {
        $remark = ProjectRemark::findOrFail($id);
        $remark->delete();
        return redirect()->back()->with('success', 'Remark deleted successfully');
    }
}
