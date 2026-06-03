<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenor;
use Illuminate\Http\Request;

class TenorController extends Controller
{
    public function index()
    {
        $tenor = Tenor::orderBy('bulan')->paginate(15);
        return view('admin.tenor.index', compact('tenor'));
    }

    public function create()
    {
        return view('admin.tenor.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bulan' => ['required', 'integer', 'min:1', 'max:360', 'unique:tenor,bulan'],
            'label' => ['nullable', 'string', 'max:50'],
        ]);

        // Auto-generate label jika dikosongkan
        $validated['label'] = $validated['label'] ?: $validated['bulan'] . ' Bulan';

        Tenor::create($validated);

        return redirect()->route('admin.tenor.index')
                         ->with('success', 'Data tenor berhasil ditambahkan.');
    }

    public function edit(Tenor $tenor)
    {
        return view('admin.tenor.edit', compact('tenor'));
    }

    public function update(Request $request, Tenor $tenor)
    {
        $validated = $request->validate([
            'bulan'     => ['required', 'integer', 'min:1', 'max:360', 'unique:tenor,bulan,' . $tenor->id],
            'label'     => ['nullable', 'string', 'max:50'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['label']     = $validated['label'] ?: $validated['bulan'] . ' Bulan';
        $validated['is_active'] = $request->boolean('is_active', true);

        $tenor->update($validated);

        return redirect()->route('admin.tenor.index')
                         ->with('success', 'Data tenor berhasil diperbarui.');
    }

    public function destroy(Tenor $tenor)
    {
        $tenor->delete();

        return redirect()->route('admin.tenor.index')
                         ->with('success', 'Data tenor berhasil dihapus.');
    }
}