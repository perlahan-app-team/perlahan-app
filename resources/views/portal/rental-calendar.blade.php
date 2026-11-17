<section class="rental-calendar {{ ($calendarPopup ?? false) ? 'rental-calendar-popup' : '' }}" data-calendar data-popup="{{ ($calendarPopup ?? false) ? 'true' : 'false' }}" @if($calendarPopup ?? false) hidden role="dialog" @endif data-land="{{ $calendarLandId ?? '' }}" data-endpoint="{{ route('lahan.kalender', ['lahan'=>'__LAHAN__']) }}" data-today="{{ today()->toDateString() }}" aria-label="Kalender ketersediaan lahan">
    <h3>Kalender Ketersediaan</h3>
    <div class="rental-calendar-toolbar"><button type="button" class="btn btn-secondary" data-prev aria-label="Bulan sebelumnya">‹</button><strong data-month></strong><button type="button" class="btn btn-secondary" data-next aria-label="Bulan berikutnya">›</button></div>
    <p class="rental-calendar-legend"><span><i class="calendar-free"></i>Hijau: tersedia</span><span><i class="calendar-busy"></i>Merah: sudah disewa</span></p>
    <p data-message class="subtle" role="status" aria-live="polite">Pilih lahan untuk melihat jadwal.</p>
    <div class="rental-calendar-week" aria-hidden="true">@foreach(['Sen','Sel','Rab','Kam','Jum','Sab','Min'] as $day)<span>{{ $day }}</span>@endforeach</div>
    <div class="rental-calendar-days" data-days></div>
    <p class="subtle">Merah berdasarkan penyewaan yang sudah disetujui. Permintaan yang masih diproses belum mengunci jadwal. Tanggal lampau tidak dapat dipilih.</p>
</section>
@once
@push('styles')<link rel="stylesheet" href="{{ asset('css/rental-calendar.css') }}">@endpush
@push('scripts')<script src="{{ asset('js/rental-calendar.js') }}" defer></script>@endpush
@endonce
