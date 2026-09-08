<?php

namespace App\Http\Controllers;

use App\Models\CustomRole;
use App\Models\CustomRolePermission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = CustomRole::withCount('users')->orderBy('nama')->get();

        return view('user.role.index', compact('roles'));
    }

    public function create()
    {
        return view('user.role.form', [
            'role' => null,
            'permissionMap' => collect(CustomRole::DAFTAR_MODUL)->mapWithKeys(fn ($label, $key) => [$key => ['boleh_akses' => false, 'read_only' => true]]),
        ]);
    }

    public function edit(CustomRole $role)
    {
        return view('user.role.form', [
            'role' => $role,
            'permissionMap' => $role->permissionMap(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate(['nama' => 'required|string|max:100']);
        $data['sekolah_id'] = auth()->user()->sekolah_id;

        $role = CustomRole::create($data);
        $this->simpanPermission($request, $role);

        return redirect()->route('role.index')->with('success', "Role \"{$role->nama}\" berhasil dibuat.");
    }

    public function update(Request $request, CustomRole $role)
    {
        $data = $request->validate(['nama' => 'required|string|max:100']);
        $role->update($data);
        $this->simpanPermission($request, $role);

        return redirect()->route('role.index')->with('success', "Role \"{$role->nama}\" berhasil diperbarui.");
    }

    private function simpanPermission(Request $request, CustomRole $role): void
    {
        foreach (CustomRole::DAFTAR_MODUL as $key => $label) {
            CustomRolePermission::updateOrCreate(
                ['custom_role_id' => $role->id, 'modul_key' => $key],
                [
                    'boleh_akses' => $request->boolean("modul.{$key}.boleh_akses"),
                    'read_only' => $request->boolean("modul.{$key}.read_only"),
                ]
            );
        }
    }

    public function destroy(CustomRole $role)
    {
        if ($role->users()->exists()) {
            return back()->with('error', "Role \"{$role->nama}\" masih dipakai oleh user, lepaskan dulu sebelum dihapus.");
        }

        $role->delete();

        return back()->with('success', 'Role berhasil dihapus.');
    }
}
