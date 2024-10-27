<?php

namespace App\Http\Controllers;

use App\Models\Promo;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PromoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $promos = Promo::with('product')->get();
        return response()->json(['data' => $promos], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    // public function create()
    // {
    //     $products = Product::all();
    //     return view('promos.create', compact('products'));
    // }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'discount_percentage' => 'required',
            'file' => 'required',
        ]);

         $filename = $this->generateRandomString();
        $extension = $request->file->extension();
        Storage::putFileAs('photos', $request->file, $filename . '.' . $extension);



        $promo = new Promo();
        $promo->title = $request->input('title');
        $promo->description = $request->input('description');
        $promo->start_date = $request->input('start_date');
        $promo->end_date = $request->input('end_date');
        $promo->discount_percentage = $request->input('discount_percentage');
        $promo->banner = $filename . '.' . $extension;
        $promo->id_product = $request->input('id_product');


        $promo->save();

        return response()->json(['message' => 'Data berhasil ditambahkan', 'data' => $promo]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Promo $promo)
    {
        $products = Product::all();
        return view('promos.edit', compact('promo', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Promo $promo, $id)
    {
        $request->validate([
            'title' => 'required',
            'banner' => 'required',
            'description' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'discount_percentage' => 'required',
            'id_product' => 'required'
        ]);

        $promo = Promo::find($id);

        if (!$promo) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        // Cek jika ada file baru yang diunggah
        if ($request->hasFile('banner')) {
            $filename = $this->generateRandomString();
            $extension = $request->banner->extension();

            // Menghapus file lama jika ada
            if ($promo->banner) {
                Storage::delete('photos/' . $promo->banner);
            }

            Storage::putFileAs('photos', $request->banner, $filename . '.' . $extension);
            $promo->images = $filename . '.' . $extension;
        }

        $promo->title = $request->input('title');
        $promo->description = $request->input('description');
        $promo->start_date = $request->input('start_date');
        $promo->end_date = $request->input('end_date');
        $promo->discount_percentage = $request->input('discount_percentage');
        $promo->id_product = $request->input('id_product');


        $promo->update();

        return response()->json(['message' => 'Data berhasil diperbarui', 'data' => $promo]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Promo $promo, $id)
    {
        $promo = Promo::find($id);

        if (!$promo) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        // Menghapus file terkait jika ada
        if ($promo->banner) {
            Storage::delete('photos/' . $promo->banner);
        }

        $promo->delete();

        return response()->json(['message' => 'Data berhasil dihapus']);
    }

    private function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}