<div class="py-24">
  {{-- Card --}}
  <div class="mx-auto bg-white rounded-lg shadow-lg overflow-hidden px-6 py-6">

    {{-- Tampilan Pesan --}}
    <div>
      @if (session()->has('error'))
          <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
              class="p-4 bg-red-500 text-white rounded-md mb-4">
              {{ session('error') }}
          </div>
      @endif

      @if (session()->has('success'))
          <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
              class="p-4 bg-green-500 text-white rounded-md mb-4">
              {{ session('success') }}
          </div>
      @endif
      
      {{-- Flash Messages dari Livewire --}}
      @if (session()->has('message'))
          <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
              class="p-4 bg-blue-500 text-white rounded-md mb-4">
              {{ session('message') }}
          </div>
      @endif
    </div>
    {{-- ./Tampilan Pesan --}}

    {{-- Judul --}}
    <div class="w-full bg-gray-200 p-4 text-center text-2xl font-bold text-gray-700">
      Siswa PKL
    </div>
    {{-- ./Judul --}}
    
    {{-- Form Entry dan Searching --}}
    <div class="mx-auto flex items-center justify-between bg-white p-6 rounded-lg shadow-md mb-6">

        <!-- Tombol Create Lapor PKL di kiri -->
        <button wire:click="create" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-200">
            Create Lapor PKL
        </button>

        {{-- Cek apakah menampilkan halaman modal --}}
        @if($isOpen)
            @include('livewire.fe.pkl.create')
        @endif
        {{-- ./Cek apakah menampilkan halaman modal --}}

        {{-- Form Searching --}}
        <input wire:model.live="search" type="text" placeholder="Search ..." class="border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        {{-- ./Form Searching --}}
    </div>
    {{-- ./Form Entry dan Searching --}}

    {{-- Table PKL --}}
    <div class="overflow-x-auto">
      <table class="min-w-full bg-white border border-gray-200">
        <thead>
          <tr>
            <th class="px-4 py-2 border-b border-gray-200 text-left text-gray-600">No</th>
            <th class="px-4 py-2 border-b border-gray-200 text-left text-gray-600">Nama Siswa</th>
            <th class="px-4 py-2 border-b border-gray-200 text-left text-gray-600">Industri</th>
            <th class="px-4 py-2 border-b border-gray-200 text-left text-gray-600">Bidang Usaha</th>
            <th class="px-4 py-2 border-b border-gray-200 text-left text-gray-600">Guru Pembimbing</th>
            <th class="px-4 py-2 border-b border-gray-200 text-left text-gray-600">Mulai</th>
            <th class="px-4 py-2 border-b border-gray-200 text-left text-gray-600">Selesai</th>
            <th class="px-4 py-2 border-b border-gray-200 text-left text-gray-600">Durasi (Hari)</th>
            <th class="px-4 py-2 border-b border-gray-200 text-left text-gray-600">Action</th>
          </tr>
        </thead>

        <tbody>
          @php
            use Carbon\Carbon;
            use Illuminate\Support\Facades\Auth;
            use App\Models\Siswa;

            $siswa_login = Siswa::where('email', Auth::user()->email)->first();
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
              <td class="px-4 py-2 border-b border-gray-200">{{ $pkl->industri->nama }}</td>
              <td class="px-4 py-2 border-b border-gray-200">{{ $pkl->industri->bidang_usaha }}</td>
              <td class="px-4 py-2 border-b border-gray-200">{{ $pkl->guru->nama ?? '-'  }}</td>
              <td class="px-4 py-2 border-b border-gray-200">{{ $pkl->mulai }}</td>
              <td class="px-4 py-2 border-b border-gray-200">{{ $pkl->selesai }}</td>
              <td class="px-4 py-2 border-b border-gray-200">{{ $selisihHari }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                @if($pkl->siswa_id === $siswa_login->id)
                  <div class="flex items-center justify-center space-x-2">
                    <!-- Edit Button -->
                    <button wire:click="edit({{ $pkl->id }})" 
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                      </svg>
                      Edit
                    </button>
                    
                    <!-- Delete Button with Confirmation -->
                    <div x-data="{ confirmDelete: false }" 
                        x-on:close-modal.window="confirmDelete = false"
                         @close-modal.window="confirmDelete = false">
                      <button     @click="confirmDelete = true" 
                              wire:click="setPklIdToDelete({{ $pkl->id }})" 
                              class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Hapus
                      </button>
                      
                      <!-- Delete Confirmation Modal -->
                      <div x-show="confirmDelete" 
                          x-transition:enter="transition ease-out duration-300"
                          x-transition:enter-start="opacity-0"
                          x-transition:enter-end="opacity-100"
                          x-transition:leave="transition ease-in duration-200"
                          x-transition:leave-start="opacity-100"
                          x-transition:leave-end="opacity-0"
                          class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" 
                          @click.away="confirmDelete = false"
                          style="display: none;">
                        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white"
                            @click.stop>
                          <div class="mt-3 text-center">
                            <!-- Warning Icon -->
                            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                              <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                              </svg>
                            </div>
                            
                            <!-- Modal Content -->
                            <h3 class="text-lg font-medium text-gray-900 mt-4">Konfirmasi Hapus Data</h3>
                            <div class="mt-2 px-7 py-3">
                              <p class="text-sm text-gray-500">
                                Apakah Anda yakin ingin menghapus data PKL ini?
                              </p>
                              <div class="mt-3 p-3 bg-gray-50 rounded-lg">
                                <p class="text-sm font-medium text-gray-900">{{ $pkl->siswa->nama }}</p>
                                <p class="text-sm text-gray-600">{{ $pkl->industri->nama }}</p>
                                <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($pkl->mulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($pkl->selesai)->format('d M Y') }}</p>
                              </div>
                              <p class="text-xs text-red-600 mt-2">
                                <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                  <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                Tindakan ini tidak dapat dibatalkan!
                              </p>
                            </div>
                            
                            <!-- Modal Actions -->
                            <div class="flex items-center justify-center space-x-4 mt-4">
                              <button wire:click="confirmDelete" 
                                      class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition duration-150 ease-in-out">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Ya, Hapus!
                              </button>
                              <button @click="confirmDelete = false" 
                                      class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 transition duration-150 ease-in-out">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Batal
                              </button>

                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                @else
                  <div class="flex items-center justify-center">
                    <span class="inline-flex items-center px-3 py-2 text-sm text-gray-400">
                      <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                      </svg>
                      Tidak ada aksi
                    </span>
                  </div>
                @endif
              </td>

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