<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Role::query()->firstOrCreate(
            ['nama' => 'admin'],
            ['deskripsi' => 'Administrator sistem']
        );

        $peserta = Role::query()->firstOrCreate(
            ['nama' => 'peserta'],
            ['deskripsi' => 'Peserta ujian tryout CPNS']
        );

        $users = User::query()->orderBy('id')->get();

        if ($users->isEmpty()) {
            return;
        }

        $firstUser = $users->shift();
        $firstUser->roles()->sync([$admin->id]);

        $users->each(function (User $otherUser) use ($peserta) {
            $otherUser->roles()->sync([$peserta->id]);
        });
    }
}
