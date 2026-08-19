<?php
namespace App\Http\Controllers;
use App\Models\User;
use App\Events\UserCreated;
use App\Models\Career;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $query = User::where('type', '!=', 'admin')->where('type', '!=', 'client')->with('salary');
        // $query_career = Career::all();

        if (request('name')) {
            $query->where('name', 'like', '%' . request('name') . '%');
        }
        if (request('email')) {
            $query->where('email', 'like', '%' . request('email') . '%');
        }
        if (request('department') && array_key_exists(request('department'), User::$departments)) {
            $query->where('department', request('department'));
        }
        if (request('type') && array_key_exists(request('type'), User::$types)) {
            $query->where('type', request('type'));
        }
        if (request('work_location') && array_key_exists(request('work_location'), User::$workLocations)) {
            $query->where('work_location', request('work_location'));
        }
        if (request('status') && array_key_exists(request('status'), User::$status)) {
            $query->where('status', request('status'));
        }
        if (request('applicants') && array_key_exists(request('applicants'), Career::$user_id)) {
            $query->where('status', request('status'));
        }


        if (\Auth::user()->type!='admin' && \Auth::user()->type!='branch manager'){
            $query->where('parent_id',\Auth::id());
        }

        if (!request()->has('search') && \Auth::user()->type=='branch manager'){
            $query->where('branch_id',\Auth::user()->branch_id);
        }

