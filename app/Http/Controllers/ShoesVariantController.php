<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Shoes;
use App\Models\ShoesVariant;
use App\Models\VariantImages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ShoesVariantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // pluck('brand_name', 'brand_name') membuat array ['Nike' => 'Nike', 'Adidas' => 'Adidas']
        $brands = Shoes::select('brand_name')
            ->distinct()
            ->orderBy('brand_name')
            ->pluck('brand_name');

        // 2. Base Query
        $query = Shoes::select('id', 'name', 'brand_name', 'category_id', 'is_active', 'created_at')
            ->with(['category:id,category_name', 'variants']);

        // 3. Logic Filter Merek (Jika user memilih merek)
        if ($request->filled('brand') && $request->brand != 'Semua') {
            $query->where('brand_name', $request->brand);
        }

        // 4. Logic Search (Search yang sudah ada)
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('brand_name', 'LIKE', "%{$search}%");
            });
        }

        // 5. Pagination & Execution
        $shoes = $query->latest()
            ->paginate(8)
            ->withQueryString(); // filter tidak hilang saat pindah halaman

        // 6. Format data varian untuk JS
        $variantsData = $shoes->getCollection()->mapWithKeys(function ($shoe) {
            return [$shoe->id => $shoe->variants];
        });

        return view('admin.shoes-variant.index', compact('shoes', 'variantsData', 'brands'));
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
    public function store(Request $request)
    {
        $request->validate([
            'shoe_id' => 'required|exists:shoes,id',
            'color' => 'required|string|max:50',
            'size' => 'required|integer|min:10|max:60', // Range ukuran sepatu wajar
            'price' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'color_code' => 'nullable|string|max:20', // Opsional jika Anda simpan hex
            // SKU harus unik di tabel variants, tapi boleh null (nanti digenerate)
            'sku' => 'nullable|string|max:50|unique:shoes_variants,sku',
            'is_available' => 'required|boolean',
            // Validasi Gambar
            'images' => 'required|array|min:1',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048', // Max 2MB per file
        ], [
            'images.required' => 'Wajib upload minimal 1 gambar.',
            'images.*.image' => 'File harus berupa gambar.',
            'images.*.max' => 'Ukuran gambar maksimal 2MB.',
            'sku.unique' => 'SKU ini sudah digunakan oleh varian lain.',
        ]);

        // Gunakan Database Transaction agar jika upload gagal, data tidak masuk DB
        DB::beginTransaction();

        try {
            // 2. Generate SKU jika kosong (Backup logic backend)
            $sku = $request->sku;
            if (empty($sku)) {
                // Format: SHOE-ID-COLOR-SIZE-RANDOM
                // Contoh: 1-BLK-42-8392
                $sku = strtoupper($request->shoe_id . '-' . substr($request->color, 0, 3) . '-' . $request->size . '-' . Str::random(4));
            }

            // 3. Simpan Data Varian ke Database
            $variant = ShoesVariant::create([
                'shoe_id' => $request->shoe_id,
                'color' => $request->color,
                'color_code' => $request->color_code, // Pastikan kolom ini ada di migration jika dipakai
                'size' => $request->size,
                'price' => $request->price,
                'stock' => $request->stock ?? 0,
                'sku' => $sku,
                'is_available' => $request->is_available,
            ]);

            // 4. Proses Upload Gambar
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {

                    // A. Logika Nama File Unik
                    // Format: variant_TIMESTAMP_RANDOM.ext
                    // Contoh: variant_1705882291_a1b2c3d4.jpg
                    $extension = $image->getClientOriginalExtension();
                    $filename = 'variant_' . time() . '_' . Str::random(10) . '.' . $extension;

                    // B. Simpan ke Storage (Folder: storage/app/public/variant-shoes)
                    $path = $image->storeAs('variant-shoes', $filename, 'public');

                    // C. Simpan Metadata ke Tabel variant_images
                    VariantImages::create([
                        'shoe_variant_id' => $variant->id,
                        'image_path' => $path, // path relatif: variant-shoes/namafile.jpg
                        'is_primary' => ($index === 0), // Gambar pertama jadi utama
                        'order' => $index + 1,
                    ]);
                }
            }

            DB::commit(); // Simpan jika sukses

            return response()->json([
                'status' => 'success',
                'message' => 'Varian sepatu berhasil ditambahkan',
                'data' => $variant
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan semua perubahan jika error

            // Hapus gambar yang terlanjur terupload (cleanup)
            if (isset($variant) && $variant->images) {
                foreach ($variant->images as $img) {
                    Storage::disk('public')->delete($img->image_path);
                }
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $variant = ShoesVariant::with(['images' => function ($q) {
            $q->orderBy('order', 'asc');
        }])->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $variant
        ]);
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
    public function update(Request $request, string $id)
    {
        $variant = ShoesVariant::findOrFail($id);

        // Validasi
        $request->validate([
            'color' => 'required|string|max:50',
            'size' => 'required|integer|min:10|max:60',
            'price' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'color_code' => 'nullable|string|max:20',
            // SKU unik kecuali punya diri sendiri
            'sku' => 'nullable|string|max:50|unique:shoes_variants,sku,' . $id,
            'is_available' => 'required|boolean',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        DB::beginTransaction();
        try {
            // A. Update Data Varian
            $variant->update([
                'color' => $request->color,
                'color_code' => $request->color_code,
                'size' => $request->size,
                'price' => $request->price,
                'stock' => $request->stock ?? 0,
                'sku' => $request->sku, // Jika user mau ganti SKU manual
                'is_available' => $request->is_available,
            ]);

            // B. Hapus Gambar Lama (Jika ada yang dipilih untuk dihapus)
            if ($request->has('deleted_images')) {
                $deletedIds = explode(',', $request->deleted_images);
                $imagesToDelete = VariantImages::whereIn('id', $deletedIds)
                    ->where('shoe_variant_id', $variant->id)
                    ->get();

                foreach ($imagesToDelete as $img) {
                    if (Storage::disk('public')->exists($img->image_path)) {
                        Storage::disk('public')->delete($img->image_path);
                    }
                    $img->delete();
                }
            }

            // C. Upload Gambar Baru (Append)
            if ($request->hasFile('images')) {
                // Cek order terakhir
                $lastOrder = $variant->images()->max('order') ?? 0;

                foreach ($request->file('images') as $index => $image) {
                    $extension = $image->getClientOriginalExtension();
                    $filename = 'variant_' . time() . '_' . Str::random(10) . '.' . $extension;
                    $path = $image->storeAs('variant-shoes', $filename, 'public');

                    VariantImages::create([
                        'shoe_variant_id' => $variant->id,
                        'image_path' => $path,
                        'is_primary' => false, // Gambar baru tidak langsung jadi primary kecuali diatur logic lain
                        'order' => $lastOrder + $index + 1,
                    ]);
                }
            }

            // D. Pastikan minimal ada 1 gambar (Validasi Backend Terakhir)
            if ($variant->images()->count() == 0) {
                throw new \Exception("Varian harus memiliki minimal 1 gambar.");
            }

            // E. Pastikan ada 1 Primary Image
            $hasPrimary = $variant->images()->where('is_primary', true)->exists();
            if (!$hasPrimary) {
                // Set gambar pertama (urutan order) jadi primary
                $firstImg = $variant->images()->orderBy('order')->first();
                if ($firstImg) {
                    $firstImg->update(['is_primary' => true]);
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Varian berhasil diperbarui',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal update: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $variant = ShoesVariant::with('images')->findOrFail($id);

            foreach ($variant->images as $image) {
                if (Storage::disk('public')->exists($image->image_path)) {
                    Storage::disk('public')->delete($image->image_path);
                }
            }

            // Hapus Record Gambar di Database (Jika tidak cascade delete di migration)
            $variant->images()->delete();
            // Hapus Varian
            $variant->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Varian berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }
}
