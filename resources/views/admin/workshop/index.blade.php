@extends('admin.layout')
@section('title', 'Workshop')
@section('page-title', 'Workshop')
@section('topbar-actions')
<a href="{{ route('admin.workshop.create') }}" class="btn-primary" style="width:auto;padding:0.5rem 1rem">+ Tambah</a>
@endsection
@section('content')
<div class="card">
  <div class="card-body" style="color:var(--muted);text-align:center;padding:3rem">
    <p>View <strong>admin/workshop/index</strong> aktif.</p>
    <p style="font-size:0.8rem;margin-top:0.5rem">Controller sudah lengkap — tambahkan HTML tabel/form sesuai kebutuhan.</p>
  </div>
</div>
@endsection
