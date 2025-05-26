<?php

namespace App\Livewire\Fe\Guru\Pkl;

use Livewire\Component;
use App\Models\Pkl;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Livewire\Volt\Compilers\Mount;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    public $siswaId, $industriId, $guruId, $mulai, $selesai;
    use WithPagination;

    public $rowPerPage=10;
    public $search;
    public $userMail;

    public function mount(){
        //membaca email user yang seddang login
        $this->userMail = Auth::user()->email;
    }   

    public function render()
    {
        return view('livewire.fe.guru.pkl.index',[
            'pkls' => Pkl::latest()->paginate($this->rowPerPage),
            'pkls' => $this->search === NULL ?
                        Pkl::latest()->paginate($this->rowPerPage) 
                        : Pkl::latest()->whereHas('siswa', function ($query) {
                                                    $query->where('nama', 'like', '%' . $this->search . '%');
                                                })
                                        ->orWhereHas('guru', function ($query) {
                                                    $query->where('nama', 'like', '%' . $this->search . '%');
                                        })
                                        ->orWhereHas('industri', function ($query) {
                                                    $query->where('nama', 'like', '%' . $this->search . '%');       
                                        })->paginate($this->rowPerPage),
            'siswa'=>Siswa::all(),
            'industris'=>Industri::all(),
            'gurus'=>Guru::all(),

                        
        ]);
    }
}
