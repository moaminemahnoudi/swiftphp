@extends('layouts.app')

@section('title', 'Create User')

@section('content')
<div style="max-width: 600px; margin: 0 auto;">
    <h1 style="margin-bottom: 2rem;">Create New User</h1>
    
    @if(!empty($errors))
        <div class="alert alert-error">
            <ul style="list-style: none;">
                @foreach($errors as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="/users" style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        @csrf
        
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="{{ $old['name'] ?? '' }}" required>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ $old['email'] ?? '' }}" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>

        <div class="form-group">
            <label for="password_confirmation">Confirm Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required>
        </div>

        <div style="display: flex; gap: 1rem;">
            <button type="submit" class="btn">Create User</button>
            <a href="/users" class="btn" style="background: #6b7280;">Cancel</a>
        </div>
    </form>
</div>
@endsection