<?php

namespace App\Http\Controllers;

use App\Mail\InternshipOfferMail;
use App\Models\InternshipInterest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PayuPaymentController extends Controller
{
    protected string $key;

    protected string $salt;

    protected string $url;

    public function __construct()
    {
        $this->key = config('services.payu.key');
        $this->salt = config('services.payu.salt');
        $this->url = config('services.payu.base_url');
    }

    public function checkout(Request $request, InternshipInterest $interest)
    {
        if ($interest->payment_status === 'success') {
            return redirect()->route('internship-interests.create')->with('info', 'Internship request already completed Txn Id: ' . $interest->txn_id . '. Please check your registered email address for further details.');
        }

        $metadata = $interest->metadata ?? [];

        if ($interest->txn_id !== null) {
            $pastTransactions = $metadata['past_transactions'] ?? [];
            $pastTransactions[] = [
                'txn_id' => $interest->txn_id,
                'payment_status' => $interest->payment_status,
                'payment_amount' => $interest->payment_amount,
                'archived_at' => now()->toIso8601String(),
            ];
            $metadata['past_transactions'] = $pastTransactions;
        }

        $txnid = $interest->id . 'T' . $interest->phone . 'I' . rand(10, 99);
        $amount = env('INTERNSHIP_REGISTRATION_FEE', '299.00');

        $interest->update([
            'txn_id' => $txnid,
            'payment_amount' => $amount,
            'payment_status' => 'pending',
            'metadata' => $metadata,
        ]);

        $params = [
            'key' => $this->key,
            'txnid' => $txnid,
            'amount' => $amount,
            'productinfo' => 'Internship Registration',
            'firstname' => $interest->name,
            'email' => $interest->email,
            'phone' => $interest->phone,
            'surl' => route('payu.response'),
            'furl' => route('payu.response'),
        ];

        // Hash Sequence: key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5||||||SALT
        $hashString = $params['key'] . '|' . $params['txnid'] . '|' . $params['amount'] . '|' . $params['productinfo'] . '|' . $params['firstname'] . '|' . $params['email'] . '|||||||||||' . $this->salt;
        $params['hash'] = strtolower(hash('sha512', $hashString));

        Log::info('payu parameters', $params);

        return view('payu.redirect', [
            'url' => $this->url,
            'params' => $params,
        ]);
    }

    public function handleResponse(Request $request)
    {
        $data = $request->all();

        Log::info('payu response', $data);

        // Sequence: SALT|status||||||udf5|udf4|udf3|udf2|udf1|email|firstname|productinfo|amount|txnid|key
        $reverseHashString = $this->salt . '|' . $data['status'] . '|||||||||||' . $data['email'] . '|' . $data['firstname'] . '|' . $data['productinfo'] . '|' . $data['amount'] . '|' . $data['txnid'] . '|' . $data['key'];

        $calculatedHash = strtolower(hash('sha512', $reverseHashString));

        if ($calculatedHash !== $data['hash']) {
            $status = 'failure';
            $message = 'Transaction Verification failed. Potential tampering detected. If money is debited then please contact us.';

            return view('payu.response', compact('data', 'message', 'status'));
        }

        $interest = InternshipInterest::where('txn_id', $data['txnid'])->firstOrFail();

        if ($interest->payment_status != 'pending') {
            if ($interest->payment_status == 'success') {
                $message = 'Internship request already completed. Please check your registered email address for further details.';
            } else {
                $message = 'Internship request already failed. Please try again.';
            }

            return redirect()->route('internship-interests.create')->with('info', $message);
        }

        $metadata = $interest->metadata ?? [];
        $metadata['bank_ref_num'] = $data['bank_ref_num'] ?? null;
        $metadata['mode'] = $data['mode'] ?? null;

        if ($data['status'] === 'success') {
            $status = 'success';
            $message = 'Payment Successful. Your internship request has been submitted for review. You will be notified once confirmed.';
        } else {
            $status = 'failure';
            $message = 'Payment Failed: ' . ($data['error_Message'] ?? 'Unknown Error. If money is debited then please contact us.');
            $metadata['pg_error_message'] = $message;
        }

        $interest->update([
            'metadata' => $metadata,
            'payment_status' => $data['status'],
        ]);

        if ($data['status'] === 'success') {
            try {
                if ($interest->position) {
                    $position = $interest->position;
                } else {
                    $position = 'Web/Software Development';
                }

                $position .= ' ' . $interest->type;

                \App\Jobs\SendEmailJob::dispatch($interest->email, new InternshipOfferMail($interest->name, $position));
            } catch (\Exception $e) {
                Log::error('Failed to dispatch internship offer email job: ' . $e->getMessage());
            }
        }

        return view('payu.response', compact('data', 'status', 'message'));
    }
}
