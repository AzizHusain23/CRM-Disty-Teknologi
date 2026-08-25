<?php

namespace App\Imports;

use App\Models\Lead;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class LeadsImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    /**
    * Memproses koleksi baris dari Excel.
    *
    * @param Collection $rows
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Laravel Excel otomatis mengubah header "Kontak / Email" menjadi "kontak_email"
            $kontak = $row['kontak_email'] ?? '';

            // 1. Abaikan jika isi sel adalah "Tidak ada" atau kosong
            if (empty(trim($kontak)) || strtolower(trim($kontak)) === 'tidak ada') {
                continue;
            }

            // 2. Filter Regex untuk mengekstrak email valid
            // Regex ini mencari pola teks yang menyerupai struktur email standar
            preg_match_all('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $kontak, $matches);

            // Jika regex berhasil menemukan setidaknya 1 email
            if (!empty($matches[0])) {
                // 3. Ambil email pertama sebagai primary (index 0)
                $emailPrimary = $matches[0][0];

                // 4. Masukkan ke database (Gunakan updateOrCreate untuk mencegah duplikat email)
                Lead::updateOrCreate(
                    ['email_primary' => $emailPrimary], // Kondisi pencarian
                    [
                        'nomer_dok' => $row['nomer_dok'] ?? null,
                        'nama'      => $row['nama'] ?? 'Tanpa Nama',
                        'institusi' => $row['instutusi'] ?? '-', // Mengikuti typo 'Instutusi' di Excel Anda
                        'status'    => 'Uncontacted'
                    ]
                );
            }
        }
    }

    /**
    * Membaca file Excel per 500 baris agar RAM server tidak overload (Crash).
    */
    public function chunkSize(): int
    {
        return 500;
    }
}
