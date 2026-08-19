<?php

namespace App\Http\Controllers;

use App\Models\ExperienceLetter;
use App\Models\InternshipInterest;
use App\Models\User;
use Illuminate\Http\Request;

class BarcodeController extends Controller
{
    public function BarcodeShow($employee_id)
    {
        $user = User::where('employee_id', $employee_id)->first();
        // return $user;

        if (!$user) {
            return redirect()->back()->with('error', 'Employee not found.');
        }

        $experienceLetter = ExperienceLetter::with('user')
            ->where('user_id', $user->id)
            ->first();

        if (!$experienceLetter) {
            return redirect()->back()->with('error', 'Experience Letter not found.');
        }

        return view('docs.barcode', compact('experienceLetter'));
    }

    public function InternshipConfirmCertificate($mobile)
    {
        $internship = InternshipInterest::where('phone', $mobile)
            ->first();

        if (!$internship) {
            return redirect()->back()->with('error', 'Internship Record not found.');
        }

        return view('docs.intern-confirm-certificate', compact('internship'));
    }


    public function BarcodeSearch(Request $request)
    {
        if (!empty($request->employee_id)) {

            $user = User::where('employee_id', $request->employee_id)->first();

            if ($user) {
                return redirect()->route('barcode.show', [
                    'employee_id' => $user->employee_id
                ]);
            }
        }

        if (!empty($request->phone)) {

            $internship = InternshipInterest::where('phone', $request->phone)->first();
            if ($internship) {
                return redirect()->route('barcode.internship.confirm.show', [
                    'phone' => $internship->phone
                ]);
            }
        }

        return back()->with('error', 'Record not found');
    }
}
