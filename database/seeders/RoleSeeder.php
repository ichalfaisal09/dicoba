<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['nama' => 'admin', 'deskripsi' => 'Administrator sistem'],
            ['nama' => 'peserta', 'deskripsi' => 'Peserta ujian tryout CPNS'],
        ];

        foreach ($roles as $role) {
            Role::query()->updateOrCreate(['nama' => $role['nama']], $role);
        }
    }
}
