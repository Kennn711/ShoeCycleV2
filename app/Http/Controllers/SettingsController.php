<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $addresses = $user->addresses()->orderBy('is_primary', 'desc')->get();

        return view('customer.settings', compact('user', 'addresses'));
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
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

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
