<?php

namespace App\Http\Controllers;

use App\Models\MasterJasa;
use Illuminate\Http\Request;

class MasterJasaController extends Controller
{
   public function store(Request $request)
    {
        $request->validate([
            'nama_jasa'   => 'required|string|max:255',
            'harga'       => 'required|numeric|min:0',
            'kategori_id' => 'required|exists:categories,id'
        ]);

        $kataJasa = [
            'jasa', 'service', 'pelayanan', 'kunjungan', 'rawat', 'periksa',
            'pengiriman', 'perawatan', 'layanan', 'konsultasi', 'asuransi',
            'pengujian', 'pemeriksaan', 'diagnostik', 'cek', 'terapi',
            'injeksi', 'vaksinasi', 'kamar', 'pembayaran', 'registrasi',
            'administrasi', 'penjemputan', 'penanganan', 'analisis',
            'pemrosesan', 'telemedisin', 'booking', 'reservasi', 'survei',
            'verifikasi', 'klaim'
        ];

        $nama = strtolower($request->input('nama_jasa'));
        $isValid = false;
        foreach ($kataJasa as $kata) {
            if (\Illuminate\Support\Str::startsWith($nama, $kata)) {
                $isValid = true;
                break;
            }
        }

        if (! $isValid) {
            return back()->withErrors([
                'nama_jasa' => "Nama jasa harus diawali dengan kata jasa seperti 'jasa...', 'pelayanan...', dll. Nama '$nama' terindikasi barang."
            ])->withInput();
        }

        MasterJasa::create([
            'nama_jasa'   => $request->nama_jasa,
            'harga'       => $request->harga,
            'kategori_id' => $request->kategori_id
        ]);

        return redirect()->back()->with('success', 'Jasa baru berhasil ditambahkan.');
    }

    public function storeInline(Request $request)
    {
        $validated = $request->validate([
            'nama'        => 'required|string|max:255',
            'harga'       => 'required|numeric|min:0',
            'kategori_id' => 'required|exists:categories,id'
        ]);

        $kataJasa = [
            'jasa', 'service', 'pelayanan', 'kunjungan', 'rawat', 'periksa',
            'pengiriman', 'perawatan', 'layanan', 'konsultasi', 'asuransi',
            'pengujian', 'pemeriksaan', 'diagnostik', 'cek', 'terapi',
            'injeksi', 'vaksinasi', 'kamar', 'pembayaran', 'registrasi',
            'administrasi', 'penjemputan', 'penanganan', 'analisis',
            'pemrosesan', 'telemedisin', 'booking', 'reservasi', 'survei',
            'verifikasi', 'klaim'
        ];

        $nama = strtolower($validated['nama']);
        $isValid = false;
        foreach ($kataJasa as $kata) {
            if (\Illuminate\Support\Str::startsWith($nama, $kata)) {
                $isValid = true;
                break;
            }
        }

        if (! $isValid) {
            return back()->withErrors([
                'nama' => "Nama jasa harus diawali dengan kata jasa seperti 'jasa...', 'pelayanan...', dll. Nama '$nama' terindikasi barang."
            ])->withInput();
        }

        MasterJasa::create([
            'nama_jasa'   => $validated['nama'],
            'harga'       => $validated['harga'],
            'kategori_id' => $validated['kategori_id']
        ]);

        return redirect()->back()->with('success', 'Jasa baru berhasil ditambahkan.');
    }



}
