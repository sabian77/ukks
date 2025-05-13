<div class="container mx-auto px-4 py-6">
  {{-- Card --}}
  <div class="mx-auto bg-white rounded-lg shadow-lg overflow-hidden px-6 py-6 max-w-4xl w-full">

    {{-- Tampilan Pesan --}}
    <div>
      @if (session()->has('error'))
          <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
              class="p-4 bg-red-600 text-white font-semibold rounded-lg shadow-md">
              {{ session('error') }}
          </div>
      @endif

      @if (session()->has('success'))
          <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
              class="p-4 bg-green-600 text-white font-semibold rounded-lg shadow-md">
              {{ session('success') }}
          </div>
      @endif
    </div>
    {{-- ./Tampilan Pesan --}}

    {{-- Judul --}}
    <div class="w-full bg-gradient-to-r from-blue-500 to-blue-700 text-white p-4 text-center text-2xl font-bold rounded-lg shadow-md">
      Laporan Siswa PKL
    </div>
    {{-- ./Judul --}}

    {{-- Form Entry dan Searching --}}
    <div class="flex flex-col md:flex-row items-center justify-between bg-white p-6 rounded-lg shadow-lg mt-4 space-y-4 md:space-y-0">

            <!-- Tombol Create Lapor PKL di kiri -->
    <button wire:click="create()" 
        class="bg-green-500 text-white px-6 py-2 rounded-lg shadow-md hover:bg-green-600 
            focus:ring-4 focus:ring-green-300 transition-all duration-300 ease-in-out 
            active:scale-95 cursor-pointer">
        + Create Lapor PKL
    </button>


        {{-- Cek apakah menampilkan halaman modal --}}
        @if($isOpen == true)
            @include('livewire.fe.pkl.create')
        @endif
        {{-- ./Cek apakah menampilkan halaman modal --}}

        {{-- Form Searching --}}
        <input wire:model.live="search" type="text" placeholder="Search ..."
            class="border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full md:w-1/3">
        {{-- ./Form Searching --}}
    </div>
    {{-- ./Form Entry dan Searching --}}

    {{-- Table PKL --}}
    <div class="overflow-x-auto mt-6">
      <table class="w-full bg-white border border-gray-200 rounded-lg shadow-lg">
        <thead class="bg-gray-300 text-gray-700">
          <tr>
            <th class="px-6 py-3 border-b border-gray-200 text-left">No</th>
            <th class="px-6 py-3 border-b border-gray-200 text-left">Nama Siswa</th>
            <th class="px-6 py-3 border-b border-gray-200 text-left">Industri</th>
            <th class="px-6 py-3 border-b border-gray-200 text-left">Bidang Usaha</th>
            <th class="px-6 py-3 border-b border-gray-200 text-left">Mulai</th>
            <th class="px-6 py-3 border-b border-gray-200 text-left">Selesai</th>
            <th class="px-6 py-3 border-b border-gray-200 text-left">Durasi (Hari)</th>
          </tr>
        </thead>

        <tbody>
          @php
            use Carbon\Carbon;
            $no = 0;
          @endphp

          @foreach($pkls as $key => $pkl)
            @php
              $no++;
              $d1 = Carbon::parse($pkl->mulai);
              $d2 = Carbon::parse($pkl->selesai);
              $selisihHari = $d1->diffInDays($d2);
            @endphp
            
            <tr class="hover:bg-gray-100 transition-all">
              <td class="px-6 py-3 border-b border-gray-200">{{ $no }}</td>
              <td class="px-6 py-3 border-b border-gray-200">{{ $pkl->siswa->nama }}</td>
              <td class="px-6 py-3 border-b border-gray-200">{{ $pkl->industri->nama }}</td>
              <td class="px-6 py-3 border-b border-gray-200">{{ $pkl->industri->bidang_usaha }}</td>
              <td class="px-6 py-3 border-b border-gray-200">{{ $pkl->mulai }}</td>
              <td class="px-6 py-3 border-b border-gray-200">{{ $pkl->selesai }}</td>
              <td class="px-6 py-3 border-b border-gray-200">{{ $selisihHari }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    {{-- ./Table PKL --}}

    {{-- Pagination --}}
    <div class="p-4 flex justify-center">
       {{ $pkls->links() }}
    </div>
    {{-- ./Pagination --}}
  </div>
  {{-- ./Card --}}
</div>
