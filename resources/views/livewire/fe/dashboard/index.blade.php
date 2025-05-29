@foreach ($pkls as $pkl)
    <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
        {{ $pkl->siswa->nama }} <br>
        {{ $pkl->guru->nama }} <br>
        {{ $pkl->industri->nama }} <br>
        {{ $pkl->bidang_usaha }} <br>
        {{ $pkl->alamat }} <br>
        {{ $pkl->mulai }} <br>
        {{ $pkl->selesai }}
    </div>
@endforeach
