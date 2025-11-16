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
        return view('admin.category.index', [
            "category" => Category::get()
        ]);
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
            'category_name' => [
                'required',
                'string',
                'min:3',
                'max:30',
                'unique:categories,category_name'
            ]
        ], [
            'category_name.required' => 'Nama kategori tidak boleh kosong',
            'category_name.min' => 'Nama kategori minimal 3 karakter',
            'category_name.unique' => 'Nama kategori sudah digunakan',
            'category_name.max' => 'Nama kategori maksimal 30 karakter'
        ]);

        // Simpan ke database
        Category::create($validation);

        return redirect()->route('category.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    // app/Http/Controllers/CategoryController.php

    // Override method untuk handle validation error
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        try {
            $validation = $request->validate([
                'category_name' => [
                    'required',
                    'string',
                    'min:3',
                    'max:30',
                    'unique:categories,category_name,' . $id
                ]
            ], [
                'category_name.required' => 'Nama kategori tidak boleh kosong',
                'category_name.min' => 'Nama kategori minimal 3 karakter',
                'category_name.max' => 'Nama kategori maksimal 30 karakter',
                'category_name.unique' => 'Nama kategori sudah digunakan'
            ]);

            $category->update($validation);

            return redirect()->route('category.index');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Set session flag untuk membuka modal edit
            return redirect()->route('category.index')
                ->withErrors($e->errors())
                ->withInput()
                ->with('edit_mode', true)
                ->with('edit_category_id', $id);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->delete();

            return redirect()->route('category.index')->with('success', 'Kategori berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('category.index')->with('error', 'Gagal menghapus kategori');
        }
    }

    public function getShoes($id)
    {
        try {
            $category = Category::findOrFail($id);

            $shoes = $category->shoes;

            return response()->json([
                'success' => true,
                'shoes' => $shoes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data sepatu'
            ], 500);
        }
    }
}
