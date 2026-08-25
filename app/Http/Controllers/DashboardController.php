<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Metrik Real-time
        $metrics = [
            'total'     => Lead::count(),
            'delivered' => Lead::where('status', 'Delivered')->count(),
            'replied'   => Lead::where('status', 'Replied')->count(),
            'queuing'   => Lead::where('status', 'Queuing')->count(),
        ];

        // Query Searchbar dan Sort By
        $query = Lead::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('institusi', 'like', "%{$search}%")
                    ->orWhere('email_primary', 'like', "%{$search}%");
            });
        }

        switch ($request->input('sort')) {
            case 'status':
                $query->orderBy('status', 'asc');
                break;
            case 'institusi':
                $query->orderBy('institusi', 'asc');
                break;
            default:
                $query->latest();
                break;
        }

        $leads = $query->paginate(10)->withQueryString();

        return view('dashboard', compact('metrics', 'leads'));
    }

    public function updatePhone(Request $request, Lead $lead)
    {
        $request->validate([
            'phone' => 'required|string|max:20',
        ]);

        $lead->update([
            'phone'  => $request->phone,
            'status' => 'Replied', // Memastikan status dikunci ke Replied
        ]);

        return back()->with('success', 'Nomor telepon/WhatsApp prospek berhasil disimpan!');
    }

    public function create()
    {
        return view('leads.create');
    }

    public function store(Request $request)
    {
        // Validasi input manual
        $request->validate([
            'nama'          => 'required|string|max:255',
            'institusi'     => 'required|string|max:255',
            'email_primary' => 'required|email|unique:leads,email_primary',
            'nomer_dok'     => 'nullable|string|max:100',
            'phone'         => 'nullable|string|max:20',
        ]);

        // Simpan ke database
        Lead::create([
            'nama'          => $request->nama,
            'institusi'     => $request->institusi,
            'email_primary' => $request->email_primary,
            'nomer_dok'     => $request->nomer_dok,
            'phone'         => $request->phone,
            // Jika nomor HP langsung diisi, set status ke Replied, jika tidak set Uncontacted
            'status'        => $request->filled('phone') ? 'Replied' : 'Uncontacted',
        ]);

        return redirect()->route('dashboard')->with('success', 'Prospek baru berhasil ditambahkan secara manual!');
    }
}
