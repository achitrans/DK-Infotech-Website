<?php

namespace App\Jobs;

use App\Services\WhatsappService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendWhatsappMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var array
     */
    public $backoff = [15, 45, 90];

    protected string $mobile;

    protected string $message;

    /**
     * Create a new job instance.
     *
     * @param  string  $mobile
     * @param  string  $message
     */
    public function __construct(string $mobile, string $message)
    {
        $this->mobile = $mobile;
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @param  WhatsappService  $whatsappService
     * @return void
     */
    public function handle(WhatsappService $whatsappService)
    {
        try {
            $whatsappService->sendMessage($this->mobile, $this->message);
        } catch (Throwable $e) {
            if (! $this->job || $this->attempts() < $this->tries) {
                throw $e;
            }

            $this->logError('Sync Attempt Failed', $e);
        }
    }

    /**
     * Handle job failure when all retry attempts have been exhausted.
     *
     * @param  Throwable  $exception
     * @return void
     */
    public function failed(Throwable $exception): void
    {
        $this->logError('Job Permanently Failed (Max Tries Exhausted)', $exception);
    }

    /**
     * Helper method to log WhatsApp errors with structured context.
     *
     * @param  string  $stage
     * @param  Throwable  $e
     * @return void
     */
    protected function logError(string $stage, Throwable $e): void
    {
        Log::channel('whatsapp')->error("SendWhatsappMessageJob [{$stage}] for {$this->mobile}: ".$e->getMessage(), [
            'stage' => $stage,
            'mobile' => $this->mobile,
            'attempts' => $this->attempts(),
            'max_tries' => $this->tries,
            'error_message' => $e->getMessage(),
            'file' => $e->getFile().':'.$e->getLine(),
            'exception' => $e,
        ]);
    }
}