//        $users = $query->simplePaginate(20)->appends(request()->except('page'));
        $users = $query->get();
        return view('users.index', compact('users'));
    }
    public function create()
    {
        return view('users.create');
    }
    public function store(Request $request)
    {
        $baseRules = [
            'name' => 'required',
            'email' => 'required|email:rfc,dns,strict|unique:users,email',
            'mobile' => 'required|string|max:15',
            'department' => 'required',
            'type' => 'required',
            'work_location' => 'nullable|in:office,remote,hybrid,temporary remote',
            'status' => 'required|in:active,inactive,suspended',
            'barcode_rfid' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100|in:'.implode(',', User::$positions),
            'tawk_code'=> 'nullable|string|max:100'
            // 'password' => 'required|min:6',
        ];
        $salaryRules = [
            'basic' => 'required|numeric',
            'hra' => 'required|numeric',
            'conveyance' => 'required|numeric',
            'special_allowance' => 'required|numeric',
            'medical_allowance' => 'required|numeric',
            'other_allowance' => 'required|numeric',
            'gross_salary' => 'required|numeric',
            'pf' => 'required|numeric',
            'esi' => 'required|numeric',
            'professional_tax' => 'required|numeric',
            'tds' => 'required|numeric',
            'effective_from' => 'required|date',
        ];
        $type = $request->input('type');
        $rules = array_merge($baseRules, $salaryRules);
        $data = $request->validate($rules);

        if ($type == 'employee') {
            $data['employee_id'] = User::generateEmployeeId();
        } elseif ($type == 'intern') {
            $data['employee_id'] = User::generateInternId();
        } else {
            $data['employee_id'] = null;
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'mobile' => $data['mobile'],
            'department' => $data['department'],
            'type' => $data['type'],
            'work_location' => $data['work_location'],
            'status' => $data['status'],
            'barcode_rfid' => $data['barcode_rfid'] ?? null,
            'tawk_code' => $data['tawk_code'] ?? null,
            'position' => $data['position'] ?? null,
            'password' => $data['password'] ?? (env('DEFAULT_PASSWORD', Str::random(15))),
            'employee_id' => $data['employee_id'],
            'created_by' => \Auth::id(),
            'parent_id' => \Auth::id(),
            'branch_id' => $this->branchContext->currentBranchId(),
        ]);

        $user->salary()->create([
            'basic' => $data['basic'],
            'hra' => $data['hra'],
            'conveyance' => $data['conveyance'],
            'special_allowance' => $data['special_allowance'],
            'medical_allowance' => $data['medical_allowance'],
            'other_allowance' => $data['other_allowance'],
            'gross_salary' => $data['gross_salary'],
            'pf' => $data['pf'],
            'esi' => $data['esi'],
            'professional_tax' => $data['professional_tax'],
            'tds' => $data['tds'],
            'effective_from' => $data['effective_from'],
            'branch_id' => $this->branchContext->currentBranchId(),
        ]);

        event(new UserCreated($user));
        return redirect()->route('users.index')->with('success', $type.' created successfully');
    }
    public function edit($id)
    {
        $user = User::with(['salary', 'kyc'])->findOrFail($id);

        if (\Auth::user()->type!='admin' && $user->parent_id != \Auth::id()){
            return abort(404);
        }

        return view('users.edit', compact('user'));
    }
    public function update(Request $request, $id)
    {
        $baseRules = [
            'name' => 'required',
            'email' => 'required|email:rfc,dns,strict|unique:users,email,' . $id,
            'mobile' => 'required|string|max:15',
            'department' => 'required',
            'type' => 'required',
            'work_location' => 'nullable|in:office,remote,hybrid,temporary remote',
            'status' => 'required|in:active,inactive,suspended',
            'barcode_rfid' => 'nullable|string|max:100',
            'tawk_code' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100|in:'.implode(',', User::$positions),
        ];
        $salaryRules = [
            'basic' => 'required|numeric',
            'hra' => 'required|numeric',
            'conveyance' => 'required|numeric',
            'special_allowance' => 'required|numeric',
            'medical_allowance' => 'required|numeric',
            'other_allowance' => 'required|numeric',
            'gross_salary' => 'required|numeric',
            'pf' => 'required|numeric',
            'esi' => 'required|numeric',
            'professional_tax' => 'required|numeric',
            'tds' => 'required|numeric',
            'effective_from' => 'required|date',
        ];
        $kycRules = [
            'father_name' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'blood_group' => 'nullable|string|max:10',
            'mobile_number_alt' => 'nullable|string|max:15',
            'address_line1' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'pan_number' => 'nullable|string|max:20',
            'aadhaar_last4' => 'nullable|string|max:20',
            'bank_name' => 'nullable|string|max:100',
            'account_no' => 'nullable|string|max:50',
            'ifsc_code' => 'nullable|string|max:20',
            'bank_branch' => 'nullable|string|max:100',
            'kyc_status' => 'nullable|in:pending,approved,rejected',
            'remarks' => 'nullable|string',
        ];
        $type = $request->input('type');
        $rules = array_merge($baseRules, $salaryRules, $kycRules);
        $data = $request->validate($rules);
        $user = User::findOrFail($id);
        if (\Auth::user()->type!='admin' && $user->parent_id != \Auth::id()){
            return abort(404);
        }
        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'mobile' => $data['mobile'],
            'department' => $data['department'],
            'type' => $data['type'],
            'work_location' => $data['work_location'],
            'status' => $data['status'],
            'barcode_rfid' => $data['barcode_rfid'] ?? null,
            'tawk_code' => $data['tawk_code'] ?? null,
            'position' => $data['position'] ?? null,
        ]);

        $salary = $user->salary()->firstOrNew(['user_id' => $user->id]);
        $salary->fill([
            'basic' => $data['basic'],
            'hra' => $data['hra'],
            'conveyance' => $data['conveyance'],
            'special_allowance' => $data['special_allowance'],
            'medical_allowance' => $data['medical_allowance'],
            'other_allowance' => $data['other_allowance'],
            'gross_salary' => $data['gross_salary'],
            'pf' => $data['pf'],
            'esi' => $data['esi'],
            'professional_tax' => $data['professional_tax'],
            'tds' => $data['tds'],
            'effective_from' => $data['effective_from'],
        ])->save();

        $kycPayload = [
            'full_name' => $data['name'],
            'email' => $data['email'],
            'mobile_number' => $data['mobile'],
            'father_name' => $data['father_name'] ?? ($user->kyc->father_name ?? null),
            'date_of_birth' => $data['date_of_birth'] ?? ($user->kyc->date_of_birth ?? null),
            'gender' => $data['gender'] ?? ($user->kyc->gender ?? null),
            'blood_group' => $data['blood_group'] ?? ($user->kyc->blood_group ?? null),
            'mobile_number_alt' => $data['mobile_number_alt'] ?? ($user->kyc->mobile_number_alt ?? null),
            'address_line1' => $data['address_line1'] ?? ($user->kyc->address_line1 ?? null),
            'city' => $data['city'] ?? ($user->kyc->city ?? null),
            'state' => $data['state'] ?? ($user->kyc->state ?? null),
            'postal_code' => $data['postal_code'] ?? ($user->kyc->postal_code ?? null),
            'pan_number' => $data['pan_number'] ?? ($user->kyc->pan_number ?? null),
            'aadhaar_last4' => $data['aadhaar_last4'] ?? ($user->kyc->aadhaar_last4 ?? null),
            'bank_name' => $data['bank_name'] ?? ($user->kyc->bank_name ?? null),
            'account_no' => $data['account_no'] ?? ($user->kyc->account_no ?? null),
            'ifsc_code' => $data['ifsc_code'] ?? ($user->kyc->ifsc_code ?? null),
            'bank_branch' => $data['bank_branch'] ?? ($user->kyc->bank_branch ?? null),
        ];

        if (\Auth::user()->isAdmin() && isset($data['kyc_status'])) {
            $kycPayload['kyc_status'] = $data['kyc_status'];
        }
        if (isset($data['remarks'])) {
            $kycPayload['remarks'] = $data['remarks'];
        }

        $user->kyc()->updateOrCreate(['user_id' => $user->id], $kycPayload);

        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if (\Auth::user()->type!='admin' && $user->parent_id != \Auth::id()){
            return abort(404);
        }
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully');
    }
}
