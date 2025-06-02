<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\CommissionTraining;
use App\Models\CommissionLevel;
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
        // Cek apakah request ini untuk menambah anak (komisi)
        if ($request->filled('parent_id')) {
            // 1) Validasi hanya untuk kasus “tambah anak (komisi)”
            $data = $request->validate([
                'parent_id'         => 'required|exists:posts,id',
                'title'             => 'required|string|max:255',
                'transaction_value' => 'required|numeric|min:0',
            ]);

            $parent = Post::find($data['parent_id']);
            $data['category_id'] = $parent->category_id;

            // Generate slug yang unik:
            $baseSlug = Str::slug($data['title']);       // misal → "kebugaran-a"
            $data['slug'] = $baseSlug . '-' . uniqid();   // misal → "kebugaran-a-60c1a3bcf7a12"

            // Semua field lain yang perlu default
            $data['body']            = '';
            $data['pic_mitra']       = '';
            $data['PIC']             = null;
            $data['phone']           = '';
            $data['email']           = '';
            $data['alamat']          = '';
            $data['keterangan_bpjs'] = null;  // enum(‘yes’,’no’) boleh NULL
            $data['pembayaran']      = '';
            $data['tanggal_awal']    = null;
            $data['tanggal_akhir']   = null;
            $data['file_path']       = '';

            // Hitung persentase komisi (rule‐based)
            if ($parent->parent_id) {
                $percent = 5.00;    // parent sudah level 2
            } else {
                $percent = 7.00;    
            }
            $data['commission_percentage'] = $percent;
            $data['commission_amount']     = $data['transaction_value'] * ($percent / 100.0);

            // Simpan child baru
            $child = Post::create($data);

            // Tambahkan komisi ke field parent (jika memang child)
            $parent->commission_amount = ($parent->commission_amount ?? 0) + $data['commission_amount'];
            $parent->save();

            return redirect()
                ->route('posts.show', $parent->slug)
                ->with('success', 'Komisi berhasil ditambahkan.');

        } else {
            // 2) Biasa: membuat post root (perusahaan baru)
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
                'transaction_value' => 'required|numeric|min:0',
            ]);

            $data['slug'] = $data['slug'] ?? Str::slug($data['title']);

            // Upload file jika ada
            if ($request->hasFile('file_path')) {
                $paths = [];
                foreach ($request->file('file_path') as $file) {
                    $paths[] = $file->store('file_path', 'public');
                }
                $data['file_path'] = json_encode($paths);
            }

            // Hitung rule‐based komisi root jika parent_id diisi,
            // tapi di kasus root parent_id biasanya null
            $percent = 0.00;
            if (!empty($data['parent_id'])) {
                $parent = Post::find($data['parent_id']);
                if ($parent && $parent->parent_id) {
                    $percent = 5.00;
                } else {
                    $percent = 7.00;
                }
            }
            $data['commission_percentage'] = $percent;
            $data['commission_amount']     = $data['transaction_value'] * ($percent / 100.0);

            // Simpan root post
            $root = Post::create($data);

            // Jika ia adalah anak sekaligus, tambahkan komisi ke parent
            if (!empty($data['parent_id'])) {
                $parent->commission_amount = ($parent->commission_amount ?? 0) + $data['commission_amount'];
                $parent->save();
            }

            return redirect()
                ->route('posts.show', $root->slug)
                ->with('success', 'Perusahaan berhasil ditambahkan & komisi dihitung.');
        }
    }

    public function show(Post $post)
    {
        
        $post->load('children.transactions', 'transactions');
        return view('post', compact('post'))
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
            'transaction_value' => 'sometimes|numeric|min:0',
        ]);

        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);

        if ($request->hasFile('file_path')) {
            $paths = [];
            foreach ($request->file('file_path') as $file) {
                $paths[] = $file->store('file_path', 'public');
            }
            $data['file_path'] = json_encode($paths);
        }

        // Kalkulasi ulang komisi jika parent_id diubah atau transaction_value berubah
        $percent = 0.00;
        if (!empty($data['parent_id'])) {
            $parent = Post::find($data['parent_id']);
            if ($parent && $parent->parent_id) {
                $percent = 5.00;
            } else {
                $percent = 7.00;
            }
        }

        $tv = isset($data['transaction_value'])
            ? (float) $data['transaction_value']
            : (float) $post->transaction_value;

        $amount = $tv * ($percent / 100.0);

        $data['commission_percentage'] = $percent;
        $data['commission_amount']     = $amount;

        $post->update($data);

        return redirect()->route('posts.show', $post->slug)
                         ->with('success', 'Perusahaan & komisi berhasil diperbarui.');
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
