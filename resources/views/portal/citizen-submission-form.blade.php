@extends('layouts.portal')
@section('title','Penyewaan Lahan')
@section('content')
<div class="page-intro"><div><span class="eyebrow">SEWA LAHAN</span><h1>Penyewaan Lahan</h1><p>Pilih lahan dan tanggal sewa, lalu lengkapi data kegiatan. Pembayaran dilakukan setelah pemeriksaan dan tagihan dari admin.</p></div></div>
<div class="detail-grid"><section class="panel"><form method="POST" action="{{ route('masyarakat.pengajuan.store') }}" enctype="multipart/form-data">@csrf
    <label class="field"><span>Lahan yang ingin disewa</span><select name="lahan_id" required><option value="">Pilih lahan tersedia</option>@foreach($lahan as $item)<option data-foto="{{ $item->foto_url ?? '' }}" data-tarif="{{ $item->tarif_sewa_harian }}" value="{{ $item->id }}" @selected((string)old('lahan_id',$selectedLahanId)===(string)$item->id)>{{ $item->nama_lahan }} · {{ number_format($item->luas,0,',','.') }} m²</option>@endforeach</select></label>
    @if($lahan->isEmpty())<p class="subtle">Saat ini belum ada lahan berstatus tersedia untuk disewa.</p>@endif
    @include('portal.rental-photo')
    @include('portal.sewa-fields')
<label class="field"><span>Nama kegiatan</span><input name="nama_kegiatan" maxlength="255" value="{{ old('nama_kegiatan') }}" placeholder="Contoh: Kebun Komunitas RW 05" required></label>
    <label class="field"><span>Deskripsi rencana kegiatan</span><textarea name="deskripsi_kegiatan" maxlength="5000" placeholder="Jelaskan tujuan, jadwal, penanggung jawab, dan penerima manfaat kegiatan." required>{{ old('deskripsi_kegiatan') }}</textarea></label>
    <label class="field"><span>Dokumen pendukung</span><input type="file" name="dokumen" accept=".pdf,.jpg,.jpeg,.png" required><small>PDF/JPG/PNG, maksimal 5 MB.</small></label>
    @include('portal.harga-sewa-form')
    <div class="form-actions"><button class="btn btn-primary" @disabled($lahan->isEmpty())>Kirim Permintaan Sewa</button><a class="btn btn-secondary" href="{{ route('data-lahan') }}">Batal</a></div>
</form></section><aside class="panel"><h2>Cara Menyewa Lahan</h2><ol class="submission-steps"><li>Pilih lahan dan tanggal sewa.</li><li>Isi kegiatan dan unggah dokumen pendukung.</li><li>Tunggu pemeriksaan admin dan petugas lapangan.</li><li>Setelah tagihan terbit, bayar dan unggah bukti pembayaran.</li><li>Admin memeriksa pembayaran dan mengonfirmasi penyewaan.</li></ol><p class="subtle">Rekomendasi merupakan informasi awal. Pemanfaatan tetap perlu memperhatikan status aset, kondisi lahan, dan tata ruang.</p></aside></div>
@endsection
