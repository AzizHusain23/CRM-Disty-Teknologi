<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\LeadsImport;

class LeadImportController extends Controller
{
    public function showImportForm()
    {
        return view('leads.import');
    }

    public function processImport(Request $request)
    {
        // Validasi file yang diupload harus berupa excel/csv
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:10240', // Maks 10MB
        ]);

        try {
            // Eksekusi proses import massal
            Excel::import(new LeadsImport, $request->file('file_excel'));
            
            return back()->with('success', 'Data Leads berhasil diimpor dan dibersihkan!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
