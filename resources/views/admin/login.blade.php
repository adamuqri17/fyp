<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - TPIRS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="login-body">

<div class="login-container">
    <div class="login-image">
        <div class="mb-4">
            <i class="fas fa-mosque fa-3x"></i>
        </div>
        <h1>TPIRS</h1>
        <p>Sistem Pengurusan Tanah Perkuburan Islam Raudhatul Sa'adah</p>
        <p class="small mt-4">"Setiap yang bernyawa pasti akan merasai mati."</p>
    </div>

    <div class="login-form-wrapper">
        <div class="login-card">
            <div class="text-center mb-5">
                <div class="brand-logo">
                    <i class="fas fa-user-shield"></i>
                </div>
                <h3 class="fw-bold text-dark">Admin Portal</h3>
                <p class="text-muted">Please sign in to continue</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.submit') }}">
                @csrf
                
                <div class="mb-4">
                    <label class="form-label text-muted small fw-bold">USERNAME</label>
                    <div class="login-input-group">
                        <span class="login-input-icon">
                            <i class="fas fa-user"></i>
                        </span>
                        <input type="text" name="username" class="login-form-control login-input-field" 
                               value="{{ old('username') }}" placeholder="Enter your username" required autofocus>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label text-muted small fw-bold">PASSWORD</label>
                    <div class="login-input-group">
                        <span class="login-input-icon">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" name="password" class="login-form-control login-input-field" 
                               placeholder="Enter your password" required>
                    </div>
                </div>

                <div class="d-grid pt-2">
                    <button type="submit" class="btn-login shadow-sm">
                        Sign In <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </div>

                <div class="text-center mt-4">
                    <a href="/" class="text-decoration-none text-muted small">
                        <i class="fas fa-home me-1"></i> Back to Homepage
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>