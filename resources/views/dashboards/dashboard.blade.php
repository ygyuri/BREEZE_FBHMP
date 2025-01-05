{{-- resources/views/dashboards/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', ucfirst($role) . ' Dashboard')

@section('content')
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-3">
                {{-- Sidebar --}}
                @include('dashboards.partials.sidebar', ['role' => $role])
            </div>
            <div class="col-md-9">
                {{-- Main Content --}}
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4>{{ ucfirst($role) }} Dashboard</h4>
                    </div>
                    <div class="card-body">
                        @if ($role === 'admin')
                            @include('dashboards.partials.admin')
                        @elseif ($role === 'donor')
                            @include('dashboards.partials.donor')
                        @elseif ($role === 'foodbank')
                            @include('dashboards.partials.foodbank')
                        @else
                            <p class="text-danger">Unauthorized access.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
