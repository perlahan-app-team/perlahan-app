@extends('layouts.portal')
@section('title','Pengguna')
@section('content')
<div class="page-intro"><div><span class="eyebrow">AKSES PLATFORM</span><h1>Pengguna</h1><p>Kelola akun masyarakat, administrator, dan petugas lapangan.</p></div><a class="btn btn-primary" href="{{ route('admin.users.create') }}">+ Tambah Pengguna</a></div>
<form class="portal-filters"><input name="cari" value="{{ request('cari') }}" placeholder="Cari nama atau email" aria-label="Cari pengguna"><button class="btn btn-primary">Cari</button><a class="btn btn-secondary" href="{{ route('admin.users.index') }}">Reset</a></form>
<div class="table-wrap"><table><thead><tr><th>Nama</th><th>Email</th><th>Peran</th><th>Terdaftar</th><th>Aksi</th></tr></thead><tbody>@forelse($users as $person)<tr><td><strong>{{ $person->name }}</strong></td><td>{{ $person->email }}</td><td>{{ ucfirst(str_replace('_',' ',$person->role)) }}</td><td>{{ $person->created_at?->format('d M Y') }}</td><td><a href="{{ route('admin.users.edit',$person) }}">Edit pengguna →</a></td></tr>@empty<tr><td colspan="5" class="empty-state">Tidak ada pengguna yang cocok.</td></tr>@endforelse</tbody></table></div>{{ $users->links() }}
@endsection
