<?php

namespace App\Http\Controllers;

use App\Models\Commission;
use App\Models\Post;
use App\Models\Transaction;
use Illuminate\Http\Request;

class CommissionController extends Controller
{
    public function index(Request $request)
    {
        // Jika ingin men‐paginate semua komisi:
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

        $parent = Post::find($data['parent_post_id']);
        $child  = Post::find($data['child_post_id']);

        if (!$parent || !$child) {
            return back()->withErrors('Parent atau anak perusahaan tidak ditemukan.');
        }

        // Tentukan persentase murah: 7% untuk level 1, 5% untuk level 2
        $percent = $parent->parent_id ? 5.00 : 7.00;

        // Hitung transaction_value
        if (!empty($data['transaction_id'])) {
            $trx = Transaction::find($data['transaction_id']);
            $tv  = $trx ? $trx->total_harga : 0;
        } else {
            $tv = $data['transaction_value'] ?? 0;
        }

        $commissionAmount = ($tv * $percent) / 100.0;

        $commission = Commission::create([
            'parent_post_id'    => $parent->id,
            'child_post_id'     => $child->id,
            'transaction_id'    => $data['transaction_id'] ?? null,
            'commission_pct'    => $percent,
            'commission_amount' => $commissionAmount,
        ]);

        return redirect()
            ->route('posts.show', $parent->slug)
            ->with('success', 'Komisi berhasil ditambahkan.');
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
}
