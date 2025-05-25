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
        return view('detailTransaction', compact('post','transaction','users'));
    }

    // Update transaksi

    public function update(Request $request, Post $post, Transaction $transaction)
    {

        // 1) Handle aksi rename / delete
        if ($request->filled('action_type')) {
            $files = $transaction->bukti_pembayaran ?: [];

            if ($request->action_type === 'delete') {
                $idx = (int) $request->file_index;
                if (isset($files[$idx])) {
                    Storage::disk('public')->delete($files[$idx]);
                    array_splice($files, $idx, 1);
                }
                $transaction->bukti_pembayaran = $files;
                $transaction->save();

                return back()->with('success','File berhasil dihapus');
            }

            if ($request->action_type === 'rename') {
                $idx     = (int) $request->file_index;
                $newName = trim($request->new_name);
                if (isset($files[$idx]) && $newName !== '') {
                    $oldPath = $files[$idx];
                    $ext     = pathinfo($oldPath, PATHINFO_EXTENSION);
                    $dir     = dirname($oldPath);
                    $newPath = $dir.'/'.$newName.'.'.$ext;

                    // rename fisik
                    Storage::disk('public')->move($oldPath, $newPath);

                    // update array
                    $files[$idx] = $newPath;
                    $transaction->bukti_pembayaran = $files;
                    $transaction->save();
                }

                return back()->with('success','File berhasil di-rename');
            }
        }

        // 2) Jika bukan rename/delete, maka ini normal form-update transaksi
        $data = $request->validate([
            'nama_produk'        => 'required|string|max:255',
            'jumlah'             => 'required|integer|min:1',
            'merk'               => 'required|string|max:255',
            'harga_satuan'       => 'required|numeric|min:0',
            'tipe_pembayaran'    => 'required|string|max:255',
            'pic_rs'             => 'required|exists:users,id',
            'approval_rs'        => 'required|boolean',
            'pic_mitra'          => 'required|string|max:255',
            'approval_mitra'     => 'required|boolean',
            'status'             => 'required|string|max:100',
            'bukti_pembayaran'   => 'nullable|array',
            'bukti_pembayaran.*' => 'file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // hitung total
        $data['total_harga'] = $data['jumlah'] * $data['harga_satuan'];

        $transaction->pic_mitra      = $data['pic_mitra'];
        $transaction->approval_mitra = $data['approval_mitra'];

        if ($transaction->approval_rs && $transaction->approval_mitra) {
            $transaction->status = 'Selesai';
        } elseif (! $transaction->approval_rs && ! $transaction->approval_mitra) {
            $transaction->status = 'Dibatalkan';
        } else {
            $transaction->status = 'Proses';
        }

        // ambil list file lama
        $existing = $transaction->bukti_pembayaran ?: [];

        // Proses upload
        $paths = [];
        foreach ($request->file('bukti_pembayaran', []) as $file) {
            if ($file instanceof UploadedFile && $file->isValid()) {
                // Simpan ke storage/app/public/bukti_pembayaran
                $paths[] = $file->store('bukti_pembayaran', 'public');
            }
        }
        if (!empty($paths)) {
            // Akan disimpan sebagai JSON array ["bukti_pembayaran/xxx.png", …]
            $data['bukti_pembayaran'] = $paths;
        }


        // update model
        $transaction->update($data);

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


}
