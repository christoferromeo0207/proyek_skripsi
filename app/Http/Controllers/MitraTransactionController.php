<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MitraTransactionController extends Controller
{
    public function __construct()
    {
        // pastikan hanya mitra yang bisa mengakses
        $this->middleware(['auth', 'role:mitra']);
    }

    /**
     * Show the form for editing only PIC Mitra, Approval Mitra, and Bukti Pembayaran.
     */
    public function edit(Transaction $transaction)
    {
        // eager‐load relasi untuk menampilkan nama PIC RS
        $transaction->load('rsUser');
        return view('mitra.transactions.edit', compact('transaction'));
    }

    /**
     * Process the mitra‐only updates: pic_mitra, approval_mitra,
     * file‐actions (rename/delete), recompute status, and new uploads.
     */
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
