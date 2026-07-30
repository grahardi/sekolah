<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class SekolahRegistrationController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/RegisterSekolah');
    }

    /**
     * Simpan sekolah (kalau NPSN belum pernah didaftarkan) lalu buat akun admin
     * pertama untuk sekolah itu. Data sekolah dikirim dari langkah 1 (hasil
     * konfirmasi lookup NPSN di frontend), bukan diambil ulang dari API di sini,
     * supaya user bisa koreksi manual dulu kalau ada data yang kurang pas.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'npsn' => ['required', 'digits_between:6,10'],
            'nama_sekolah' => ['required', 'string', 'max:255'],
            'alamat' => ['nullable', 'string', 'max:255'],
            'kecamatan' => ['nullable', 'string', 'max:255'],
            'kabupaten_kota' => ['nullable', 'string', 'max:255'],
            'provinsi' => ['nullable', 'string', 'max:255'],
            'status_sekolah' => ['nullable', 'string', 'max:100'],
            'bentuk_pendidikan' => ['nullable', 'string', 'max:100'],
            'jenjang_pendidikan' => ['nullable', 'string', 'max:100'],

            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $sekolah = Sekolah::firstOrCreate(
            ['npsn' => $validated['npsn']],
            [
                'nama' => $validated['nama_sekolah'],
                'alamat' => $validated['alamat'] ?? null,
                'kecamatan' => $validated['kecamatan'] ?? null,
                'kabupaten_kota' => $validated['kabupaten_kota'] ?? null,
                'provinsi' => $validated['provinsi'] ?? null,
                'status_sekolah' => $validated['status_sekolah'] ?? null,
                'bentuk_pendidikan' => $validated['bentuk_pendidikan'] ?? null,
                'jenjang_pendidikan' => $validated['jenjang_pendidikan'] ?? null,
            ]
        );

        $user = User::create([
            'sekolah_id' => $sekolah->id,
            'role' => 'admin',
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
