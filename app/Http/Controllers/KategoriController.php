<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KategoriController extends Controller
{
    public function index()
    {
        return view('categories.index.index');
    }

    public function search(Request $request)
    {
        $query = Kategori::query();

        if ($request->filled('kode')) {
            $query->where('kode', $request->kode);
        }
        if ($request->filled('nama')) {
            $query->where('nama', 'LIKE', '%' . $request->nama . '%');
        }

        $data_search = $query->select('kode', 'nama')
            ->orderBy('id')
            ->get();

        return response()->json([
            'status' => 200,
            'data' => $data_search
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
        ]);
        try {
            DB::beginTransaction();
            Kategori::create($validated);

            DB::commit();
            return redirect('categories')->with('success', 'Data berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('categories')->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
        ]);
        try {
            DB::beginTransaction();
            $data_item = Kategori::findOrFail($id);
            $data_item->update($validated);

            DB::commit();
            return redirect('categories')->with('success', 'Data berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('categories')->with('error', 'Gagal mengupdate data: ' . $e->getMessage());
        }
    }

    public function formView($method, $id = 0)
    {
        if ($method == 'new') {
            $item = [];
        } else {
            $item = Kategori::find($id);
        }
        $data['item'] = $item;
        $data['method'] = $method;
        return view('categories.form.index', $data);
    }

    public function singleView($kode)
    {
        $data['data'] = Kategori::with('masterItems')->where('kode', $kode)->first();
        return view('categories.single.index', $data);
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();
            $item = Kategori::with('masterItems')->findOrFail($id);

            if ($item->masterItems()->exists()) {
                DB::rollBack();
                return redirect('categories')->with('error', 'Kategori tidak dapat dihapus karena masih memiliki relasi ke master items.');
            }

            $item->delete();
            DB::commit();
            return redirect('categories')->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('categories')->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
