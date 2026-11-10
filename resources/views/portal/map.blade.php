@extends('layouts.portal')
@section('title','Peta Lahan')
@push('styles')<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">@endpush
@section('content')
@php($role = auth()->user()->role)
<div class="page-intro"><div><span class="eyebrow">JELAJAHI POTENSI</span><h1>Peta Lahan</h1><p>Temukan lokasi lahan berdasarkan koordinat yang tersimpan.</p></div></div>
<form class="portal-filters"><input name="cari" value="{{ request('cari') }}" placeholder="Cari lahan atau alamat" aria-label="Cari lahan"><select name="status" aria-label="Status lahan"><option value="">Semua status</option>@foreach(['tersedia','diajukan','dimanfaatkan','tidak_aktif'] as $status)<option value="{{ $status }}" @selected(request('status')===$status)>{{ ucfirst(str_replace('_',' ',$status)) }}</option>@endforeach</select><button class="btn btn-primary">Terapkan</button><a class="btn btn-secondary" href="{{ route('peta') }}">Reset</a></form>
<p id="map-message" class="subtle">{{ $lahans->count() }} lahan dengan koordinat tersedia. Peta memerlukan koneksi internet.</p><div id="land-map" aria-label="Peta lokasi lahan"></div>
<div class="map-list">@forelse($lahans as $item)<article data-lat="{{ $item->latitude }}" data-lng="{{ $item->longitude }}"><h2>{{ $item->nama_lahan }}</h2><p>{{ $item->alamat }} · {{ number_format($item->luas,0,',','.') }} m²</p>@include('portal.status',['status'=>$item->status])<div class="form-actions"><button class="btn btn-secondary focus-land" type="button">Tampilkan di Peta</button>@if($role==='masyarakat')<a href="{{ route('masyarakat.lahan.show',$item) }}">Lihat detail</a>@elseif($role==='admin')<a href="{{ route('admin.lahan.edit',$item) }}">Lihat / Edit</a>@endif</div></article>@empty<article>Tidak ada lahan dengan koordinat yang cocok.</article>@endforelse</div>
@endsection
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
if(typeof L==='undefined'){document.getElementById('map-message').textContent='Peta belum dapat dimuat. Periksa koneksi internet. Daftar lokasi tetap tersedia di bawah.';}else{
const map=L.map('land-map').setView([-7.2575,112.7521],12);
const tiles=L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19,attribution:'&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'}).addTo(map);
tiles.on('tileerror',()=>{document.getElementById('map-message').textContent='Sebagian peta tidak dapat dimuat. Periksa koneksi internet.';});
const bounds=[];
document.querySelectorAll('[data-lat]').forEach(card=>{const point=[Number(card.dataset.lat),Number(card.dataset.lng)];if(!point.every(Number.isFinite))return;bounds.push(point);const popup=document.createElement('div');const title=document.createElement('strong');title.textContent=card.querySelector('h2').textContent;popup.append(title);const text=document.createElement('p');text.textContent=card.querySelector('p').textContent;popup.append(text);const marker=L.marker(point).addTo(map).bindPopup(popup);card.querySelector('.focus-land').addEventListener('click',()=>{map.setView(point,16);marker.openPopup();document.getElementById('land-map').scrollIntoView({behavior:'smooth',block:'center'});});});
if(bounds.length)map.fitBounds(bounds,{padding:[30,30],maxZoom:15});
}
</script>
@endpush
