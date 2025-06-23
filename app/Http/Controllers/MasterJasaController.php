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
            'kategori_id' => 'required|exists:categories,id'
        ]);

        MasterJasa::create([
            'nama_jasa' => $request->nama_jasa,
            'harga'     => $request->harga,
            'kategori_id' => 'required|exists:categories,id'
        ]);

        return redirect()->back()->with('success', 'Jasa baru berhasil ditambahkan.');
    }
   public function storeInline(Request $request)
    {
       
        $validated = $request->validate([
            'nama'        => 'required|string|max:255',
            'harga'       => 'required|numeric|min:0',
            'kategori_id' => 'required|exists:categories,id'
        ]);

        MasterJasa::create([
            'nama_jasa'   => $validated['nama'],
            'harga'       => $validated['harga'],
            'kategori_id' => $validated['kategori_id']
        ]);

        return redirect()->back()->with('success', 'Jasa baru berhasil ditambahkan.');
    }


}
