<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class WhatsappService
{
    public $whatsappApiBaseUrl = 'https://nodwaqpi1.dkinfotechsolutions.in';

    public function sendMessage($mobile, $message)
    {
        $whatsappSetting = \App\Models\Setting::where('name', 'whatsapp_session')
            ->where('is_internal', 1)
            ->first();
        if (!$whatsappSetting) {
            return response()->json(['error' => 'WhatsApp session not found'], 404);
        }

        if(PhoneValidator::isValid($mobile)){
            $response = Http::timeout(121)->connectTimeout(10)
                ->post($this->whatsappApiBaseUrl . '/message/send-text', [
                    'session' => $whatsappSetting->value,
                    'username' => env('WHATSAPP_USERNAME'),
                    'password' => env('WHATSAPP_PASSWORD'),
                    'to' => '91' . $mobile,
                    'text' => $message,
                ]);
            Log::channel('whatsapp')->info('Whatsapp message: ', ['response' => $response->json()]);
        }else{
            Log::channel('whatsapp')->info('Invalid mobile number: '.$mobile);
        }
    }

    public function sendMessageAsync($mobile, $message)
    {
        if($mobile && PhoneValidator::isValid($mobile)){
            \App\Jobs\SendWhatsappMessageJob::dispatch($mobile, $message);
        }else{
            Log::channel('whatsapp')->info('Invalid mobile number: '.$mobile);
        }
    }

    public function sendWelcome($userModel)
    {

        $message = "Dear $userModel->name,\n ";

        if ($userModel->isClient()) {
            $message .= "Welcome to " . env('COMPANY_NAME') . " - we're excited to have you onboard! \nAs a client, you play a crucial role in our journey, and we are committed to providing you with exceptional service and support. \nYour account has been successfully created. Below are your login details to get started: \n";
        } else {
            $message .= "Welcome to " . env('COMPANY_NAME') . "! We are excited to have you as a part of our growing team. Your skills and talents will be a valuable asset to our organization, and we look forward to achieving great things together.\n";
        }

        $message .= "To help you get started, please find your login details below:\nLogin Portal: " . env('APP_URL') . " \nUsername: " . $userModel->email . " \nTemporary Password: " . env('DEFAULT_PASSWORD') . " \n\nMobile App: https://play.google.com/store/apps/details?id=com.dkinfo.taskify \n\n Note: For security reasons, please change your password as soon as possible & upon your first login.\n";
        $message .= "Please complete your KYC by uploading details & documents in your profile kyc section. \n \n";

        if ($userModel->isEmployee()) {
            $message .= "We are committed to making your onboarding experience smooth and welcoming. Your supervisor will be reaching out shortly with further steps and introductions.\n \n Once again, welcome to the team!\n";
        }

        if ($userModel->isClient()) {
            $message .= "Our team is here to support you every step of the way. If you have any questions or need assistance, please feel free to reach out to us at " . env('COMPANY_EMAIL') . " or " . env('COMPANY_PHONE') . ". \nWe look forward to helping you grow with smart, tailored tech solutions. \n";
        }

        $message .= "Warm regards,\n";
        $message .= env('COMPANY_NAME') . " Team \n";
        $message .= env('COMPANY_WEBSITE') . " \n";


        $this->sendMessageAsync($userModel->mobile, $message);
    }

    public function sendProjectNotification($project, $user)
    {
        if ($project){
            $project_name = $project->name ?? '';
            $project_desc = $project->description ?? '';
            $project_start = $project->start_date ?? '';
            $project_end = $project->end_date ?? '';
            $message = "Dear $user->name,\n";
            $message .= "A new project <strong>{$project_name}</strong> has been added.\n";
            $message .= "Project Details:\n";
            $message .= "Name: {$project_name}\n";
            $message .= "Description: {$project_desc}\n";
            $message .= "Start Date: {$project_start}\n";
            $message .= "End Date: {$project_end}\n";
            $this->sendMessageAsync($user->mobile, $message);
        }
    }


    public function sendKycStatus($userType, $mobile, $kyc, $oldStatus='', $newStatus='')
    {
        $message = "Dear {$kyc->user->name}, \n\n";
        if ($kyc->kyc_status == 'rejected') {
            $message .= "We regret to inform you that your KYC verification has been rejected due to {$kyc->remarks}.\n\n";
            $message .= "Please review the submitted details and re-upload the required documents via our portal: \n🔗 " . env('APP_URL') . "\n\n";
            $message .= "If you need any assistance or clarification, feel free to contact us.\nThank you for your cooperation. \n\n";
        } else {
            $message .= "We're pleased to inform you that your KYC verification has been successfully approved.\n\n";
            $message .= "You may now proceed with accessing all services and features available through our platform. If you have any questions or need assistance, feel free to reach out.\n\n";
            $message .= "Thank you for choosing us.\n\n";
        }

        $message .= "Best regards,\n" . env('COMPANY_NAME');

        $this->sendMessageAsync($mobile, $message);
    }
}
