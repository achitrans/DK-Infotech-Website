<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    private $response = ['status' => false, 'message' => '', 'data' => []];

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            $this->response['error'] = $validator->errors()->all();
            return response()->json($this->response, 400);
        }


        $user = User::where('email', $request->email)->first();
        if (!$user) {
            $this->response['message'] = 'Invalid Credentials';
            return response()->json($this->response, 400);
        } else {
            if ($user->is_banned == 'yes') {
                $this->response['message'] = 'User is not active/disabled. Please contact support';
                return response()->json($this->response);
            }

            if (Auth::attempt(['email' => $validator->validated()['email'], 'password' => $validator->validated()['password']])) {

//                // deleting the past token
//                DB::table('personal_access_tokens')
//                    ->where('tokenable_type', 'App\Models\User')
//                    ->where('tokenable_id', $user->id)->delete();

                $token = $user->createToken($user->id . 't' . date('YmdHis'));

                $this->response['status'] = true;
                $this->response['message'] = 'Login Successful';
                $this->response['data'] = $user;
                $this->response['token'] = $token->plainTextToken;


            } else {
                $this->response['message'] = 'Invalid Credentials.';
            }

            return response()->json($this->response);
        }
    }

    public function getAuthUser()
    {
        $this->response['message'] = 'Success';
        $this->response['status'] = true;
        $this->response['data'] = Auth::user();
        return response()->json($this->response);
    }

    public function logoutAll()
    {
        Auth::user()->tokens()->delete();
        $this->response['message'] = 'All Session Ended.';
        $this->response['status'] = true;
        return response($this->response);
    }

    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();
        $this->response['message'] = 'Session Ended.';
        $this->response['status'] = true;
        return response($this->response);
    }

    public function mobileLogin(Request $request)
    {

        $token = $request->query('token');
        if (!$token) abort(403, 'Invalid Request. Please restart application.');

        $personalToken = PersonalAccessToken::findToken($token);
        if (!$personalToken) abort(403, 'Invalid Request. Please restart application..');

        $user = $personalToken->tokenable;
        // OPTIONAL — single use token for security:
        // $personalToken->delete();

        Auth::login($user);
        $request->session()->regenerate();

        return redirect(route('dashboard'));
    }


    public function mobileTest()
    {
        return "inside mobile with session. Name:". \Illuminate\Support\Facades\Auth::user()->name;
    }

}
