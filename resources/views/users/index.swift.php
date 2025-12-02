@extends('layouts.app')

@section('title', 'Users')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h1>Users</h1>
    <a href="/users/create" class="btn">Add New User</a>
</div>

@if(empty($users))
    <div class="alert alert-info" style="background: #dbeafe; color: #1e40af; border: 1px solid #93c5fd;">
        No users found. <a href="/users/create">Create the first user</a>.
    </div>
@else
    <div style="background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #f8f9fa;">
                <tr>
                    <th style="padding: 1rem; text-align: left; border-bottom: 1px solid #e5e7eb;">ID</th>
                    <th style="padding: 1rem; text-align: left; border-bottom: 1px solid #e5e7eb;">Name</th>
                    <th style="padding: 1rem; text-align: left; border-bottom: 1px solid #e5e7eb;">Email</th>
                    <th style="padding: 1rem; text-align: left; border-bottom: 1px solid #e5e7eb;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr style="border-bottom: 1px solid #f3f4f6;">
                    <td style="padding: 1rem;">{{ $user->id }}</td>
                    <td style="padding: 1rem;">{{ $user->name }}</td>
                    <td style="padding: 1rem;">{{ $user->email }}</td>
                    <td style="padding: 1rem;">
                        <a href="/users/{{ $user->id }}" style="color: #2563eb; text-decoration: none; margin-right: 1rem;">View</a>
                        <a href="/users/{{ $user->id }}/edit" style="color: #059669; text-decoration: none; margin-right: 1rem;">Edit</a>
                        <a href="/users/{{ $user->id }}/delete" style="color: #dc2626; text-decoration: none;" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
@endsection