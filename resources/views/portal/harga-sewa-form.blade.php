<section aria-label="Informasi biaya sewa">
<h3>Informasi Biaya Sewa</h3>
<p>Harga sewa <strong data-rental-rate aria-live="polite">Pilih lahan terlebih dahulu</strong>.</p>
<p class="subtle" data-rental-estimate aria-live="polite">Pilih tanggal mulai dan selesai untuk melihat estimasi total.</p>
<p class="subtle">Penyewaan awal tidak memerlukan pembayaran atau bukti bayar. Setelah verifikasi lapangan selesai, admin menerbitkan tagihan. Bayar dan unggah bukti melalui detail penyewaan. Admin memeriksa pembayaran sebelum menyetujui penyewaan.</p>
</section>
@push('scripts')
<script>
(() => {
    const rateLabel = document.querySelector('[data-rental-rate]');
    const estimate = document.querySelector('[data-rental-estimate]');
    const form = rateLabel.closest('form');
    const land = form.elements.lahan_id;
    const start = form.elements.sewa_mulai;
    const end = form.elements.sewa_selesai;
    const rupiah = value => new Intl.NumberFormat('id-ID', {style:'currency', currency:'IDR', maximumFractionDigits:0}).format(value);
    const update = () => {
        const rate = Number(land.selectedOptions[0]?.dataset.tarif || 0);
        rateLabel.textContent = rate ? rupiah(rate) + ' per hari' : 'Pilih lahan terlebih dahulu';
        const days = Math.round((Date.parse(end.value) - Date.parse(start.value)) / 86400000) + 1;
        estimate.textContent = rate && Number.isFinite(days) && days > 0
            ? `Estimasi ${days} hari: ${rupiah(rate * days)}. Total akhir diterbitkan admin setelah pemeriksaan.`
            : 'Pilih tanggal mulai dan selesai untuk melihat estimasi total.';
    };
    [land, start, end].forEach(field => field.addEventListener('change', update));
    update();
})();
</script>
@endpush
