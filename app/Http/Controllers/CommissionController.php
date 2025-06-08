<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Commission;
use App\Models\Post;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CommissionController extends Controller
{
    public function index(Request $request)
    {
        
        $commissions = Commission::with(['parent', 'child', 'transaction'])
                          ->orderByDesc('created_at')
                          ->paginate(15);

        return view('commissions.index', compact('commissions'));
    }


    public function create()
    {
        abort(404);
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'parent_post_id'    => 'required|exists:posts,id',
            'child_post_id'     => 'required|exists:posts,id',
            'transaction_id'    => 'nullable|exists:transactions,id',
            'transaction_value' => 'nullable|numeric|min:0',
        ]);

        // diambil dari data yang baru diinputkan
        $parent = Post::findOrFail($data['parent_post_id']);
        $child  = Post::findOrFail($data['child_post_id']);

        // save di parent_id di posts
        $child->parent_id = $parent->id;
        $child->save();

        // 2) Hitung nilai transaksi
        $tv = $data['transaction_id']
            ? Transaction::find($data['transaction_id'])->total_harga
            : ($data['transaction_value'] ?? 0);

        // untuk komisi 7%
        Commission::create([
            'parent_post_id'    => $parent->id,
            'child_post_id'     => $child->id,
            'transaction_id'    => $data['transaction_id'] ?? null,
            'commission_pct'    => 7.00,
            'commission_amount' => $tv * 0.07,
        ]);

        // untuk komisi 5% jika ada
        if ($parent->parent_id) {
            Commission::create([
                'parent_post_id'    => $parent->parent_id,
                'child_post_id'     => $parent->id,       
                'transaction_id'    => $data['transaction_id'] ?? null,
                'commission_pct'    => 5.00,
                'commission_amount' => $tv * 0.05,
            ]);
        }

        return redirect()
            ->route('posts.show', $parent->slug)
            ->with('success', 'Komisi berhasil dibuat untuk dua level.');
    }



    public function show(Commission $commission)
    {
 
        return view('commissions.show', compact('commission'));
    }


    public function edit(Commission $commission)
    {
        $allChildren   = Post::whereNull('parent_id')->get();
        $allParents    = Post::whereNull('parent_id')->get();
        $allTransactions = Transaction::orderBy('created_at', 'desc')->get();

        return view('commissions.edit', compact('commission', 'allChildren', 'allParents', 'allTransactions'));
    }


    public function update(Request $request, Commission $commission)
    {
        $data = $request->validate([
            'transaction_id'    => 'nullable|exists:transactions,id',
            'transaction_value' => 'nullable|numeric|min:0',
        ]);

        if (!empty($data['transaction_id'])) {
            $trx = Transaction::find($data['transaction_id']);
            $tv  = $trx ? $trx->total_harga : 0;
            $commission->transaction_id = $data['transaction_id'];
        } else {
            $tv = $data['transaction_value'] ?? 0;
            $commission->transaction_id = null;
        }

        // Hitung ulang nominal komisi
        $percent = $commission->commission_pct;
        $commission->commission_amount = ($tv * $percent) / 100.0;
        $commission->save();

        return redirect()
            ->route('posts.show', $commission->parent->slug)
            ->with('success', 'Komisi berhasil diperbarui.');
    }

    public function destroy(Commission $commission)
    {
        $parentSlug = $commission->parent->slug;
        $commission->delete();

        return redirect()
            ->route('posts.show', $parentSlug)
            ->with('success', 'Komisi berhasil dihapus.');
    }

     public function disburse($id)
    {
        // Temukan data komisi berdasarkan ID
        $commission = Commission::findOrFail($id);

        // Cek jika status sudah "Sudah Diambil"
        if ($commission->status === 'Sudah Diambil') {
            // Kembali ke halaman sebelumnya dengan pesan (opsional)
            return redirect()->back()
                             ->with('error', 'Komisi sudah berstatus “Sudah Diambil”.');
        }

        // Update kolom status menjadi "Sudah Diambil"
        $commission->status = 'Sudah Diambil';
        $commission->save();

        // Redirect kembali ke halaman yang sama, bisa sertakan flash message
        return redirect()->back()
                         ->with('success', 'Status komisi berhasil diubah menjadi “Sudah Diambil”.');
    }
}
