<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Models\User;
use App\Models\CommissionTraining;
use App\Models\CommissionLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Phpml\Classification\DecisionTree;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::query();
        $categories = Category::orderBy('name')->get();

        // Pencarian judul
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Filter kategori
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $posts = $query->paginate(10);

        return view('posts', compact('posts', 'categories'))
               ->with('title', 'Daftar Perusahaan');
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        // Pilihan induk (hanya yang belum punya parent, untuk mencegah loop)
        $parents = Post::whereNull('parent_id')->get();

        return view('posts.create', compact('categories', 'parents'));
    }

    public function store(Request $request)
    {
        // 1) Validasi termasuk parent_id & transaction_value
        $data = $request->validate([
            'title'             => 'required|string|max:255',
            'category_id'       => 'required|exists:categories,id',
            'slug'              => 'nullable|string|unique:posts,slug',
            'body'              => 'nullable|string',
            'pic_mitra'         => 'nullable|string|max:255',
            'PIC'               => 'nullable|exists:users,id',
            'phone'             => 'nullable|string|max:20',
            'email'             => 'nullable|email|max:255',
            'alamat'            => 'nullable|string',
            'keterangan_bpjs'   => 'nullable|in:yes,no',
            'pembayaran'        => 'nullable|string|max:100',
            'tanggal_awal'      => 'nullable|date',
            'tanggal_akhir'     => 'nullable|date',
            'file_path'         => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            // tambahan
            'parent_id'         => 'nullable|exists:posts,id',
            'transaction_value' => 'required|numeric|min:0',
        ]);

        // 2) Generate slug jika perlu
        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);

        // 3) Handle upload file jika ada
        if ($request->hasFile('file_path')) {
            $paths = [];
            foreach ($request->file('file_path') as $file) {
                $paths[] = $file->store('public/file_path');
            }
            $data['file_path'] = json_encode($paths);
        }

        // 4) Simpan entri baru (anak perusahaan)
        $child = Post::create($data);

        // Penggunaan Decision Tree
        // $training = CommissionTraining::all();
        // $samples  = $training->pluck('features')->toArray();
        // $labels   = $training->pluck('label')->toArray();
        // $clf      = new DecisionTree(10, 2);
        // $clf->train($samples, $labels);

        // // ─── Predict ───
        // $predLabel = $clf->predict([ $data['transaction_value'] ]);
        // $level     = CommissionLevel::where('label', $predLabel)->first();
        // $percent   = $level?->percentage ?? 0;

        // // ─── Hitung & update parent ───
        // $amount = $data['transaction_value'] * ($percent/100);
        // if ($parent = Post::find($data['parent_id'])) {
        //     $parent->commission_percentage = $percent;
        //     $parent->commission_amount     = $amount;
        //     $parent->save();
        // }

        return redirect()->route('posts.index')
                         ->with('success', 'Perusahaan berhasil ditambahkan & komisi dihitung.');
    }

    public function show(Post $post)
    {
        // Contoh: eager‐load transaksi terkait
        $transactions = $post->transactions()->latest()->get();

        return view('post', compact('post', 'transactions'))
               ->with('title', $post->title);
    }

    public function edit(Post $post)
    {
        $categories = Category::orderBy('name')->get();
        $users      = User::orderBy('name')->get();
        // Pilihan induk kecuali dirinya sendiri
        $parents    = Post::whereNull('parent_id')
                          ->where('id', '!=', $post->id)
                          ->get();

        return view('edit', compact('post', 'categories', 'users', 'parents'));
    }

    
    public function update(Request $request, Post $post)
    {
        // 1) Validasi
        $data = $request->validate([
            'title'             => 'required|string|max:255',
            'category_id'       => 'required|exists:categories,id',
            'slug'              => 'nullable|string|unique:posts,slug,' . $post->id,
            'body'              => 'nullable|string',
            'pic_mitra'         => 'nullable|string|max:255',
            'PIC'               => 'nullable|exists:users,id',
            'phone'             => 'nullable|string|max:20',
            'email'             => 'nullable|email|max:255',
            'alamat'            => 'nullable|string',
            'keterangan_bpjs'   => 'nullable|in:yes,no',
            'pembayaran'        => 'nullable|string|max:100',
            'tanggal_awal'      => 'nullable|date',
            'tanggal_akhir'     => 'nullable|date',
            'file_path'         => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            // tambahan
            'parent_id'         => 'nullable|exists:posts,id',
            'transaction_value' => 'sometimes|numeric|min:0',
        ]);

        // 2) Upload file baru jika ada
        if ($request->hasFile('file_path')) {
            $paths = [];
            foreach ($request->file('file_path') as $file) {
                $paths[] = $file->store('public/file_path');
            }
            $data['file_path'] = json_encode($paths);
        }

        // 3) Simpan ID parent lama untuk pembersihan bila berubah
        $oldParentId = $post->parent_id;
    
        // 4) Update data anak
        $post->update($data);

        // 5) Penggunaan Decision Tree
        // $training   = CommissionTraining::all();
        // $samples    = $training->pluck('features')->toArray();
        // $labels     = $training->pluck('label')->toArray();

        // $classifier = new DecisionTree(10, 2);
        // $classifier->train($samples, $labels);

        // $predLabel = $classifier->predict([ $data['transaction_value'] ]);
        // $level     = CommissionLevel::where('label', $predLabel)->first();
        // $percent   = $level?->percentage ?? 0;
        // $amount    = $data['transaction_value'] * ($percent / 100);

        // // 6) Clear komisi di induk lama bila pindah parent
        // if ($oldParentId && $oldParentId !== $data['parent_id']) {
        //     Post::where('id', $oldParentId)
        //         ->update([
        //             'commission_percentage' => null,
        //             'commission_amount'     => null,
        //         ]);
        // }

        // // 7) Update komisi di induk baru
        // if ($newParent = Post::find($data['parent_id'])) {
        //     $newParent->commission_percentage = $percent;
        //     $newParent->commission_amount     = $amount;
        //     $newParent->save();
        // }

        return redirect()->route('posts.show', $post->slug)
                         ->with('success', 'Perusahaan & komisi berhasil diperbarui.');
    }

    public function destroy(Post $post)
    {
        $post->delete();

        return redirect()->route('posts.index')
                         ->with('success', 'Perusahaan berhasil dihapus!');
    }
}
