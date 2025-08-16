<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\MasterItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class MasterItemsController extends Controller
{
    public function index()
    {
        return view('master_items.index.index');
    }

    public function search(Request $request)
    {
        $query = MasterItem::query();

        if ($request->filled('kode')) {
            $query->where('kode', $request->kode);
        }
        if ($request->filled('nama')) {
            $query->where('nama', 'LIKE', '%' . $request->nama . '%');
        }
        if ($request->filled('hargamin') && $request->filled('hargamax')) {
            $query->whereBetween('harga_beli', [$request->hargamin, $request->hargamax]);
        } elseif ($request->filled('hargamin')) {
            $query->where('harga_beli', '>=', $request->hargamin);
        } elseif ($request->filled('hargamax')) {
            $query->where('harga_beli', '<=', $request->hargamax);
        }

        if ($request->filled('kategori_nama')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('nama', 'LIKE', '%' . $request->kategori_nama . '%');
            });
        }

        $data_search = $query->with('categories')
            ->select('kode', 'nama', 'photo', 'jenis', 'harga_beli', 'laba', 'supplier', 'id')
            ->orderBy('id')
            ->get()
            ->map(function ($item) {
                return [
                    'kode' => $item->kode,
                    'nama' => $item->nama,
                    'photo' => $item->photo,
                    'jenis' => $item->jenis,
                    'harga_beli' => $item->harga_beli,
                    'laba' => $item->laba,
                    'supplier' => $item->supplier,
                    'categories' => $item->categories->pluck('nama')->toArray(),
                ];
            });

        return response()->json([
            'status' => 200,
            'data' => $data_search
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'harga_beli' => 'required|numeric|min:0',
            'laba' => 'required|numeric|min:0|max:100',
            'supplier' => 'required|string|max:255',
            'jenis' => 'required|string|max:255',
            'category' => 'required|array',
            'category.*' => 'exists:kategori,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        try {
            DB::beginTransaction();
            $item = MasterItem::create($validated);

            if ($request->hasFile('photo')) {
                $photoPath = $this->uploadFile('master_items', $request->file('photo'), $request->nama);
                $item->photo = $photoPath;
                $item->save();
            }
    
            $item->categories()->attach($request->category);
            DB::commit();
            return redirect('master-items')->with('success', 'Data berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }
    
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'harga_beli' => 'required|numeric|min:0',
            'laba' => 'required|numeric|min:0|max:100',
            'supplier' => 'required|string|max:255',
            'jenis' => 'required|string|max:255',
            'category' => 'required|array',
            'category.*' => 'exists:kategori,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        try {
            DB::beginTransaction();
            $data_item = MasterItem::findOrFail($id);
            $data_item->update($validated);
    
            if ($request->hasFile('photo')) {
                $photoPath = $this->updateFile(
                    'master_items',
                    $request->file('photo'),
                    $data_item->photo,
                    $request->nama
                );
                $data_item->photo = $photoPath;
                $data_item->save();
            }
    
            $data_item->categories()->sync($request->category);
    
            DB::commit();
            return redirect('master-items')->with('success', 'Data berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('master-items')->with('error', 'Gagal mengupdate data: ' . $e->getMessage());
        }
    }

    public function formView($method, $id = 0)
    {
        if ($method == 'new') {
            $item = [];
        } else {
            $item = MasterItem::find($id);
        }
        $categories = Kategori::get();
        $data['item'] = $item;
        $data['method'] = $method;
        $data['categories'] = $categories;
        return view('master_items.form.index', $data);
    }

    public function singleView($kode)
    {
        $data['data'] = MasterItem::where('kode', $kode)->first();
        return view('master_items.single.index', $data);
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();
            $item = MasterItem::findOrFail($id);
            $item->categories()->detach();
            $item->delete();
            DB::commit();
            return redirect('master-items')->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('master-items')->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    public function updateRandomData()
    {
        try {
            DB::beginTransaction();
            $data = MasterItem::get();
            $updatedItems = [];

            foreach ($data as $item) {
                $prevKode = $item->kode;
                $kode = str_pad($item->id, 5, '0', STR_PAD_LEFT);
                $item->harga_beli = rand(100, 1000000);
                $item->laba = rand(0, 100);
                $item->kode = $kode;
                $item->supplier = $this->getRandomSupplier();
                $item->jenis = $this->getRandomJenis();
                $item->save();
                $updatedItems[] = [
                    'id' => $item->id,
                    'prev_kode' => $prevKode,
                    'new_kode' => $kode
                ];
            }
            DB::commit();

            $message = 'Data random berhasil diupdate. ';
            foreach ($updatedItems as $upd) {
                $message .= "ID {$upd['id']}: kode {$upd['prev_kode']} -> {$upd['new_kode']}. ";
            }
            return redirect('master-items')->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('master-items')->with('error', 'Gagal update random data: ' . $e->getMessage());
        }
    }

    private function getRandomSupplier()
    {
        $array = ['Tokopaedi', 'Bukulapuk', 'TokoBagas', 'E Commurz', 'Blublu'];
        $random = rand(0, 4);
        return $array[$random];
    }

    private function getRandomJenis()
    {
        $array = ['Obat', 'Alkes', 'Matkes', 'Umum', 'ATK'];
        $random = rand(0, 4);
        return $array[$random];
    }
}
