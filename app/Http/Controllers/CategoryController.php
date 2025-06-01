<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Models\Author;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * Add a new post to a specific category
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $categoryId
     * @return \Illuminate\Http\RedirectResponse
     */
    /**
     * Display a listing of categories
     *
     * @return \Illuminate\View\View
     */




    public function index()
    {
        Log::info('Accessing categories index page', [
            'user_id' => Auth::id(),
            'ip' => request()->ip()
        ]);

        try {
            $categories = Category::with('posts')->get();
            $users = User::all();

            Log::debug('Categories loaded successfully', [
                'category_count' => $categories->count(),
                'user_count' => $users->count()
            ]);

            return view('categories', compact('categories', 'users'));

            
        } catch (\Exception $e) {
            Log::error('Error loading categories index page', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->route('home')
                ->with('error', 'Unable to load categories. Please try again later.');
        }
    }

    public function addData(Request $request, $categoryId)
    {
        Log::info('Attempting to add a new post', [
            'user_id' => Auth::id(),
            'category_id' => $categoryId,
            'ip' => request()->ip()
        ]);

        try {
            // Check if category exists
            $category = Category::findOrFail($categoryId);
            
            Log::debug('Category found', ['category_name' => $category->name]);

            // 1. Validate all fields according to DB columns
            $validationRules = [
                'title'           => 'required|string|max:255',
                'pic_mitra'       => 'required|string|max:255',
                'body'            => 'required|string',
                'phone'           => 'required|string|max:20',
                'email'           => 'required|email|max:255',
                'alamat'          => 'required|string|max:255',
                'keterangan_bpjs' => 'required|in:yes,no',
                'pembayaran'      => 'required|string|max:100',
                'tanggal_awal'    => 'required|date',
                'tanggal_akhir'   => 'required|date|after_or_equal:tanggal_awal',
                'PIC'             => 'required|exists:users,id',
            ];
            
            // Only add file validation if a file is being uploaded
            if ($request->hasFile('file_path')) {
                $validationRules['file_path'] = 'required|file|mimes:jpg,jpeg,png,pdf|max:2048';
            }
            
            $validated = $request->validate($validationRules);

            Log::debug('Request validated successfully');

            // 2. Prepare new model
            $post = new Post();
            $post->title           = $validated['title'];
            $post->slug            = Str::slug($validated['title']);
            $post->pic_mitra       = $validated['pic_mitra'];
            $post->body            = $validated['body'];
            $post->phone           = $validated['phone'];
            $post->email           = $validated['email'];
            $post->alamat          = $validated['alamat'];
            $post->keterangan_bpjs = $validated['keterangan_bpjs'];
            $post->pembayaran      = $validated['pembayaran'];
            $post->tanggal_awal    = $validated['tanggal_awal'];
            $post->tanggal_akhir   = $validated['tanggal_akhir'];
            
            $post->category_id     = $categoryId;
            

            $post->PIC = $validated['PIC'];
            
            Log::debug('Assigning PIC value', [
                'assigned_pic' => $validated['PIC'],
                'user_exists' => User::where('id', $validated['PIC'])->exists()
            ]);
            
            if ($request->hasFile('file_path') && $request->file('file_path')->isValid()) {
                Log::debug('File upload detected, processing file');
                
                try {
                    $file = $request->file('file_path');
                    $path = $file->store('posts', 'public');
                    $post->file_path = $path;
                    
                    Log::info('File uploaded successfully', [
                        'original_filename' => $file->getClientOriginalName(),
                        'stored_path' => $path,
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType()
                    ]);
                } catch (\Exception $e) {
                    Log::error('File upload failed', [
                        'error' => $e->getMessage(),
                        'file' => $request->file('file_path')->getClientOriginalName()
                    ]);
                    
                    return redirect()
                        ->back()
                        ->withInput()
                        ->with('error', 'File upload failed. Please try again.');
                }
            }
            
            // 6. Save to database
            $post->save();
            
            Log::info('Post created successfully', [
                'post_id' => $post->id,
                'post_title' => $post->title,
                'category_id' => $post->category_id,
                'pic_user_id' => $post->PIC
            ]);
            
            return redirect()
                ->back()
                ->with('success', 'Post added successfully!');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validation failed when adding post', [
                'errors' => $e->errors(),
                'user_id' => Auth::id(),
                'request_data' => $request->except(['file_path', 'password'])
            ]);
            
            throw $e; // Laravel will handle redirecting with errors
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Category not found', [
                'category_id' => $categoryId,
                'user_id' => Auth::id()
            ]);
            
            return redirect()
                ->back()
                ->with('error', 'Category not found!');
                
        } catch (\Exception $e) {
            Log::error('Failed to add post', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'category_id' => $categoryId
            ]);
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'An error occurred while adding the post. Please try again.');
        }
    }

    public function create()
    {
      
        Log::info('Accessing category creation form', [
            'user_id'=> Auth::id(),
            'ip' => request()->ip(),
        ]);

  
        return view('categoryCreate');
    }


     public function store(Request $request)
    {
        Log::info('Attempting to store new category', [
            'user_id' => Auth::id(),
            'ip'      => request()->ip(),
        ]);

        // Validasi input: name dan description wajib diisi
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            // 1) Buat slug dari nama
            $slug = Str::slug($validated['name']);

            // Jika slug kembar, tambahkan angka di belakang (misal rumah-sakit-2, dst):
            $originalSlug = $slug;
            $counter = 1;
            while (Category::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter++;
            }

            // 2) Generate warna baru (hex) yang belum ada di tabel
            //    Kita pakai loop: generate random hex, cek unique
            do {
                // Misal kita generate kode warna acak di rentang #000000 sampai #FFFFFF
                $randomColor = sprintf(
                    '#%06X', 
                    mt_rand(0, 0xFFFFFF)
                );
            } while (Category::where('color', $randomColor)->exists());

            // 3) Simpan data ke database
            $category = new Category();
            $category->name        = $validated['name'];
            $category->slug        = $slug;
            $category->color       = $randomColor;
            $category->description = $validated['description'] ?? null;
            $category->save();

            Log::info('Category created successfully', [
                'category_id'   => $category->id,
                'category_name' => $category->name,
                'slug'          => $category->slug,
                'color'         => $category->color,
            ]);

            return redirect()
                ->route('categories.index')
                ->with('success', 'Kategori Mitra baru berhasil ditambahkan!');
        } catch (\Exception $e) {
            Log::error('Failed to create category', [
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'user_id' => Auth::id(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan kategori. Silakan coba lagi.');
        }
    }



    private function logPostView($postId)
    {
        try {
            Log::info('Post viewed', [
                'post_id' => $postId,
                'user_id' => Auth::id() ?? 'guest',
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to log post view', [
                'error' => $e->getMessage()
            ]);
        }
    }
}