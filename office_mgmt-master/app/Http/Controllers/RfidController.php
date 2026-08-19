<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use stdClass;

class RfidController extends Controller
{
    public function scan(Request $request)
    {
        try {
            $data = $request->validate([
                'rfid' => 'required|string|max:100|min:1',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => $e->validator->errors(),
                'data' => new stdClass()
            ], 422);
        }

        Log::channel('rfid_scan')->info($data['rfid']);
        $user = User::where('barcode_rfid', $data['rfid'])->first();
        if (!$user) {
            return response()->json(['status'=>false, 'message' => 'Data not found','data'=> new stdClass()], 404);
        }else{
            return response()->json(['status'=>true, 'message' => 'RFID scanned successfully', 'data' => $user->only(['name', 'email', 'barcode_rfid'])], 200);
        }

    }
}
