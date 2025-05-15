<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Author;
use Illuminate\Http\Request;

class AuthorsController extends Controller
{
    public function index(Request $request) {
        $search = $request->input('search');
        
        // Fetch users with search query if provided, otherwise fetch all users
        $query = User::query();
        
        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }
        
        $users = $query->paginate(7);
    
        return view('authors', ['authors' => $users, 'search' => $search]);
    }

    public function create()
    {
        // Ambil semua data authors
        $authors = Author::all();
        
        // Kirim data ke view
        return view('posts.create', compact('authors'));
    }
}
