@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
<div style="text-align: center; padding: 4rem 0;">
    <h1 style="font-size: 3rem; margin-bottom: 1rem; color: #1f2937;">Welcome to SwiftPHP</h1>
    <p style="font-size: 1.25rem; color: #6b7280; margin-bottom: 2rem;">A lightweight, modern PHP framework designed for speed, security, and simplicity.</p>
    
    <div style="display: flex; gap: 1rem; justify-content: center; margin-bottom: 3rem;">
        <a href="/users" class="btn">View Users</a>
        <a href="/users/create" class="btn" style="background: #059669;">Create User</a>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin-top: 3rem;">
        <div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <h3 style="color: #2563eb; margin-bottom: 1rem;">⚡ Fast & Lightweight</h3>
            <p>Optimized for performance with minimal overhead and fast routing.</p>
        </div>
        
        <div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <h3 style="color: #2563eb; margin-bottom: 1rem;">🔒 Secure by Default</h3>
            <p>Built-in CSRF protection, input validation, and secure password hashing.</p>
        </div>
        
        <div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <h3 style="color: #2563eb; margin-bottom: 1rem;">🗄️ Multi-Database</h3>
            <p>Support for MySQL, PostgreSQL, and SQLite with fluent query builder.</p>
        </div>
    </div>
</div>
@endsection