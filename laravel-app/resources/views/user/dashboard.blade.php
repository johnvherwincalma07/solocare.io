@extends('layouts.user')

@section('title', 'User Dashboard')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/user.css') }}">

@section('content')

<header class="admin-topbar">
    <div class="topbar-left">
        <button class="toggle-btn" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
        <img src="{{ asset('images/SC.svg') }}" alt="Logo" class="topbar-logo">

        <div class="topbar-text">
            <h2 class="topbar-title fw-bold m-0">{{ $system->system_brand_name }}</h2>
            <p class="topbar-subtitle" style="color: #ffc107;"> Solo Parent - User Account</p>
        </div>
    </div>

    <div class="topbar-right">
        <!-- DARK MODE SLIDER -->
        <label class="theme-switch" title="Light / Dark Mode">
            <input type="checkbox" id="darkModeToggle">
            <div class="theme-slider">
                <i class="fas fa-sun theme-icon sun"></i>
                <i class="fas fa-moon theme-icon moon"></i>
            </div>
        </label>

        @php
            $notifCount = isset($allNotifications) ? count($allNotifications) : 0;
        @endphp

        <!-- Notification Button -->
        <div class="notif-dropdown ms-auto position-relative">
            <button id="notifBtn" class="notif-icon"><i class="fas fa-bell"></i>
                @if($notifCount > 0)
                    <span class="notif-count">{{ $notifCount }}</span>
                @endif
            </button>

            <!-- Dropdown Menu -->
            <div id="notifMenu" class="dropdown-menu dropdown-menu-end notif-menu shadow">
                <div class="notif-header d-flex justify-content-between align-items-center">
                    <strong>Notifications</strong>
                    <small class="text-muted">
                        {{ $notifCount }} {{ Str::plural('new', $notifCount) }}
                    </small>
                </div>

                <div class="notif-scroll">
                    @forelse($allNotifications as $notif)
                        <div class="notif-card {{ $notif['is_read'] ?? false ? '' : 'unread' }}">
                            <div class="notif-icon"><i class="fas fa-bell"></i></div>
            
                            <div class="notif-content">
                                <div class="notif-message">{{ $notif['message'] }}</div>
            
                                @if(isset($notif['date']))
                                    <div class="notif-time">{{ \Carbon\Carbon::parse($notif['date'])->format('M d, Y h:i A') }}</div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="notif-empty">
                            No notifications yet
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</header>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const toggle = document.getElementById("darkModeToggle");

    if (localStorage.getItem("theme") === "dark") {
        document.body.classList.add("dark-mode");
        toggle.checked = true;
    }

    toggle.addEventListener("change", () => {
        if (toggle.checked) {
            document.body.classList.add("dark-mode");
            localStorage.setItem("theme", "dark");
        } else {
            document.body.classList.remove("dark-mode");
            localStorage.setItem("theme", "light");
        }
    });
});
</script>

