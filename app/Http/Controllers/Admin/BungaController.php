<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BungaPinjaman;
use Illuminate\Http\Request;

class BungaController extends Controller
{
    public function index()
    {
        $bunga = BungaPinjaman::latest()->paginate(15);
        return view('admin.bunga.index', compact('bunga'));
    }

    public function create()
    {
        return view('admin.bunga.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_bunga' => ['required', 'string', 'max:100'],
            'persentase' => ['required', 'numeric', 'min:0', 'max:100'],
            'jenis'      => ['required', 'in:flat,efektif'],
        ]);

        BungaPinjaman::create($validated);

        return redirect()->route('admin.bunga.index')
                         ->with('success', 'Data bunga berhasil ditambahkan.');
    }

    public function edit(BungaPinjaman $bunga)
    {
        return view('admin.bunga.edit', compact('bunga'));
    }

    public function update(Request $request, BungaPinjaman $bunga)
    {
        $validated = $request->validate([
            'nama_bunga' => ['required', 'string', 'max:100'],
            'persentase' => ['required', 'numeric', 'min:0', 'max:100'],
            'jenis'      => ['required', 'in:flat,efektif'],
            'is_active'  => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $bunga->update($validated);

        return redirect()->route('admin.bunga.index')
                         ->with('success', 'Data bunga berhasil diperbarui.');
    }

    public function destroy(BungaPinjaman $bunga)
    {
        $bunga->delete();

        return redirect()->route('admin.bunga.index')
                         ->with('success', 'Data bunga berhasil dihapus.');
    }
}