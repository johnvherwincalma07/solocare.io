@extends('layouts.page')

@section('content')
<div class="d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card shadow-lg otp-card">
        <div class="row g-0">

            <!-- LEFT PANEL -->
            <div class="col-md-5 d-none d-md-flex otp-left-panel">
                <h3 class="fw-bold mb-3">🔒 Secure Account</h3>
                <p class="text-white-50 text-center">
                    Enter the OTP sent to your registered email to verify your identity and reset your password securely.
                </p>
                <img src="{{ asset('images/otp.jpg') }}" alt="OTP Illustration" class="img-fluid mt-3">
            </div>

            <!-- RIGHT PANEL (FORM) -->
            <div class="col-md-7 otp-right-panel">
                @if($errors->any())
                    <div class="alert alert-danger rounded-pill mb-3 text-center">
                        {{ $errors->first() }}
                    </div>
                @endif

                <h5 class="fw-bold text-center mb-4">Verify OTP</h5>
                <p class="text-center mb-4" style="font-size: 1.05rem; color: #333;">
                    Enter the 6-digit OTP sent to your email.
                </p>

                <form method="POST" action="{{ route('password.verifyOtp') }}">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email ?? old('email') }}">

                    <!-- OTP boxes -->
                    <div class="otp-inputs d-flex justify-content-between mb-3">
                        @for ($i = 0; $i < 6; $i++)
                            <input type="text" name="otp[]" maxlength="1" class="otp-box form-control" required>
                        @endfor
                    </div>

                    <!-- Resend OTP -->
                    <div class="text-center mb-4">
                        <span>Didn't receive the OTP? </span>
                        <button type="button" class="btn btn-link p-0 fw-bold" style="font-size: 0.95rem;" onclick="resendOtp()">
                            Resend OTP
                        </button>
                    </div>

                    <div class="d-grid mt-3">
                        <button type="submit" class="btn text-white fw-bold btn-lg otp-btn">
                            Verify OTP
                        </button>
                    </div>
                </form>

                <!-- Back to Home Button -->
                <div class="d-grid mt-3">
                    <a href="{{ route('home') }}" class="btn btn-outline-secondary fw-bold btn-lg">
                        ← Back to Home
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
/* === OTP CARD STYLING === */
.otp-card {
    border-radius: 20px;
    max-width: 900px;
    width: 100%;
    overflow: hidden;
}

/* LEFT PANEL */
.otp-left-panel {
    background: linear-gradient(135deg, #003366, #0055a5);
    color: #fff;
    padding: 2rem;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
}
.otp-left-panel img {
    max-width: 180px;
    margin-top: 1rem;
}

/* RIGHT PANEL */
.otp-right-panel {
    padding: 3rem 2rem;
}

/* OTP INPUT BOXES */
.otp-inputs {
    gap: 10px;
}
.otp-box {
    width: 50px;
    height: 50px;
    font-size: 1.5rem;
    text-align: center;
    border-radius: 12px;
    border: 1px solid #ccc;
    outline: none;
}
.otp-box:focus {
    border-color: #0055a5;
    box-shadow: 0 0 0 0.2rem rgba(0,85,165,0.25);
}

/* BUTTONS */
.otp-btn {
    background: linear-gradient(135deg, #003366, #0055a5);
    border-radius: 12px;
    font-weight: bold;
}

/* Back to home button */
.otp-right-panel .btn-outline-secondary {
    border-radius: 12px;
    font-weight: bold;
}

/* Resend OTP */
.otp-right-panel .btn-link {
    color: #0055a5;
    text-decoration: none;
}
.otp-right-panel .btn-link:hover {
    text-decoration: underline;
}

/* Responsive */
@media (max-width: 768px) {
    .otp-left-panel { display: none; }
    .otp-right-panel { padding: 2rem 1.5rem; }
}
</style>

<script>
// Auto move focus to next OTP box
document.addEventListener('DOMContentLoaded', () => {
    const inputs = document.querySelectorAll('.otp-box');
    inputs.forEach((input, i) => {
        input.addEventListener('input', () => {
            if(input.value.length === 1 && i < inputs.length - 1){
                inputs[i+1].focus();
            }
        });
        input.addEventListener('keydown', (e) => {
            if(e.key === "Backspace" && input.value === "" && i > 0){
                inputs[i-1].focus();
            }
        });
    });
});

// Resend OTP placeholder function
function resendOtp() {
    alert("A new OTP has been sent to your email!"); // Replace with real AJAX call
}
</script>
@endsection
