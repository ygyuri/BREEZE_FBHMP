{{-- resources/views/dashboards/admin.blade.php --}}
@extends('layouts.app')

@section('title', 'Admin Dashboard')  <!-- Define title for this page -->

@section('content')
    <div class="container">
        <h1>Admin Dashboard</h1>
        <h5>Welcome, Admin!</h5>
        <p>You have full access to manage the application, view reports, and oversee users.</p>
        <div class="mt-3">
            <a href="{{ route('users.index') }}" class="btn btn-primary">Manage Users</a>
            <a href="{{ route('reports.index') }}" class="btn btn-secondary">View Reports</a>
        </div>
    </div>
@endsection
