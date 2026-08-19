<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendEmailJob implements ShouldQueue
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

    protected string $recipient;

    protected $mailable;

    protected ?string $logChannel;

    /**
     * Create a new job instance.
     *
     * @param  mixed  $mailable
     */
    public function __construct(string $recipient, $mailable, ?string $logChannel = null)
    {
        $this->recipient = $recipient;
        $this->mailable = $mailable;
        $this->logChannel = $logChannel;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            Mail::to($this->recipient)->send($this->mailable);
        } catch (Throwable $e) {
            // Re-throw exception on queue so Laravel queue worker attempts retries up to $tries without logging transient attempt warnings
            if (! $this->job || $this->attempts() < $this->tries) {
                throw $e;
            }

            // Fallback error logging if executed synchronously (not via queue worker)
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
     * Helper method to log email errors with structured context.
     *
     * @param  string  $stage
     * @param  Throwable  $e
     * @return void
     */
    protected function logError(string $stage, Throwable $e): void
    {
        $channel = $this->logChannel ?? 'jobs';
        $logger = Log::channel($channel);

        $logger->error("SendEmailJob [{$stage}] for recipient {$this->recipient}: ".$e->getMessage(), [
            'stage' => $stage,
            'recipient' => $this->recipient,
            'mailable' => is_object($this->mailable) ? get_class($this->mailable) : null,
            'attempts' => $this->attempts(),
            'max_tries' => $this->tries,
            'error_message' => $e->getMessage(),
            'file' => $e->getFile().':'.$e->getLine(),
            'exception' => $e,
        ]);
    }
}
