<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActivityController extends Controller
{
    public function store(
        Request $request,
        Customer $customer
    ): RedirectResponse {
        $validated = $request->validate([
            'type' => [
                'required',
                'in:phone_call,whatsapp,meeting,visit,note',
            ],
            'subject' => [
                'nullable',
                'string',
                'max:255',
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'activity_at' => [
                'required',
                'date',
            ],
            'activate_customer' => [
                'nullable',
                'boolean',
            ],
        ], [
            'type.required' => 'Jenis aktivitas wajib dipilih.',
            'type.in' => 'Jenis aktivitas tidak valid.',
            'activity_at.required' => 'Tanggal aktivitas wajib diisi.',
            'activity_at.date' => 'Tanggal aktivitas tidak valid.',
        ]);

        $shouldActivate = $request->boolean('activate_customer');

        if ($shouldActivate && $customer->status !== 'prospect') {
            return back()
                ->withInput()
                ->withErrors([
                    'activate_customer' => 'Hanya customer berstatus Prospect yang dapat dikonversi menjadi Active melalui aktivitas.',
                ]);
        }

        DB::transaction(function () use (
            $validated,
            $request,
            $customer,
            $shouldActivate
        ) {
            Activity::create([
                'customer_id' => $customer->id,
                'user_id' => $request->user()->id,
                'type' => $validated['type'],
                'subject' => $validated['subject'] ?? null,
                'description' => $validated['description'] ?? null,
                'activity_at' => $validated['activity_at'],
            ]);

            if ($shouldActivate) {
                $customer->update([
                    'status' => 'active',
                ]);
            }
        });

        return redirect()
            ->route('customers.show', $customer)
            ->with(
                'success',
                $request->boolean('activate_customer')
                    ? 'Aktivitas dicatat dan customer diubah menjadi Active.'
                    : 'Aktivitas customer berhasil dicatat.'
            );
    }
}
