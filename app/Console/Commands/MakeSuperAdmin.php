<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeSuperAdmin extends Command
{
    protected $signature = 'user:make-super-admin {email}';
    protected $description = 'Jadikan user (berdasarkan email) sebagai super-admin portal sekolah.co.id';

    public function handle(): int
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error("User dengan email {$email} tidak ditemukan.");
            return self::FAILURE;
        }

        $user->update(['is_super_admin' => true]);

        $this->info("Berhasil! {$user->name} ({$email}) sekarang jadi super-admin portal.");
        $this->line('Akses panel di: /admin-portal');

        return self::SUCCESS;
    }
}
