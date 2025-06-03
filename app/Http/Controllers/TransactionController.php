<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
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
        return view('transaction', compact('post','users'), [
            'title'=> 'Data Transaksi Produk',
        ]);
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
            'nama_produk'         => 'required|string|max:255',
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
            'bukti_pembayaran.*'  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);
        // dd($data);

        $data['total_harga'] = $data['jumlah'] * $data['harga_satuan'];

       // Proses upload
        $paths = [];
        foreach ($request->file('bukti_pembayaran', []) as $file) {
            if ($file instanceof UploadedFile && $file->isValid()) {
                // Simpan ke storage/app/public/bukti_pembayaran
                $paths[] = $file->store('bukti_pembayaran', 'public');
            }
        }
        if (count($paths)) {
            $data['bukti_pembayaran'] = $paths;
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
        $transaction->load('rsUser'); 
        return view('detailTransaction', compact('post','transaction','users'));
    }

    // Update transaksi

   public function update(Request $request, Post $post, Transaction $transaction)
    {
        // 1) Tangani aksi rename / delete jika request 'action_type' terisi
        if ($request->filled('action_type')) {
            $files = $transaction->bukti_pembayaran ?: [];

            if ($request->action_type === 'delete') {
                $idx = (int) $request->file_index;
                if (isset($files[$idx])) {
                    // Hapus file fisik
                    Storage::disk('public')->delete($files[$idx]);
                    // Hapus dari array
                    array_splice($files, $idx, 1);
                }
                // Simpan array yang sudah di‐update
                $transaction->bukti_pembayaran = $files;
                $transaction->save();

                return back()->with('success', 'File berhasil dihapus');
            }

            if ($request->action_type === 'rename') {
                $idx     = (int) $request->file_index;
                $newName = trim($request->new_name);

                if (isset($files[$idx]) && $newName !== '') {
                    $oldPath = $files[$idx];
                    $ext     = pathinfo($oldPath, PATHINFO_EXTENSION);
                    $dir     = dirname($oldPath);
                    $newPath = $dir . '/' . $newName . '.' . $ext;

                    // Rename fisik di storage
                    Storage::disk('public')->move($oldPath, $newPath);

                    // Update array
                    $files[$idx] = $newPath;
                    $transaction->bukti_pembayaran = $files;
                    $transaction->save();
                }

                return back()->with('success', 'File berhasil di‐rename');
            }
        }

        // 2) Jika bukan rename/delete, maka proses update form transaksi biasa

        // 2.a) Validasi input—HANYA field yang boleh diubah marketing
        $data = $request->validate([
            'nama_produk'        => 'required|string|max:255',
            'jumlah'             => 'required|integer|min:1',
            'merk'               => 'required|string|max:255',
            'harga_satuan'       => 'required|numeric|min:0',
            'tipe_pembayaran'    => 'required|string|max:255',
            'pic_rs'             => 'required|exists:users,id',
            'approval_rs'        => 'required|boolean',
            // 'pic_mitra' dan 'approval_mitra' tidak divalidasi di sini,
            // kita ambil dari model $transaction lama.
            'bukti_pembayaran'   => 'nullable|array',
            'bukti_pembayaran.*' => 'file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // 3) Hitung total_harga berdasarkan input
        $transaction->total_harga = $data['jumlah'] * $data['harga_satuan'];

        // 4) Assignment field‐field yang boleh diubah
        $transaction->nama_produk     = $data['nama_produk'];
        $transaction->jumlah          = $data['jumlah'];
        $transaction->merk            = $data['merk'];
        $transaction->harga_satuan    = $data['harga_satuan'];
        $transaction->tipe_pembayaran = $data['tipe_pembayaran'];
        $transaction->pic_rs          = $data['pic_rs'];
        $transaction->approval_rs     = $data['approval_rs'];

        // 5) Pastikan pic_mitra dan approval_mitra tetap dari database (marketing tdk boleh ubah)
        //    Jadi kita tidak menyentuh $transaction->pic_mitra dan $transaction->approval_mitra di sini.

        // 6) Hitung status otomatis dari kedua approval
        if ($transaction->approval_rs && $transaction->approval_mitra) {
            $transaction->status = 'Selesai';
        } elseif (! $transaction->approval_rs && ! $transaction->approval_mitra) {
            $transaction->status = 'Dibatalkan';
        } else {
            $transaction->status = 'Proses';
        }

        // 7) Proses upload file baru, gabungkan dengan file lama
        $existing = $transaction->bukti_pembayaran ?: [];
        $newFiles = [];

        if ($request->hasFile('bukti_pembayaran')) {
            foreach ($request->file('bukti_pembayaran') as $file) {
                if ($file instanceof UploadedFile && $file->isValid()) {
                    // Simpan ke folder publik di storage/app/public/bukti_pembayaran
                    $newFiles[] = $file->store('bukti_pembayaran', 'public');
                }
            }
        }
        // Gabungkan
        if (!empty($newFiles)) {
            $transaction->bukti_pembayaran = array_merge($existing, $newFiles);
        }

        // 8) Simpan transaksi
        $transaction->save();

        return redirect()
            ->route('posts.show', $post->slug)
            ->with('success', 'Transaksi berhasil diperbarui');
    }


    // Hapus transaksi
    public function destroy(Post $post, Transaction $transaction)
    {
        $transaction->delete();
        return back()->with('success','Transaksi berhasil dihapus');
    }


}