<script>
    document.getElementById('notifBtn').addEventListener('click', function(e) {
        e.stopPropagation();
        const menu = document.getElementById('notifMenu');
        menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function() {
        const menu = document.getElementById('notifMenu');
        if (menu) menu.style.display = 'none';
    });
</script>

<div class="user-dashboard">

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <div class="logo profile-avatar text-center">
        @if(Auth::user()->avatar_url)
            <img src="{{ Auth::user()->avatar_url }}" 
                 alt="Profile Photo" 
                 class="user-profile-avatar-img mx-auto">
        @else
            <div class="user-profile-avatar-initials mx-auto">
                {{ strtoupper(substr(Auth::user()->first_name, 0, 1)) }}
                {{ strtoupper(substr(Auth::user()->last_name, 0, 1)) }}
            </div>
        @endif
    
        <h5 class="mt-3 mb-1">{{ Auth::user()->last_name }}, {{ Auth::user()->first_name }}</h5>
        <span class="badge bg-primary px-3 py-1" style="font-size:.85rem; border-radius:12px;">Barangay {{ Auth::user()->barangay }}</span>
    </div>
    <hr>
    <ul class="menu">
        <li title="Home" onclick="showSection('home')"><i class="fas fa-home me-3"></i><span>Home</span></li>
        <li title="Profile" onclick="showSection('profile')"><i class="fas fa-user me-3"></i><span>Profile</span></li>
        <li title="Application" onclick="showSection('application')"><i class="fas fa-file-alt me-3"></i><span>Application</span></li>
        <li title="Announcement" onclick="showSection('announcement')"><i class="fas fa-bullhorn me-3"></i><span>Announcement</span></li>
        <li title="My Benefits" onclick="showSection('benefits')"><i class="fas fa-gift me-3"></i><span>My Benefits</span></li>
        <li title="Chat" onclick="showSection('chat')"><i class="fas fa-comments me-3"></i><span>Chat</span></li>
        <li title="Security" onclick="showSection('security')"><i class="fas fa-shield-alt me-3"></i><span>Security</span></li>
        <li id="logoutBtn" title="Logout"><i class="fas fa-sign-out-alt me-3"></i><span>Logout</span></li>
    </ul>
</aside>


<!-- Main Content -->
<main class="main-content">

    <!-- HOME SECTION -->
    <section id="home" class="content-section" style="display:block;">
            <div class="home-container">

            <!-- Welcome Card -->
            <div class="welcome-card mb-4">
                <h2>Welcome, {{ Auth::user()->username }}</h2>
                <p>Solo Parent User Account</p>
            </div>

            <!-- Quick Stats -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card stat-card bg-primary text-white p-4">
                        <h5 class="fw-bold">Application Status</h5>
                        <p class="fs-5">
                            @if($hasSubmitted)
                                <span class="badge bg-light text-primary">Submitted</span>
                            @else
                                <span class="badge bg-light text-primary">Not Submitted</span>
                            @endif
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card bg-success text-white p-4">
                        <h5 class="fw-bold">Notifications</h5>
                        <p class="fs-5">
                            <span class="badge bg-light text-success">{{ count($allNotifications) ?? 0 }}</span> new
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card bg-warning text-dark p-4">
                        <h5 class="fw-bold">Upcoming Schedule</h5>
                        <p class="fs-6">
                            @php
                                $nextSchedule = collect($scheduledNotifications)
                                    ->filter(fn($n) => isset($n['date']))
                                    ->sortBy('date')
                                    ->first();
                            @endphp
    
                            @if($nextSchedule)
                                {{ \Carbon\Carbon::parse($nextSchedule['date'])->format('M d, Y') }}
                                <br>{{ \Carbon\Carbon::parse($nextSchedule['date'])->format('h:i A') }}
                            @else
                                No upcoming schedule
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card quick-actions-card p-4 mb-4">
                <h4 class="fw-bold mb-3">Quick Actions</h4>
                <div class="d-flex flex-wrap gap-3">
                    <button class="btn btn-gradient" onclick="showSection('profile')">
                        <i class="fas fa-user me-2"></i> View Profile
                    </button>
                    <button class="btn btn-gradient" onclick="showSection('application')">
                        <i class="fas fa-file-alt me-2"></i> Submit Application
                    </button>
                    <button class="btn btn-gradient" onclick="showSection('chat')">
                        <i class="fas fa-comments me-2"></i> Chat with Admin
                    </button>
                    <button class="btn btn-gradient" onclick="showSection('benefits')">
                        <i class="fas fa-gift me-2"></i> My Benefits
                    </button>
                </div>
            </div>
        
            <!-- Notifications -->
            <div class="card notifications-card p-4">
                <h4 class="fw-bold mb-3">Recent Notifications</h4>
                <ul class="list-group list-group-flush">
                    @forelse($allNotifications as $notif)
                        <li class="list-group-item">
                            <i class="fas fa-bell text-primary me-2"></i>
                            {{ $notif['message'] }}
                            @if(isset($notif['date']))
                                <span class="text-muted small d-block">
                                    {{ \Carbon\Carbon::parse($notif['date'])->format('M d, Y H:i') }}
                                </span>
                            @endif
                        </li>
                    @empty
                        <li class="list-group-item text-muted">No notifications yet</li>
                    @endforelse
                </ul>
            </div>
    
        </div>
    </section>

    <!-- PROFILE SECTION -->
    <section id="profile" class="content-section" style="display:none;">
    <div class="card profile-card">
        <div class="profile-header d-flex flex-wrap align-items-center gap-3">

            <div class="user-profile-avatar">
                @if($user->avatar_url)
                    <img src="{{ $user->avatar_url }}" 
                         alt="Profile Photo" 
                         class="user-profile-avatar-img">
                @else
                    <div class="user-profile-avatar-initials">
                        {{ strtoupper(substr($user->first_name, 0, 1)) }}
                        {{ strtoupper(substr($user->last_name, 0, 1)) }}
                    </div>
                @endif
            
                <!-- Change photo form -->
                <form action="{{ route('user.profile.avatar.update') }}"
                      method="POST"
                      enctype="multipart/form-data"
                      class="avatar-upload-form">
                    @csrf
                    <label class="avatar-upload-btn">
                        <i class="fas fa-camera"></i> Change
                        <input type="file"
                               name="avatar"
                               accept="image/*"
                               hidden
                               onchange="this.form.submit()">
                    </label>
                </form>
            </div>



            <!-- BASIC INFO -->
            <div class="profile-basic">
                <h3 class="section-title fw-bold mb-1">
                    {{ $user->first_name }} {{ $user->last_name }}
                </h3>
                <p class="note">Verified personal details (read-only)</p>
            </div>

        </div>

        <!-- INFO -->
        <div class="info-grid">
            <div>
                <strong>Full Name</strong>
                <p>
                    {{ $user->first_name }}
                    @if($user->middle_name) {{ $user->middle_name }} @endif
                    {{ $user->last_name }}
                </p>
            </div>

            <div>
                <strong>Email</strong>
                <p>{{ $user->email }}</p>
            </div>

            <div>
                <strong>Address</strong>
                <p>{{ $user->full_address }}</p>
            </div>

            <div>
                <strong>Phone Number</strong>
                <p>{{ $user->contact }}</p>
            </div>

            <div>
                <strong>Account Status</strong>
                <span class="status {{ strtolower($user->status ?? 'unverified') }}">{{ strtoupper($user->status ?? 'UNVERIFIED') }} </span>
            </div>
        </div>

        <div class="update-note">
            To update your personal information, please contact your assigned case worker or visit the office.
        </div>

    </div>
</section>

    <script>
        const sidebar = document.querySelector('.sidebar');
    const toggleBtn = document.querySelector('.toggle-btn');
    
    toggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('visible');
    });
    
    </script>

    <!-- APPLICATION SECTION-->
    <section id="application" class="content-section" style="display: none ;">
        <div id="submittedApplication" style="display: {{ $hasSubmitted ? 'block' : 'none' }};">
            <div class="card shadow-lg rounded-4 p-4 p-md-5 mx-auto" style="background: #f8f9fa;">
                <div class="row align-items-start">
                    <-- Left Section: Message -->
                    <div class="col-md-5 mb-4 mb-md-0 text-center text-md-start">
                        <h4 class="fw-bold text-success mb-4">
                            <img src="{{ asset('images/success.gif') }}" alt="Success" style="width: 100px; height: auto;">
                            Application Submitted!
                        </h4>
                        <p class="text-muted fs-5 mb-3">
                            You have successfully submitted your Solo Parent application. Only <strong>one application</strong> is allowed per applicant.
                        </p>
                        
                        <p class="text-muted fs-6 mb-3">
                            Please wait for an <strong>official notification</strong> from the Barangay Office
                            regarding the verification and approval of your application.
                        </p>
                        
                        {{-- Reference Number --}}
                        <p class="fw-bold fs-5">
                            Reference No:
                            <span class="badge bg-primary fs-6">{{ $application->reference_no ?? 'N/A' }}</span>
                        </p>
                        
                                 {{-- BARANGAY PHYSICAL SUBMISSION INSTRUCTION --}}
                        <div class="alert alert-warning mt-4 rounded-3">
                            <strong>IMPORTANT REMINDER</strong><br><br>
                            This online application serves as a <strong>PRE-REGISTRATION ONLY</strong>.
                            <br><br>
                            You are required to personally submit the
                            <strong>ORIGINAL and PHYSICAL COPIES</strong> of all required documents to the
                            <strong>Barangay Hall of {{ $application->barangay ?? Auth::user()->barangay }}</strong>.
                            <br><br>
                            Failure to submit physical documents may result in
                            <strong>non-processing or rejection</strong> of the application.
                        </div>
                        
                    </div>

                    {{-- Right Section: Application Details + Timeline --}}
                    <div class="col-md-7">
                        @if($application)
                            <div class="card shadow-sm rounded p-4 mb-4">
                                <h5 class="mb-3">Your Latest Application</h5>

                                <p><strong>Reference No:</strong> {{ $application->reference_no ?? $application->id }}</p>
                                <p><strong>Status:</strong> {{ $application->status }}</p>
                                <p><strong>Stage:</strong> {{ $currentStage }}</p>

                                {{-- Download Application Button --}}
                                <p class="fw-bold fs-5">
                                    PDF Application Form:
                                    <a href="{{ route('applications.download', $application->application_id ?? $application->id) }}"
                                    class="btn btn-primary btn-md fw-bold py-3 px-4 mb-3 w-100 d-flex align-items-center justify-content-center"
                                    target="_blank"
                                    rel="noopener">
                                        <i class="fas fa-file-pdf me-2"></i> Download Application
                                    </a>
                                </p>

                                <a href="{{ route('solo-parent.view', $application->application_id ?? $application->id) }}" class="btn btn-info mb-4" target="_blank">
                                    Preview PDF
                                </a>

                            </div>
                        @else
                            <p>You have not submitted any application yet.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Application Form -->
        <div id="applicationForm" style="display: {{ $hasSubmitted ? 'none' : 'block' }};">
            <div class="card p-4">
                <h2 class="section-title fw-bold mb-3 mt-2">Solo Parent Application Form</h2>
                <p class="text-muted">
                    Pursuant to <strong>Republic Act No. 11861 (Expanded Solo Parents Welfare Act)</strong>
                    and the <strong>Department of Social Welfare and Development (DSWD)</strong> guidelines.
                    Please fill out this form completely and accurately.
                </p>

                
                <div class="alert alert-info rounded-3 mb-4">
                <strong>NOTICE:</strong><br>
                After submitting this form, please wait for official barangay instructions.
                Online submission does <strong>NOT</strong> guarantee approval.
                </div>
                
                <form id="soloParentForm" action="{{ route('applications.store') }}" method="POST" enctype="multipart/form-data" class="mx-auto p-2">
                    @csrf

                    <!-- I. Personal Information -->
                    <div class="form-section">
                    <h4>I. Personal Information</h4>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Last Name:</label>
                            <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $application->last_name ?? '') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">First Name:</label>
                            <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $application->first_name ?? '') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Middle Name:</label>
                            <input type="text" name="middle_name" class="form-control" value="{{ old('middle_name', $application->middle_name ?? '') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Extension:</label>
                            <input type="text" name="name_extension" class="form-control" placeholder="Jr, Sr, III (optional)" value="{{ old('name_extension', $application->name_extension ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Place of Birth:</label>
                            <input type="text" name="place_of_birth" class="form-control" value="{{ old('place_of_birth', $application->place_of_birth ?? '') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date of Birth:</label>
                            <input type="date" name="birth_date" class="form-control" value="{{ old('birth_date', $application->birth_date ?? '') }}" id="dobInput" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Age:</label>
                            <input type="number" name="age" class="form-control" value="{{ old('age', $application->age ?? '') }}" min="18" id="ageInput" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sex:</label>
                            <select name="sex" class="form-select" required>
                                <option value="">Select</option>
                                <option value="male" {{ old('sex', $application->sex ?? '')=='male'?'selected':'' }}>Male</option>
                                <option value="female" {{ old('sex', $application->sex ?? '')=='female'?'selected':'' }}>Female</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Street / House No.</label>
                            <input type="text" name="street" class="form-control" value="{{ old('street', $application->street ?? '') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Barangay</label>
                            <input type="text" name="barangay" class="form-control" value="{{ old('barangay', $application->barangay ?? '') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Municipality</label>
                            <input type="text" name="municipality" class="form-control" value="{{ old('municipality', $application->municipality ?? '') }}"required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Province</label>
                            <input type="text" name="province" class="form-control" value="{{ old('province', $application->province ?? '') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Educational Attainment</label>
                            <input type="text" name="educational_attainment" class="form-control" value="{{ old('educational_attainment', $application->educational_attainment ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Civil Status</label>
                            <select name="civil_status" class="form-select" required>
                                <option value="">Select</option>
                                @foreach(['Single','Married','Widowed','Separated','Annulled'] as $cs)
                                    <option value="{{ $cs }}"
                                        {{ old('civil_status', $application->civil_status ?? '')==$cs?'selected':'' }}>
                                        {{ $cs }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Occupation</label>
                            <input type="text" name="occupation" class="form-control" value="{{ old('occupation', $application->occupation ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Religion</label>
                            <input type="text" name="religion" class="form-control" value="{{ old('religion', $application->religion ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Company / Agency Name</label>
                            <input type="text" name="company_agency" class="form-control" value="{{ old('company_agency', $application->company_agency ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Monthly Income (₱)</label>
                            <input type="number" name="monthly_income" class="form-control" value="{{ old('monthly_income', $application->monthly_income ?? '') }}" min="0">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Employment Status</label>
                            <div class="radio-group-container" data-name="employment_status">
                            @foreach(['Employed','Self-Employed','Not Employed'] as $status)
                                <div class="form-check">
                                    <input class="form-check-input" type="radio"
                                        name="employment_status"
                                        value="{{ $status }}"
                                        {{ old('employment_status', $application->employment_status ?? '')==$status?'checked':'' }}>
                                    <label class="form-check-label">{{ $status }}</label>
                                </div>
                            @endforeach
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contact Number</label>
                            <input type="text" name="contact_number" class="form-control" placeholder="09XXXXXXXXX" pattern="09[0-9]{9}" value="{{ old('contact_number', $application->contact_number ?? '') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $application->email ?? auth()->user()->email) }}">
                        </div>

                        @foreach(['pantawid'=>'Pantawid Beneficiary', 'indigenous_person'=>'Indigenous Person', 'lgbtq'=>'LGBTQ+', 'pwd'=>'Person with Disability (PWD)'] as $field=>$label)
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">{{ $label }}</label>
                            <div class="radio-group-container" data-name="{{ $field }}">
                                @foreach(['Yes','No'] as $opt)
                                <div class="form-check">
                                    <input class="form-check-input" type="radio"
                                        name="{{ $field }}"
                                        value="{{ $opt }}"
                                        {{ old($field, $application->$field ?? '')==$opt?'checked':'' }}>
                                    <label class="form-check-label">{{ $opt }}</label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach

                    </div>
                    </div>

                    <!-- II. Family Composition -->
                    <div class="form-section">
                    <div class="d-flex justify-content-between align-items-center mt-5 mb-3">
                        <h4>II. Family Composition</h4>
                        <div>
                            <button type="button" id="addRowBtn" class="btn btn-primary btn-md me-2">Add Row</button>
                            <button type="button" id="deleteRowBtn" class="btn btn-danger btn-md">Delete Row Selected</button>
                        </div>
                    </div>
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered align-middle" id="familyTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Select</th>
                                    <th>Name</th>
                                    <th>Relationship</th>
                                    <th>Age</th>
                                    <th>Date of Birth</th>
                                    <th>Civil Status</th>
                                    <th>Occupation</th>
                                    <th>Monthly Income (₱)</th>
                                    <th>Educational Attainment</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $family = old('family')
                                        ? collect(old('family'))
                                        : collect(json_decode($application->family ?? '[]', true));
                                @endphp

                                @if($family->count())
                                    @foreach($family as $member)
                                    <tr>
                                        <td><input type="checkbox" class="row-select form-check-input"></td>
                                        <td><input type="text" name="family_name[]" class="form-control" value="{{ $member['name'] ?? '' }}"></td>
                                        <td><input type="text" name="family_relationship[]" class="form-control" value="{{ $member['relationship'] ?? '' }}"></td>
                                        <td><input type="number" name="family_age[]" class="form-control" min="0" value="{{ $member['age'] ?? '' }}"></td>
                                        <td><input type="date" name="family_dob[]" class="form-control" value="{{ $member['birth_date'] ?? '' }}"></td>
                                        <td>
                                            <select name="family_civil_status[]" class="form-select">
                                                @foreach(['Single','Married','Widowed','Separated','Annulled'] as $cs)
                                                    <option value="{{ $cs }}"
                                                        {{ ($member['civil_status'] ?? '')==$cs?'selected':'' }}>
                                                        {{ $cs }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="text" name="family_occupation[]" class="form-control" value="{{ $member['occupation'] ?? '' }}"></td>
                                        <td><input type="number" name="family_income[]" class="form-control" min="0" value="{{ $member['monthly_income'] ?? '' }}"></td>
                                        <td><input type="text" name="family_education[]" class="form-control" value="{{ $member['educational_attainment'] ?? '' }}"></td>
                                    </tr>
                                    @endforeach
                                @else
                                {{-- Default empty row --}}
                                <tr>
                                    <td><input type="checkbox" class="row-select form-check-input"></td>
                                    <td><input type="text" name="family_name[]" class="form-control"></td>
                                    <td><input type="text" name="family_relationship[]" class="form-control"></td>
                                    <td><input type="number" name="family_age[]" class="form-control" min="0"></td>
                                    <td><input type="date" name="family_dob[]" class="form-control"></td>
                                    <td>
                                        <select name="family_civil_status[]" class="form-select">
                                            <option>Single</option>
                                            <option>Married</option>
                                            <option>Widowed</option>
                                            <option>Separated</option>
                                            <option>Annulled</option>
                                        </select>
                                    </td>
                                    <td><input type="text" name="family_occupation[]" class="form-control"></td>
                                    <td><input type="number" name="family_income[]" class="form-control" min="0"></td>
                                    <td><input type="text" name="family_education[]" class="form-control"></td>
                                </tr>
                            @endif
                            </tbody>

                        </table>
                    </div>
                    </div>

                    <!-- III. Circumstances of Being a Solo Parent -->
                    <div class="form-section">
                    <h4>III. Circumstances of Being a Solo Parent</h4>
                    <p class="text-muted mb-3 fs-6">
                        Ibigay ang dahilan kung bakit ikaw ay nagiging solo parent. Halimbawa: "Namayapa ang aking asawa,"
                        "Iniwan ako ng aking asawa," "Ako ang legal guardian ng bata," o iba pa.
                    </p>
                    <textarea class="form-control mb-4" name="solo_parent_reason" rows="4" required>{{ old('solo_parent_reason', $application->solo_parent_reason ?? '') }}</textarea>
                    </div>
                    
                    <!-- IV. Needs / Problems -->
                    <div class="form-section">
                    <h4>IV. Needs / Problems</h4>
                    <p class="text-muted mb-3 fs-6">
                        Ilarawan ang iyong pangangailangan o problema bilang solo parent. Halimbawa:
                        "Kakulangan sa pinansyal na suporta," "Kailangan ng edukasyon para sa aking anak," "Kakulangan sa trabaho," o iba pa.
                    </p>
                    <textarea class="form-control mb-4" name="solo_parent_needs" rows="4" required>{{ old('solo_parent_needs', $application->solo_parent_needs ?? '') }}</textarea>
                    
                    <!-- V. Solo Parent Category -->
                    <h4 class="fw-bold text-primary mt-5 mb-3">
                        V. Solo Parent Category
                    </h4>
                    
                    <div class="alert alert-warning border-start border-4 border-warning rounded-3">
                        <strong>IMPORTANT NOTICE</strong><br><br>
                    
                        The <strong>Solo Parent Category</strong> will be
                        <strong>identified and officially validated by the Barangay Social Worker</strong>
                        based on your submitted information and
                        <strong>physical documents</strong>.
                        <br><br>
                    
                        Each Solo Parent Category has its own
                        <strong>specific required documents</strong>.
                        Only the documents applicable to your
                        <strong>validated category</strong> will be required.
                        <br><br>
                    
                        Applicants are <strong>NOT allowed to self-select</strong> their category to prevent
                        misclassification and ensure compliance with
                        <strong>Republic Act No. 11861</strong>.
                    </div>
                    </div>
                    
                    <input type="hidden" name="category" value="FOR VALIDATION">
                    
                    <!-- VI. Residency & Verification Documents -->
                    <div class="form-section">
                    <h4>
                        VI. Residency & Verification Documents
                    </h4>
                    
                    <div class="alert alert-warning border-start border-4 border-warning">
                        These documents are required to verify that the applicant is a
                        <strong>true resident</strong> of the declared barangay.
                    </div>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Upload Voter’s Certificate
                            </label>
                            <input type="file"
                                   name="voters_certificate"
                                   class="form-control"
                                   accept=".jpg,.png,.pdf"
                                   required>
                        </div>
                    
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Upload Barangay Certificate of Residency
                            </label>
                            <input type="file"
                                   name="barangay_certificate"
                                   class="form-control"
                                   accept=".jpg,.png,.pdf"
                                   required>
                        </div>
                    </div>
                    </div>

                    <!-- VII. Emergency Contact -->
                    <div class="form-section">
                    <h4>VII. Emergency Contact</h4>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" name="emergency_name" value="{{ old('emergency_name', $application->emergency_name ?? '') }}" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Relationship</label>
                            <input type="text" name="emergency_relationship" value="{{ old('emergency_relationship', $application->emergency_relationship ?? '') }}" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contact Number</label>
                            <input type="text" name="emergency_contact" value="{{ old('emergency_contact', $application->emergency_contact ?? '') }}" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Address</label>
                            <input type="text" name="emergency_address" value="{{ old('emergency_address', $application->emergency_address ?? '') }}" class="form-control" required>
                        </div>
                    </div>
                    </div>
        
                    <!-- VII. Declaration -->
                    <div class="alert alert-danger border-start border-4 border-danger mt-5">
                    <div class="checkbox-group-container mb-2" data-name="declaration">
                        <div id="declarationContainer" class="checkbox-container">
                            <input class="form-check-input" type="checkbox" name="declaration" id="declaration" required>
                            <label class="form-check-label">
                                I certify that all information provided is true and correct. I understand that
                                this application is for <strong>pre-registration only</strong> and that I am
                                required to personally submit all physical documents to the Barangay Hall.
                                Failure to comply may result in disqualification.
                            </label>
                        </div>
                    </div>
                    </div>

                    <button type="submit" id="submitBtn" class="btn btn-primary w-100">Submit Application</button>

                </form>
            </div>
        </div>
    </section>

    <script>
    document.addEventListener("DOMContentLoaded", function() {

        // Helpers
        function createErrorMessage(message) {
        const div = document.createElement('div');
        div.className = 'error-msg d-flex align-items-center mt-1';
        div.style.color = '#dc3545';
    
        const icon = document.createElement('span');
        icon.innerHTML = '&#9888;'; 
        icon.style.marginRight = '5px';
        div.appendChild(icon);
    
        const text = document.createElement('span');
        text.textContent = message;
        div.appendChild(text);
    
        return div;
    }

        // DOM references
        const soloParentForm = document.getElementById('soloParentForm');
        const submitBtn = document.getElementById('submitBtn');
        const confirmSubmitBtn = document.getElementById('confirmSubmitBtn');
        const submitConfirmModalEl = document.getElementById('submitConfirmModal');
        const submitConfirmModal = submitConfirmModalEl ? new bootstrap.Modal(submitConfirmModalEl) : null;
    
        const dobInput = document.getElementById('dobInput');
        const ageInput = document.getElementById('ageInput');
    
        const addRowBtn = document.getElementById('addRowBtn');
        const deleteRowBtn = document.getElementById('deleteRowBtn');
        const tableBody = document.querySelector('#familyTable tbody');


        // AGE CALCULATION
        function calculateAgeFromDateString(dateString) {
            if(!dateString) return '';
            const today = new Date();
            const birthDate = new Date(dateString);
            let age = today.getFullYear() - birthDate.getFullYear();
            const m = today.getMonth() - birthDate.getMonth();
            if(m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) age--;
            return age;
        }

        function guardFutureDateAndCompute(inputDateEl, outputAgeEl) {
            if(!inputDateEl) return;
            inputDateEl.addEventListener('change', function() {
                clearError(inputDateEl);
                if(!this.value) {
                    if(outputAgeEl) outputAgeEl.value = '';
                    return;
                }
                const picked = new Date(this.value);
                const today = new Date();
                picked.setHours(0,0,0,0);
                today.setHours(0,0,0,0);

                if(picked > today) {
                    showError(inputDateEl, 'Date of birth cannot be in the future.');
                    this.value = today.toISOString().slice(0,10);
                }

                const age = calculateAgeFromDateString(this.value);
                if(outputAgeEl) outputAgeEl.value = age >= 0 ? age : '';
            });
        }
        guardFutureDateAndCompute(dobInput, ageInput);

        // FAMILY ROW HANDLERS
        function attachFamilyRowListeners(row) {
            if(!row) return;
            const dob = row.querySelector('input[name="family_dob[]"]');
            const age = row.querySelector('input[name="family_age[]"]');

            if(dob) {
                dob.addEventListener('change', function() {
                    clearError(dob);
                    if(!this.value) { if(age) age.value = ''; return; }
                    const picked = new Date(this.value);
                    const today = new Date();
                    picked.setHours(0,0,0,0); today.setHours(0,0,0,0);
                    if(picked > today) {
                        showError(dob, 'Date of birth cannot be in the future.');
                        this.value = today.toISOString().slice(0,10);
                    }
                    const a = calculateAgeFromDateString(this.value);
                    if(age) age.value = a >= 0 ? a : '';
                });
            }

            row.querySelectorAll('input, select, textarea').forEach(el => {
                el.addEventListener('input', () => clearError(el));
                el.addEventListener('change', () => clearError(el));
            });
        }
        tableBody.querySelectorAll('tr').forEach(r => attachFamilyRowListeners(r));

        // Add / Delete Row
        if(addRowBtn) {
            addRowBtn.addEventListener('click', function() {
                const firstRow = tableBody.rows[0];
                if(!firstRow) return;
                const newRow = firstRow.cloneNode(true);

                newRow.querySelectorAll('input, textarea').forEach(input => {
                    if(input.type === 'checkbox') input.checked = false;
                    else input.value = '';
                    clearError(input);
                });
                newRow.querySelectorAll('select').forEach(select => { select.selectedIndex = 0; clearError(select); });

                tableBody.appendChild(newRow);
                attachFamilyRowListeners(newRow);
            });
        }

        if(deleteRowBtn) {
            deleteRowBtn.addEventListener('click', function() {
                const rows = Array.from(tableBody.querySelectorAll('tr'));
                let anyDeleted = false;
                rows.forEach(row => {
                    const checkbox = row.querySelector('.row-select');
                    if(checkbox && checkbox.checked) {
                        row.remove();
                        anyDeleted = true;
                    }
                });
                if(!anyDeleted && tableBody.rows.length > 1) tableBody.rows[tableBody.rows.length-1].remove();
                else if(!anyDeleted) {
                    const firstRow = tableBody.rows[0];
                    if(firstRow) {
                        firstRow.querySelectorAll('input').forEach(i => { if(i.type !== 'checkbox') i.value = ''; else i.checked = false; clearError(i); });
                        firstRow.querySelectorAll('select').forEach(s => { s.selectedIndex = 0; clearError(s); });
                    }
                }
            });
        }


        // FULL show Error/clear
        function showError(el, message) {
            if (!el) return;
        
            if (el.classList.contains('radio-group-container') || el.classList.contains('checkbox-container')) {
                el.classList.add('invalid');
                if (!el.nextElementSibling || !el.nextElementSibling.classList.contains('error-msg')) {
                    el.insertAdjacentElement('afterend', createErrorMessage(message));
                }
            } else {
                const input = (el instanceof HTMLInputElement || el instanceof HTMLSelectElement || el instanceof HTMLTextAreaElement)
                    ? el
                    : el.querySelector('input, select, textarea') || el;
        
                input.classList.add('is-invalid');
                if (input.nextElementSibling && input.nextElementSibling.classList.contains('error-msg')) {
                    input.nextElementSibling.remove();
                }
                input.insertAdjacentElement('afterend', createErrorMessage(message));
            }
        }

        function clearError(el) {
            if (!el) return;
        
            if (el.classList.contains('radio-group-container') || el.classList.contains('checkbox-container')) {
                el.classList.remove('invalid');
                if (el.nextElementSibling && el.nextElementSibling.classList.contains('error-msg')) {
                    el.nextElementSibling.remove();
                }
            } else {
                const input = (el instanceof HTMLInputElement || el instanceof HTMLSelectElement || el instanceof HTMLTextAreaElement)
                    ? el
                    : el.querySelector('input, select, textarea') || el;
        
                input.classList.remove('is-invalid');
                if (input.nextElementSibling && input.nextElementSibling.classList.contains('error-msg')) {
                    input.nextElementSibling.remove();
                }
            }
        }

        // FORM VALIDATION
        function validateForm() {
            let valid = true;
            // Clear all errors first
            soloParentForm.querySelectorAll('input, select, textarea').forEach(el => clearError(el));
        
            soloParentForm.querySelectorAll('[required]').forEach(el => {
                if ((el.type === 'checkbox' || el.type === 'radio')) return; // skip radio/checkbox here
                if ((el.value || '').trim() === '') {
                    showError(el, 'This field is required');
                    valid = false;
                }
                if(el.type === 'file' && el.files.length === 0){
                    showError(el, 'Please upload a file');
                    valid = false;
                }
            });
        
            // Radio groups
            document.querySelectorAll('.radio-group-container').forEach(container => {
                const name = container.dataset.name;
                const radios = container.querySelectorAll(`input[name="${name}"]`);
                if (!Array.from(radios).some(r => r.checked)) {
                    showError(container, 'Please select an option');
                    valid = false;
                }
            });
        
            // Contact number
            const contact = soloParentForm.querySelector('input[name="contact_number"]');
            if (contact && !/^09[0-9]{9}$/.test(contact.value.trim())) {
                showError(contact, 'Invalid mobile number');
                valid = false;
            }
        
            // Declaration
            const declaration = document.getElementById('declaration');
            const declarationContainer = document.getElementById('declarationContainer');
            if (declaration && !declaration.checked) {
                showError(declarationContainer, 'You must agree before submitting');
                valid = false;
            }
        
            return valid;
        }

        // Attach validation to form submit
        soloParentForm?.addEventListener('submit', function(e) {
            e.preventDefault(); // prevent default submit
            const isValid = validateForm();
            
            if (isValid) {
                // Optionally show your confirmation modal or submit the form
                const submitConfirmModalEl = document.getElementById('submitConfirmModal');
                if(submitConfirmModalEl){
                    const submitConfirmModal = new bootstrap.Modal(submitConfirmModalEl);
                    submitConfirmModal.show();
                } else {
                    // If no modal, just submit normally
                    this.submit();
                }
            } else {
                // Scroll to first invalid field
                const firstError = this.querySelector('.is-invalid');
                if(firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });


            // CONFIRM SUBMIT BUTTON HANDLER
            confirmSubmitBtn?.addEventListener('click', function () {
                // Disable button to prevent double submit
                confirmSubmitBtn.disabled = true;
                confirmSubmitBtn.innerText = 'Submitting...';
            
                // Actually submit the form
                soloParentForm.submit();
            });

        });
        </script>

    <!-- SUBMIT CONFIRMATION MODAL -->
    <div class="modal fade" id="submitConfirmModal" tabindex="-1" aria-labelledby="submitConfirmLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-primary shadow-lg">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="submitConfirmLabel">Confirm Submission</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <p class="fs-5 mb-3">
                        Are you sure all the information you entered is complete and accurate?
                    </p>
                    <p class="text-muted small mb-0">
                        Once submitted, you won’t be able to edit your application until it’s reviewed.
                    </p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-primary px-4" id="confirmSubmitBtn">Yes, Submit</button>
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="errorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Error</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>There was a problem submitting your application. Please try again.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <section id="announcement" class="content-section" style="display:none;">
        <div class="card container-fluid">
            <h2 class="section-title">Announcements</h2>
            <p class="section-subtitle"> Stay updated with the latest news and activities for Solo Parents.</p>
    
            @if(!empty($announcements) && count($announcements) > 0)
                <div class="announcement-list">
                    @foreach ($announcements as $announcement)
                        <div class="announcement-card">
                            <div class="announcement-icon"></div>
    
                            <div class="announcement-body">
                                <h4 class="announcement-title">
                                    {{ $announcement['title'] ?? $announcement->title }}
                                </h4>
    
                                @if(!empty($announcement['content'] ?? $announcement->content))
                                    <p class="announcement-text">
                                        {{ $announcement['content'] ?? $announcement->content }}
                                    </p>
                                @endif
    
                                <div class="announcement-footer">
                                    <span class="announcement-date">
                                        {{ isset($announcement['created_at'])
                                            ? \Carbon\Carbon::parse($announcement['created_at'])->format('F j, Y')
                                            : $announcement->created_at->format('F j, Y') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="no-announcements">
                    No announcements available as of {{ now()->format('F j, Y') }}.
                </p>
            @endif
        </div>
    </section>

    <!-- BENEFIT SECTION -->
    <section id="benefits" class="content-section" style="display:none;">
        <div class="card container-fluid">
    
            <!-- Header -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1">Solo Parent Benefits</h2>
                    <p class="text-muted mb-0">Mandated under RA 11861 — Expanded Solo Parents Welfare Act</p>
                </div>
    
                <div class="mt-3 mt-md-0 text-md-end">
                    <span class="badge category-badge me-2">Category: <strong>{{ $beneficiary->category ?? '-' }}</strong></span>
                    <span class="badge barangay-badge">Barangay: <strong>{{ $beneficiary->barangay ?? '-' }}</strong></span>
                </div>
            </div>
    
            <!-- Scan QR Button -->
            <div class="mb-3">
                <button class="btn btn-primary" id="open-scanner-btn">
                    <i class="fas fa-qrcode me-2"></i> Scan QR to Mark as Received
                </button>
            </div>
    
            <!-- Benefits Grid -->
            <div class="row g-3">
                @foreach($beneficiary->benefits ?? [] as $benefit)
                    <div class="col-lg-4 col-md-6">
                        <div class="benefit-card modern-card shadow-sm rounded-4 p-4"
                             data-beneficiary-id="{{ $beneficiary->beneficiary_id }}"
                             data-benefit="{{ $benefit->benefit_name }}">
    
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon {{ $benefit->icon_bg ?? 'bg-blue' }} text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                                    <i class="{{ $benefit->icon ?? 'fas fa-gift' }} fa-lg"></i>
                                </div>
                                <h5 class="fw-bold mb-0">{{ $benefit->benefit_name }}</h5>
                            </div>
    
                            <p class="text-muted mb-2">
                                Schedule: <strong>{{ $benefit->schedule ?? 'As Scheduled' }}</strong>
                            </p>
    
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="status-badge {{ $benefit->status ?? 'active' }}">
                                    {{ ucfirst($benefit->status ?? 'Active') }}
                                </span>
                                <span class="text-muted small">
                                    {{ $benefit->date_given ? \Carbon\Carbon::parse($benefit->date_given)->format('M d, Y') : '-' }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
        
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
        
            const openScannerBtn = document.getElementById('open-scanner-btn');
            const qrScannerModal = new bootstrap.Modal(document.getElementById('qrScannerModal'));
            let html5QrCode;
        
            openScannerBtn.addEventListener('click', () => {
                qrScannerModal.show();
        
                html5QrCode = new Html5Qrcode("qr-reader");
                html5QrCode.start(
                    { facingMode: "environment" },
                    { fps: 10, qrbox: 250 },
                    qrCodeMessage => handleQrScan(qrCodeMessage),
                    errorMessage => console.warn('QR scan error:', errorMessage)
                ).catch(err => console.error('Unable to start QR scanner:', err));
            });
        
            function handleQrScan(qrCode) {
                // Expected QR format: BENEFICIARY_ID|BENEFIT_NAME
                const [beneficiaryId, benefitName] = qrCode.split('|');
                if(!beneficiaryId || !benefitName) return alert('Invalid QR code');
        
                const card = document.querySelector(`.benefit-card[data-beneficiary-id="${beneficiaryId}"][data-benefit="${benefitName}"]`);
                if(!card) return alert('Benefit not found for this QR code');
        
                const statusBadge = card.querySelector('.status-badge');
                const dateSpan = card.querySelector('.text-muted.small');
        
                if(statusBadge && dateSpan) {
                    statusBadge.className = 'status-badge received';
                    statusBadge.textContent = 'Received';
        
                    const now = new Date();
                    const formattedDate = now.toLocaleString('en-US', {
                        month: 'short', day: 'numeric', year: 'numeric',
                        hour: '2-digit', minute:'2-digit'
                    });
                    dateSpan.textContent = formattedDate;
        
                    // Send to backend to save
                    markBenefitAsReceivedBackend(beneficiaryId, benefitName, now.toISOString());
                }
        
                html5QrCode.stop().then(() => html5QrCode.clear()).catch(err => console.error(err));
                qrScannerModal.hide();
            }
        
            document.getElementById('qrScannerModal').addEventListener('hidden.bs.modal', () => {
                if(html5QrCode) html5QrCode.stop().then(() => html5QrCode.clear()).catch(() => {});
            });
        
            async function markBenefitAsReceivedBackend(beneficiaryId, benefitName, timestamp) {
                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const res = await fetch('/user/benefits/mark-received', {
                        method: 'POST',
                        headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': csrfToken },
                        body: JSON.stringify({ beneficiaryId, benefitName, timestamp })
                    });
                    const data = await res.json();
                    if(!data.success) console.warn('Backend failed to save benefit:', data.message);
                } catch(err) {
                    console.error('Error sending benefit received to backend:', err);
                }
            }
        
        });
        </script>

    <!-- CHAT SECTION -->
    <section id="chat" class="content-section chat-section" style="display:none;">
    <div class="chat-container card">

        <h2 class="section-title fw-bold mb-3 mt-2">💬 Chat with Admin</h2>
        <p class="text-muted mb-3">
            Communicate directly with the admin for any system-related concerns.
        </p>

        <div class="chat-card modern-card">

            <!-- Chat Window -->
            <div id="chatWindow" class="chat-window">
                <div id="messagesContainer"></div>
            </div>

            <!-- Quick Questions -->
            <div id="quickQuestions" class="quick-questions"></div>

            <!-- Chat Input -->
            <div class="chat-input-area">
                <textarea
                    id="chatInput"
                    class="chat-input"
                    placeholder="Type your message..."
                    rows="1"
                ></textarea>
                <button id="sendChatBtn" class="btn btn-gradient">
                    Send
                </button>
            </div>

        </div>
    </div>
</section>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
    const chatBox = document.getElementById('chatWindow');
    const messagesContainer = document.getElementById('messagesContainer');
    const quickQuestionsContainer = document.getElementById('quickQuestions');
    const messageInput = document.getElementById('chatInput');
    const sendBtn = document.getElementById('sendChatBtn');
    const userId = {{ auth()->id() }};

    // --- Quick System Questions & Answers ---
    const quickQuestions = [
    { question: "How do I reset my password?", answer: "To reset your password, go to Settings > Account > Reset Password." },
    { question: "Where can I check my application status?", answer: "You can check your application status in the My Applications section." },
    { question: "How do I update my personal information?", answer: "Go to Profile > Edit Information to update your details." },
    { question: "How do I submit a new request?", answer: "Click on 'New Request' in the dashboard and fill out the form." },
    { question: "How can I view system notifications?", answer: "Notifications appear at the top-right bell icon in your dashboard." },
    { question: "How do I report a technical issue?", answer: "Please send a message here describing the issue, and admin will assist you." }
    ];

    // --- Track rendered messages ---
    let lastMessageId = 0;

    // --- Render Quick Questions ---
    function renderQuickQuestions() {
    quickQuestionsContainer.innerHTML = '';
    quickQuestions.forEach(item => {
        const btn = document.createElement('div');
        btn.classList.add('quick-question-btn');
        btn.textContent = item.question;
        btn.addEventListener('click', () => {
        // Add user message
        addMessage(item.question, 'user-msg');

        // Automatically show system answer after short delay
        setTimeout(() => {
            addMessage(item.answer, 'system-msg');
            chatBox.scrollTop = chatBox.scrollHeight;
        }, 500);
        });
        quickQuestionsContainer.appendChild(btn);
    });
    }

    // --- Add Message ---
    function addMessage(text, type = 'user-msg') {
    const div = document.createElement('div');
    div.classList.add('message', type);

    const bubble = document.createElement('div');
    bubble.classList.add('msg-bubble');
    bubble.textContent = text;

    div.appendChild(bubble);
    messagesContainer.appendChild(div);
    chatBox.scrollTop = chatBox.scrollHeight;
    }

    // --- Fetch Messages ---
    function fetchMessages() {
    axios.get('/chat/messages')
        .then(res => {
        res.data.forEach(msg => {
            if(msg.id > lastMessageId){ // only add new messages
            const type = msg.sender_id == userId ? 'user-msg' : 'admin-msg';
            addMessage(msg.message, type);
            lastMessageId = msg.id;
            }
        });
        }).catch(err => console.error(err));
    }

    // --- Initial Render ---
    renderQuickQuestions();
    fetchMessages();
    setInterval(fetchMessages, 2000);

    // --- Send Message ---
    sendBtn.addEventListener('click', () => {
    const text = messageInput.value.trim();
    if(!text) return;

    axios.post('/chat/send', { message: text }, {
        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
    }).then(res => {
        messageInput.value = '';
        fetchMessages();
    }).catch(err => console.error(err));
    });

    // --- Enter key ---
    messageInput.addEventListener('keypress', e => {
    if(e.key === 'Enter' && !e.shiftKey){
        e.preventDefault();
        sendBtn.click();
    }
    });
    </script>

    <!-- SECURITY SECTION -->
    <section id="security" class="content-section" style="display:none;">
    <div class="card security-card">

        <h2 class="section-title fw-bold mb-2 mt-2"> Account Security</h2>
        <p class="text-muted mb-4">
            Change your password. Use a strong password to keep your account secure.
        </p>

        {{-- Success --}}
        @if(session('status'))
            <div class="alert alert-success text-center fade-in">
                {{ session('status') }}
            </div>
        @endif

        {{-- Errors --}}
        @if($errors->any())
            <div class="alert alert-danger fade-in">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="changePasswordForm" method="POST" action="{{ route('user.change.password') }}">
            @csrf

            <!-- Current Password -->
            <div class="form-floating mb-3 position-relative">
                <input type="password" name="current_password" id="current_password"
                       class="form-control form-control-lg" placeholder="Current Password" required>
                <label>Current Password</label>
                <span class="toggle-password" data-target="current_password"></span>
            </div>

            <!-- New Password -->
            <div class="form-floating mb-2 position-relative">
                <input type="password" name="new_password" id="new_password"
                       class="form-control form-control-lg" placeholder="New Password" required>
                <label>New Password</label>
                <span class="toggle-password" data-target="new_password"></span>
            </div>

            <!-- Strength Meter -->
            <div class="mb-3">
                <div class="progress" style="height:6px;">
                    <div id="strengthBar" class="progress-bar"></div>
                </div>
                <small id="strengthText" class="text-muted"></small>
            </div>

            <!-- Confirm Password -->
            <div class="form-floating mb-4 position-relative">
                <input type="password" name="new_password_confirmation"
                       id="new_password_confirmation"
                       class="form-control form-control-lg"
                       placeholder="Confirm Password" required>
                <label>Confirm Password</label>
                <span class="toggle-password" data-target="new_password_confirmation"></span>
            </div>

            <button type="button" class="btn btn-gradient w-100 py-2"
                    id="confirmChangePasswordBtn">
                Update Password
            </button>
        </form>

        <p class="text-center text-muted mt-4 small">
            You’ll be logged out after changing your password.
            A security email notification will be sent.
        </p>
    </div>
</section>

    <div class="modal fade" id="confirmChangePasswordModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold">Confirm Password Change</h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <i class="fas fa-shield-alt text-warning fa-3x mb-3"></i>
                <p class="fw-bold mb-1">Are you sure?</p>
                <p class="text-muted small">
                    This will immediately update your password.
                </p>
            </div>

            <div class="modal-footer justify-content-center">
                <button id="proceedChangePassword" class="btn btn-danger px-4">
                    Yes, Change
                </button>
                <button class="btn btn-secondary" data-bs-dismiss="modal">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

    <div class="modal fade" id="confirmChangePasswordModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold">Confirm Password Change</h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <i class="fas fa-shield-alt text-warning fa-3x mb-3"></i>
                <p class="fw-bold mb-1">Are you sure?</p>
                <p class="text-muted small">
                    This will immediately update your password.
                </p>
            </div>

            <div class="modal-footer justify-content-center">
                <button id="proceedChangePassword" class="btn btn-danger px-4">
                    Yes, Change
                </button>
                <button class="btn btn-secondary" data-bs-dismiss="modal">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

    <script>
    document.addEventListener("DOMContentLoaded", () => {
    
        // Show and Hide Password
        document.querySelectorAll(".toggle-password").forEach(el=>{
            el.onclick = ()=>{
                const input = document.getElementById(el.dataset.target);
                input.type = input.type === "password" ? "text" : "password";
            };
        });
        
        // Strength Meter
        const pwd = document.getElementById("new_password");
        const bar = document.getElementById("strengthBar");
        const text = document.getElementById("strengthText");
        
        pwd.oninput = ()=>{
            let s = 0;
            if(pwd.value.length>=8) s++;
            if(/[A-Z]/.test(pwd.value)) s++;
            if(/[0-9]/.test(pwd.value)) s++;
            if(/[^A-Za-z0-9]/.test(pwd.value)) s++;
        
            bar.className="progress-bar";
            if(s<=1){
                bar.style.width="25%";
                bar.classList.add("bg-danger");
                text.textContent="Weak password";
            }else if(s===2){
                bar.style.width="50%";
                bar.classList.add("bg-warning");
                text.textContent="Moderate password";
            }else{
                bar.style.width="100%";
                bar.classList.add("bg-success");
                text.textContent="Strong password";
            }
        };
        
        // Modal Submit
        const modal = new bootstrap.Modal(
            document.getElementById("confirmChangePasswordModal")
        );
        
        document.getElementById("confirmChangePasswordBtn").onclick = ()=>{
            if(bar.style.width!=="100%"){
                alert("Please use a strong password.");
                return;
            }
            modal.show();
        };
        
        document.getElementById("proceedChangePassword").onclick = ()=>{
            document.getElementById("changePasswordForm").submit();
        };
    });
    </script>

</main>

<!-- QR Scanner Modal -->
<div class="modal fade" id="qrScannerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Scan QR to Receive Benefits</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <div id="qr-reader" style="width:100%;"></div>
            <p class="text-center mt-2">Align QR code in frame to scan</p>
        </div>
        </div>
    </div>
</div>

<!-- LOGOUT MODAL -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">

        <!-- HEADER -->
        <div class="modal-header border-0 bg-danger text-white text-center flex-column position-relative">
            <div class="logout-icon mb-2">
            <i class="fas fa-sign-out-alt fa-2x"></i>
            </div>
            <h5 class="modal-title fw-bold" id="logoutModalLabel">Confirm Logout</h5>
            <button type="button" class="btn-close btn-close-white position-absolute end-0 top-0 m-3" data-bs-dismiss="modal"></button>
        </div>

        <!-- BODY -->
        <div class="modal-body text-center px-4">
            <p class="text-muted mb-3">Are you sure you want to log out of your account?</p>
        </div>

        <!-- FOOTER -->
        <div class="modal-footer border-0 justify-content-center gap-2 pb-4">
            <button type="button" class="btn btn-danger px-4 fw-semibold" id="confirmLogoutBtn">
            <i class="fas fa-sign-out-alt me-1"></i> Logout
            </button>
            <button type="button" class="btn btn-light px-4 fw-semibold" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i> Cancel
            </button>
        </div>

        </div>
    </div>
</div>


<script>
function showSection(sectionId) {
    // Hide all sections
    document.querySelectorAll('.content-section').forEach(sec => sec.style.display = 'none');
    // Show the selected one
    document.getElementById(sectionId).style.display = 'block';

    // Update active sidebar
    document.querySelectorAll('.sidebar .menu li').forEach(li => li.classList.remove('active'));
    event.currentTarget.classList.add('active');
}

</script>
    
<script>
// Sidebar Collapse Toggle
window.toggleSidebar = function() {
  const sidebar = document.querySelector(".sidebar");
  const mainContent = document.querySelector(".main-content");
  const toggleBtn = document.querySelector(".toggle-btn");

  sidebar.classList.toggle("hidden");
  mainContent.classList.toggle("expanded");

  // toggle rotate animation
  toggleBtn.classList.toggle("rotate");
};
</script>

<meta name="csrf-token" content="{{ csrf_token() }}">

<script>
document.addEventListener('DOMContentLoaded', function () {
    const logoutBtn = document.getElementById('logoutBtn');
    const confirmLogoutBtn = document.getElementById('confirmLogoutBtn');
    const logoutModalEl = document.getElementById('logoutModal');
    const logoutModal = new bootstrap.Modal(logoutModalEl);

    // Show modal on logout click
    logoutBtn.addEventListener('click', function () {
        logoutModal.show();
    });

    // Perform actual logout
    confirmLogoutBtn.addEventListener('click', function () {
        fetch("{{ route('logout') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            }
        })
        .then(res => {
            if (res.ok) {
                window.location.href = "{{ route('home') }}";
            }
        });
    });
});
</script>


@endsection
