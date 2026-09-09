<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // ── Manajemen User (khusus admin) ───────────────────────────────────────

    public function index()
    {
        $users = User::where('sekolah_id', Auth::user()->sekolah_id)
            ->orderBy('role')->orderBy('name')->paginate(15);

        // Guru yg akunnya BELUM terhubung ke Pegawai (Kepegawaian) - baik
        // krn blm py record Guru sama sekali, atau py Guru tapi pegawai_id
        // null (dulunya "Guru Bantu"/manual). Ditampilkan biar admin bisa
        // hubungkan drpd data pokoknya dobel-dobel gak sinkron.
        $guruBelumTerhubung = User::where('sekolah_id', Auth::user()->sekolah_id)
            ->where('role', 'guru')
            ->whereDoesntHave('guru', fn ($q) => $q->whereNotNull('pegawai_id'))
            ->get();

        $pegawaiTersedia = \App\Models\Pegawai::where('sekolah_id', Auth::user()->sekolah_id)
            ->whereDoesntHave('guru', fn ($q) => $q->whereNotNull('user_id'))
            ->orderBy('nama_lengkap')
            ->get();

        return view('user.index', compact('users', 'guruBelumTerhubung', 'pegawaiTersedia'));
    }

    public function hubungkanPegawai(Request $request, User $user)
    {
        $request->validate(['pegawai_id' => 'required|exists:pegawais,id']);

        $pegawai = \App\Models\Pegawai::findOrFail($request->pegawai_id);

        $guru = \App\Models\Guru::where('user_id', $user->id)->first();

        if ($guru) {
            // Akun ini udah py Guru (mis. dulu "Guru Bantu") - hubungkan ke Pegawai
            $guru->update([
                'pegawai_id' => $pegawai->id,
                'nama' => $pegawai->nama_lengkap,
                'nip_nuptk' => $pegawai->nip_nuptk,
            ]);
        } else {
            \App\Models\Guru::create([
                'sekolah_id' => $user->sekolah_id,
                'user_id' => $user->id,
                'pegawai_id' => $pegawai->id,
                'nama' => $pegawai->nama_lengkap,
                'nip_nuptk' => $pegawai->nip_nuptk,
            ]);
        }

        // Sinkronkan jg nama login-nya biar konsisten
        $user->update(['name' => $pegawai->nama_lengkap]);

        return back()->with('success', "Akun {$user->name} berhasil dihubungkan ke data Pegawai {$pegawai->nama_lengkap}.");
    }

    /** Kartu login siap cetak - HANYA guru yg password-nya masih default (belum pernah diganti) */
    public function kartuLogin()
    {
        $guruList = User::where('sekolah_id', Auth::user()->sekolah_id)
            ->where('role', 'guru')
            ->where('is_password_generated', true)
            ->whereNotNull('password_plain')
            ->orderBy('name')
            ->get();

        return view('user.kartu-login', compact('guruList'));
    }

    public function create()
    {
        return view('user.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|max:100|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role'     => 'required|in:admin,guru,induk',
        ]);

        User::create([
            'sekolah_id' => Auth::user()->sekolah_id,
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'password_plain' => $data['password'],
            'is_password_generated' => true,
            'role'     => $data['role'],
            'aktif'    => true,
        ]);

        return redirect()->route('user.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('user.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:100',
            'email' => ['required', 'email', 'max:100', Rule::unique('users', 'email')->ignore($user->id)],
            'role'  => 'required|in:admin,guru,induk',
            'aktif' => 'nullable|boolean',
        ]);

        // Cegah admin menonaktifkan/menurunkan role dirinya sendiri secara tidak sengaja
        if ($user->id === Auth::id() && ($data['role'] !== 'admin' || empty($data['aktif']))) {
            return back()->withErrors(['role' => 'Anda tidak bisa menonaktifkan atau menurunkan role akun Anda sendiri.']);
        }

        $user->update([
            'name'  => $data['name'],
            'email' => $data['email'],
            'role'  => $data['role'],
            'aktif' => $request->boolean('aktif'),
        ]);

        return redirect()->route('user.index')->with('success', 'Data user berhasil diperbarui.');
    }

    public function resetPassword(Request $request, User $user)
    {
        $data = $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user->update(['password' => Hash::make($data['password']), 'password_plain' => $data['password'], 'is_password_generated' => true]);

        return back()->with('success', "Password untuk {$user->name} berhasil direset.");
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->withErrors(['error' => 'Anda tidak bisa menghapus akun Anda sendiri.']);
        }

        $user->delete();
        return redirect()->route('user.index')->with('success', 'User berhasil dihapus.');
    }

    // ── Ganti Password Sendiri (semua role) ─────────────────────────────────

    public function showChangePassword()
    {
        return view('user.change-password');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password'         => 'required|string|min:6|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password lama tidak sesuai.']);
        }

        $user->update(['password' => Hash::make($request->password), 'password_plain' => null, 'is_password_generated' => false]);

        return back()->with('success', 'Password berhasil diubah.');
    }
}
