<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Shoes;
use App\Models\ShoesVariant;
use App\Models\VariantImages;
use Illuminate\Container\Attributes\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB as FacadesDB;
use Illuminate\Support\Facades\Storage;

class ShoesVariantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.shoes-variant.index', [
            "categories" => Category::select('id', 'category_name')->get(),
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
     * Get variant data for edit
     */

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {}

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
    public function update(Request $request, ShoesVariant $shoeVariant) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ShoesVariant $shoeVariant) {}
}
