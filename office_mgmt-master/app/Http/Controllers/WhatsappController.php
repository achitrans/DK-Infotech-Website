<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\PhoneValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WhatsappController extends Controller
{
    public $whatsappApiBaseUrl = 'https://nodwaqpi1.dkinfotechsolutions.in';

    /**
     * Protected helper to execute external WhatsApp API calls safely with try-catch and logging.
     */
    protected function callWhatsappApi(string $method, string $endpoint, array $data = [], int $timeout = 30): array
    {
        $whatsappSetting = Setting::where('name', 'whatsapp_session')
            ->where('is_internal', 1)
            ->first();

        $sessionValue = $whatsappSetting ? $whatsappSetting->value : null;

        $payload = array_merge([
            'session' => $sessionValue,
            'username' => env('WHATSAPP_USERNAME'),
            'password' => env('WHATSAPP_PASSWORD'),
        ], $data);

        $url = rtrim($this->whatsappApiBaseUrl, '/').'/'.ltrim($endpoint, '/');

        try {
            $httpClient = Http::timeout($timeout)->connectTimeout(10);

            if (strtoupper($method) === 'GET') {
                $response = $httpClient->get($url, $payload);
            } else {
                $response = $httpClient->post($url, $payload);
            }

            $responseData = null;
            try {
                $responseData = $response->json();
            } catch (\Throwable $e) {
                $responseData = ['raw_body' => $response->body()];
            }

            Log::channel('whatsapp')->info("WhatsApp API {$endpoint} response: ", [
                'status' => $response->status(),
                'response' => $responseData,
            ]);

            return [
                'success' => $response->successful(),
                'status_code' => $response->status(),
                'data' => $responseData,
                'response' => $response,
                'error' => $response->successful() ? null : ($responseData['message'] ?? 'API request failed with status '.$response->status()),
            ];
        } catch (\Throwable $e) {
            Log::channel('whatsapp')->error("WhatsApp API {$endpoint} exception: ".$e->getMessage(), [
                'endpoint' => $endpoint,
                'payload' => array_merge($payload, ['password' => '***']),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'status_code' => 500,
                'data' => null,
                'response' => null,
                'error' => 'API Connection Error.',
            ];
        }
    }

    public function index()
    {
        // checking the existence of the 'whatsapp' setting
        $whatsappSetting = Setting::where('name', 'whatsapp_session')
            ->where('is_internal', 1)
            ->first();
        if (! $whatsappSetting) {
            $whatsappSetting = new Setting;
            $whatsappSetting->name = 'whatsapp_session';
            $whatsappSetting->value = Str::random(67);
            $whatsappSetting->is_internal = 1;
            $whatsappSetting->save();
        }

        $responseBody = $this->checkSessionStatus();

        if ($responseBody && isset($responseBody['data']['message'])) {
            $status = $responseBody['data']['message'];
            if ($status === 'Connected') {
                // session is connected
                $id = $responseBody['data']['user']['id'] ?? null;
                $id = explode(':', $id)[0] ?? null;

                return view('whatsapp.index', ['status' => $status, 'data' => $id, 'message' => $status]);
            } elseif ($status === 'Connecting' && Cache::has($whatsappSetting->value)) {
                // session is connecting
                $data = Cache::get($whatsappSetting->value);

                return view('whatsapp.index', ['status' => $status, 'message' => 'Connecting, please wait...', 'qr' => $data['qr-image'] ?? null, 'expiry' => $data['expiry'] ?? null]);
            } else {
                // logout session and restart it
                $this->callWhatsappApi('POST', '/session/logout');

                $startRes = $this->callWhatsappApi('POST', '/session/start');
                if ($startRes['success'] && isset($startRes['data']['data'])) {
                    $startData = $startRes['data']['data'];
                    Cache::remember($whatsappSetting->value, now()->addSeconds(26), function () use ($startData) {
                        return $startData;
                    });
                    $status = 'Scan the QR code to connect your WhatsApp session.';

                    return view('whatsapp.index', [
                        'status' => $status,
                        'message' => $status,
                        'qr' => $startData['qr-image'] ?? null,
                        'expiry' => $startData['expiry'] ?? null,
                    ]);
                } else {
                    return view('whatsapp.index', [
                        'status' => $status ?? 'Disconnected',
                        'error' => 'Unknown session status: '.($startRes['error'] ?? 'Failed to start session'),
                    ]);
                }
            }
        } else {
            if (isset($responseBody['error'])) {
                $message = $responseBody['error'];
            } elseif (isset($responseBody['message'])) {
                $message = $responseBody['message'];
            } else {
                $message = 'Failed to check session status.';
            }

            return view('whatsapp.index', ['error' => $message]);
        }
    }

    public function checkSessionStatus()
    {
        $whatsappSetting = Setting::where('name', 'whatsapp_session')
            ->where('is_internal', 1)
            ->first();
        if (! $whatsappSetting) {
            return ['error' => 'WhatsApp session not found'];
        }

        $res = $this->callWhatsappApi('GET', '/session/status');
        if ($res['success'] && is_array($res['data'])) {
            return $res['data'];
        }

        return ['error' => $res['error'] ?? 'Failed to check session status.'];
    }

    public function logout()
    {
        $whatsappSetting = Setting::where('name', 'whatsapp_session')
            ->where('is_internal', 1)
            ->first();
        if (! $whatsappSetting) {
            return response()->json(['error' => 'WhatsApp session not found'], 404);
        }

        $res = $this->callWhatsappApi('POST', '/session/logout');

        if ($res['success']) {
            Cache::forget($whatsappSetting->value);
            Setting::where('name', 'whatsapp_session')->where('is_internal', 1)->update(['value' => Str::random(67)]);

            return redirect()->route('whatsapp.index')->with('success', 'WhatsApp session logged out successfully.');
        } else {
            return redirect()->route('whatsapp.index')->withErrors(['error' => 'Failed to logout WhatsApp session: '.($res['error'] ?? '')]);
        }
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:1100',
            'message' => 'required|string',
            'image' => 'nullable|file|mimes:jpg,jpeg,png|max:1025',
        ]);

        $whatsappSetting = Setting::where('name', 'whatsapp_session')
            ->where('is_internal', 1)
            ->first();
        if (! $whatsappSetting) {
            return response()->json(['error' => 'WhatsApp session not found'], 404);
        }

        $phoneNumbers = explode(',', str_replace([' ', '-', '(', ')', '+91'], '', $request->phone));
        $successfulNumbers = [];
        $failedNumbers = [];
        $inValidNumbers = [];
        $path = null;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $path = $file->store('whatsapp_image/'.now()->toDateString().'/', 'public');
            $endpoint = '/message/send-image';
        } else {
            $endpoint = '/message/send-text';
        }

        try {
            foreach ($phoneNumbers as $item) {
                $item = trim($item);
                if (empty($item)) {
                    continue;
                }

                if (PhoneValidator::isValid($item)) {
                    $body = [
                        'to' => '91'.$item,
                        'text' => $request->input('message'),
                    ];

                    if ($path) {
                        $body['image_url'] = asset(Storage::url($path));
                    }

                    $res = $this->callWhatsappApi('POST', $endpoint, $body, 90);

                    if ($res['success']) {
                        $successfulNumbers[] = $item;
                    } else {
                        $failedNumbers[] = $item;
                    }

                    sleep(rand(2, 5));
                } else {
                    $inValidNumbers[] = $item;
                }
            }
        } finally {
            if ($path) {
                Storage::disk('public')->delete($path);
            }
        }

        $msgParts = [];
        if (! empty($successfulNumbers)) {
            $msgParts[] = 'Message sent to '.implode(', ', $successfulNumbers);
        }
        if (! empty($failedNumbers)) {
            $msgParts[] = 'Failed to send to '.implode(', ', $failedNumbers);
        }
        if (! empty($inValidNumbers)) {
            $msgParts[] = 'Invalid numbers: '.implode(', ', $inValidNumbers);
        }

        $msg = implode('. ', $msgParts);

        if (empty($successfulNumbers)) {
            return back()->withErrors(['error' => $msg ?: 'Failed to send message.']);
        }

        return back()->with('success', $msg);
    }
}
