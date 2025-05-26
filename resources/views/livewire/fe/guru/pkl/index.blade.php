<div class="py-24">
  {{-- Card --}}
  <div class="mx-auto bg-white rounded-lg shadow-lg overflow-hidden px-6 py-6">

    {{-- Judul --}}
    <div class="w-full bg-gray-200 p-4 text-center text-2xl font-bold text-gray-700">
      Laporan PKL Siswa
    </div>
    {{-- ./Judul --}}

    {{-- Form Searching --}}
    <div class="mx-auto flex items-center justify-end bg-white p-6 rounded-lg shadow-md mb-6">
        <input wire:model.live="search" type="text" placeholder="Search ..." 
            class="border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>
    {{-- ./Form Searching --}}

    {{-- Table PKL --}}
    <div class="overflow-x-auto">
      <table class="min-w-full bg-white border border-gray-200">
        <thead>
          <tr>
            <th class="px-4 py-2 border-b border-gray-200 text-left text-gray-600">No</th>
            <th class="px-4 py-2 border-b border-gray-200 text-left text-gray-600">Nama Siswa</th>
            <th class="px-4 py-2 border-b border-gray-200 text-left text-gray-600">Guru Pembimbing</th>
            <th class="px-4 py-2 border-b border-gray-200 text-left text-gray-600">Industri</th>
            <th class="px-4 py-2 border-b border-gray-200 text-left text-gray-600">Bidang Usaha</th>
            <th class="px-4 py-2 border-b border-gray-200 text-left text-gray-600">Mulai</th>
            <th class="px-4 py-2 border-b border-gray-200 text-left text-gray-600">Selesai</th>
            <th class="px-4 py-2 border-b border-gray-200 text-left text-gray-600">Durasi (Hari)</th>
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
              $selisihHari = $d1->diffInDays($d2); // Selisih dalam hari
            @endphp
            
            <tr class="hover:bg-gray-100 transition duration-200">
              <td class="px-4 py-2 border-b border-gray-200">{{ $no }}</td>
              <td class="px-4 py-2 border-b border-gray-200">{{ $pkl->siswa->nama }}</td>
              <td class="px-4 py-2 border-b border-gray-200">{{ $pkl->guru->nama }}</td>
              <td class="px-4 py-2 border-b border-gray-200">{{ $pkl->industri->nama }}</td>
              <td class="px-4 py-2 border-b border-gray-200">{{ $pkl->industri->bidang_usaha }}</td>
              <td class="px-4 py-2 border-b border-gray-200">{{ $pkl->mulai }}</td>
              <td class="px-4 py-2 border-b border-gray-200">{{ $pkl->selesai }}</td>
              <td class="px-4 py-2 border-b border-gray-200">{{ $selisihHari }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    {{-- ./Table PKL --}}

    {{-- Pagination --}}
    <div class="p-4">
       {{ $pkls->links() }}
    </div>
    {{-- ./Pagination --}}
  </div>
  {{-- ./Card --}}
</div>
