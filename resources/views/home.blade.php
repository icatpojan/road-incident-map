@extends('layouts.app')

@section('title', 'Beranda - Road Incident Map')

@section('content')
<div class="hero-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h1 class="hero-title">
                    <i class="fas fa-rocket me-3"></i>
                    Selamat Datang di Road Incident Map
                </h1>
                <p class="hero-subtitle">
                    Aplikasi web modern yang dibangun dengan Laravel 7 dan Bootstrap 5
                </p>

                <div class="row mt-5">
                    <div class="col-md-4 mb-3">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-shield-alt fa-3x text-primary mb-3"></i>
                                <h5 class="card-title">Keamanan</h5>
                                <p class="card-text">Sistem autentikasi yang aman dengan role-based access control.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-mobile-alt fa-3x text-primary mb-3"></i>
                                <h5 class="card-title">Responsive</h5>
                                <p class="card-text">Tampilan yang responsif dan optimal di semua perangkat.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-bolt fa-3x text-primary mb-3"></i>
                                <h5 class="card-title">Cepat</h5>
                                <p class="card-text">Performansi tinggi dengan teknologi modern.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-5">
                    @guest
                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg me-3">
                        <i class="fas fa-sign-in-alt me-2"></i>Login
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-user-plus me-2"></i>Register
                    </a>
                    @else
                    @if(Auth::user()->hasRole('admin'))
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard
                    </a>
                    @else
                    <a href="{{ route('user.dashboard') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-tachometer-alt me-2"></i>User Dashboard
                    </a>
                    @endif
                    @endguest
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
