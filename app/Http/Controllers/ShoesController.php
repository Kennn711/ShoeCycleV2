<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Shoes;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ShoesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::orderBy('category_name')->get();
        $shoes = Shoes::with('category')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.shoes.index', compact('categories', 'shoes'));
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
        try {
            $validation = $request->validate([
                'name' => [
                    'required',
                    'string',
                    'min:3',
                    'max:100',
                    'unique:shoes,name'
                ],
                'category_id' => [
                    'required',
                    'exists:categories,id'
                ],
                'brand' => [
                    'nullable',
                    'string',
                    'max:50'
                ],
                'description' => [
                    'nullable',
                    'string',
                    'max:1000'
                ],
                'is_active' => [
                    'required',
                    'boolean'
                ]
            ], [
                'name.required' => 'Nama sepatu tidak boleh kosong',
                'name.min' => 'Nama sepatuinimal 3 karakter',
                'name.max' => 'Nama sepatu maksimal 100 karakter',
                'name.unique' => 'Nama sepatu sudah digunakan',
                'category_id.required' => 'Kategori harus dipilih',
                'category_id.exists' => ' mKategori tidak valid',
                'brand.max' => 'Merk maksimal 50 karakter',
                'description.max' => 'Deskripsi maksimal 1000 karakter',
            ]);

            // Generate slug
            $validation['slug'] = Str::slug($validation['name']);

            // Create shoe
            $shoe = Shoes::create($validation);

            return redirect()
                ->route('shoes.index');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->route('shoes.index')
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()
                ->route('shoes.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {}

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
    public function update(Request $request, string $id)
    {
        $shoe = Shoes::findOrFail($id);

        try {
            $validated = $request->validate([
                'name' => [
                    'required',
                    'string',
                    'min:3',
                    'max:100',
                    'unique:shoes,name,' . $id
                ],
                'category_id' => [
                    'required',
                    'exists:categories,id'
                ],
                'brand' => [
                    'nullable',
                    'string',
                    'max:50'
                ],
                'description' => [
                    'nullable',
                    'string',
                    'max:1000'
                ],
                'is_active' => [
                    'required',
                    'boolean'
                ]
            ], [
                'name.required' => 'Nama sepatu tidak boleh kosong',
                'name.min' => 'Nama sepatu minimal 3 karakter',
                'name.max' => 'Nama sepatu maksimal 100 karakter',
                'name.unique' => 'Nama sepatu sudah digunakan',
                'category_id.required' => 'Kategori harus dipilih',
                'category_id.exists' => 'Kategori tidak valid',
                'brand.max' => 'Merk maksimal 50 karakter',
                'description.max' => 'Deskripsi maksimal 1000 karakter',
            ]);

            // Update slug if name changed
            if ($shoe->name !== $validated['name']) {
                $validated['slug'] = Str::slug($validated['name']);
            }

            $shoe->update($validated);

            return redirect()
                ->route('shoes.index')
                ->with('success', 'Sepatu berhasil diperbarui');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->route('shoes.index')
                ->withErrors($e->errors())
                ->withInput()
                ->with('edit_mode', true)
                ->with('edit_shoe_id', $id);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            Shoes::findOrFail($id)->delete();

            return redirect()
                ->route('shoes.index');
        } catch (\Exception $e) {
            return redirect()
                ->route('shoes.index')
                ->with('error', 'Gagal menghapus sepatu');
        }
    }
}
