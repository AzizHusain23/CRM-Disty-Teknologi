<?php

namespace App\Jobs;

use App\Models\Lead;
use App\Models\Campaign;
use App\Models\EmailLog;
use App\Mail\ProspectBlastMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $lead;
    public $campaign;
    // Batas percobaan jika gagal mengirim
    public $tries = 3;
    /**
     * Create a new job instance.
     */
    public function __construct(Lead $lead, Campaign $campaign)
    {
        $this->lead = $lead;
        $this->campaign = $campaign;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Kirim Email
            Mail::to($this->lead->email_primary)->send(new ProspectBlastMail($this->campaign, $this->lead));

            // Update status pipeline CRM ke Delivered
            $this->lead->update(['status' => 'Delivered']);

            // Catat log pengiriman
            EmailLog::create([
                'campaign_id' => $this->campaign->id,
                'lead_id' => $this->lead->id,
                'status' => 'Delivered',
            ]);

        } catch (Throwable $e) {
            // Jika gagal (bounced/invalid email), ubah status ke Rejected
            $this->lead->update(['status' => 'Rejected']);

            EmailLog::create([
                'campaign_id' => $this->campaign->id,
                'lead_id' => $this->lead->id,
                'status' => 'Bounced',
                'error_message' => $e->getMessage(),
            ]);

            throw $e; // Lempar exception agar queue tahu job ini retried/failed
        }
    }
}
