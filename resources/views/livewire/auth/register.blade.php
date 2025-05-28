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
        <div class="space-y-2">
            <label for="nis" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                <i class="fas fa-id-card text-blue-500 mr-2"></i>
                {{ __('NIS') }} <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <input 
                    wire:model="nis"
                    id="nis"
                    type="text"
                    required
                    placeholder="{{ __('Masukkan Nomor Induk Siswa') }}"
                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition duration-200"
                />
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-hashtag text-gray-400"></i>
                </div>
            </div>
            @error('nis')
                <p class="text-red-500 text-sm mt-1 flex items-center">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Gender -->
        <div class="space-y-2">
            <label for="gender" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                <i class="fas fa-venus-mars text-purple-500 mr-2"></i>
                {{ __('Jenis Kelamin') }} <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <select 
                    wire:model="gender" 
                    id="gender"
                    required
                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white appearance-none transition duration-200"
                >
                    <option value="">{{ __('Pilih Jenis Kelamin') }}</option>
                    <option value="L"> {{ __('Laki-laki') }}</option>
                    <option value="P"> {{ __('Perempuan') }}</option>
                </select>
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-user text-gray-400"></i>
                </div>
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <i class="fas fa-chevron-down text-gray-400"></i>
                </div>
            </div>
            @error('gender')
                <p class="text-red-500 text-sm mt-1 flex items-center">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Alamat -->
        <div class="space-y-2">
            <label for="alamat" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                <i class="fas fa-map-marker-alt text-green-500 mr-2"></i>
                {{ __('Alamat') }} <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <textarea 
                    wire:model="alamat"
                    id="alamat"
                    required
                    rows="4"
                    placeholder="{{ __('Masukkan alamat lengkap (Jalan, RT/RW, Kelurahan, Kecamatan, Kota)') }}"
                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition duration-200 resize-vertical"
                ></textarea>
                <div class="absolute top-3 left-0 pl-3 flex items-start pointer-events-none">
                    <i class="fas fa-home text-gray-400 mt-1"></i>
                </div>
            </div>
            @error('alamat')
                <p class="text-red-500 text-sm mt-1 flex items-center">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Kontak -->
        <div class="space-y-2">
            <label for="kontak" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                <i class="fas fa-phone text-orange-500 mr-2"></i>
                {{ __('Nomor Kontak') }} <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <input 
                    wire:model="kontak"
                    id="kontak"
                    type="tel"
                    required
                    placeholder="{{ __('Masukkan nomor telepon/HP (contoh: 08123456789)') }}"
                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition duration-200"
                />
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-mobile-alt text-gray-400"></i>
                </div>
            </div>
            @error('kontak')
                <p class="text-red-500 text-sm mt-1 flex items-center">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Foto -->
        <div class="space-y-2">
            <label for="foto" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                <i class="fas fa-camera text-indigo-500 mr-2"></i>
                {{ __('Foto Siswa') }} <span class="text-red-500">*</span>
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