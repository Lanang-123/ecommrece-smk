<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\User;
use App\Http\Resources\CartUserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $carts = Cart::all();
        return response()->json(['data' => $carts]);
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
    public function store(Request $request)
    {
        $validation = $request->validate([
            'id_product' => 'required',
            'quantity' => 'required',
        ]);

        $idUser = Auth::user()->id;

        $cart = new Cart();
        $cart->id_user = $idUser;
        $cart->id_product = $request->input('id_product');
        $cart->quantity = $request->input('quantity');

        $cart->save();
        return response()->json(['message' => 'Data berhasil ditambahkan', 'data' => $cart]);
    }

    /**
     * Display the specified resource.
     */
    public function showByUser()
    {
        $idUser = Auth::user()->id;
        $cartUser = Cart::with([
            'user:id,name,email',
            'product' => function ($query) {
                $query->with('category:id,name_category'); // Memuat kategori yang terkait dengan produk
            }
        ])->where('id_user', $idUser)->get();

        return response()->json(['data' => $cartUser]);
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cart $cart)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cart $cart)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $idUser = Auth::user()->id;

        // Mencari cart item berdasarkan ID dan ID user
        $cartItem = Cart::where('id', $id)->where('id_user', $idUser)->first();

        if (!$cartItem) {
            return response()->json(['message' => 'Item tidak ditemukan'], 404);
        }

        // Menghapus cart item
        $cartItem->delete();

        return response()->json(['message' => 'Item berhasil dihapus dari keranjang']);
    }

}