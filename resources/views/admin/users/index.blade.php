@extends('admin.layout')
@section('title', 'Manajemen Pengguna')
@section('page-title', 'Manajemen Pengguna')
@section('page-sub', 'Kelola akun admin dan staf')

@section('topbar-actions')
  <a href="{{ route('admin.users.create') }}" class="btn-primary" style="width:auto;padding:0.5rem 1.2rem">
    + Tambah Staf
  </a>
@endsection

@section('content')

@if(session('success'))
  <div class="alert alert-success" style="margin-bottom:1rem">{!! session('success') !!}</div>
@endif
@if(session('warning'))
  <div class="alert alert-warning" style="margin-bottom:1rem">{!! session('warning') !!}</div>
@endif
@if(session('error'))
  <div class="alert alert-error" style="margin-bottom:1rem">{{ session('error') }}</div>
@endif

<div class="card">
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Nama</th>
          <th>Username</th>
          <th>Email</th>
          <th>Role</th>
          <th>2FA</th>
          <th>Status</th>
          <th>Login Terakhir</th>
          <th style="width:160px">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @foreach($users as $user)
        <tr>
          <td style="font-weight:600;font-size:0.88rem">{{ $user->full_name }}</td>
          <td style="font-family:monospace;font-size:0.82rem">{{ $user->username }}</td>
          <td style="font-size:0.82rem;color:var(--muted)">{{ $user->email }}</td>
          <td>
            @if($user->role === 'admin')
              <span class="badge" style="background:#1d4ed820;color:#1d4ed8">Admin</span>
            @else
              <span class="badge" style="background:#059669 20;color:#059669">Staf</span>
            @endif
          </td>
          <td>
            @if($user->totp_enabled)
              <span class="badge badge-green">Aktif</span>
            @else
              <span class="badge badge-gray">Belum</span>
            @endif
          </td>
          <td>
            @if($user->force_password_change)
              <span class="badge" style="background:#f59e0b20;color:#f59e0b">Wajib Ganti PW</span>
            @elseif($user->isLocked())
              <span class="badge badge-red">Terkunci</span>
            @else
              <span class="badge badge-green">Aktif</span>
            @endif
          </td>
          <td style="font-size:0.78rem;color:var(--muted)">
            {{ $user->last_login_at ? \Carbon\Carbon::parse($user->last_login_at)->format('d/m/Y H:i') : '—' }}
          </td>
          <td>
            <div style="display:flex;gap:6px;flex-wrap:wrap">
              @if($user->role === 'staf')
                {{-- Reset Password --}}
                <form method="POST" action="{{ route('admin.users.reset-password', $user->id) }}">
                  @csrf
                  <button type="submit" class="btn-secondary"
                    style="padding:4px 10px;font-size:0.72rem"
                    onclick="return confirm('Reset password {{ $user->username }}?')">
                    Reset PW
                  </button>
                </form>

                {{-- Hapus --}}
                @if($user->id !== session('auth_user_id'))
                  <button type="button" class="btn-danger btn-delete"
                    data-action="{{ route('admin.users.destroy', $user->id) }}"
                    style="padding:4px 10px;font-size:0.72rem">Hapus</button>
                @endif
              @else
                <span style="font-size:0.75rem;color:var(--muted)">—</span>
              @endif
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
