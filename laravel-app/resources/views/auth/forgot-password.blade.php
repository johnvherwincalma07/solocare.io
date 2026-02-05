@extends('layouts.page')

@section('content')
<!-- ================= FORGOT PASSWORD MODAL ================= -->
<div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">

            <!-- LEFT + RIGHT PANEL BODY -->
            <div class="row g-0">

                <!-- LEFT PANEL: Gradient + Image -->
                <div class="col-md-5 d-none d-md-flex flex-column justify-content-center align-items-center text-center"
                    style="background: linear-gradient(135deg, #003366, #0055a5); color:#fff; padding: 2rem;">
                    <h3 class="fw-bold mb-3">🔒 Forgot Password</h3>
                    <p class="text-white-50 mb-3">
                        Enter your registered email/text to receive an OTP and reset your password securely.
                    </p>
                    <img src="{{ asset('images/forgot3.jpg') }}" alt="Forgot Password" class="img-fluid" style="max-width: 180px;">
                </div>

                <!-- RIGHT PANEL: Form -->
                <div class="col-md-7 p-4">
                    <div id="formContainer" @if(session('otp_sent')) style="display:none;" @endif>
                        <form id="forgotForm" method="POST" action="{{ route('password.email') }}">
                            @csrf
                            <p class="text-center mb-4 fw-semibold text-secondary">
                                Enter your email/text to receive OTP verification.
                            </p>

                            <div class="mb-4">
                                <label for="email" class="form-label fw-semibold">Email Address/Contact Number</label>
                                <input type="email" id="email" name="email" class="form-control form-control-lg rounded-3" placeholder="Enter your registered email/text" required>
                                <div id="emailFeedback" class="form-text text-danger mt-1" style="display:none;">
                                    Please enter a valid email address/text.
                                </div>
                            </div>

                            @if ($errors->has('email'))
                                <div class="alert alert-danger rounded-pill mb-3">
                                    {{ $errors->first('email') }}
                                </div>
                            @endif

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-lg fw-bold" style="background: linear-gradient(135deg, #003366, #0055a5);">
                                    Send OTP
                                </button>
                            </div>

                            <div class="d-grid text-center">
                                <a href="{{ url('/') }}" class="btn btn-secondary fw-bold btn-lg">
                                    Back to Home
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- ================= SUCCESS MODAL ================= -->
<div class="modal fade" id="otpSuccessModal" tabindex="-1" aria-labelledby="otpSuccessModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg p-4" style="border-radius: 20px;">
            <div class="modal-header bg-success text-white" style="border-radius: 20px 20px 0 0;">
                <h5 class="modal-title fw-bold" id="otpSuccessModalLabel">
                    <i class="fas fa-check-circle me-2"></i>OTP Sent Successfully!
                </h5>
            </div>
            <div class="modal-body text-center">
                <p class="fw-semibold mb-3">📩 An OTP has been sent to your email.</p>
                <p class="text-secondary">Redirecting in <span id="countdown">3</span> seconds...</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button class="btn btn-success fw-bold" id="manualRedirectBtn">Go Now</button>
            </div>
        </div>
    </div>
</div>

<style>
/* Form styling */
#forgotPasswordModal .form-control {
    border-radius: 12px;
    padding: 0.75rem 1rem;
    border: 1px solid #ccc;
    transition: all 0.3s ease;
}
#forgotPasswordModal .form-control:focus {
    border-color: #004c99;
    box-shadow: 0 0 0 0.2rem rgba(0, 76, 153, 0.25);
}

/* Scrollbar */
#forgotPasswordModal .modal-body {
    overflow-y: auto;
}
#forgotPasswordModal .modal-body::-webkit-scrollbar { width: 8px; }
#forgotPasswordModal .modal-body::-webkit-scrollbar-track { background: #f0f0f0; border-radius:4px; }
#forgotPasswordModal .modal-body::-webkit-scrollbar-thumb { background:#b0b0b0; border-radius:4px; border:2px solid #f0f0f0; }
#forgotPasswordModal .modal-body { scrollbar-width: thin; scrollbar-color: #b0b0b0 #f0f0f0; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const emailInput = document.getElementById('email');
    const emailFeedback = document.getElementById('emailFeedback');
    const submitBtn = document.querySelector('#forgotForm button[type="submit"]');

    // Disable submit by default
    submitBtn.disabled = true;

    // Real-time email validation
    emailInput.addEventListener('input', function () {
        const emailValue = emailInput.value.trim();
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if(emailPattern.test(emailValue)){
            emailInput.style.borderColor = 'green';
            emailFeedback.style.display = 'none';
            submitBtn.disabled = false;
        } else {
            emailInput.style.borderColor = 'red';
            emailFeedback.style.display = 'block';
            submitBtn.disabled = true;
        }

        if(emailValue === ''){
            emailInput.style.borderColor = '#ccc';
            emailFeedback.style.display = 'none';
            submitBtn.disabled = true;
        }
    });

    @if(!session('otp_sent'))
        // Show forgot password modal
        var forgotModalEl = document.getElementById('forgotPasswordModal');
        var forgotModal = new bootstrap.Modal(forgotModalEl, { backdrop: 'static', keyboard: false });
        forgotModal.show();
    @else
        // Show success modal
        var successModalEl = document.getElementById('otpSuccessModal');
        var successModal = new bootstrap.Modal(successModalEl, { backdrop: 'static', keyboard: false });
        successModal.show();

        let countdownEl = document.getElementById('countdown');
        let countdown = 3;
        let redirectUrl = "{{ route('otp.verify') }}";

        const interval = setInterval(() => {
            countdown--;
            countdownEl.textContent = countdown;
            if(countdown <= 0){
                clearInterval(interval);
                window.location.href = redirectUrl;
            }
        }, 1000);

        document.getElementById("manualRedirectBtn").addEventListener("click", function() {
            window.location.href = redirectUrl;
        });
    @endif
});
</script>
@endsection
