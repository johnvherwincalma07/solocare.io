<header id="main-header">
    <div class="w-100 text-center notice-bar py-1 bg-white">
        Notice: All information provided will be treated with strict confidentiality
        in accordance with Republic Act 10173 (Data Privacy Act).
    </div>
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(90deg, #003366, #004c99);">
        <div class="d-flex align-items-center justify-content-between w-100 py-2 px-3 px-lg-5">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="{{ asset('images/SC.svg') }}" alt="Logo" class="logo-img me-2"
                style="height:60px;">
                <div class="d-flex flex-column lh-1">
                    <span class="fw-bold system-name">
                        {{ $system->system_brand_name }}
                    </span>
                    <small style="color: #ffc107;">
                        City of General Trias, Cavite
                    </small>
                </div>
            </a>
            <!-- Desktop Links -->
            <ul class="navbar-nav d-none d-lg-flex flex-row gap-3">
                <li class="nav-item hover-link" data-section="home"><i class="fas fa-home"></i> Home </li>
                <li class="nav-item hover-link" data-section="about"><i class="fas fa-info-circle"></i> About </li>
                <li class="nav-item hover-link" data-section="articles"><i class="fas fa-newspaper"></i> Articles </li>
                <li class="nav-item hover-link" data-section="gallery"><i class="fas fa-images"></i> Gallery </li>
                <li class="nav-item hover-link" data-section="process"><i class="fas fa-tasks"></i> Process </li>
                <li class="nav-item hover-link" data-section="track"><i class="fas fa-map-marker-alt"></i> Track </li>
                <li class="nav-item hover-link" data-section="faq"><i class="fas fa-question-circle"></i> FAQs </li>
                <li class="nav-item hover-link" data-section="contact"><i class="fas fa-address-book"></i> Contact </li>
            </ul>
            <!-- Login/Logout -->
            <div class="d-none d-lg-block">
                @auth
                <form id="logoutForm" action="{{ route('logout') }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="button" id="logoutBtn" class="btn btn-outline-light fw-semibold px-4">
                        Logout
                    </button>
                </form>
                @else
                <button class="btn btn-outline-light fw-semibold px-4" data-bs-toggle="modal"
                data-bs-target="#loginModal">
                    Login
                </button>
                @endauth
            </div>
            <!-- Mobile Toggler -->
            <button class="navbar-toggler border-0 d-lg-none" type="button" id="sidebarToggle">
                <span class="navbar-toggler-icon">
                </span>
            </button>
        </div>
    </nav>
</header>

<!-- MOBILE SIDEBAR -->
<div id="mobileSidebar" class="mobile-sidebar">
    <div class="sidebar-header d-flex justify-content-between align-items-center p-3 border-bottom border-light">
        <h5 class="text-white mb-0 fw-bold">
            Menu
        </h5>
        <button id="closeSidebar" class="btn-close btn-close-white">
        </button>
    </div>
    <ul class="navbar-nav mt-3 px-3">
        <li class="nav-item mb-2 hover-link" data-section="home"> Home </li>
        <li class="nav-item mb-2 hover-link" data-section="about"> About </li>
        <li class="nav-item mb-2 hover-link" data-section="articles"> Articles </li>
        <li class="nav-item mb-2 hover-link" data-section="gallery"> Gallery </li>
        <li class="nav-item mb-2 hover-link" data-section="process"> Process </li>
        <li class="nav-item mb-2 hover-link" data-section="track"> Track </li>
        <li class="nav-item mb-2 hover-link" data-section="faq"> FAQs </li>
        <li class="nav-item mb-3 hover-link" data-section="contact"> Contact </li>
    </ul>
    <div class="px-3 mb-4">
        <button class="btn btn-light w-100 fw-bold py-2" data-bs-toggle="modal" data-bs-target="#loginModal"> Login </button>
    </div>
</div>
<div id="sidebarOverlay" class="sidebar-overlay">
</div>
<!-- LOGIN MODAL -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel"
aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content modern-login-modal">
            <!-- Header -->
            <div class="modal-header modern-header">
                <div class="icon-badge"><i class="fas fa-user-lock"></i></div>
                <h5 class="modal-title fw-bold text-white" id="loginModalLabel"> Welcome Back </h5>
                <p class="text-white-50 small mb-0"> Login to your SoloCare account </p>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <!-- Body -->
            <div class="modal-body px-4 py-4">
                <form method="POST" action="{{ route('login') }}" id="loginForm">
                    @csrf
                    <!-- Username -->
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="username" name="username" placeholder="Username or Email" required>
                        <label for="username"> Username or Email </label>
                    </div>
                    <!-- Password -->
                    <div class="form-floating mb-2 password-wrapper">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                        <label for="password"> Password </label>
                        <i id="togglePassword" class="fas fa-eye password-toggle"></i>
                    </div>
                    <!-- Forgot -->
                    <div class="text-end small mb-3">
                        <a href="{{ route('password.request') }}" class="text-primary fw-semibold"> Forgot password?</a>
                    </div>
                    <!-- Login Button -->
                    <div class="d-grid mb-3">
                        <button type="submit" class="btn modern-login-btn fw-bold"> Login </button>
                    </div>
                    <!-- Register -->
                    <p class="text-center small mb-0">
                        Don’t have an account?
                        <a href="#" class="fw-semibold text-primary" data-bs-toggle="modal" data-bs-target="#registerModal" data-bs-dismiss="modal"> Register here </a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="loginErrorModal" tabindex="-1" aria-labelledby="loginErrorLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-danger shadow-lg">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold" id="loginErrorLabel">
                    Login Failed
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                aria-label="Close">
                </button>
            </div>
            <div class="modal-body text-center">
                Invalid username or password.
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* =======================
       SIDEBAR (MOBILE)
    ======================= */
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('mobileSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const closeSidebar = document.getElementById('closeSidebar');

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.add('active');
            overlay.classList.add('active');
        });
    }

    if (closeSidebar) {
        closeSidebar.addEventListener('click', () => {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        });
    }

    if (overlay) {
        overlay.addEventListener('click', () => {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        });
    }

    /* =======================
       LOGIN ERROR MODAL
    ======================= */
    const loginError = "{{ session('login_error') ?? '' }}";
    if (loginError) {
        const errorModalEl = document.getElementById('loginErrorModal');
        if (errorModalEl) {
            const errorModal = new bootstrap.Modal(errorModalEl);
            errorModal.show();
        }
    }

    /* =======================
       LOGIN LOADING OVERLAY
    ======================= */
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function () {
            const loginModalEl = document.getElementById('loginModal');
            if (loginModalEl) {
                const modal =
                    bootstrap.Modal.getInstance(loginModalEl) ||
                    new bootstrap.Modal(loginModalEl);
                modal.hide();
            }

            const loadingOverlay = document.getElementById('loadingOverlay');
            if (loadingOverlay) {
                loadingOverlay.style.display = 'flex';
            }
        });
    }

    /* =======================
       LOGOUT LOADING OVERLAY
    ======================= */
    const logoutBtn = document.getElementById('logoutBtn');
    const logoutForm = document.getElementById('logoutForm');

    if (logoutBtn && logoutForm) {
        logoutBtn.addEventListener('click', function () {
            const loadingOverlay = document.getElementById('loadingOverlay');
            if (loadingOverlay) {
                loadingOverlay.style.display = 'flex';
            }
            logoutForm.submit();
        });
    }

    window.addEventListener('load', function () {
        setTimeout(() => {
            const loadingOverlay = document.getElementById('loadingOverlay');
            if (loadingOverlay) {
                loadingOverlay.style.display = 'none';
            }
        }, 700);
    });

    /* =======================
       SECTION SWITCHING
    ======================= */
    const sections = document.querySelectorAll('.data-section');
    const navItems = document.querySelectorAll('.hover-link[data-section]');

    function showSection(sectionId) {
        sections.forEach(section => {
            if (section.id === sectionId) {
                section.style.display = 'block';
                section.classList.add('show');
            } else {
                section.classList.remove('show');
                section.style.display = 'none';
            }
        });

        navItems.forEach(item => {
            item.classList.toggle(
                'active',
                item.dataset.section === sectionId
            );
        });

        if (sidebar.classList.contains('active')) {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        }
    }

    navItems.forEach(item => {
        item.addEventListener('click', () => {
            showSection(item.dataset.section);
        });
    });

    // Default section
    if (document.getElementById('home')) {
        showSection('home');
    }

    /* =======================
       REQUIREMENTS ACCORDION
    ======================= */
    document.querySelectorAll('.req-toggle').forEach(btn => {
        btn.addEventListener('click', () => {
            btn.classList.toggle('active');
            if (btn.nextElementSibling) {
                btn.nextElementSibling.classList.toggle('show');
            }
        });
    });

});

/* =======================
   PASSWORD TOGGLE
======================= */
document.addEventListener('DOMContentLoaded', function () {
    const password = document.getElementById('password');
    const togglePassword = document.getElementById('togglePassword');

    if (togglePassword && password) {
        togglePassword.addEventListener('click', () => {
            const type =
                password.getAttribute('type') === 'password'
                    ? 'text'
                    : 'password';
            password.setAttribute('type', type);

            togglePassword.classList.toggle('fa-eye');
            togglePassword.classList.toggle('fa-eye-slash');
        });
    }
});
</script>
