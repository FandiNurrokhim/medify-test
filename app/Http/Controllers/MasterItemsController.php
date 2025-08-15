<?php

namespace App\Http\Controllers;

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

        $data_search = $query->select('kode', 'nama', 'jenis', 'harga_beli', 'laba', 'supplier')
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
            'harga_beli' => 'required|numeric|min:0',
            'laba' => 'required|numeric|min:0|max:100',
            'supplier' => 'required|string|max:255',
            'jenis' => 'required|string|max:255',
        ]);
        try {
            DB::beginTransaction();
            MasterItem::create($validated);

            DB::commit();
            return redirect('master-items')->with('success', 'Data berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('master-items')->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
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
        ]);
        try {
            DB::beginTransaction();
            $data_item = MasterItem::findOrFail($id);
            $data_item->update($validated);

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
        $data['item'] = $item;
        $data['method'] = $method;
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
            foreach ($data as $item) {
                $kode = str_pad($item->id, 5, '0', STR_PAD_LEFT);
                $item->harga_beli = rand(100, 1000000);
                $item->laba = rand(0, 100);
                $item->kode = $kode;
                $item->supplier = $this->getRandomSupplier();
                $item->jenis = $this->getRandomJenis();
                $item->save();
            }
            DB::commit();
            return redirect('master-items')->with('success', 'Data random berhasil diupdate');
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
