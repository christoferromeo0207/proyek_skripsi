<?php

namespace App\Http\Controllers;

use App\Models\MasterJasa;
use Illuminate\Http\Request;

class MasterJasaController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nama_jasa' => 'required|string|max:255',
            'harga'     => 'required|numeric|min:0',
        ]);

        MasterJasa::create([
            'nama_jasa' => $request->nama_jasa,
            'harga'     => $request->harga,
        ]);

        return redirect()->back()->with('success', 'Jasa baru berhasil ditambahkan.');
    }
}
