<?php

namespace App\Livewire\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class Register extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        event(new Registered(($user = User::create($validated))));

        $admin = Role::query()->firstOrCreate(
            ['nama' => 'admin'],
            ['deskripsi' => 'Administrator sistem']
        );

        $peserta = Role::query()->firstOrCreate(
            ['nama' => 'peserta'],
            ['deskripsi' => 'Peserta ujian tryout CPNS']
        );

        if ($user->id === 1) {
            $user->roles()->sync([$admin->id]);
        } else {
            $user->roles()->sync([$peserta->id]);
        }

        Auth::login($user);

        Session::regenerate();

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}
