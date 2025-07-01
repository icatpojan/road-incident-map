@extends('layouts.app')

@section('title', 'User Dashboard - Road Incident Map')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-2">
                                <i class="fas fa-user text-primary me-3"></i>
                                Selamat Datang, {{ Auth::user()->name }}!
                            </h2>
                            <p class="text-muted mb-0">
                                Anda login sebagai <span class="badge bg-secondary">User</span>
                            </p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="text-muted">
                                <small>
                                    <i class="fas fa-clock me-1"></i>
                                    {{ now()->format('d M Y H:i') }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ Auth::user()->created_at->format('d') }}</h4>
                            <p class="mb-0">Days Member</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-calendar-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ Auth::user()->email }}</h4>
                            <p class="mb-0">Email Address</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-envelope fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ Auth::user()->username }}</h4>
                            <p class="mb-0">Username</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-at fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Profile Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td>{{ Auth::user()->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Username:</strong></td>
                                    <td>{{ Auth::user()->username }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ Auth::user()->email }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Role:</strong></td>
                                    <td>
                                        @if(Auth::user()->hasRole('admin'))
                                        <span class="badge bg-danger">Admin</span>
                                        @else
                                        <span class="badge bg-secondary">User</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Member Since:</strong></td>
                                    <td>{{ Auth::user()->created_at->format('d M Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Last Login:</strong></td>
                                    <td>{{ now()->format('d M Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <div class="text-center">
                                <div class="mb-3">
                                    <i class="fas fa-user-circle fa-5x text-primary"></i>
                                </div>
                                <h5>{{ Auth::user()->name }}</h5>
                                <p class="text-muted">{{ Auth::user()->email }}</p>
                                <button class="btn btn-primary btn-sm">
                                    <i class="fas fa-edit me-1"></i>Edit Profile
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-cog me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('home') }}" class="btn btn-outline-primary">
                            <i class="fas fa-home me-2"></i>Back to Home
                        </a>
                        <a href="{{ route('map.index') }}" class="btn btn-outline-success">
                            <i class="fas fa-map me-2"></i>Open Map
                        </a>
                        <button class="btn btn-outline-info">
                            <i class="fas fa-edit me-2"></i>Edit Profile
                        </button>
                        <button class="btn btn-outline-warning">
                            <i class="fas fa-key me-2"></i>Change Password
                        </button>
                        <button class="btn btn-outline-secondary">
                            <i class="fas fa-bell me-2"></i>Notifications
                        </button>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line me-2"></i>Activity Summary
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="mb-3">
                            <i class="fas fa-chart-pie fa-3x text-success"></i>
                        </div>
                        <h4>Welcome!</h4>
                        <p class="text-muted">Your account is active and ready to use.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
