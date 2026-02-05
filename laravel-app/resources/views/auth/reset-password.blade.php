@extends('layouts.page')

@section('content')
<!-- ================= RESET PASSWORD PAGE WITH OTP ================= -->
<div class="d-flex justify-content-center align-items-center" style="min-height: 40vh;">
    <div class="card border-0 shadow-lg p-4" style="border-radius: 20px; max-width: 700px; width: 90%;">
        <!-- HEADER -->
        <div class="card-header text-white text-center" style="background: linear-gradient(135deg, #003366, #0055a5); border-radius: 15px 15px 0 0;">
            <h5 class="fw-bold"><i class="fas fa-key me-2"></i>Reset Password</h5>
        </div>

        <!-- BODY -->
        <div class="card-body px-2 py-5">
            @if($errors->any())
                <div class="alert alert-danger rounded-pill mb-4">
                    {{ $errors->first() }}
                </div>
            @endif

            @if(session('status'))
                <!-- ✅ Success Modal -->
                <div class="text-center" id="successModal">
                    <div class="alert alert-success rounded-pill">
                        ✅ Password successfully changed!
                    </div>
                    <p class="mb-3">You will be redirected to Home/Login shortly.</p>
                    <a href="{{ url('/?showLoginModal=true') }}" id="backHomeBtn" class="btn text-white fw-bold btn-lg" style="background: linear-gradient(135deg, #003366, #0055a5); border-radius:12px; text-decoration:none;">
                        Redirecting in <span id="countdown">5</span>...
                    </a>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const countdownEl = document.getElementById('countdown');
                        const backHomeBtn = document.getElementById('backHomeBtn');
                        let countdown = 5;
                        const interval = setInterval(() => {
                            countdown--;
                            if (countdown > 0) {
                                countdownEl.textContent = countdown;
                            } else {
                                clearInterval(interval);
                                window.location.href = backHomeBtn.href;
                            }
                        }, 1000);
                    });
                </script>
            @else
                <!-- ✅ Reset Password Form -->
                <p class="text-center mb-4" style="font-size: 1.05rem; color: #333;">
                    Enter your email, OTP, and new password to reset your account.
                </p>

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email ?? old('email') }}">

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Email</label>
                        <input type="email" id="email" class="form-control form-control-lg" value="{{ $email ?? old('email') }}" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="otp" class="form-label fw-semibold">OTP</label>
                        <input type="text" id="otp" name="otp" class="form-control form-control-lg" placeholder="Enter the OTP sent to your email" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">New Password</label>
                        <input type="password" id="password" name="password" class="form-control form-control-lg" placeholder="Enter new password" autocomplete="new-password" required>
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label fw-semibold">Confirm Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control form-control-lg" placeholder="Confirm new password" autocomplete="new-password" required>
                    </div>

                    <div class="mt-4 d-grid gap-2">
                        <button type="submit" class="btn btn-lg text-white fw-bold" style="background: linear-gradient(135deg, #003366, #0055a5); border-radius:12px;">
                            Reset Password
                        </button>
                        <a href="{{ url('/') }}" class="btn btn-lg bg-secondary text-white fw-bold" style="border-radius:12px; text-decoration:none;">
                            &larr; Back to Home
                        </a>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
