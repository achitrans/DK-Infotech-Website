<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class InquiryController extends Controller
{
    private $response = ['status' => false, 'message' => '', 'data' => []];

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required|max:100',
            'email'     => 'nullable|email|max:100',
            'phone'     => 'required|max:15',
            'subject'   => 'required|max:100',
            'message'   => 'nullable|max:1000',
            'city'      => 'nullable|max:100',
            'state'     => 'nullable|max:100',
        ]);

        if ($validator->fails()) {
            $this->response['error'] = $validator->errors()->all();
            return response()->json($this->response, 400);
        }

        try {
            Inquiry::create([
                'user_id'=>0,
                'name'=>$request->name,
                'email'=>$request->email ?? '',
                'phone'=>$request->phone ?? '',
                'subject'=>$request->subject ?? '',
                'message'=>$request->message ?? '',
                'source'=>'website',
                'status'=>'new',
                'state'=>$request->state ?? '',
                'city'=>$request->city ?? ''
            ]);

            $this->response['status'] = true;
            $this->response['message'] = 'Success';
            return response()->json($this->response);
        }catch (\Exception $exception){
            Log::error('Inquiry api error: '. $exception->getMessage());
            $this->response['status'] = false;
            $this->response['message'] = 'Invalid data.';
            return response()->json($this->response, 400);
        }

    }
}
