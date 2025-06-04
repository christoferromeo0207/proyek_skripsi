<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Models\Commission;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Http\Response;  
use Phpml\Classification\DecisionTree;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::query();
        $categories = Category::orderBy('name')->get();

      
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

       
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
        $parents = Post::whereNull('parent_id')->get();

        return view('posts.create', compact('categories', 'parents'));
    }

    public function store(Request $request)
    {
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
            'file_path'         => 'nullable|array',
            'file_path.*'       => 'file|mimes:jpg,jpeg,png,pdf|max:2048',
            'parent_id'         => 'nullable|exists:posts,id',
        ]);

        // Generate slug jika tidak diset
        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);

        // Simpan file unggahan jika ada
        if ($request->hasFile('file_path')) {
            $paths = [];
            foreach ($request->file('file_path') as $file) {
                $paths[] = $file->store('file_path', 'public');
            }
            $data['file_path'] = json_encode($paths);
        }

        // Buat record Post baru (tanpa komisi)
        $post = Post::create($data);

        return redirect()
            ->route('posts.show', $post->slug)
            ->with('success', 'Perusahaan berhasil ditambahkan.');
    }
    
    public function update(Request $request, Post $post)
    {
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
            'file_path.*'       => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'parent_id'         => 'nullable|exists:posts,id',
        ]);

        // Generate slug jika tidak diset
        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);

        // Proses unggah file baru jika ada
        if ($request->hasFile('file_path')) {
            $paths = [];
            foreach ($request->file('file_path') as $file) {
                $paths[] = $file->store('file_path', 'public');
            }
            $data['file_path'] = json_encode($paths);
        }

        // Update Post (tanpa mengikutsertakan komisi)
        $post->update($data);

        return redirect()
            ->route('posts.show', $post->slug)
            ->with('success', 'Perusahaan berhasil diperbarui.');
    }

   public function show(Post $post)
    {
       $post->load('children', 'transactions');


        $allChildren = Post::whereNull('parent_id')
                           ->where('id', '!=', $post->id)
                           ->with('transactions')
                           ->get();

        $commissions = Commission::with(['child', 'transaction'])
                          ->where('parent_post_id', $post->id)
                          ->orderByDesc('created_at')
                          ->get();


        return view('post', compact('post', 'allChildren', 'commissions'))
            ->with('title', $post->title);
    }




    public function edit(Post $post)
    {
        $categories = Category::orderBy('name')->get();
        $users      = User::where('role', 'marketing')
                          ->orderBy('name')
                          ->get();

        $parents = Post::whereNull('parent_id')
                       ->where('id', '!=', $post->id)
                       ->get();

        return view('edit', compact('post', 'categories', 'users', 'parents'));
    }
    


    public function clearCommission(Post $child)
    {
        // 1. Pastikan ini memang anak (memiliki parent_id)
        if (is_null($child->parent_id)) {
            return redirect()->back()
                            ->with('error', 'Tidak dapat menghapus komisi: ini bukan anak perusahaan.');
        }

        // 2. Ambil parent‐nya
        $parent = $child->parent;

        // 3. Kosongkan (reset) kolom komisi di child
        $child->commission_percentage = null;
        $child->commission_amount     = null;
        $child->transaction_value     = null;
        $child->save();

        // 4. Kosongkan kolom komisi di parent juga
        //    (jika Anda benar‐benar ingin me‐null seluruh field komisi parent,
        //     termasuk transaction_value, commission_percentage, dan commission_amount)
        if ($parent) {
            $parent->transaction_value      = null;
            $parent->commission_percentage  = null;
            $parent->commission_amount      = null;
            $parent->save();
        }

        // 5. Redirect kembali ke halaman detail parent
        //    (Jika saja ingin tetap menghitung ulang berdasarkan anak lain, 
        //     lewati langkah 4 dan gunakan logika perhitungan ulang. 
        //     Tetapi karena Anda meminta agar parent juga “dikosongkan”,
        //     maka kita tidak menghitung ulang, melainkan langsung null.)
        return redirect()
            ->route('posts.show', $parent->slug)
            ->with('success', 'Komisi berhasil dihapus untuk ' . $child->title . ' dan parent telah dikosongkan.');
    }


    public function destroy(Post $post)
    {
        $post->delete();

        return redirect()->route('posts.index')
                         ->with('success', 'Perusahaan berhasil dihapus!');
    }



   public function renameFile(Request $request, Post $post, int $index)
    {
        // validasi
        $request->validate([
            'new_name' => 'required|string|max:255',
        ]);

        $files = json_decode($post->file_path ?? '[]', true);

        if (! isset($files[$index])) {
            abort(404, 'File not found.');
        }

        $oldPath = $files[$index];                              
        $ext     = pathinfo($oldPath, PATHINFO_EXTENSION);      
        $base    = Str::slug(pathinfo($oldPath, PATHINFO_FILENAME)); 
        $newName = Str::slug($request->new_name) . '.' . $ext;  
        
        $newPath = dirname($oldPath) . '/' . $newName;          

        Storage::disk('public')->move($oldPath, $newPath);

        $files[$index]    = $newPath;
        $post->file_path  = json_encode(array_values($files));
        $post->save();

        return back()->with('success', 'File renamed successfully.');
    }


    public function deleteFile(Post $post, int $index)
    {

        $files = json_decode($post->file_path ?? '[]', true);

        if (! isset($files[$index])) {
            abort(404, 'File not found.');
        }

        $path = $files[$index];

  
        Storage::disk('public')->delete($path);

      
        array_splice($files, $index, 1);
        $post->file_path = json_encode(array_values($files));
        $post->save();

        return back()->with('success', 'File deleted successfully.');
    }

    public function viewFile(Post $post, int $index)
    {
        $files = json_decode($post->file_path ?? '[]', true);
        if (! isset($files[$index])) {
            abort(404, 'File not found');
        }

  
        $fullPath = Storage::disk('public')->path($files[$index]);

     
        return response()->file($fullPath);
    }

      public function downloadFile(Post $post, int $index)
    {
        $files = json_decode($post->file_path ?? '[]', true);
        if (! isset($files[$index])) {
            abort(404, 'File not found');
        }

        $fullPath = Storage::disk('public')->path($files[$index]);
        $name     = basename($files[$index]);

 
        return response()->download($fullPath, $name);
    }


}
