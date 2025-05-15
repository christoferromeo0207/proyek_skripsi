<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use Illuminate\Routing\Controller;

class DashboardController extends Controller
{
    public function index()
    {
       $jumlahMitra = Post::count();
       $jumlahKategori = Category::count();
       $jumlahPegawai = User::count();

        $stats = [
            ['Jumlah Mitra',           $jumlahMitra,           'Perusahaan', 'fas fa-handshake', route('posts.index')],
            ['Kategori Perusahaan',    $jumlahKategori,        'Kategori',   'fas fa-folder', route('categories.index')],
            ['Pegawai Marketing',      $jumlahPegawai,'Karyawan',   'fas fa-users', route('user.index')],
        ];

        return view('dashboard', compact('stats'));
        
    }
}

