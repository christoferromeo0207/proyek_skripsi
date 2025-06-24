<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Transaction;
use App\Models\User;
use App\Models\MasterBarang;
use App\Models\MasterJasa;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::latest()->get()->map(function($transaction) {
            if ($transaction->jenis_transaksi == 'jasa') {
                $transaction->durasi = \Carbon\Carbon::parse($transaction->tanggal_mulai)->diffInDays(\Carbon\Carbon::parse($transaction->tanggal_selesai));
            }
            return $transaction;
        });

        return view('transactions.index', [
            'title'        => 'Data Transaksi Produk',
            'transactions' => $transactions,
        ]);
    }


    public function create(Post $post)
    {
        $users   = User::all();
        $barangs = MasterBarang::where('kategori_id', $post->category_id)->get();
        $jasas   = MasterJasa::where('kategori_id', $post->category_id)->get();
        return view('transaction', compact('post', 'users', 'barangs', 'jasas'), [
            'title'=> 'Data Transaksi Produk',
        ]);
    }

    public function show(Post $post, Transaction $transaction)
    {
        $users = User::all();
        return view('detailTransaction', compact('post','transaction','users'));
    }

    public function showJasa(Post $post, Transaction $transaction)
    {
        abort_if($transaction->jenis_transaksi !== 'jasa', 404);

        $users = User::all(); // Jika diperlukan untuk dropdown

        return view('detailJasa', compact('post', 'transaction', 'users'));
    }


    public function store(Post $post, Request $request)
    {
        $jenis = $request->input('jenis_transaksi');

        // Tambahkan flag default jasa ke input request yang diproses validator
        $request->request->add([
            'gunakan_harga_default_jasa' => $request->input('gunakan_harga_default_jasa', 0),
        ]);

        // Pre-isi harga_satuan jika menggunakan harga default jasa
        if ($jenis === 'jasa') {
            $jasa = MasterJasa::find($request->input('master_jasa_id'));
            if ($jasa) {
                if ($request->input('gunakan_harga_default_jasa') == '1') {
                    // Jika pakai default harga, assign ke harga_satuan
                    $request->merge([
                        'harga_satuan' => $jasa->harga,
                        'harga_satuan_jasa' => $jasa->harga, // agar validasi tidak gagal
                    ]);
                } else {
                    // Jika input manual, pastikan harga_satuan diisi dari input user
                    $request->merge([
                        'harga_satuan' => $request->input('harga_satuan_jasa'),
                    ]);
                }
            }
        }

        //@dd($request->all());

        $rules = [
            'jenis_transaksi'    => 'required|in:barang,jasa',
            'tipe_pembayaran'    => 'required|string',
            'pic_rs'             => 'required|exists:users,id',
            'approval_rs'        => 'required|boolean',
            'pic_mitra'          => 'required|string',
            'approval_mitra'     => 'required|boolean',
            'status'             => 'required|string',
            'bukti_pembayaran'   => 'nullable|array',
            'bukti_pembayaran.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ];

        if ($jenis === 'barang') {
            $rules = array_merge($rules, [
                'master_barang_id' => 'required|exists:master_barangs,id',
                'merk'             => 'nullable|string|max:255',
                'jumlah'           => 'required|numeric|min:1',
                'harga_satuan'     => 'required|numeric|min:0',
            ]);
        } elseif ($jenis === 'jasa') {
            $rules = array_merge($rules, [
                'master_jasa_id'   => 'required|exists:master_jasas,id',
                'tanggal_mulai'    => 'required|date',
                'tanggal_selesai'  => 'required|date|after_or_equal:tanggal_mulai',
                'harga_satuan_jasa' => 'required|numeric|min:0'
            ]);

            // Harga jasa hanya required jika tidak pakai default
            if ($request->input('gunakan_harga_default_jasa') == '0') {
                $rules['harga_satuan'] = 'required|numeric|min:0';
            }
        }

        $data = $request->validate($rules);

        $kataJasa = [
            'jasa', 'service', 'pelayanan', 'kunjungan', 'rawat', 'periksa',
            'pengiriman', 'perawatan', 'layanan', 'konsultasi', 'asuransi', 'pengujian',
            'pemeriksaan', 'diagnostik', 'cek', 'terapi', 'injeksi', 'vaksinasi', 'kamar',
            'pembayaran', 'registrasi', 'administrasi', 'penjemputan', 'penanganan', 'analisis',
            'pemrosesan', 'telemedisin', 'booking', 'reservasi', 'survei', 'verifikasi', 'klaim'
        ];

        if ($jenis === 'barang') {
            $barang = MasterBarang::findOrFail($data['master_barang_id']);
            $namaBarang = strtolower($barang->nama_barang);

            foreach ($kataJasa as $kata) {
                if (Str::contains($namaBarang, strtolower($kata))) {
                    return back()->withErrors([
                        'master_barang_id' => "Nama barang mengandung kata '$kata' yang terindikasi jasa. Silakan periksa kembali."
                    ])->withInput();
                }
            }

            if (is_null($barang->kategori_id)) {
                $barang->kategori_id = $post->category_id;
                $barang->save();
            }

            $data['nama_produk']       = $barang->nama_barang;
            $data['total_harga']       = $data['harga_satuan'] * $data['jumlah'];
            $data['master_jasa_id']    = null;
            $data['tanggal_mulai']     = null;
            $data['tanggal_selesai']   = null;
        } else {
            $jasa = MasterJasa::findOrFail($data['master_jasa_id']);
            $namaJasa = strtolower($jasa->nama_jasa);

            $isValid = false;
            foreach ($kataJasa as $kata) {
                if (Str::startsWith($namaJasa, strtolower($kata))) {
                    $isValid = true;
                    break;
                }
            }

            if (! $isValid) {
                return back()->withErrors([
                    'master_jasa_id' => "Nama jasa harus diawali dengan kata jasa (misal: 'jasa pengiriman', 'pelayanan...', dll). Nama '$jasa->nama_jasa' terindikasi barang."
                ])->withInput();
            }

            $data['nama_produk']       = $jasa->nama_jasa;
            $data['merk']              = null;
            $data['jumlah']            = 1;
            $data['harga_satuan']      = $request->input('gunakan_harga_default_jasa') == '1'
                ? $jasa->harga
                : ($data['harga_satuan_jasa'] ?? $jasa->harga);
            $data['total_harga']       = $data['harga_satuan'];
            $data['master_barang_id']  = null;
        }

        // Upload file
        $paths = [];
        foreach ($request->file('bukti_pembayaran', []) as $file) {
            if ($file instanceof UploadedFile && $file->isValid()) {
                $paths[] = $file->store('bukti_pembayaran', 'public');
            }
        }
        if (!empty($paths)) {
            $data['bukti_pembayaran'] = $paths;
        }

        // Simpan transaksi
        $post->transactions()->create($data);

        return redirect()
            ->route('posts.show', $post->slug)
            ->with('success', 'Transaksi berhasil ditambahkan');
    }






    public function edit(Post $post, Transaction $transaction)
    {
        $users = User::all();
        $transaction->load('rsUser'); 
        return view('detailTransaction', compact('post','transaction','users'));
    }

    public function update(Request $request, Post $post, Transaction $transaction)
    {
        $isJasa = $transaction->jenis_transaksi === 'jasa';

        $rules = [
            'nama_produk'        => 'required|string|max:255',
            'harga_satuan'       => 'required|numeric|min:0',
            'tipe_pembayaran'    => 'required|string|max:255',
            'pic_rs'             => 'required|exists:users,id',
            'approval_rs'        => 'required|boolean',
            'bukti_pembayaran'   => 'nullable|array',
            'bukti_pembayaran.*' => 'file|mimes:jpg,jpeg,png,pdf|max:2048',
        ];

        if (! $isJasa) {
            $rules['jumlah'] = 'required|integer|min:1';
            $rules['merk']   = 'nullable|string|max:255';
        }

        $data = $request->validate($rules);

        // Assign data
        $transaction->nama_produk     = $data['nama_produk'];
        $transaction->harga_satuan    = $data['harga_satuan'];
        $transaction->tipe_pembayaran = $data['tipe_pembayaran'];
        $transaction->pic_rs          = $data['pic_rs'];
        $transaction->approval_rs     = $data['approval_rs'];

        if (! $isJasa) {
            $transaction->jumlah = $data['jumlah'];
            $transaction->merk   = $data['merk'] ?? null;
            $transaction->total_harga = $data['harga_satuan'] * $data['jumlah'];
        } else {
            $transaction->jumlah = 1;
            $transaction->total_harga = $data['harga_satuan'];
        }

        // Status otomatis
        if ($transaction->approval_rs && $transaction->approval_mitra) {
            $transaction->status = 'Selesai';
        } elseif (! $transaction->approval_rs && ! $transaction->approval_mitra) {
            $transaction->status = 'Dibatalkan';
        } else {
            $transaction->status = 'Proses';
        }

        // Upload file baru
        $existing = $transaction->bukti_pembayaran ?: [];
        $newFiles = [];
        if ($request->hasFile('bukti_pembayaran')) {
            foreach ($request->file('bukti_pembayaran') as $file) {
                if ($file instanceof UploadedFile && $file->isValid()) {
                    $newFiles[] = $file->store('bukti_pembayaran', 'public');
                }
            }
        }
        if (!empty($newFiles)) {
            $transaction->bukti_pembayaran = array_merge($existing, $newFiles);
        }

        $transaction->save();

        return redirect()
            ->route('posts.show', $post->slug)
            ->with('success', 'Transaksi berhasil diperbarui');
    }


    public function destroy(Post $post, Transaction $transaction)
    {
        $transaction->delete();
        return back()->with('success','Transaksi berhasil dihapus');
    }
}
