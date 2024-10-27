<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;


class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();
        return response()->json($categories);
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
            'name_category' => 'required',

        ]);



        $category = new Category();
        $category->name_category = $request->input('name_category');


        $category->save();
        return response()->json(['message' => 'Data berhasil ditambahkan', 'data' => $category]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category, $id)
    {
        $validation = $request->validate([
            'name_category' => 'required',
        ]);


        $category = Category::find($id);


        if (!$category) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }



        $category->name_category = $request->input('name_category');

        $category->save();
        return response()->json(['message' => 'Data berhasil diubah', 'data' => $category]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category, $id)
    {
        $category = Category::find($id);
        $category->delete();
        return response()->json(['message' => 'Data berhasil dihapus']);
    }

    function generateRandomString($length = 10)
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
