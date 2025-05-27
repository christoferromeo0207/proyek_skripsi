<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class MitraTransactionController extends Controller
{
    public function __construct()
    {
        // pastikan hanya mitra yang bisa mengakses
        $this->middleware(['auth', 'role:mitra']);
    }

    public function create(Post $post)
    {
        // Pastikan hanya Mitra pemilik post yang boleh
        abort_if($post->pic_mitra !== Auth::user()->name, 403);

        return view('mitra.transactions.create', compact('post'));
    }


    public function edit(Transaction $transaction)
    {
        // eager‐load relasi untuk menampilkan nama PIC RS
        $transaction->load('rsUser');
        return view('mitra.transactions.edit', compact('transaction'));
    }

     public function store(Request $req, Post $post)
    {
        abort_if($post->pic_mitra !== Auth::user()->name, 403);

        // 1) Validasi input user
        $data = $req->validate([
            'nama_produk'     => 'required|string|max:255',
            'jumlah'          => 'required|integer|min:1',
            'merk'            => 'required|string|max:255',
            'harga_satuan'    => 'required|numeric|min:0',
            'tipe_pembayaran' => 'required|string|max:255',
            'pic_mitra'       => 'required|string|max:255',
        ]);

        // 2) Hitung total
        $data['total_harga']    = $data['jumlah'] * $data['harga_satuan'];

        // 3) Set nilai yang tidak di‐input oleh user
        $data['post_id']        = $post->id;
        $data['bukti_pembayaran']= null;
        $data['pic_rs']         = null;
        $data['approval_rs']    = 0;
        $data['approval_mitra'] = 0;
        $data['status']         = 'Proses';

        // 4) Simpan ke DB
        Transaction::create($data);

        return redirect()
            ->route('mitra.informasi.show', $post)
            ->with('success','Transaksi berhasil dibuat.');
    }

  
    public function update(Request $request, Transaction $transaction)
    {
        // 1) validasi hanya untuk mitra‐editable
        $data = $request->validate([
            'pic_mitra'          => 'required|string|max:255',
            'approval_mitra'     => 'required|boolean',
            'bukti_pembayaran.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx|max:2048',
            'action_type'        => 'sometimes|in:rename,delete',
            'file_index'         => 'sometimes|integer|min:0',
            'new_name'           => 'sometimes|string|max:255',
        ]);

        // 2) jika ada aksi rename/delete pada file
        if ($request->filled('action_type')) {
            $files = $transaction->bukti_pembayaran_json;
            $idx   = $data['file_index'];

            if ($data['action_type'] === 'delete' && isset($files[$idx])) {
                Storage::disk('public')->delete($files[$idx]);
                array_splice($files, $idx, 1);
            }

            if ($data['action_type'] === 'rename' && isset($files[$idx])) {
                $oldPath = $files[$idx];
                $ext     = pathinfo($oldPath, PATHINFO_EXTENSION);
                $newName = Str::slug($data['new_name']) . '.' . $ext;
                $newPath = dirname($oldPath) . '/' . $newName;
                Storage::disk('public')->move($oldPath, $newPath);
                $files[$idx] = $newPath;
            }

            $transaction->bukti_pembayaran_json = $files;
            $transaction->save();

            return back()->with('success', 'File berhasil diperbarui.');
        }

        // 3) update PIC Mitra & Approval Mitra
        $transaction->pic_mitra      = $data['pic_mitra'];
        $transaction->approval_mitra = $data['approval_mitra'];

        // 4) recompute status
        if ($transaction->approval_rs && $transaction->approval_mitra) {
            $transaction->status = 'Selesai';
        } elseif (! $transaction->approval_rs && ! $transaction->approval_mitra) {
            $transaction->status = 'Dibatalkan';
        } else {
            $transaction->status = 'Proses';
        }

        // 5) simpan upload baru, jika ada
        if ($request->hasFile('bukti_pembayaran')) {
            $stored = [];
            foreach ($request->file('bukti_pembayaran') as $file) {
                $path = $file->store("transactions/{$transaction->id}", 'public');
                $stored[] = $path;
            }
            $transaction->bukti_pembayaran_json = array_merge(
                $transaction->bukti_pembayaran_json,
                $stored
            );
        }

        // 6) simpan perubahan
        $transaction->save();

        return back()->with('success', 'Data transaksi berhasil diperbarui.');
    }


}
