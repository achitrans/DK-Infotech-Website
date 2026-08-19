<?php

namespace App\Http\Controllers;

use App\Models\AdvanceSalary;
use App\Models\User;
use App\Models\UserMonthlySalary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class UserMonthlySalaryController extends Controller
{

    public function index(Request $request)
    {
        // Default to last month
        $strToTime = strtotime('first day of last month');
        $lastMonth = date('m', $strToTime);
        $lastYear = date('Y', $strToTime);
        $month = $request->input('month', $lastMonth);
        $year = $request->input('year', $lastYear);

        // Check if selected month/year is current or future
        $selected = sprintf('%04d-%02d', $year, $month);
        $now = date('Y-m');
        if ($selected >= $now) {
            return redirect()->back()->withErrors(['month' => 'You cannot view or process salary for the current or a future month.']);
        }

        $employees = User::where('type', 'employee')->get();
        $monthlySalaries = UserMonthlySalary::where('salary_month', $month)->where('salary_year', $year)->get()->keyBy('user_id');
        return view('user_monthly_salaries.index', compact('month', 'year', 'employees', 'monthlySalaries'));
    }

    public function create($userId, $year, $month)
    {
        $user = User::findOrFail($userId);
        $userSalary = $user->salary; // Assuming hasOne relation

        // Calculate total days in month excluding Sundays and holidays (except holidays on Sunday)
        $totalDays = 0;
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, (int)$month, (int)$year);
        $holidays = \App\Models\Holiday::whereYear('date', $year)->whereMonth('date', $month)->get();
        $holidayDates = $holidays->pluck('date')->map(function($d) { return $d->format('Y-m-d'); })->toArray();
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $date = sprintf('%04d-%02d-%02d', $year, $month, $d);
            $isSunday = date('w', strtotime($date)) == 0;
            $isHoliday = in_array($date, $holidayDates);
            if (!$isSunday && !$isHoliday) {
                $totalDays++;
            }
        }

        // Get present and absent days from attendances table
        $presentDays = $user->attendances()
            ->whereYear('attendance_date', $year)
            ->whereMonth('attendance_date', $month)
            ->where('status', 'present')
            ->count();
        $absentDays = $totalDays - $presentDays;

        // Count approved paid and unpaid leaves for the user in the month
        $paidLeaves = 0;
        $unpaidLeaves = 0;
        if (method_exists($user, 'leaves')) {
            $paidLeaves = $user->leaves()
                ->whereYear('from_date', $year)
                ->whereMonth('from_date', $month)
                ->where('status', 'approved')
                ->where('is_paid', true)
                ->count();
            $unpaidLeaves = $user->leaves()
                ->whereYear('from_date', $year)
                ->whereMonth('from_date', $month)
                ->where('status', 'approved')
                ->where('is_paid', false)
                ->count();
        }

        $outstandingAdvances = $this->getOutstandingAdvances($user);
        return view('user_monthly_salaries.create', compact('user', 'userSalary', 'year', 'month', 'totalDays', 'presentDays', 'absentDays', 'paidLeaves', 'unpaidLeaves', 'outstandingAdvances'));
    }

    public function store(Request $request, $userId, $year, $month)
    {
        $data = $request->validate([
            'basic'             => 'required|numeric',
            'hra'               => 'required|numeric',
            'conveyance'        => 'required|numeric',
            'special_allowance' => 'required|numeric',
            'medical_allowance' => 'required|numeric',
            'other_allowance'   => 'required|numeric',
            'gross_salary'      => 'required|numeric',
            'total_days'        => 'required|integer',
            'present_days'      => 'required|integer',
            'paid_leaves'       => 'required|integer',
            'absent_days'       => 'required|integer',
            'pf'                => 'required|numeric',
            'esi'               => 'required|numeric',
            'professional_tax'  => 'required|numeric',
            'tds'               => 'required|numeric',
            'lop_days'          => 'required|numeric|max:99',
            'lop_amount'        => 'required|numeric',
            'gross_deduction'   => 'required|numeric',
            'net_salary'        => 'required|numeric',
            'payment_status'    => 'required|in:unpaid,paid,pending',
            'payment_date'      => 'nullable|date',
            'remarks'           => 'nullable|string',
            'pd.*'              => 'nullable|max:100',
        ]);
        $data['user_id'] = $userId;
        $data['salary_year'] = $year;
        $data['salary_month'] = $month;
        $data['branch_id'] = $this->branchContext->currentBranchId();

        if (isset($data['pd'])) {
            foreach ($data['pd'] as $key => $value) {
                $data['payment_details'][$key] = $value;
            }
        }

        $advanceTotals = $this->applyAdvanceDeductions(User::findOrFail($userId));
        $data['advance_deductions'] = $advanceTotals['deductions'];
        $data['advance_total_deduction'] = $advanceTotals['total'];
        $data['net_salary'] = $this->calculateNetSalary($data['gross_salary'], $data['gross_deduction'], $advanceTotals['total']);

        UserMonthlySalary::create($data);
        return redirect()->route('user-monthly-salaries.index',
            ['month' => $month, 'year'  => $year])
            ->with('success', 'Monthly salary created');
    }

    public function edit($id)
    {
        $salary = UserMonthlySalary::findOrFail($id);
        $user = $salary->user;
        if ($salary->payment_details==null){
            $salary->payment_details='';
        }
        return view('user_monthly_salaries.edit', compact('salary', 'user'));
    }

    public function update(Request $request, $id)
    {
        $salary = UserMonthlySalary::findOrFail($id);
        $data = $request->validate([
            'basic'             => 'required|numeric',
            'hra'               => 'required|numeric',
            'conveyance'        => 'required|numeric',
            'special_allowance' => 'required|numeric',
            'medical_allowance' => 'required|numeric',
            'other_allowance'   => 'required|numeric',
            'gross_salary'      => 'required|numeric',
            'total_days'        => 'required|integer',
            'present_days'      => 'required|integer',
            'paid_leaves'       => 'required|integer',
            'absent_days'       => 'required|integer',
            'pf'                => 'required|numeric',
            'esi'               => 'required|numeric',
            'professional_tax'  => 'required|numeric',
            'tds'               => 'required|numeric',
            'lop_days'          => 'required|numeric|max:99',
            'lop_amount'        => 'required|numeric',
            'gross_deduction'   => 'required|numeric',
            'net_salary'        => 'required|numeric',
            'payment_status'    => 'required|in:unpaid,paid,pending',
            'payment_date'      => 'nullable|date',
            'remarks'           => 'nullable|string',
            'pd.*'              => 'nullable|max:100',
        ]);
        if (isset($data['pd'])) {
            foreach ($data['pd'] as $key => $value) {
                $data['payment_details'][$key] = $value;
            }
        }
        $salary->update($data);
        return redirect()->route('user-monthly-salaries.index',
                        ['month' => $salary->salary_month,'year'  => $salary->salary_year])
                        ->with('success', 'Monthly salary updated');
    }

    public function show($id)
    {
        $salary = UserMonthlySalary::findOrFail($id);
        $user = $salary->user;
        return view('user_monthly_salaries.show', compact('salary', 'user'));
    }

    public function employeeSalary()
    {
        $data = UserMonthlySalary::where('user_id',Auth::id())->get();
        return view('user_monthly_salaries.employee.index', compact('data'));
    }

    public function employeeSalarySlip($id)
    {
        try{
            $id = Crypt::decrypt($id);
        }catch (\Exception $exception){

        }
        $salary = UserMonthlySalary::findOrFail($id);

         $f = new \NumberFormatter("en-in", \NumberFormatter::SPELLOUT);
        $word = ucwords($f->format($salary->net_salary));
        if ($salary->user_id != Auth::id()){
            abort(403);
        }
                // Count approved paid and unpaid leaves for the user in the month
        $paidLeaves = 0;
        $unpaidLeaves = 0;
        if (method_exists($salary->user, 'leaves')) {
            $paidLeaves = $salary->user->leaves()
                ->whereYear('from_date', $salary->salary_year)
                ->whereMonth('from_date', $salary->salary_month)
                ->where('status', 'approved')
                ->where('is_paid', true)
                ->count();
            $unpaidLeaves = $salary->user->leaves()
                ->whereYear('from_date', $salary->salary_year)
                ->whereMonth('from_date', $salary->salary_month)
                ->where('status', 'approved')
                ->where('is_paid', false)
                ->count();
        }

        return view('user_monthly_salaries.employee.slip', compact('salary','word','paidLeaves','unpaidLeaves'));
    }

    protected function getOutstandingAdvances(User $user)
    {
        return AdvanceSalary::approved()
            ->outstanding()
            ->where('user_id', $user->id)
            ->where('branch_id', $this->branchContext->currentBranchId())
            ->orderBy('created_at')
            ->get();
    }

    protected function applyAdvanceDeductions(User $user): array
    {
        $advances = $this->getOutstandingAdvances($user);
        $deductions = [];
        $total = 0;
        foreach ($advances as $advance) {
            $scheduled = $advance->term_type === AdvanceSalary::TERM_FULL
                ? $advance->outstanding_amount
                : min($advance->deduction_value ?? 0, $advance->outstanding_amount);
            if ($scheduled <= 0) {
                continue;
            }
            $applied = $advance->applyDeduction($scheduled);
            if ($applied <= 0) {
                continue;
            }
            $deductions[] = [
                'advance_id' => $advance->id,
                'term_type' => $advance->term_type,
                'deducted_amount' => round($applied, 2),
                'remaining' => round($advance->outstanding_amount, 2),
            ];
            $total += $applied;
        }

        return [
            'deductions' => $deductions,
            'total' => round($total, 2),
        ];
    }

    protected function calculateNetSalary(float $gross, float $deductions, float $advanceDeduction): float
    {
        $net = $gross - $deductions - $advanceDeduction;
        return max(0, round($net, 2));
    }
}
