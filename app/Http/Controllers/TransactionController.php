<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    
    public function index()
    {
        $transactions = Transaction::latest()->get();
        return view('transactions.index', [
            'title'        => 'Data Transaksi Produk',
            'transactions' => $transactions,
        ]);
    }

    // Form tambah transaksi
    public function create(Post $post)
    {
        $users = User::all();
        return view('transactions.create', compact('post','users'));
    }

    public function show(Post $post, Transaction $transaction)
    {
        $users = User::all();
        return view('detailTransaction', compact('post','transaction','users'));
    }

    // Simpan transaksi baru
    public function store(Post $post, Request $request)
    {
        $data = $request->validate([
            'nama_produk'         => 'required|string',
            'jumlah'              => 'required|numeric|min:1',
            'merk'                => 'required|string',
            'harga_satuan'        => 'required|numeric|min:1',
            'tipe_pembayaran'     => 'required|string',
            'pic_rs'              => 'required|exists:users,id',
            'approval_rs'         => 'required|boolean',
            'pic_mitra'           => 'required|string',
            'approval_mitra'      => 'required|boolean',
            'status'              => 'required|string',
            'bukti_pembayaran'    => 'nullable|array',
            'bukti_pembayaran.*'  => 'file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $data['total_harga'] = $data['jumlah'] * $data['harga_satuan'];

        // Proses upload
        $paths = [];
        foreach ($request->file('bukti_pembayaran', []) as $file) {
            if ($file instanceof UploadedFile && $file->isValid()) {
                $paths[] = $file->store('public/bukti_pembayaran');
            }
        }
        if (!empty($paths)) {
            $data['bukti_pembayaran'] = $paths; // Casting will handle JSON encoding
        }


       $post->transactions()->create($data);

        return redirect()
            ->route('posts.show', $post->slug)
            ->with('success','Transaksi berhasil diperbarui');
    }

    // Form edit transaksi
    public function edit(Post $post, Transaction $transaction)
    {
        $users = User::all();
        return view('detailTransaction', compact('post','transaction','users'));
    }

    // Update transaksi
    public function update(Request $request, Post $post, Transaction $transaction)
    {
        $data = $request->validate([
            'nama_produk'         => 'required|string|max:255',
            'jumlah'              => 'required|integer|min:1',
            'merk'                => 'required|string|max:255',
            'harga_satuan'        => 'required|numeric|min:0',
            'tipe_pembayaran'     => 'required|string|max:255',
            'pic_rs'              => 'required|exists:users,id',
            'approval_rs'         => 'required|boolean',
            'pic_mitra'           => 'required|string|max:255',
            'approval_mitra'      => 'required|boolean',
            'status'              => 'required|string|max:100',
            'bukti_pembayaran'    => 'nullable|array',
            'bukti_pembayaran.*'  => 'file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $data['total_harga'] = $data['jumlah'] * $data['harga_satuan'];

        // Upload tambahan
        $newPaths = [];
        foreach ($request->file('bukti_pembayaran', []) as $file) {
            if ($file instanceof UploadedFile && $file->isValid()) {
                $newPaths[] = $file->store('public/bukti_pembayaran');
            }   
        }

        $existingFiles=$transaction->bukti_pembayaran ?: [];

        if (!empty($newPaths)) {
            $existingFiles = $transaction->bukti_pembayaran ?? [];
            $data['bukti_pembayaran'] = array_merge($existingFiles, $newPaths);
        }

        $transaction->update($data);
        Log::info("Update Transaksi: {$transaction->id}", $newPaths);

        return redirect()
            ->route('posts.show', $post->slug)
            ->with('success','Transaksi berhasil diperbarui');
    }


    // Hapus transaksi
    public function destroy(Post $post, Transaction $transaction)
    {
        $transaction->delete();
        return back()->with('success','Transaksi berhasil dihapus');
    }



    // Hapus salah satu file bukti
    public function destroyFile($id, Transaction $transaction, $filename)
    {
        // hapus file fisik
        Storage::disk('public')->delete("bukti_pembayaran/$filename");

        // update array di DB
        $files = $transaction->bukti_pembayaran ?: [];
        $files = array_values(
            array_filter($files, fn($path) => basename($path) !== $filename)
        );
        $transaction->update(['bukti_pembayaran' => $files]);

        return back()->with('success', "File $filename berhasil dihapus.");
    }
}
