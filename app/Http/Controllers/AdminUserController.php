<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AdminUserController extends Controller
{
    // Cek apakah user adalah owner
    private function isOwner()
    {
        // Mendukung sesi auth Laravel atau fallback ke hardcoded email
        $userEmail = Auth::user() ? Auth::user()->email : session('user_email', 'admin@diskominfo.go.id');
        return strtolower($userEmail) === 'admin@diskominfo.go.id';
    }

    // Menampilkan Form Buat Akun Admin
    public function create()
    {
        return view('pages.admin.create_user');
    }

    // Menyimpan Akun Admin Baru
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'is_active' => true,
        ]);

        return redirect()->route('admin.user.index')
            ->with('success', 'Akun admin baru berhasil dibuat!');
    }

    // Kelola Semua Akun Admin (Khusus Owner)
    public function index()
    {
        $users = User::all();
        $isOwner = $this->isOwner();
        return view('pages.admin.users_list', compact('users', 'isOwner'));
    }

    // Toggle Aktif/Nonaktif Akun Admin
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        
        // Jangan biarkan akun owner dinonaktifkan
        if (strtolower($user->email) === 'admin@diskominfo.go.id') {
            return redirect()->back()->with('error', 'Akun Owner utama tidak dapat dinonaktifkan!');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $statusText = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->back()->with('success', "Akun {$user->name} berhasil {$statusText}.");
    }

    // View History Pembuatan Sertifikat oleh Admin Lain (Khusus Owner)
    public function history()
    {
        if (!$this->isOwner()) {
            abort(403, 'Akses ditolak. Fitur ini hanya untuk Owner (admin@diskominfo.go.id).');
        }

        $certificates = Certificate::latest()->get();
        return view('pages.admin.history', compact('certificates'));
    }
}
