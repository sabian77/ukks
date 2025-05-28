<?php

use App\Models\User;
use App\Models\Siswa;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new #[Layout('components.layouts.auth')] class extends Component {
    use WithFileUploads;

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $nis = '';
    public string $gender = '';
    public string $alamat = '';
    public string $kontak = '';
    public $foto;

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'nis' => ['required', 'string', 'max:50', 'unique:siswas,nis'],
            'gender' => ['required', 'in:L,P'],
            'alamat' => ['required', 'string', 'max:255'],
            'kontak' => ['required', 'string', 'max:50'],
            'foto' => ['required', 'image', 'max:2048'], // maksimal 2MB
        ]);

        $validated['password'] = Hash::make($validated['password']);

        // Upload foto
        $fotoPath = $this->foto->store('photos/siswa', 'public');

        // Simpan user ke tabel `users`
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        // Berikan role "siswa" menggunakan Shield
        $user->assignRole('siswa');

        // Simpan data siswa ke tabel `siswas`
        Siswa::create([
            'nama' => $validated['name'],
            'nis' => $validated['nis'],
            'gender' => $validated['gender'],
            'alamat' => $validated['alamat'],
            'kontak' => $validated['kontak'],
            'email' => $validated['email'],
            'foto' => $fotoPath,
        ]);

        event(new Registered($user));

        Auth::login($user);

        $this->redirectIntended(route('dashboard', absolute: false), navigate: true);
    }
};
?>

<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Create an account')" :description="__('Enter your details below to create your account')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="register" class="flex flex-col gap-6" enctype="multipart/form-data">
        <!-- Name -->
        <flux:input
            wire:model="name"
            :label="__('Name')"
            type="text"
            required
            autofocus
            autocomplete="name"
            :placeholder="__('Full name')"
        />

        <!-- Email Address -->
        <flux:input
            wire:model="email"
            :label="__('Email address')"
            type="email"
            required
            autocomplete="email"
            placeholder="email@example.com"
        />
        
        <!-- NIS -->
        <flux:input
            wire:model="nis"
            id="nis"
            type="text"
            required
            placeholder="{{ __('Masukkan Nomor Induk Siswa') }}"
            :label="__('NIS')"
        />
            @error('nis')
                <p class="text-red-500 text-sm mt-1 flex items-center">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    {{ $message }}
                </p>
            @enderror

                <!-- gender -->
        <flux:select 
            wire:model="gender" 
            id="gender" 
            required :label="__('Jenis Kelamin')">
            <option value="">{{ __('Pilih Jenis Kelamin') }}</option>
            <option value="L">{{ __('Laki-laki') }}</option>
            <option value="P">{{ __('Perempuan') }}</option>
        </flux:select>

        @error('gender')
            <p class="text-red-500 text-sm mt-1 flex items-center">
                <i class="fas fa-exclamation-circle mr-1"></i>
                    {{ $message }}
            </p>
         @enderror

        <!-- Alamat -->
        <flux:input
            wire:model="alamat"
            id="alamat"
            :label="__('Alamat')"
            type="email"
            required
            placeholder="Masukkan alamat lengkap "
        />

         @error('alamat')
            <p class="text-red-500 text-sm mt-1 flex items-center">
                <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
            </p>
        @enderror

                <!-- Kontak -->
        <flux:input
            wire:model="kontak"
            id="kontak"
            :label="__('Kontak')"
            type="tel"
            required
            placeholder="Masukkan nomor telepon/HP (contoh: 08123456789)"
        />

         @error('alamat')
            <p class="text-red-500 text-sm mt-1 flex items-center">
                <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
            </p>
        @enderror

        <!-- Foto -->
        <div class="space-y-2">
            <label for="foto" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                <i class="fas fa-camera text-indigo-500 mr-2"></i>
                {{ __('Foto Siswa') }} 
            </label>
            <div class="flex items-center justify-center w-full">
                <label for="foto" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600 transition duration-200">
                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                        @if ($foto)
                            <div class="text-center">
                                <i class="fas fa-check-circle text-green-500 text-2xl mb-2"></i>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    <span class="font-semibold">{{ $foto->getClientOriginalName() }}</span>
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ round($foto->getSize() / 1024, 2) }} KB
                                </p>
                            </div>
                        @else
                            <i class="fas fa-cloud-upload-alt text-gray-400 text-3xl mb-2"></i>
                            <p class="mb-2 text-sm text-gray-500 dark:text-gray-400">
                                <span class="font-semibold">{{ __('Klik untuk upload') }}</span> {{ __('atau drag and drop') }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG, JPEG (MAX. 2MB)</p>
                        @endif
                    </div>
                    <input 
                        wire:model="foto" 
                        id="foto" 
                        type="file" 
                        class="hidden" 
                        accept="image/png,image/jpg,image/jpeg"
                        required
                    />
                </label>
            </div>
            @error('foto')
                <p class="text-red-500 text-sm mt-1 flex items-center">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Password -->
        <flux:input
            wire:model="password"
            :label="__('Password')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('Password')"
            viewable
        />

        <!-- Confirm Password -->
        <flux:input
            wire:model="password_confirmation"
            :label="__('Confirm password')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('Confirm password')"
            viewable
        />

        <div class="flex items-center justify-end">
            <flux:button type="submit" variant="primary" class="w-full">
                {{ __('Create account') }}
            </flux:button>
        </div>
    </form>

    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
        {{ __('Already have an account?') }}
        <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
    </div>
</div>