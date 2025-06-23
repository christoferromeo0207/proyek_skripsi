<?php

namespace App\Http\Controllers;

use App\Models\MasterBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MasterBarangController extends Controller
{
    // Daftar kata jasa disatukan agar reusable
    protected $kataJasa = [
        'jasa', 'service', 'pelayanan', 'kunjungan', 'rawat', 'periksa',
        'pengiriman', 'perawatan', 'layanan', 'konsultasi', 'asuransi',
        'pengujian', 'pemeriksaan', 'diagnostik', 'cek', 'terapi',
        'injeksi', 'vaksinasi', 'kamar', 'pembayaran', 'registrasi',
        'administrasi', 'penjemputan', 'penanganan', 'analisis',
        'pemrosesan', 'telemedisin', 'booking', 'reservasi', 'survei',
        'verifikasi', 'klaim'
    ];

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'kategori_id' => 'required|exists:categories,id'
        ]);

        // Validasi kata jasa
        if ($this->containsKataJasa($validated['nama_barang'])) {
            return back()->withErrors([
                'nama_barang' => "Nama barang mengandung kata terindikasi jasa. Silakan periksa kembali."
            ])->withInput();
        }

        MasterBarang::create([
            'nama_barang' => $validated['nama_barang'],
            'kategori_id' => $validated['kategori_id'],
        ]);

        return redirect()->back()->with('success', 'Barang baru berhasil ditambahkan.');
    }

    public function storeInline(Request $request)
    {
        $validated = $request->validate([
            'nama'        => 'required|string|max:255',
            'kategori_id' => 'required|exists:categories,id'
        ]);

        // Validasi kata jasa
        if ($this->containsKataJasa($validated['nama'])) {
            return back()->withErrors([
                'nama' => "Nama barang mengandung kata terindikasi jasa. Silakan periksa kembali."
            ])->withInput();
        }

        MasterBarang::create([
            'nama_barang' => $validated['nama'],
            'kategori_id' => $validated['kategori_id']
        ]);

        return redirect()->back()->with('success', 'Barang baru berhasil ditambahkan.');
    }

    // Fungsi pengecekan kata jasa
    private function containsKataJasa($nama)
    {
        $nama = strtolower($nama);
        foreach ($this->kataJasa as $kata) {
            if (Str::contains($nama, $kata)) {
                return true;
            }
        }
        return false;
    }
}
