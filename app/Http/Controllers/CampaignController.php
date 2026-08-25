<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Lead;
use App\Jobs\SendEmailJob;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function create()
    {
        $totalUncontacted = Lead::where('status', 'Uncontacted')->count();
        return view('campaigns.create', compact('totalUncontacted'));
    }

    public function storeAndSend(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        // 1. Simpan Campaign
        $campaign = Campaign::create([
            'user_id' => auth()->id() ?? 1, // Fallback ID jika tanpa login
            'subject' => $request->subject,
            'body' => $request->body,
            'status' => 'Processing',
        ]);

        // 2. Ambil Leads berstatus 'Uncontacted'
        $leads = Lead::where('status', 'Uncontacted')->get();

        $delaySeconds = 0;

        foreach ($leads as $lead) {
            // Ubah status ke Queuing
            $lead->update(['status' => 'Queuing']);

            // Dispatch Job ke Queue dengan jeda 2 detik antar email (mencegah terdeteksi SPAM)
            SendEmailJob::dispatch($lead, $campaign)->delay(now()->addSeconds($delaySeconds));

            $delaySeconds += 2; 
        }

        return redirect()->route('dashboard')->with('success', count($leads) . ' email telah dimasukkan ke dalam antrean pengiriman!');
    }
}
