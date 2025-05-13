<?php

namespace App\Livewire\fe\Pkl;

use App\Models\Pkl;
use App\Models\Guru;
use App\Models\Siswa;
use Livewire\Component;
use App\Models\Industri;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    public $siswaId, $industriId, $guruId, $mulai, $selesai;
    public $isOpen = 0;
    public $siswa_login = null; // Initialize siswa_login property

    use WithPagination;

    public $rowPerPage = 3;
    public $search;
    public $userMail;

    public function mount()
    {
        // Check if user is logged in
        if (Auth::check()) {
            $this->userMail = Auth::user()->email;
            
            // Log for debugging
            \Log::info("User logged in with email: " . $this->userMail);
            
            // Find the student record
            $this->siswa_login = Siswa::where('email', '=', $this->userMail)->first();
            
            if ($this->siswa_login) {
                \Log::info("Found student with ID: " . $this->siswa_login->id);
                $this->siswaId = $this->siswa_login->id;
            } else {
                \Log::warning("No student found with email: " . $this->userMail);
                session()->flash('error', 'Data siswa tidak ditemukan untuk akun ini.');
            }
        } else {
            \Log::warning("User not logged in");
            session()->flash('error', 'Silakan login terlebih dahulu.');
        }
    }
    
    public function render()
    {
        return view('livewire.fe.pkl.index', [
            'pkls' => $this->search === NULL ?
                        Pkl::latest()->paginate($this->rowPerPage) :
                        Pkl::latest()
                            ->whereHas('siswa', function ($query) {
                                $query->where('nama', 'like', '%' . $this->search . '%');
                            })
                            ->orWhereHas('industri', function ($query) {
                                $query->where('nama', 'like', '%' . $this->search . '%');
                            })
                            ->paginate($this->rowPerPage),
            
            // Pass siswa_login to view
            'siswa_login' => $this->siswa_login,
            
            // Get all industries and teachers
            'industris' => Industri::all(),
            'gurus' => Guru::all(),
        ]);
    }

    public function create()
    {
        \Log::info("Create method called");
        
        if (!$this->siswa_login) {
            \Log::warning("No student data found");
            session()->flash('error', 'Anda tidak dapat membuat laporan PKL karena data siswa tidak ditemukan.');
            return;
        }
        
        $this->resetInputFields();
        // Pre-select the current student
        $this->siswaId = $this->siswa_login->id;
        $this->openModal();
        
        \Log::info("Modal should be open now, isOpen = " . ($this->isOpen ? 'true' : 'false'));
    }
    
    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        // If student is logged in, keep their ID pre-selected
        $this->siswaId = $this->siswa_login ? $this->siswa_login->id : '';
        $this->industriId = '';
        $this->guruId = '';
        $this->mulai = '';
        $this->selesai = '';
    }

    public function store()
    {
        $this->validate([
            'siswaId' => 'required',
            'industriId' => 'required',
            'guruId' => 'required',
            'mulai' => 'required|date',
            'selesai' => 'required|date|after:mulai',
        ]);
        
        DB::beginTransaction();
        
        try {
            $siswa = Siswa::find($this->siswaId);
            
            // Check if siswa exists
            if (!$siswa) {
                session()->flash('error', 'Siswa tidak ditemukan.');
                DB::rollBack();
                return;
            }

            // Check if student has already reported
            if ($siswa->status_lapor_pkl) {
                session()->flash('error', 'Siswa ini sudah melapor PKL sebelumnya.');
                DB::rollBack();
                return;
            }

            // Create new PKL record
            Pkl::create([
                'siswa_id' => $this->siswaId,
                'industri_id' => $this->industriId,
                'guru_id' => $this->guruId,
                'mulai' => $this->mulai,
                'selesai' => $this->selesai,
            ]);

            // Update student's status
            $siswa->update(['status_lapor_pkl' => 1]);

            DB::commit();
            
            $this->closeModal();
            $this->resetInputFields();
            
            session()->flash('success', 'Data PKL berhasil disimpan!');
        }
        catch (\Exception $e) {
            DB::rollBack();
            \Log::error("PKL store error: " . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}