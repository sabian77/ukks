<div class="fixed inset-0 z-20 flex items-center justify-center bg-gray-900 bg-opacity-50 transition-opacity">
  <div class="bg-white rounded-lg shadow-xl transform transition-all sm:max-w-lg w-full p-6">
    
    <!-- Judul Modal -->
    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Lapor PKL</h2>
    
    @if(isset($siswa_login) && $siswa_login)
      <p class="text-gray-600">{{ $siswa_login->nama }}</p>
    @else
      <p class="text-gray-600 text-red-500 font-bold">Data siswa tidak tersedia</p>
    @endif
    
    <hr class="my-4">
    
    <form>
      <!-- Siswa -->
      <fieldset class="border border-gray-300 rounded-lg p-4">
        <legend class="text-lg font-bold text-gray-700 px-2">Siswa</legend>
        <select wire:model="siswaId" class="mt-2 w-full border rounded-md p-2 focus:ring focus:ring-blue-300">
          <option value="">Pilih Siswa</option>
          @if(isset($siswa_login) && $siswa_login)
            <option value="{{ $siswa_login->id }}">{{ $siswa_login->nama }}</option>
          @endif
        </select>
      </fieldset>
      
      <!-- Industri -->
      <fieldset class="border border-gray-300 rounded-lg p-4 mt-4">
        <legend class="text-lg font-bold text-gray-700 px-2">Industri</legend>
        <select wire:model="industriId" class="mt-2 w-full border rounded-md p-2 focus:ring focus:ring-blue-300">
          <option value="">Pilih Industri</option>
          @foreach ($industris as $industri)
            <option value="{{ $industri->id }}">{{ $industri->nama }}</option>
          @endforeach
        </select>
      </fieldset>
      
      <!-- Guru Pembimbing -->
      <fieldset class="border border-gray-300 rounded-lg p-4 mt-4">
        <legend class="text-lg font-bold text-gray-700 px-2">Guru Pembimbing</legend>
        <select wire:model="guruId" class="mt-2 w-full border rounded-md p-2 focus:ring focus:ring-blue-300">
          <option value="">Pilih Guru Pembimbing PKL</option>
          @foreach ($gurus as $guru)
            <option value="{{ $guru->id }}">{{ $guru->nama }}</option>
          @endforeach
        </select>
      </fieldset>
      
      <!-- Pelaksanaan PKL -->
      <fieldset class="border border-gray-300 rounded-lg p-4 mt-4">
        <legend class="text-lg font-bold text-gray-700 px-2">Pelaksanaan PKL</legend>
        <label for="mulai" class="block mb-2 text-sm font-bold text-gray-700">Mulai</label>
        <input wire:model="mulai" type="date" id="mulai" 
          class="w-full border rounded-md p-2 focus:ring focus:ring-blue-300">
        
        <label for="selesai" class="block mt-4 mb-2 text-sm font-bold text-gray-700">Selesai</label>
        <input wire:model="selesai" type="date" id="selesai"
          class="w-full border rounded-md p-2 focus:ring focus:ring-blue-300">
      </fieldset>
      
      <!-- Tombol -->
      <div class="flex justify-end space-x-3 mt-6">
        <button wire:click="closeModal()"
          class="px-4 py-2 rounded-md shadow-md bg-gray-200 text-gray-700 hover:bg-gray-300 transition-all">
          Cancel
        </button>
        <button wire:click.prevent="store()"
          class="px-6 py-2 rounded-md shadow-md bg-green-600 text-white hover:bg-green-500 transition-all"
          @if(!isset($siswa_login) || !$siswa_login) disabled @endif>
          Save
        </button>
      </div>
    </form>
  </div>
</div>