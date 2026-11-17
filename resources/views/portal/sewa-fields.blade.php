<div class="rental-date-picker">
<div class="form-grid">
    <label class="field"><span>Tanggal mulai sewa</span><input type="text" data-date-open="start" placeholder="Pilih tanggal mulai" readonly required aria-haspopup="dialog" aria-expanded="false"><input type="hidden" name="sewa_mulai" value="{{ old('sewa_mulai', isset($pengajuan) ? $pengajuan->sewa_mulai?->toDateString() : '') }}"></label>
    <label class="field"><span>Tanggal selesai sewa</span><input type="text" data-date-open="end" placeholder="Pilih tanggal selesai" readonly required aria-haspopup="dialog" aria-expanded="false"><input type="hidden" name="sewa_selesai" value="{{ old('sewa_selesai', isset($pengajuan) ? $pengajuan->sewa_selesai?->toDateString() : '') }}"></label>
</div>
@include('portal.rental-calendar', ['calendarLandId' => isset($pengajuan) ? $pengajuan->lahan_id : null, 'calendarPopup' => true])
</div>
<p class="subtle">Untuk sewa satu hari, pilih tanggal mulai dan selesai yang sama. Tanggal yang sama dihitung sebagai satu hari penuh. Jadwal dikonfirmasi setelah persetujuan admin.</p>
