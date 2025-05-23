<?php

namespace App\Livewire\Fe\Industri;

use App\Models\Industri;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Index extends Component
{
    use WithPagination;

    public $nama, $bidang_usaha, $alamat, $kontak, $email, $website;
    public $isOpen = false;  // <-- perbaikan ini
    public $rowPerPage = 3;
    public $search;

    public function render()
    {
        return view('livewire.fe.industri.index', [
            'industris' => $this->search === null
                ? Industri::latest()->paginate($this->rowPerPage)
                : Industri::latest()
                    ->where('nama', 'like', '%' . $this->search . '%')
                    ->orWhere('bidang_usaha', 'like', '%' . $this->search . '%')
                    ->orWhere('alamat', 'like', '%' . $this->search . '%')
                    ->paginate($this->rowPerPage),
        ]);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    private function resetInputFields()
    {
        $this->nama = '';
        $this->bidang_usaha = '';
        $this->alamat = '';
        $this->kontak = '';
        $this->email = '';
        $this->website = '';
    }

    public function store()
    {
        $this->validate([
            'nama' => 'required',
            'bidang_usaha' => 'required',
            'alamat' => 'required',
            'kontak' => 'required',
            'email' => 'required|email',
            'website' => 'required',
        ]);

        DB::beginTransaction();

        try {
            // Cek apakah nama industri sudah ada
            $exists = Industri::where('nama', $this->nama)->exists();

            if ($exists) {
                DB::rollBack();
                session()->flash('error', 'Gagal: Nama industri sudah terdaftar.');
                return;
            }

            Industri::create([
                'nama' => $this->nama,
                'bidang_usaha' => $this->bidang_usaha,
                'alamat' => $this->alamat,
                'kontak' => $this->kontak,
                'email' => $this->email,
                'website' => $this->website,
            ]);

            DB::commit();

            $this->resetInputFields();
            session()->flash('success', 'Data industri berhasil disimpan!');
            $this->closeModal();

            // Optional: reload page / refresh data jika perlu
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}