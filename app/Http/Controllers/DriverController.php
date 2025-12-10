<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Pest\Support\Str;

class DriverController extends Controller
{
    public function index(Request $request)
    {
        // Ambil user yang role-nya 'driver'
        $query = User::where('role', 'driver');

        // Logic Search Sederhana
        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', "%{$request->q}%")
                    ->orWhere('email', 'LIKE', "%{$request->q}%");
            });
        }

        $drivers = $query->latest()->paginate(8);

        return view('admin.driver.index', compact('drivers'));
    }

    public function show($id)
    {
        $driver = User::findOrFail($id);
        return response()->json([
            'status' => 'success',
            'data' => $driver
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'profile_picture.required' => 'Wajib upload foto profil driver.',
            'profile_picture.image' => 'File harus berupa gambar.',
        ]);

        try {
            // 1. Upload Foto Profil
            $path = null;
            if ($request->hasFile('profile_picture')) {
                $file = $request->file('profile_picture');
                $filename = 'driver_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                // Simpan di folder profile-pictures
                $path = $file->storeAs('profile-pictures', $filename, 'public');
            }

            // 2. Create User
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'driver',
                'profile_picture' => $path,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Driver berhasil ditambahkan',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            // Hapus gambar jika DB gagal
            if ($path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menambah driver: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $driver = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6', // Password opsional
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        try {
            $dataToUpdate = [
                'name' => $request->name,
                'email' => $request->email,
            ];

            // 1. Cek Password (hanya update jika diisi)
            if ($request->filled('password')) {
                $dataToUpdate['password'] = Hash::make($request->password);
            }

            // 2. Cek File Gambar
            if ($request->hasFile('profile_picture')) {
                // Hapus foto lama jika ada
                if ($driver->profile_picture && Storage::disk('public')->exists($driver->profile_picture)) {
                    Storage::disk('public')->delete($driver->profile_picture);
                }

                // Upload baru
                $file = $request->file('profile_picture');
                $filename = 'driver_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('profile-pictures', $filename, 'public');

                $dataToUpdate['profile_picture'] = $path;
            }

            $driver->update($dataToUpdate);

            return response()->json([
                'status' => 'success',
                'message' => 'Driver berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal update driver: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $driver = User::where('role', 'driver')->findOrFail($id);

            if ($driver->profile_picture && Storage::disk('public')->exists($driver->profile_picture)) {
                Storage::disk('public')->delete($driver->profile_picture);
            }

            $driver->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Driver berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus driver: ' . $e->getMessage()
            ], 500);
        }
    }
}
