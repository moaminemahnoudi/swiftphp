@extends('layouts.app')

@section('title', 'User Details')

@section('content')
<div style="max-width: 600px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>User Details</h1>
        <a href="/users" class="btn" style="background: #6b7280;">Back to Users</a>
    </div>

    <div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">ID</label>
            <p style="font-size: 1.125rem;">{{ $user->id }}</p>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">Name</label>
            <p style="font-size: 1.125rem;">{{ $user->name }}</p>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">Email</label>
            <p style="font-size: 1.125rem;">{{ $user->email }}</p>
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <a href="/users/{{ $user->id }}/edit" class="btn" style="background: #059669;">Edit User</a>
            <a href="/users/{{ $user->id }}/delete" class="btn" style="background: #dc2626;" onclick="return confirm('Are you sure you want to delete this user?')">Delete User</a>
        </div>
    </div>
</div>
@endsection