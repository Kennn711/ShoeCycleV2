<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validasi Input
        $validated = $request->validate([
            'label'          => 'required|in:Home,Office,Apartment,Boarding House,Other',
            'recipient_name' => 'required|string|max:255',
            'phone_number'   => 'required|string|max:20',
            'full_address'   => 'required|string',
            'district'       => 'required|string|max:255',
            'village'        => 'required|string|max:255',
            'courier_note'   => 'nullable|string',
            'latitude'       => 'nullable|numeric',
            'longitude'      => 'nullable|numeric',
            'is_primary'     => 'nullable|in:on,off,1,0', // Checkbox html mengirim 'on' jika dicentang
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();

            // Konversi checkbox 'on' menjadi boolean true
            $isPrimary = $request->has('is_primary');

            // 2. Logic Alamat Utama
            // Jika ini alamat pertama user, paksa jadi primary
            if ($user->addresses()->count() === 0) {
                $isPrimary = true;
            }

            // Jika user memilih ini sebagai alamat utama, matikan status utama di alamat lainnya
            if ($isPrimary) {
                $user->addresses()->update(['is_primary' => false]);
            }

            // 3. Simpan Data
            $address = Address::create([
                'user_id'        => $user->id,
                'recipient_name' => $validated['recipient_name'],
                'phone_number'   => $validated['phone_number'],
                'label'          => $validated['label'],
                'full_address'   => $validated['full_address'],
                'district'       => $validated['district'],
                'village'        => $validated['village'],
                'courier_note'   => $validated['courier_note'] ?? null,
                'latitude'       => $validated['latitude'] ?? null,
                'longitude'      => $validated['longitude'] ?? null,
                'is_primary'     => $isPrimary,
            ]);

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Alamat berhasil disimpan.',
                'data'    => $address
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal menyimpan alamat: ' . $e->getMessage()
            ], 500);
        }
    }
}
