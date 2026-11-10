@extends('layouts.portal')
@section('title',$lahan->exists ? 'Edit Lahan' : 'Tambah Lahan')
@section('content')
<div class="page-intro"><div><span class="eyebrow">INVENTARIS LAHAN</span><h1>{{ $lahan->exists ? 'Edit Lahan' : 'Tambah Lahan' }}</h1><p>Isi informasi yang akan ditampilkan pada daftar dan peta lahan.</p></div></div>
<section class="panel"><form method="POST" enctype="multipart/form-data" action="{{ $lahan->exists ? route('admin.lahan.update',$lahan) : route('admin.lahan.store') }}">@csrf @if($lahan->exists) @method('PUT') @endif
<div class="form-grid">
@foreach(['kode_lahan'=>'Kode lahan','nama_lahan'=>'Nama lahan','kota'=>'Kota / Kabupaten'] as $name=>$label)<label class="field"><span>{{ $label }}</span><input name="{{ $name }}" value="{{ old($name,$lahan->$name) }}" maxlength="{{ $name==='nama_lahan' ? 255 : 100 }}" required></label>@endforeach
<label class="field"><span>Luas (m²)</span><input type="number" name="luas" step="0.01" min="0.01" value="{{ old('luas',$lahan->luas) }}" required></label>
<label class="field"><span>Latitude</span><input type="number" name="latitude" step="0.0000001" min="-90" max="90" value="{{ old('latitude',$lahan->latitude) }}" placeholder="-7.2575000" required></label><label class="field"><span>Longitude</span><input type="number" name="longitude" step="0.0000001" min="-180" max="180" value="{{ old('longitude',$lahan->longitude) }}" placeholder="112.7521000" required></label>
</div><label class="field"><span>Alamat lengkap</span><textarea name="alamat" maxlength="2000" required>{{ old('alamat',$lahan->alamat) }}</textarea></label><label class="field"><span>Kondisi lahan</span><textarea name="kondisi" maxlength="5000" required placeholder="Kondisi tanah, akses, vegetasi, dan kendala yang diketahui">{{ old('kondisi',$lahan->kondisi) }}</textarea></label><label class="field"><span>Deskripsi</span><textarea name="deskripsi" maxlength="5000">{{ old('deskripsi',$lahan->deskripsi) }}</textarea></label>
<label class="field"><span>Status ketersediaan</span><select name="status">@foreach(['tersedia'=>'Tersedia','tidak_aktif'=>'Tidak aktif'] as $value=>$label)<option value="{{ $value }}" @selected(old('status',$lahan->status)===$value)>{{ $label }}</option>@endforeach</select>@if(in_array($lahan->status,['diajukan','dimanfaatkan']))<small>Status saat ini {{ $lahan->status }} mengikuti keputusan pengajuan.</small>@endif</label>
@if($lahan->foto_path)<img class="land-photo" src="{{ $lahan->foto_url }}" alt="Kondisi {{ $lahan->nama_lahan }}">@endif
<label class="field"><span>Foto lahan (JPG/PNG, maksimal 5 MB)</span><input type="file" name="foto" accept="image/jpeg,image/png"><small>Kosongkan untuk mempertahankan foto saat ini.</small></label>
<div class="form-actions"><button class="btn btn-primary">Simpan Lahan</button><a class="btn btn-secondary" href="{{ route('admin.lahan.index') }}">Batal</a></div></form></section>
@endsection
