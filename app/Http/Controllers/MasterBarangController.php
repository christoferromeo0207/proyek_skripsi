<?php

namespace App\Http\Controllers;

use App\Models\MasterBarang;
use Illuminate\Http\Request;

class MasterBarangController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255',
        ]);

        MasterBarang::create([
            'nama_barang' => $request->nama_barang,
        ]);

        return redirect()->back()->with('success', 'Barang baru berhasil ditambahkan.');
    }
}
