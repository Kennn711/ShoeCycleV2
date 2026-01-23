<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SettingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $userAddresses = $user->addresses()->orderBy('is_primary', 'desc')->get();
        $addresses = $userAddresses;
        $storeConfig = ['lat' => -7.472613, 'lng' => 112.433912];
        return view('customer.settings', compact('user', 'userAddresses', 'addresses', 'storeConfig'));
    }

    public function verifyCurrentPassword(Request $request)
    {
        $isValid = Hash::check($request->current_password, auth()->user()->password);
        return response()->json(['valid' => $isValid]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah.']);
        }

        User::whereId(Auth::id())->update([
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with('success', 'Password berhasil diperbarui.');
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:100'
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        // 3. Logika Upload Gambar
        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $filename = 'user_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();

            // Simpan file baru
            $path = $file->storeAs('profile-pictures', $filename, 'public');

            // Hapus foto lama jika ada (agar storage tidak penuh)
            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            // Masukkan path baru ke array data
            $data['profile_picture'] = $path;
        }

        // 4. Update Database
        $user->update($data);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function checkEmail(Request $request)
    {
        $exists = User::where('email', $request->email)
            ->where('id', '!=', Auth::id())
            ->exists();
        return response()->json(['exists' => $exists]);
    }

    public function deleteAccount()
    {
        $user = Auth::user();
        Auth::logout();
        $user->delete(); // Ini akan melakukan Soft Delete

        return redirect()->route('landing-page')->with('success', 'Akun Anda berhasil dihapus.');
    }
}
