<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Product::with('category')->get();
        return response()->json(['data' => $posts], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $id_category)
    {
        $request->validate([
            'title' => 'required',
            'file' => 'required',
            'price' => 'required',
            'description' => 'required',
            'stock' => 'required',
        ]);

        $filename = $this->generateRandomString();
        $extension = $request->file->extension();
        Storage::putFileAs('photos', $request->file, $filename . '.' . $extension);

        $product = new Product();
        $product->id_category = $id_category;
        $product->title = $request->input('title');
        $product->images = $filename . '.' . $extension;
        $product->price = $request->input('price');
        $product->sold = 0;
        $product->stock = $request->input('stock');
        $product->description = $request->input('description');
        $product->code = $this->generateSequentialCode(); // Menghasilkan kode berurutan

        $product->save();

        return response()->json(['message' => 'Data berhasil ditambahkan', 'data' => $product]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product, $id)
    {
        $product = Product::with('category:id,name_category')->findOrFail($id);

        return response()->json(['data' => $product], 200);
    }

    public function showByCategory($id_category)
    {
        $products = Product::where('id_category', $id_category)->get();
        return response()->json(['data' => $products], 200);
    }

    public function showByName($productName)
    {
        $products = Product::where('title', 'LIKE', '%' . $productName . '%')->get();
        return response()->json(['data' => $products], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product, $id)
    {
        $request->validate([
            'title' => 'required',
            'price' => 'required',
            'stock' => 'required',
            'description' => 'required',
        ]);

        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        // Cek jika ada file baru yang diunggah
        if ($request->hasFile('file')) {
            $filename = $this->generateRandomString();
            $extension = $request->file->extension();

            // Menghapus file lama jika ada
            if ($product->images) {
                Storage::delete('photos/' . $product->images);
            }

            Storage::putFileAs('photos', $request->file, $filename . '.' . $extension);
            $product->images = $filename . '.' . $extension;
        }

        $product->code = $request->input('code');
        $product->title = $request->input('title');
        $product->price = $request->input('price');
        $product->sold = $request->input('sold');
        $product->stock = $request->input('stock');
        $product->id_category = $request->input('id_category');
        $product->description = $request->input('description');

        // Jaga agar kode tetap sama saat update

        $product->save();

        return response()->json(['message' => 'Data berhasil diperbarui', 'data' => $product]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        // Menghapus file terkait jika ada
        if ($product->images) {
            Storage::delete('photos/' . $product->images);
        }

        $product->delete();

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

    public function getImage($filename)
    {
        $path = storage_path('app/photos/' . $filename);

        if (!file_exists($path)) {
            return response()->json(['message' => 'Image not found.'], 404);
        }

        $file = file_get_contents($path);
        $type = mime_content_type($path);

        return response($file, 200)->header('Content-Type', $type);
    }

    private function generateSequentialCode()
    {
        // Mendapatkan kode terakhir
        $lastProduct = Product::orderBy('created_at', 'desc')->first();
        if ($lastProduct) {
            $lastCode = $lastProduct->code;
            // Mengambil nomor urutan terakhir dan menambahkannya
            $lastNumber = (int) substr($lastCode, 1);
            $newNumber = $lastNumber + 1;
            // Format kode baru dengan leading zero
            $newCode = 'T' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
        } else {
            // Jika belum ada produk, mulai dengan T001
            $newCode = 'T001';
        }

        return $newCode;
    }
}
