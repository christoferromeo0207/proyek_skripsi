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
            'parent_id'         => 'nullable|exists:posts,id',
            'transaction_value' => 'required|numeric|min:0',
        ]);

        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);

       
        if ($request->hasFile('file_path')) {
            $paths = [];
            foreach ($request->file('file_path') as $file) {
                $paths[] = $file->store('file_path', 'public');
            }
            $data['file_path'] = json_encode($paths);
        }

       
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

        $transactions = $post->transactions()->latest()->get();

        return view('post', compact('post', 'transactions'))
               ->with('title', $post->title);
    }

    public function edit(Post $post)
    {
        $categories = Category::orderBy('name')->get();
        $users = User::where('role','marketing')
                ->orderBy('name')
                ->get();
        // Pilihan induk untuk komisi
        $parents    = Post::whereNull('parent_id')
                          ->where('id', '!=', $post->id)
                          ->get();

        return view('edit', compact('post', 'categories', 'users', 'parents'));
    }

    
    public function update(Request $request, Post $post)
    {
        // Validasi
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
            'file_path[]'         => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            // tambahan
            'parent_id'         => 'nullable|exists:posts,id',
            'transaction_value' => 'sometimes|numeric|min:0',
        ]);
    

        if ($request->hasFile('file_path')) {
            $paths = [];
            foreach ($request->file('file_path') as $file) {
                $paths[] = $file->store('file_path', 'public');
            }
            $data['file_path'] = json_encode($paths);
        }

        $oldParentId = $post->parent_id;
    
        // untuk komisi jika ada
        $post->update($data);


        // $training   = CommissionTraining::all();
        // $samples    = $training->pluck('features')->toArray();
        // $labels     = $training->pluck('label')->toArray();

        // $classifier = new DecisionTree(10, 2);
        // $classifier->train($samples, $labels);

        // $predLabel = $classifier->predict([ $data['transaction_value'] ]);
        // $level     = CommissionLevel::where('label', $predLabel)->first();
        // $percent   = $level?->percentage ?? 0;
        // $amount    = $data['transaction_value'] * ($percent / 100);

        // if ($oldParentId && $oldParentId !== $data['parent_id']) {
        //     Post::where('id', $oldParentId)
        //         ->update([
        //             'commission_percentage' => null,
        //             'commission_amount'     => null,
        //         ]);
        // }

        // // Update komisi
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
