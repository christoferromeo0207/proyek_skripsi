<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /**
     * Tampilkan daftar perusahaan (posts).
     */
    public function index(Request $request)
    {
        $query = Post::query();

        // Pencarian judul
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Filter kategori berdasarkan slug
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $posts = $query->paginate(10);

        return view('posts', compact('posts'))
        -> with('title', 'Daftar Perusahaan');
    }

    /**
     * Tampilkan form create perusahaan.
     */
    public function create()
    {
        $categories = Category::all(); 
        return view('posts.create', compact('categories'));
    }

    /**
     * Simpan perusahaan baru.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'           => 'required|string|max:255',
            'category_id'     => 'required|exists:categories,slug',    // category_id di DB menampung slug
            'slug'            => 'required|string|unique:posts,slug',
            'body'            => 'nullable|string',
            'pic_mitra'       => 'nullable|string|max:255',
            'PIC'             => 'nullable|string|max:255',
            'phone'           => 'nullable|string|max:20',
            'email'           => 'nullable|email|max:255',
            'alamat'          => 'nullable|string',
            'keterangan_bpjs' => 'nullable|in:yes,no',
            'pembayaran'      => 'nullable|string|max:100',
            'tanggal_awal'    => 'nullable|date',
            'tanggal_akhir'   => 'nullable|date',
            'file_path'       => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // Jika slug tidak diisi, generate dari title
        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);

        // Handle upload file jika ada
        if ($request->hasFile('file_path')) {
            $paths = [];
            foreach ($request->file('file_path') as $file) {
                $paths[] = $file->store('public/file_path');
            }
            $validated['file_path'] = json_encode($paths);
        }

        Post::create($data);

        return redirect()
            ->route('posts.index')
            ->with('success', 'Perusahaan berhasil ditambahkan!');
    }

    public function show(Post $post)
    {
        // eager‐load or query transactions belonging to this post
        $transactions = $post->transactions()->latest()->get();
        return view('post', compact('post','transactions'));
    }

    /**
     * Tampilkan form edit perusahaan.
     */
    public function edit(Post $post)
    {
        $categories = Category::all();
        $users = User::all();
        return view('edit', compact('post','categories','users'));
    }
    

    /**
     * Proses update perusahaan.
     */
    public function update(Request $request, Post $post)
    {
        $data = $request->validate([
            'title'           => 'required|string|max:255',
            'category_id'     => 'required|exists:categories,slug',
            'slug'            => 'nullable|string|unique:posts,slug,' . $post->id,
            'body'            => 'nullable|string',
            'pic_mitra'       => 'nullable|string|max:255',
            'PIC'             => 'nullable|exists:users,id',
            'phone'           => 'nullable|string|max:20',
            'email'           => 'nullable|email|max:255',
            'alamat'          => 'nullable|string',
            'keterangan_bpjs' => 'nullable|in:yes,no',
            'pembayaran'      => 'nullable|string|max:100',
            'tanggal_awal'    => 'nullable|date',
            'tanggal_akhir'   => 'nullable|date',
            'file_path'       => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        if ($request->hasFile('bukti_pembayaran')) {
            $paths = [];
            foreach ($request->file('bukti_pembayaran') as $file) {
                $paths[] = $file->store('public/bukti_pembayaran');
            }
            $data['bukti_pembayaran'] = json_encode($paths);
        }
        
        $post->update($data);
    
        return redirect()
               ->route('posts.show', $post->slug)
               ->with('success','Perusahaan berhasil diperbarui.');
 
    }



    /**
     * Hapus perusahaan.
     */
    public function destroy(Post $post)
    {
        $post->delete();

        return redirect()
            ->route('posts.index')
            ->with('success', 'Perusahaan berhasil dihapus!');
    }
}
