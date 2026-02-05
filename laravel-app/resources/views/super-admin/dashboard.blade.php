@extends('layouts.super')

@section('title', 'Super Admin Dashboard')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/super.css') }}">
@endsection

@section('content')
<header class="admin-topbar">
    <div class="topbar-left">
        <button class="toggle-btn" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        <img src="{{ asset('images/SC.svg') }}" alt="Logo" class="topbar-logo">

        <div class="topbar-text">
            <h2 class="topbar-title">{{ $system->system_brand_name }}</h2>
            <p class="topbar-subtitle">System Administrator - Super Admin</p>
        </div>
    </div>

    <div class="topbar-right d-flex align-items-center gap-2">
        <div class="dropdown">
            <button class="btn notif-btn d-flex align-items-center position-relative" type="button" id="notifDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-bell text-white"></i>

            </button>

            <ul class="dropdown-menu dropdown-menu-end p-2 shadow" aria-labelledby="notifDropdown" style="min-width: 300px;">
                <li class="dropdown-header">Notifications</li>
                <li><hr class="dropdown-divider"></li>


                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2" href="#">
                            <i class="fas fa-user-plus text-primary"></i>
                            <div>
                                <strong>New Application</strong><br>
                                <small class="text-muted"></small>
                            </div>
                        </a>
                    </li>

                    <li><p class="dropdown-item text-muted text-center">No new applications</p></li>



                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('admin.dashboard') }}#chat-section">
                            <i class="fas fa-comment text-success"></i>
                            <div>
                                <strong></strong>:<br>
                                <small class="text-muted"></small>
                            </div>
                        </a>
                    </li>

                    <li><p class="dropdown-item text-muted text-center">No new chat messages</p></li>

            </ul>
        </div>

        <div class="dropdown">
            <button class="btn profile-btn d-flex align-items-center gap-2" type="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="{{ asset('images/SC.svg') }}" alt="Admin Profile" class="rounded-circle" style="width:32px; height:32px; object-fit:cover;">
                <span class="fw-semibold text-white">{{ Auth::user()->name }}</span>
                <i class="fas fa-caret-down text-white"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="profileDropdown">
                <li class="dropdown-header d-flex align-items-center gap-2">
                    <img src="{{ asset('images/SC.svg') }}" alt="Admin" class="rounded-circle" style="width:40px; height:40px; object-fit:cover;">
                    <div>
                        <strong>{{ Auth::user()->name }}</strong>
                        <p class="text-muted mb-0" style="font-size:12px;">Super Administrator</p>
                    </div>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item d-flex align-items-center gap-2" href="#"><i class="fas fa-user"></i> View Profile</a></li>
                <li><a class="dropdown-item d-flex align-items-center gap-2" href="#"><i class="fas fa-cog"></i> Settings</a></li>
            </ul>
        </div>
    </div>
</header>

<aside class="sidebar">
    <ul class="menu">
        <li class="active" data-target="dashboard-section"><i class="fas fa-home fa-fw me-2"></i> Dashboard</li>
        <li data-target="solo-parent-section"><i class="fas fa-user-friends fa-fw me-2"></i> Solo Parent List</li>
        <li data-target="applicants-section"><i class="fas fa-file-alt fa-fw me-2"></i> Applicants</li>
        <li data-target="benefits-section"><i class="fas fa-hand-holding-heart fa-fw me-2"></i> Benefits & Schedule</li>
        <li data-target="user-management-section"><i class="fas fa-users-cog fa-fw me-2"></i> User Management</li>
        <li data-target="report-section"><i class="fas fa-chart-bar fa-fw me-2"></i> Reports</li>
        <li data-target="announcement-section"><i class="fas fa-bullhorn fa-fw me-2"></i> Announcements</li>
        <li data-target="chat-section"><i class="fas fa-comments fa-fw me-2"></i> Chat Module</li>
        <li data-target="settings-section"><i class="fas fa-cogs fa-fw me-2"></i> System Settings</li>
        <li data-target="audit-log-section"><i class="fas fa-clipboard-list fa-fw me-2"></i> Audit Logs</li>
        <li id="logoutBtn"><i class="fas fa-sign-out-alt fa-fw me-2"></i> Logout</li>
    </ul>
</aside>

<!-- LOGOUT MODAL -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <!-- HEADER -->
            <div class="modal-header border-0 bg-danger text-white text-center flex-column position-relative">
                <div class="logout-icon mb-2">
                    <i class="fas fa-sign-out-alt fa-2x"></i>
                </div>
                <h5 class="modal-title fw-bold" id="logoutModalLabel"> Confirm Logout </h5>
                <button type="button" class="btn-close btn-close-white position-absolute end-0 top-0 m-3" data-bs-dismiss="modal"></button>
            </div>
            <!-- BODY -->
            <div class="modal-body text-center px-4">
                <p class="text-muted mb-3"> Are you sure you want to log out of your account?</p>
            </div>
            <!-- FOOTER -->
            <div class="modal-footer border-0 justify-content-center gap-2 pb-4">
                <button type="button" class="btn btn-danger px-4 fw-semibold" id="confirmLogout">
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
function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    sidebar.classList.toggle('active');
}

document.addEventListener("DOMContentLoaded", function () {
    const menuItems = document.querySelectorAll('.sidebar .menu li');

    menuItems.forEach(item => {
        item.addEventListener('click', function() {
            const target = this.getAttribute('data-target');
            if (target && target !== 'dashboard-section') {
                console.log('Navigate to: ' + target);
            }
        });
    });

    const logoutBtn = document.getElementById("logoutBtn");
    const confirmLogout = document.getElementById("confirmLogout");

    if (logoutBtn) {
        logoutBtn.addEventListener("click", () => {
            const modal = new bootstrap.Modal(document.getElementById("logoutModal"));
            modal.show();
        });
    }

    if (confirmLogout) {
        confirmLogout.addEventListener("click", () => {
            fetch("/logout", {
                method: "POST",
                headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
            }).then(() => window.location.href = "/");
        });
    }
});
</script>

<main class="main-content fade-in">

<!-- SUPER ADMIN DASHBOARD -->
<div class="content-section" id="dashboard-section">
    <div class="dashboard-container">
        <!-- Welcome Card -->
        <div class="welcome-card d-flex align-items-center mb-4 p-3 shadow-sm rounded">
            <i class="fas fa-user-circle fa-3x text-primary me-3">
            </i>
            <div>
                <h3 class="mb-1">Welcome, Super Admin!</h3>
                <p class="mb-0">Manage the system efficiently and securely.</p>
            </div>
        </div>
        <!-- System Stat Cards -->
        <div class="row g-3 mb-4">
            <!-- TOTAL USERS -->
            <div class="col-md-3">
                <div class="card stat-card shadow-sm rounded-4 p-3 hover-shadow">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle bg-primary me-3">
                            <i class="fas fa-users fa-lg text-white"></i>
                        </div>
                        <div>
                            <h5 id="total-users" class="mb-0 text-primary"></h5>
                            <small class="text-muted">Total Users</small>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ACTIVE SESSIONS -->
            <div class="col-md-3">
                <div class="card stat-card shadow-sm rounded-4 p-3 hover-shadow">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle bg-warning me-3">
                            <i class="fas fa-signal fa-lg text-white"></i>
                        </div>
                        <div>
                            <h5 id="active-sessions" class="mb-0 text-warning"></h5>
                            <small class="text-muted">Active Sessions</small>
                        </div>
                    </div>
                </div>
            </div>
            <!-- SYSTEM HEALTH -->
            <div class="col-md-3">
                <div class="card stat-card shadow-sm rounded-4 p-3 hover-shadow">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle bg-success me-3">
                            <i class="fas fa-heartbeat fa-lg text-white">
                            </i>
                        </div>
                        <div>
                            <h5 id="system-health" class="mb-0 text-success"></h5>
                            <small class="text-muted">System Health</small>
                        </div>
                    </div>
                </div>
            </div>
            <!-- SYSTEM ERRORS -->
            <div class="col-md-3">
                <div class="card stat-card shadow-sm rounded-4 p-3 hover-shadow">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle bg-danger me-3">
                            <i class="fas fa-exclamation-triangle fa-lg text-white"></i>
                        </div>
                        <div>
                            <h5 id="system-errors" class="mb-0 text-danger"></h5>
                            <small class="text-muted">System Errors</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Quick Action Buttons -->
        <div class="d-flex gap-2 mb-4 flex-wrap">
            <button class="btn btn-primary flex-fill hover-shadow"><i class="fas fa-users-cog me-2"></i>Manage Users</button>
            <button class="btn btn-warning flex-fill hover-shadow"><i class="fas fa-bell me-2"></i>Push Announcement</button>
            <button class="btn btn-success flex-fill hover-shadow"><i class="fas fa-database me-2"></i>Backup System</button>
            <button class="btn btn-info flex-fill hover-shadow"><i class="fas fa-file-alt me-2"></i>View Reports</button>
        </div>
        <div class="row g-4">
            <!-- LEFT PANEL -->
            <div class="col-lg-8">
                <!-- User Growth Chart -->
                <div class="card dashcard-container shadow-sm rounded mb-4 hover-shadow">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-1"><i class="fas fa-chart-line me-2 text-primary"></i>User Growth</h5>
                        <small class="text-muted">Track system adoption and user increase over time </small>
                    </div>
                    <div class="card-body d-flex justify-content-center align-items-center" style="height:300px;">
                        <canvas id="userGrowthChart" width="450" height="250">
                        </canvas>
                    </div>
                </div>
                <!-- Recent Activity -->
                <div class="card dashcard-container shadow-sm rounded hover-shadow">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-1"><i class="fas fa-history me-2 text-warning"></i> Recent Activity </h5>
                        <small class="text-muted"> Latest actions performed in the system </small>
                    </div>
                    <div class="card-body" id="recent-activity" style="max-height:250px; overflow-y:auto;"></div>
                </div>
            </div>
            <!-- RIGHT PANEL -->
            <div class="col-lg-4">
                <!-- System Status -->
                <div class="card dashcard-container shadow-sm rounded mb-4 hover-shadow">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-1"><i class="fas fa-server me-2 text-success"></i> System Status </h5>
                        <small class="text-muted"> Check connectivity and service status </small>
                    </div>
                    <div class="card-body" id="system-status">
                    </div>
                </div>
                <!-- Solo Beneficiaries Chart -->
                <div class="card dashcard-container shadow-sm rounded hover-shadow">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-1"><i class="fas fa-users me-2 text-info"></i> Solo Beneficiaries </h5>
                        <small class="text-muted"> Distribution by category </small>
                    </div>
                    <div class="card-body d-flex justify-content-center" style="height:420px;">
                        <canvas id="soloBeneficiariesChart" width="330" height="330"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {

    // Dashboard Stats
    const stats = { totalUsers: 35, activeSessions: 12, systemErrors: 1 };
        document.getElementById('total-users').innerText = stats.totalUsers;
        document.getElementById('active-sessions').innerText = stats.activeSessions;
        document.getElementById('system-errors').innerText = stats.systemErrors;
    
    // System Status
    const systemStatuses = [
        { label: 'Database', status: 'Connected', badge: 'bg-success' },
        { label: 'API', status: 'Online', badge: 'bg-success' },
        { label: 'Mail Service', status: 'Offline', badge: 'bg-danger' }
    ];
    
    const statusList = document.getElementById('system-status');
    statusList.innerHTML = '';
    systemStatuses.forEach(s => {
        const div = document.createElement('div');
        div.classList.add('d-flex','justify-content-between','mb-2');
        div.innerHTML = `<span class="text-muted">${s.label}</span><span class="badge ${s.badge}">${s.status}</span>`;
        statusList.appendChild(div);
    });

    // System Health Badge
    const systemHealthEl = document.getElementById('system-health');
    let healthLabel = 'Good';
    let badgeClass = 'bg-success';
    if(systemStatuses.some(s=>s.status!=='Connected'&&s.status!=='Online')){
        healthLabel='Warning';
        badgeClass='bg-warning';
    }
    systemHealthEl.innerHTML = `<span class="badge ${badgeClass}">${healthLabel}</span>`;
    
    // User Growth Chart
    const ctxUser = document.getElementById('userGrowthChart').getContext('2d');
    new Chart(ctxUser, {
    type:'line',
        data:{ labels:['Jul','Aug','Sep','Oct','Nov','Dec'], datasets:[{ label:'Users', data:[5,10,15,20,25,35], borderColor:'#003366', backgroundColor:'rgba(0,51,102,0.1)', fill:true, tension:0.3 }] },
        options:{ responsive:false, maintainAspectRatio:false, plugins:{legend:{display:false}}, scales:{x:{grid:{display:false}}, y:{grid:{color:'rgba(0,0,0,0.05)'}, beginAtZero:true}} }
    });

    // Solo Beneficiaries Chart
    const ctxSolo = document.getElementById('soloBeneficiariesChart').getContext('2d');
    new Chart(ctxSolo,{
    type:'doughnut',
        data:{ labels:['A1','A2','A3','A4','A5','A6','A7','B','C','D','E','F'], datasets:[{ label:'Solo Beneficiaries', data:[0,10,0,3,5,0,0,3,7,4,0,3], backgroundColor:['#0d6efd','#ffc107','#198754','#6610f2','#fd7e14','#20c997','#dc3545','#6c757d','#0dcaf0','#adb5bd','#6610f2','#fd7e14'], borderWidth:1 }] },
        options:{ responsive:false, maintainAspectRatio:false, plugins:{legend:{position:'bottom', labels:{boxWidth:12, padding:10}}} }
    });
    
    // Fetch Recent Activity from Database
    const recentActivityEl = document.getElementById('recent-activity');
    
    function getIconByStatus(status){
        if(status==='Success') return '<i class="fas fa-check-circle text-success"></i>';
        if(status==='Deleted') return '<i class="fas fa-trash-alt text-danger"></i>';
        if(status==='Updated') return '<i class="fas fa-edit text-warning"></i>';
        return '<i class="fas fa-eye text-info"></i>';
    }
    
    function renderRecentActivity(logs){
        recentActivityEl.innerHTML = '';
        if(logs.length===0){
            recentActivityEl.innerHTML = '<div class="text-muted text-center py-3">No recent activity</div>';
            return;
        }
        logs.slice(0,5).forEach(log=>{
            const div=document.createElement('div');
            div.className='recent-activity-item d-flex justify-content-between mb-2';
            div.innerHTML=`
                <div>${getIconByStatus(log.status)} <strong>${log.user}</strong> ${log.action} <small>[${log.module}]</small></div>
                <div class="text-muted">${new Date(log.created_at).toLocaleString()}</div>
            `;
            recentActivityEl.appendChild(div);
        });
    }
    
    // Fetch from backend
    function loadRecentActivity(){
    fetch("{{ route('super.audit.logs') }}")
        .then(res=>res.json())
        .then(data=>renderRecentActivity(data))
        .catch(err=>console.error('Failed to load recent activity', err));
    }
    
    loadRecentActivity();
    setInterval(loadRecentActivity, 10000); 
});
</script>

<!-- ✅ LEAFLET CSS & JS -->
<link rel="stylesheet" href="{{ asset('css/leaflet.css') }}">
<script src="{{ asset('js/leaflet.js') }}"></script>
<script src="{{ asset('js/leaflet-heat.js') }}"></script>

<!-- SUPER ADMIN SOLO PARENT APPLICATION LIST-->
<div id="applicants-section" class="content-section" style="display:none;">
    <div class="admin-container">

        <h3 class="section-title fw-bold mb-4 mt-2"><i class="fas fa-users me-2"></i>Solo Parent Application List</h3>

        <!-- APPLICATION STAT CARDS -->
        <div class="row g-3 mb-4" id="applicationStats">
            <div class="col-md-3">
                <div class="card stat-card stat-blue shadow-sm border-0 rounded-4 p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle bg-primary me-3">
                        <i class="fas fa-file-alt fa-lg text-white"></i>
                        </div>
                        <div>
                        <h5 class="mb-0">{{ count($applications) }}</h5>
                        <small class="text-muted">Total Applications</small>
                        </div>
                    </div>
                </div>
            </div>
    
            <div class="col-md-3">
                <div class="card stat-card stat-yellow shadow-sm border-0 rounded-4 p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle bg-warning me-3">
                        <i class="fas fa-hourglass-half fa-lg text-white"></i>
                        </div>
                        <div>
                        <h5 class="mb-0 text-warning">{{ $pendingApplications }}</h5>
                        <small class="text-muted">Pending</small>
                        </div>
                    </div>
                </div>
            </div>
    
            <div class="col-md-3">
                <div class="card stat-card stat-green shadow-sm border-0 rounded-4 p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle bg-success me-3">
                        <i class="fas fa-check fa-lg text-white"></i>
                        </div>
                        <div>
                        <h5 class="mb-0 text-success">{{ $approvedApplications }}</h5>
                        <small class="text-muted">Approved</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card stat-red shadow-sm border-0 rounded-4 p-3">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-danger me-3">
                    <i class="fas fa-times fa-lg text-white"></i>
                    </div>
                    <div>
                    <h5 class="mb-0 text-danger">{{ $rejectedApplications }}</h5>
                    <small class="text-muted">Rejected</small>
                    </div>
                </div>
            </div>
        </div>
        </div>
        
        <!-- Title + Export Buttons -->
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-2">
            <h3 class="section-title fw-bold mb-0">
                <i class="fas fa-users me-2"></i>Solo Parent Application List
            </h3>

            <div class="d-flex gap-2">
                <button id="exportPdfBtn" class="btn btn-sm btn-danger">
                    <i class="fas fa-file-pdf"></i> PDF
                </button>
                <button id="exportExcelBtn" class="btn btn-sm btn-success">
                    <i class="fas fa-file-csv"></i> Excel
                </button>
            </div>
        </div>
        
        
        <!-- Filters -->
        <div class="row g-2 align-items-center my-3">

            <!-- Search -->
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" id="searchInput"
                        class="form-control border-start-0"
                        placeholder="Search by applicant name...">
                </div>
            </div>

            <!-- Filters -->
            <div class="col-md-6 d-flex justify-content-end">

                <span class="input-group-text bg-white border-end-0">
                    <i class="fas fa-filter"></i>
                </span>
                <select id="statusFilter" class="form-select me-2">
                    <option value="all">All Status</option>
                    <option value="Pending">Pending</option>
                    <option value="Approved">Approved</option>
                    <option value="Rejected">Rejected</option>
                </select>

                <span class="input-group-text bg-white border-end-0">
                    <i class="fas fa-map-marker-alt"></i>
                </span>
                <select id="barangayFilter" class="form-select">
                    <option value="all">All Barangays</option>
                    @foreach($barangays as $brgy)
                        <option value="{{ $brgy }}">{{ $brgy }}</option>
                    @endforeach
                </select>

            </div>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-primary">
                    <tr>
                        <th class="text-center"><input type="checkbox" id="selectAll"></th>
                        <th>#</th>
                        <th>Ref No.</th>
                        <th>Applicant</th>
                        <th>Address</th>
                        <th>Date</th>
                        <th>Category</th>
                        <th>Stage</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody id="applicant-list">
                @foreach($applications as $app)
                    <tr class="app-row">
                        <td class="text-center">
                            <input type="checkbox" class="row-checkbox"
                                   data-phone="{{ $app->contact_no ?? '' }}"
                                   data-email="{{ $app->email ?? '' }}">
                        </td>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $app->reference_no }}</td>
                        <td>{{ $app->last_name }}, {{ $app->first_name }}</td>
                        <td>{{ $app->barangay }}, {{ $app->municipality }}</td>
                        <td>{{ $app->created_at->format('Y-m-d') }}</td>
                        <td>{{ $app->category ?? '-' }}</td>
                        <td>{{ $app->application_stage ?? 'Review' }}</td>
                        <td>{{ $app->status ?? 'Pending' }}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm text-white view" style="background-color:#003366;"> View </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <small>
                Showing <span id="pageStart">1</span> -
                <span id="pageEnd">5</span> of
                <span id="totalRows">0</span>
            </small>
            <ul class="pagination pagination-sm mb-0" id="pagination"></ul>
        </div>

    </div>
</div>


<script>
document.addEventListener("DOMContentLoaded", function () {

    // Modal Elements
    const viewModalEl = document.getElementById("viewModal");
    const viewDetails = document.getElementById("viewDetails");

    const viewModal = viewModalEl ? new bootstrap.Modal(viewModalEl) : null;

    let selectedRow = null;

    // Helper Functions
    const safeParseJSON = (str) => { try { return JSON.parse(str || "{}"); } catch { return {}; } };
    const formatDate = (dateStr) => { const d = new Date(dateStr); return isNaN(d) ? "-" : d.toLocaleDateString("en-US", { year:"numeric", month:"long", day:"numeric" }); };
    const buildFullName = (app) => `${app.last_name || ""}${app.last_name ? ", " : ""}${app.first_name || ""}${app.middle_name ? " " + app.middle_name : ""}`.trim() || "-";
    const buildAddress = (app) => [app.street, app.barangay, app.municipality, app.province].filter(Boolean).join(", ") || "-";
    const formatCurrency = (amt) => "₱" + Number(amt || 0).toLocaleString();

    const renderFamilyComposition = (app) => {
        let members = [];
        if (Array.isArray(app.family)) members = app.family;
        else if (typeof app.family === "string" && app.family.trim()) {
            try { members = JSON.parse(app.family) || []; } catch { members = []; }
        }
        if (!members.length) return `<div class="alert alert-warning py-2 mb-0">No family members listed.</div>`;
        const rows = members.map(m => `
            <tr>
                <td>${m.name || "-"}</td>
                <td>${m.relationship || "-"}</td>
                <td>${m.age || "-"}</td>
                <td>${formatDate(m.birth_date)}</td>
                <td>${m.civil_status || "-"}</td>
                <td>${m.occupation || "-"}</td>
                <td>${formatCurrency(m.monthly_income)}</td>
                <td>${m.educational_attainment || "-"}</td>
            </tr>`).join("");
        return `<div class="table-responsive"><table class="table table-sm align-middle"><thead class="table-primary"><tr><th>Name</th><th>Relationship</th><th>Age</th><th>Date of Birth</th><th>Civil Status</th><th>Occupation</th><th>Monthly Income</th><th>Educational Attainment</th></tr></thead><tbody>${rows}</tbody></table></div>`;
    };

    const renderDocuments = (files) => {
        if (!Array.isArray(files) || !files.length) return `<div class="alert alert-warning py-2 mb-0">N/A</div>`;
        return `<h5 class="mt-4 fw-bold">Requirements / Uploaded Documents</h5>` + files.map(f => `<a href="${f.url || '#'}" target="_blank" class="badge bg-primary text-decoration-none me-2 mb-2" style="font-size:0.9rem; display:inline-flex; align-items:center;"><i class="fas fa-file-alt me-1"></i> ${f.name || "Document"}</a>`).join("");
    };

    const generateViewHTML = (app, files) => `
        <!-- PERSONAL INFORMATION -->
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-header bg-light fw-bold"><i class="fas fa-id-card me-2 text-primary"></i> Personal Information</div>
            <div class="card-body">
                <div class="row g-4 align-items-center">
                    <div class="col-md-3 text-center">
                        <div class="avatar-box mx-auto"><i class="fas fa-user fa-3x text-primary"></i></div>
                        <h6 class="mt-3 mb-1 fw-semibold">${buildFullName(app)}</h6>
                        <span class="badge bg-primary-subtle text-primary px-3 py-1 rounded-pill">Applicant</span>
                    </div>
                    <div class="col-md-9">
                        <div class="row gx-3 gy-2">
                            <div class="col-md-4"><label class="form-label text-muted">Sex</label><div class="fw-semibold">${app.sex || "-"}</div></div>
                            <div class="col-md-4"><label class="form-label text-muted">Age</label><div class="fw-semibold">${app.age || "-"}</div></div>
                            <div class="col-md-4"><label class="form-label text-muted">Civil Status</label><div class="fw-semibold">${app.civil_status || "-"}</div></div>
                            <div class="col-md-6"><label class="form-label text-muted">Birth Date</label><div>${formatDate(app.birth_date)}</div></div>
                            <div class="col-md-6"><label class="form-label text-muted">Place of Birth</label><div>${app.place_of_birth || "-"}</div></div>
                            <div class="col-md-12"><label class="form-label text-muted">Address</label><div>${buildAddress(app)}</div></div>
                            <div class="col-md-4"><label class="form-label text-muted">Education</label><div>${app.educational_attainment || "-"}</div></div>
                            <div class="col-md-4"><label class="form-label text-muted">Occupation</label><div>${app.occupation || "-"}</div></div>
                            <div class="col-md-4"><label class="form-label text-muted">Monthly Income</label><div>${formatCurrency(app.monthly_income)}</div></div>
                            <div class="col-md-6"><label class="form-label text-muted">Contact Number</label><div>${app.contact_number || "-"}</div></div>
                            <div class="col-md-6"><label class="form-label text-muted">Email Address</label><div>${app.email || "-"}</div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- FAMILY COMPOSITION -->
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-header bg-light fw-bold"><i class="fas fa-users me-2 text-success"></i> Family Composition</div>
            <div class="card-body">${renderFamilyComposition(app)}</div>
        </div>
        <!-- REASON -->
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-header bg-light fw-bold"><i class="fas fa-question-circle me-2 text-warning"></i> Reason</div>
            <div class="card-body"><div class="p-3 rounded-3" style="background:#f9fafb; line-height:1.6;">${app.solo_parent_reason || "-"}</div></div>
        </div>
        <!-- PROBLEM -->
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-header bg-light fw-bold"><i class="fas fa-exclamation-triangle me-2 text-danger"></i> Problem / Needs</div>
            <div class="card-body"><div class="p-3 rounded-3" style="background:#f9fafb; line-height:1.6;">${app.solo_parent_needs || "-"}</div></div>
        </div>
        <!-- DOCUMENTS -->
        <div class="card shadow-sm border-0 rounded-4 mb-2">
            <div class="card-header bg-light fw-bold"><i class="fas fa-folder-open me-2 text-primary"></i> Uploaded Documents</div>
            <div class="card-body">${renderDocuments(files)}</div>
        </div>
    `;

    // VIEW BUTTON
    document.addEventListener("click", (e) => {
        const btn = e.target.closest(".view");
        if (!btn) return;
        const tr = btn.closest("tr");
        if (!tr) return;
        const appData = safeParseJSON(tr.dataset.details);
        const filesUrl = tr.dataset.filesUrl;
        fetch(filesUrl).then(res => res.json()).then(data => {
            const filesArray = (data.success && Array.isArray(data.files)) ? data.files : [];
            viewDetails.innerHTML = generateViewHTML(appData, filesArray);
            if (viewModal) viewModal.show();
        }).catch(() => { viewDetails.innerHTML = generateViewHTML(appData, []); if (viewModal) viewModal.show(); });
    });
});
</script>

<script>
document.addEventListener("DOMContentLoaded", () => {

    // PAGINATION + TABLE LOGIC
    const rows = [...document.querySelectorAll(".app-row")];
    const rowsPerPage = 5;
    let currentPage = 1;

    const pagination = document.getElementById("pagination");
    const pageStart = document.getElementById("pageStart");
    const pageEnd = document.getElementById("pageEnd");
    const totalRows = document.getElementById("totalRows");
    const selectedCount = document.getElementById("selectedCount");
    const bulkSMS = document.getElementById("bulkSMS");
    const bulkEmail = document.getElementById("bulkEmail");

    totalRows.textContent = rows.length;

    function renderPage(page) {
        currentPage = page;
        const start = (page - 1) * rowsPerPage;
        const end = start + rowsPerPage;

        rows.forEach((row, i) => {
            row.style.display = i >= start && i < end ? "" : "none";
        });

        pageStart.textContent = start + 1;
        pageEnd.textContent = Math.min(end, rows.length);
        renderPagination();
    }

    function renderPagination() {
        pagination.innerHTML = "";
        const totalPages = Math.ceil(rows.length / rowsPerPage);

        pagination.innerHTML += `
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" onclick="changePage(${currentPage - 1})">
                    <i class="fas fa-chevron-left"></i>
                </a>
            </li>`;

        for (let i = 1; i <= totalPages; i++) {
            pagination.innerHTML += `
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" onclick="changePage(${i})">${i}</a>
                </li>`;
        }

        pagination.innerHTML += `
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link" onclick="changePage(${currentPage + 1})">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </li>`;
    }

    window.changePage = p => {
        const totalPages = Math.ceil(rows.length / rowsPerPage);
        if (p >= 1 && p <= totalPages) renderPage(p);
    };

    function updateSelection() {
        const checked = document.querySelectorAll(".row-checkbox:checked");
        if (selectedCount) selectedCount.textContent = checked.length;
        if (bulkSMS && bulkEmail) {
            bulkSMS.disabled = bulkEmail.disabled = checked.length === 0;
        }

        rows.forEach(row =>
            row.classList.toggle(
                "selected",
                row.querySelector(".row-checkbox").checked
            )
        );
    }

    document.querySelectorAll(".row-checkbox").forEach(cb =>
        cb.addEventListener("change", updateSelection)
    );

    const selectAll = document.getElementById("selectAll");
    if (selectAll) {
        selectAll.addEventListener("change", e => {
            document.querySelectorAll(".row-checkbox").forEach(cb => cb.checked = e.target.checked);
            updateSelection();
        });
    }

    if (bulkSMS) bulkSMS.onclick = () => alert("Send SMS to selected applicants");
    if (bulkEmail) bulkEmail.onclick = () => alert("Send Email to selected applicants");

    renderPage(1);

    // EXPORT MODALS (PDF & CSV)
    const pdfModalEl = document.getElementById("exportPdfModal");
    const csvModalEl = document.getElementById("exportCsvModal");

    const pdfModal = pdfModalEl ? new bootstrap.Modal(pdfModalEl) : null;
    const csvModal = csvModalEl ? new bootstrap.Modal(csvModalEl) : null;

    const exportPdfBtn = document.getElementById("exportPdfBtn");
    const exportExcelBtn = document.getElementById("exportExcelBtn");

    if (exportPdfBtn && pdfModal) exportPdfBtn.onclick = () => pdfModal.show();
    if (exportExcelBtn && csvModal) exportExcelBtn.onclick = () => csvModal.show();

    const downloadPdfBtn = document.getElementById("downloadPdfBtn");
    const downloadCsvBtn = document.getElementById("downloadCsvBtn");

    if (downloadPdfBtn) {
        downloadPdfBtn.onclick = () => {
            const year = document.getElementById("pdfYear")?.value || "All";
            const month = document.getElementById("pdfMonth")?.value || "All";
            const url = `/super-admin/solo-parent/export/pdf?year=${year}&month=${month}`;
            window.location.href = url;
            if (pdfModal) pdfModal.hide();
        };
    }

    if (downloadCsvBtn) {
        downloadCsvBtn.onclick = () => {
            const year = document.getElementById("csvYear")?.value || "All";
            const month = document.getElementById("csvMonth")?.value || "All";
            const url = `/super-admin/solo-parent/export/excel?year=${year}&month=${month}`;
            window.location.href = url;
            if (csvModal) csvModal.hide();
        };
    }

});
</script>



<!-- SOLO PARENTS SECTION -->
<div id="solo-parent-section" class="content-section" style="display:none;">
    <div class="admin-container">

        <!-- Overview Section -->
        <div id="soloParentOverview">
            <h2 class="section-title fw-bold mb-2 mt-2"><i class="fas fa-map-marker-alt me-2"></i>Solo Parent Beneficiary GIS Mapping Tracker</h2>
            <p class="text-muted mb-4">Overall Report – General Trias</p>
                <!-- SOLO PARENT STAT CARDS -->
                <div class="row g-3 mb-4" id="beneficiaryStats">
                    <div class="col-md-4">
                        <div class="card stat-card stat-blue shadow-sm border-0 rounded-4 p-3">
                            <div class="d-flex align-items-center">
                                <div class="icon-circle bg-primary me-3">
                                  <i class="fas fa-users fa-lg text-white"></i>
                                </div>
                                <div>
                                  <h5 class="mb-0" id="beneficiaryTotalValue">25</h5>
                                  <small class="text-muted">Total Solo Parents</small>
                                </div>
                            </div>
                        </div>
                    </div>
                
                    <div class="col-md-4">
                        <div class="card stat-card stat-green shadow-sm border-0 rounded-4 p-3">
                            <div class="d-flex align-items-center">
                                <div class="icon-circle bg-success me-3">
                                    <i class="fas fa-building fa-lg text-white"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0" id="beneficiaryRegisteredBarangays">33</h5>
                                    <small class="text-muted">Registered Barangays</small>
                                </div>
                            </div>
                        </div>
                    </div>
                
                    <div class="col-md-4">
                        <div class="card stat-card stat-yellow shadow-sm border-0 rounded-4 p-3">
                            <div class="d-flex align-items-center">
                                <div class="icon-circle bg-warning me-3">
                                    <i class="fas fa-map-marker-alt fa-lg text-white"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0" id="beneficiaryHighestDensity">{{ $highestDensityBarangay }}</h5>
                                    <small class="text-muted">Highest Density Barangay</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
    
                <!-- MAP + BARANGAY DISTRIBUTION -->
                <div class="row g-3 mb-4">
                    <div class="col-lg-8">
                        <div class="card card-gradient-top map-card border-0 shadow-sm h-100 position-relative overflow-hidden">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4 class="mt-3 mb-3 fw-bold"> Map Visualization</h4>
                                <div class="btn-group mt-3 mb-3">
                                    <button id="toggleBeneficiaryHeat" class="btn btn-danger btn-sm">Heatmap</button>
                                    <button id="toggleBeneficiaryMarkers" class="btn btn-primary btn-sm"> Markers</button>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div id="beneficiaryGisMap" class="rounded" style="height: 450px;"></div>
                            </div>
                        </div>
                    </div>
    
                    <div class="col-lg-4">
                        <div class="card card-gradient-top barangay-card border-0 shadow-sm h-90 position-relative overflow-hidden">
                            <div class="card-header mt-2 mb-3"><h4 class="fw-bold mt-2 mb-3"> Barangay Distribution</h4></div>
                            <div class="card-body overflow-auto" id="beneficiaryBarangayList" style="max-height:450px; background-color: #f5f5f5;"></div>
                        </div>
                    </div>
                </div>
                
                <!-- OFFICIAL BENEFICIARY LIST CARD BUTTON -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div id="showBeneficiaryTableBtn" class="beneficiary-card-btn shadow-sm rounded-4 p-4 d-flex align-items-center justify-content-between">
                    
                            <div class="d-flex align-items-center">
                                <div class="icon-circle bg-primary me-3"><i class="fas fa-list fa-lg text-white"></i></div>
                            <div>
                            <h5 class="mb-1 fw-bold">Official Solo Parent Beneficiary List</h5>
                            <small class="text-muted"> View all verified & approved solo parents in General Trias</small>
                        </div>
                    </div>
            
                    <div class="fw-semibold text-primary"> View List <i class="fas fa-arrow-right ms-1"></i></div>
            
                    </div>
                </div>
            </div>
        </div>
        
        <!-- SOLO PARENT BENEFICIARY TABLE SECTION -->
        <div id="beneficiaryTableSection" style="display:none;">
            <div class="mb-3 mt-2 text-start">
                <button id="backToOverviewBtn" class="btn fw-semibold px-4 py-2 rounded-4 shadow-sm" style="background: linear-gradient(135deg,#003366,#00509e); color:#fff;">
                    <i class="fas fa-arrow-left me-2"></i> Back to Map & Stats
                </button>
            </div>
          

        <!-- Title + Export Buttons -->
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-2">
            <h3 class="section-title fw-bold mt-2"><i class="fas fa-users me-2 "></i>Official Solo Parent Beneficiary List</h3>
            <div class="d-flex gap-2">
                <button id="beneficiaryExportPdfBtn" class="btn btn-sm btn-danger"><i class="fas fa-file-pdf"></i> PDF</button>
                <button id="beneficiaryExportExcelBtn" class="btn btn-sm btn-success"><i class="fas fa-file-csv"></i> Excel</button>
            </div>
        </div>
        
        <div class="card-body table-responsive">
            <!-- Filters -->
            <div class="row g-2 align-items-center ">
                <!-- Search -->
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search"></i></span>
                        <input type="text" id="searchInput" class="form-control border-start-0" placeholder="Search by applicant name...">
                    </div>
                </div>

                <!-- Filters -->
                <div class="col-md-6 d-flex justify-content-end">

                    <span class="input-group-text bg-white border-end-0">
                        <i class="fas fa-filter"></i>
                    </span>
                    <select id="statusFilter" class="form-select me-2">
                        <option value="all">All Status</option>
                        <option value="Pending">Pending</option>
                        <option value="Approved">Approved</option>
                        <option value="Rejected">Rejected</option>
                    </select>

                    <span class="input-group-text bg-white border-end-0">
                        <i class="fas fa-map-marker-alt"></i>
                    </span>
                    <select id="barangayFilter" class="form-select">
                        <option value="all">All Barangays</option>
                        @foreach($barangays as $brgy)
                            <option value="{{ $brgy }}">{{ $brgy }}</option>
                        @endforeach
                    </select>

                </div>
           

                <!-- Table -->
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th>#</th>
                            <th>Beneficiary Name</th>
                            <th>Barangay</th>
                            <th>Date Added</th>
                            <th>Assistance Status</th>
                            <th>Category</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="beneficiaryTable">
                        @foreach ($beneficiaries as $item)
                            <tr data-ready='@json($item)'>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->first_name }} {{ $item->last_name }}</td>
                                <td>{{ $item->barangay }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->date_added)->format('Y-m-d') }}</td>
                                <td>{{ $item->assistance_status }}</td>
                                <td>{{ $item->category ?? 'N/A' }}</td>
                                <td class="text-center">
                                    <button class="btn btn-sm view-details-btn text-white" style="background-color: #003366;" data-id="{{ $item->beneficiary_id }}">
                                        View
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-beneficiary-btn text-white" data-id="{{ $item->beneficiary_id }}">
                                        Remove
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const showTableBtn = document.getElementById('showBeneficiaryTableBtn');
    const tableSection = document.getElementById('beneficiaryTableSection');
    const overviewSection = document.getElementById('soloParentOverview');
    const backBtn = document.getElementById('backToOverviewBtn');

    // Show Table
    showTableBtn.addEventListener('click', () => {
        overviewSection.style.display = 'none';
        tableSection.style.display = 'block';
    });

    // Back to Map / Stats
    backBtn.addEventListener('click', () => {
        tableSection.style.display = 'none';
        overviewSection.style.display = 'block';
    });
});
</script>


<!-- LEAFLET AND JS -->
<link rel="stylesheet" href="{{ asset('css/leaflet.css') }}">
<script src="{{ asset('js/leaflet.js') }}"></script>
<script src="{{ asset('js/leaflet-heat.js') }}"></script>
<script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const beneficiaryTbody = document.getElementById('beneficiaryTable');
    const barangayList = document.getElementById('beneficiaryBarangayList');

    let map, markersLayer, heatLayer;
    let barangayCounts = {};
    let barangayMarkers = {};
    let markersVisible = true;
    let heatVisible = true;

    // COLOR FUNCTION
    function getColor(count) {
        if (count > 140) return '#a50026';
        if (count > 120) return '#d73027';
        if (count > 100) return '#fc8d59';
        if (count > 80) return '#fee08b';
        if (count > 50) return '#d9ef8b';
        if (count > 20) return '#91cf60';
        return '#1a9850';
    }

    // INITIALIZE MAP
    map = L.map('beneficiaryGisMap', { center: [14.3869, 120.882], zoom: 13 });
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);
    markersLayer = L.layerGroup().addTo(map);

    // HARDCODED BARANGAY DATA
    const barangayData = {
        "type": "FeatureCollection",
        "features": [
            { "type": "Feature", "properties": { "name": "Alingaro" }, "geometry": { "type": "Point", "coordinates": [120.9048787, 14.2381066] } },
            { "type": "Feature", "properties": { "name": "Arnaldo" }, "geometry": { "type": "Point", "coordinates": [120.8803019, 14.3831627] } },
            { "type": "Feature", "properties": { "name": "Bacao I" }, "geometry": { "type": "Point", "coordinates": [120.8900311, 14.3977771] } },
            { "type": "Feature", "properties": { "name": "Bacao II" }, "geometry": { "type": "Point", "coordinates": [120.8858951, 14.4100147] } },
            { "type": "Feature", "properties": { "name": "Bagumbayan" }, "geometry": { "type": "Point", "coordinates": [120.8835, 14.3798] } },
            { "type": "Feature", "properties": { "name": "Biclatan" }, "geometry": { "type": "Point", "coordinates": [120.912878, 14.2771994] } },
            { "type": "Feature", "properties": { "name": "Buenavista I" }, "geometry": { "type": "Point", "coordinates": [120.8959479, 14.3271282] } },
            { "type": "Feature", "properties": { "name": "Buenavista II" }, "geometry": { "type": "Point", "coordinates": [120.8960793, 14.3114811] } },
            { "type": "Feature", "properties": { "name": "Buenavista III" }, "geometry": { "type": "Point", "coordinates": [120.9024043, 14.3061381] } },
            { "type": "Feature", "properties": { "name": "Corregidor" }, "geometry": { "type": "Point", "coordinates": [120.8850, 14.3770] } },
            { "type": "Feature", "properties": { "name": "Dulong Bayan" }, "geometry": { "type": "Point", "coordinates": [120.8797, 14.3860] } },
            { "type": "Feature", "properties": { "name": "Governor Ferrer" }, "geometry": { "type": "Point", "coordinates": [120.8795247, 14.383427] } },
            { "type": "Feature", "properties": { "name": "Javalera" }, "geometry": { "type": "Point", "coordinates": [120.9114302, 14.2577577] } },
            { "type": "Feature", "properties": { "name": "Manggahan" }, "geometry": { "type": "Point", "coordinates": [120.9064721, 14.2971489] } },
            { "type": "Feature", "properties": { "name": "Navarro" }, "geometry": { "type": "Point", "coordinates": [120.8989409, 14.3840042] } },
            { "type": "Feature", "properties": { "name": "Panungyanan" }, "geometry": { "type": "Point", "coordinates": [120.9200361, 14.2356709] } },
            { "type": "Feature", "properties": { "name": "Pasong Camachile I" }, "geometry": { "type": "Point", "coordinates": [120.8975435, 14.3682681] } },
            { "type": "Feature", "properties": { "name": "Pasong Camachile II" }, "geometry": { "type": "Point", "coordinates": [120.9012254, 14.3595612] } },
            { "type": "Feature", "properties": { "name": "Pasong Kawayan I" }, "geometry": { "type": "Point", "coordinates": [120.8775768, 14.3458178] } },
            { "type": "Feature", "properties": { "name": "Pasong Kawayan II" }, "geometry": { "type": "Point", "coordinates": [120.8775399, 14.3263932] } },
            { "type": "Feature", "properties": { "name": "Pinagtipunan" }, "geometry": { "type": "Point", "coordinates": [120.8790989, 14.3722796] } },
            { "type": "Feature", "properties": { "name": "Prinza" }, "geometry": { "type": "Point", "coordinates": [120.8794414, 14.3798346] } },
            { "type": "Feature", "properties": { "name": "Sampalucan" }, "geometry": { "type": "Point", "coordinates": [120.8808, 14.3855] } },
            { "type": "Feature", "properties": { "name": "San Francisco" }, "geometry": { "type": "Point", "coordinates": [120.9193556, 14.3133041] } },
            { "type": "Feature", "properties": { "name": "San Gabriel" }, "geometry": { "type": "Point", "coordinates": [120.8820, 14.3821] } },
            { "type": "Feature", "properties": { "name": "San Juan I" }, "geometry": { "type": "Point", "coordinates": [120.8754271, 14.3827656] } },
            { "type": "Feature", "properties": { "name": "San Juan II" }, "geometry": { "type": "Point", "coordinates": [120.8765, 14.3802] } },
            { "type": "Feature", "properties": { "name": "Santa Clara" }, "geometry": { "type": "Point", "coordinates": [120.8848835, 14.3782211] } },
            { "type": "Feature", "properties": { "name": "Santiago" }, "geometry": { "type": "Point", "coordinates": [120.9028009, 14.3391407] } },
            { "type": "Feature", "properties": { "name": "Tapia" }, "geometry": { "type": "Point", "coordinates": [120.8789283, 14.3584905] } },
            { "type": "Feature", "properties": { "name": "Tejero" }, "geometry": { "type": "Point", "coordinates": [120.9035, 14.3332] } },
            { "type": "Feature", "properties": { "name": "Vibora" }, "geometry": { "type": "Point", "coordinates": [120.8862, 14.3745] } }
        ]
    };

    barangayData.features.forEach(f => {
        const coords = [f.geometry.coordinates[1], f.geometry.coordinates[0]];
        const name = f.properties.name;
        const marker = L.circleMarker(coords, {
            radius: 7, fillColor: getColor(0), color: '#fff', weight: 1, fillOpacity: 0.95
        }).addTo(markersLayer);
        marker.bindTooltip(`${name}<br>Solo Parents: 0`, { direction: "top" });
        marker.bindPopup(`<b>${name}</b><br>Solo Parents: 0`);
        barangayMarkers[name] = marker;
    });

    // RENDER BARANGAY LIST ALWAYS
    function renderBarangayList() {
        barangayList.innerHTML = '';
        barangayData.features.forEach(f => {
            const name = f.properties.name;
            const count = barangayCounts[name] || 0; 
            const div = document.createElement('div');
            div.className = 'barangay-item p-2 mb-1';
            div.style.borderLeft = `6px solid ${getColor(count)}`;
            div.innerHTML = `<strong>${name}</strong><br><small>Solo Parents: ${count}</small>`;
            div.addEventListener('click', () => {
                map.flyTo([f.geometry.coordinates[1], f.geometry.coordinates[0]], 16);
                barangayMarkers[name]?.openPopup();
            });
            barangayList.appendChild(div);
        });
    }

    // LOAD BENEFICIARIES
    async function loadBeneficiaries() {
        try {
            const res = await fetch("/super-admin/beneficiaries");
            const data = await res.json();
    
            beneficiaryTbody.innerHTML = '';
    
            // Reset counts to 0 for all barangays
            barangayData.features.forEach(f => barangayCounts[f.properties.name] = 0);
    
            if (data.beneficiaries && data.beneficiaries.length > 0) {
                data.beneficiaries.forEach((ben, idx) => {
                    const row = document.createElement('tr');
                    row.dataset.id = ben.beneficiary_id;
                    row.innerHTML = `
                        <td>${idx + 1}</td>
                        <td>${ben.first_name} ${ben.last_name}</td>
                        <td>${ben.barangay || '-'}</td>
                        <td>${ben.date_added?.split(' ')[0] || '-'}</td>
                        <td>${ben.assistance_status || 'N/A'}</td>
                        <td>${ben.category || 'N/A'}</td>
                        <td class="text-center">Actions...</td>
                    `;
                    beneficiaryTbody.appendChild(row);
    
                    if (ben.barangay && barangayCounts[ben.barangay] !== undefined) {
                        barangayCounts[ben.barangay]++;
                    }
                });
            }
    
            // ALWAYS render barangay list
            renderBarangayList();
            refreshMap();
        } catch (err) {
            console.error(err);
            // In case of error, still show barangays with 0
            barangayData.features.forEach(f => barangayCounts[f.properties.name] = 0);
            renderBarangayList();
        }
    }

    // MAP REFRESH & STATS UPDATE
    function refreshMap() {
        let totalSolo = 0;
        let maxBarangay = { name: '', count: 0 };

        Object.keys(barangayMarkers).forEach(name => {
            const marker = barangayMarkers[name];
            const count = barangayCounts[name] || 0;

            marker.bindTooltip(`${name}<br>Solo Parents: ${count}`, { direction: "top" });
            marker.bindPopup(`<b>${name}</b><br>Solo Parents: ${count}`);

            if (marker.heatCircle) map.removeLayer(marker.heatCircle);

            if (count > 0 && heatVisible) {
                const f = barangayData.features.find(f => f.properties.name === name);
                if (f) {
                    const lat = f.geometry.coordinates[1];
                    const lng = f.geometry.coordinates[0];
                    const radius = 50 + count * 5;
                    const heatCircle = L.circle([lat, lng], {
                        radius: radius,
                        color: getColor(count),
                        fillColor: getColor(count),
                        fillOpacity: 0.3,
                        weight: 0
                    }).addTo(map);
                    marker.heatCircle = heatCircle;
                }
            }

            if (count > maxBarangay.count) maxBarangay = { name, count };
            totalSolo += count;
        });

        document.getElementById('beneficiaryTotalValue').textContent = totalSolo;
        document.getElementById('beneficiaryRegisteredBarangays').textContent = Object.keys(barangayCounts).length;
        document.getElementById('beneficiaryHighestDensity').textContent =
            maxBarangay.count > 0 ? `${maxBarangay.name} (${maxBarangay.count})` : 'None';
    }

    // TOGGLE HEAT & MARKERS
    document.getElementById("toggleBeneficiaryHeat")?.addEventListener("click", () => {
        heatVisible = !heatVisible;
        refreshMap();
        const btn = document.getElementById("toggleBeneficiaryHeat");
        btn.textContent = heatVisible ? "Hide Heatmap" : "Show Heatmap";
    });

    document.getElementById("toggleBeneficiaryMarkers")?.addEventListener("click", () => {
        markersVisible = !markersVisible;
        if (markersVisible) { map.addLayer(markersLayer); this.textContent = "🔘 Hide Markers"; }
        else { map.removeLayer(markersLayer); this.textContent = "🔘 Show Markers"; }
    });

    // ADD LEGEND
    function addMapLegend() {
        const legend = L.control({ position: 'bottomright' });
        legend.onAdd = function(map) {
            const div = L.DomUtil.create('div', 'info legend');
            const grades = [0, 20, 50, 80, 100, 120, 140];
            div.style.background = 'white';
            div.style.padding = '10px';
            div.style.borderRadius = '8px';
            div.style.boxShadow = '0 0 15px rgba(0,0,0,0.2)';
            div.innerHTML += `<strong>Solo Parents Count</strong><br>`;
            for (let i = 0; i < grades.length; i++) {
                const from = grades[i];
                const to = grades[i + 1];
                div.innerHTML +=
                    `<i style="background:${getColor(from + 1)};width:18px;height:18px;display:inline-block;margin-right:6px;border-radius:3px;"></i> ` +
                    from + (to ? `&ndash;${to}<br>` : '+');
            }
            return div;
        };
        legend.addTo(map);
    }

    addMapLegend();
    loadBeneficiaries();

    // FILTER & RESET
    const searchInput = document.getElementById('beneficiarySearch');
    const dateInput = document.getElementById('beneficiaryFilterDate');
    const brgySelect = document.getElementById('beneficiaryFilterBrgy');
    const resetBtn = document.getElementById('beneficiaryReset');

    function filterTable() {
        const search = searchInput.value.toLowerCase();
        const date = dateInput.value;
        const brgy = brgySelect.value;
        Array.from(beneficiaryTbody.querySelectorAll('tr')).forEach(row => {
            const name = row.cells[1].textContent.toLowerCase();
            const barangay = row.cells[2].textContent;
            const rowDate = row.cells[3].textContent;
            const matches = (name.includes(search)) &&
                            (brgy === 'all' || barangay === brgy) &&
                            (!date || rowDate === date);
            row.style.display = matches ? '' : 'none';
        });
    }

    searchInput.addEventListener('input', filterTable);
    dateInput.addEventListener('change', filterTable);
    brgySelect.addEventListener('change', filterTable);
    resetBtn.addEventListener('click', () => { searchInput.value=''; dateInput.value=''; brgySelect.value='all'; filterTable(); });

});
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {

    // VIEW BENEFICIARY MODAL (DATASET ONLY)
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.view-details-btn');
        if (!btn) return;

        const row = btn.closest('tr');
        if (!row || !row.dataset.ready) {
            alert('Beneficiary data not found.');
            return;
        }

        const data = JSON.parse(row.dataset.ready);
        const modalEl = document.getElementById('beneficiaryViewModal');

        modalEl.querySelector('#beneficiaryViewName').textContent =
            `${data.first_name ?? ''} ${data.last_name ?? ''}`;
        modalEl.querySelector('#beneficiaryViewAddress').textContent =
            data.address ?? 'N/A';
        modalEl.querySelector('#beneficiaryViewBarangay').textContent =
            data.barangay ?? 'N/A';
        modalEl.querySelector('#beneficiaryViewStatus').textContent =
            data.assistance_status ?? 'N/A';
        modalEl.querySelector('#beneficiaryViewCategory').textContent =
            data.category ?? 'N/A';
        modalEl.querySelector('#beneficiaryViewCreatedAt').textContent =
            data.date_added ?? 'N/A';

        // Benefits
        const benefitsBox = modalEl.querySelector('#beneficiaryViewBenefits');
        benefitsBox.innerHTML = '';
        if (Array.isArray(data.benefits) && data.benefits.length > 0) {
            data.benefits.forEach(b => {
                const badge = document.createElement('span');
                badge.className = 'badge bg-success me-1';
                badge.textContent = b;
                benefitsBox.appendChild(badge);
            });
        } else {
            benefitsBox.innerHTML =
                `<span class="badge bg-secondary">No benefits listed</span>`;
        }

        new bootstrap.Modal(modalEl).show();
    });

    // DELETE BENEFICIARY MODAL

    let deleteId = null;
    let deleteRow = null;

    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.delete-beneficiary-btn');
        if (!btn) return;

        deleteId = btn.dataset.id;
        deleteRow = btn.closest('tr');

        new bootstrap.Modal(
            document.getElementById('beneficiaryDeleteModal')
        ).show();
    });

    document.getElementById('confirmDeleteBeneficiary')
        .addEventListener('click', async function () {
            if (!deleteId) return;

            this.disabled = true;
            this.textContent = 'Deleting...';

            try {
                const res = await fetch(`/admin/beneficiaries/${deleteId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector(
                            'meta[name="csrf-token"]'
                        ).content
                    }
                });

                if (!res.ok) throw new Error('Delete failed');
                deleteRow.remove();

                bootstrap.Modal.getInstance(
                    document.getElementById('beneficiaryDeleteModal')
                ).hide();
            } catch (err) {
                alert(err.message);
            }

            this.disabled = false;
            this.textContent = 'Delete';
            deleteId = null;
            deleteRow = null;
        });

    // SOLO PARENT EXPORT MODALS (PDF & EXCEL)
    const pdfModal = new bootstrap.Modal(document.getElementById('soloParentPdfExportModal'));
    const excelModal = new bootstrap.Modal(document.getElementById('soloParentExcelExportModal'));

    document.getElementById('beneficiaryExportPdfBtn')?.addEventListener('click', () => pdfModal.show());
    document.getElementById('beneficiaryExportExcelBtn')?.addEventListener('click', () => excelModal.show());

    document.getElementById('confirmSoloParentPdfExport')?.addEventListener('click', () => {
        const year = document.getElementById('soloParentPdfYear').value;
        const month = document.getElementById('soloParentPdfMonth').value;
    
        // Use exact Laravel route
        let url = `{{ route('beneficiaries.export.pdf') }}?year=${year}&month=${month}`;
        window.location.href = url; 
    
        pdfModal.hide();
    });

    document.getElementById('confirmSoloParentExcelExport')?.addEventListener('click', () => {
        const year = document.getElementById('soloParentExcelYear').value;
        const month = document.getElementById('soloParentExcelMonth').value;
    
        let url = `{{ route('beneficiaries.export.excel') }}?year=${year}&month=${month}`;
        window.location.href = url;
    
        excelModal.hide();
    });

    // LEAFLET MAP (SAFE)
    if (document.getElementById('beneficiaryGisMap')) {
        const map = L.map('beneficiaryGisMap')
            .setView([14.3869, 120.8810], 12);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19
        }).addTo(map);
    }

});
</script>


<!-- USER MANAGEMENT SECTION -->
<div id="user-management-section" class="content-section" style="display:none;">
    <div class="admin-container">

        <!-- ACTIVE USERS VIEW -->
        <div id="active-users-view">
            <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-1"><i class="fas fa-users me-2"></i>User Management</h3>
                <p class="text-muted mb-0">Manage system users, roles, and account status</p>
            </div>
    
            <div class="d-flex gap-2">
                <button class="btn btn-outline-danger" id="showDeactivatedBtn"><i class="fas fa-user-slash me-1"></i> Deactivated Accounts</button>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal"><i class="fas fa-user-plus me-1"></i> Add User</button>
            </div>
        </div>
    
        <div class="row g-3 mb-4" id="userStats">
            <div class="col-md-3">
                <div class="card stat-card stat-blue shadow-sm border-0 rounded-4 p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle bg-primary me-3">
                            <i class="fas fa-users fa-lg text-white"></i>
                        </div>
                        <div>
                            <h5 class="mb-0" id="totalUsers">{{ $userStats['total'] }}</h5>
                            <small class="text-muted">Total Users</small>
                        </div>
                    </div>
                </div>
            </div>
    
            <div class="col-md-3">
                <div class="card stat-card stat-green shadow-sm border-0 rounded-4 p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle bg-success me-3">
                            <i class="fas fa-user-check fa-lg text-white"></i>
                        </div>
                        <div>
                            <h5 class="mb-0" id="activeUsers">{{ $userStats['active'] }}</h5>
                            <small class="text-muted">Active Users</small>
                        </div>
                    </div>
                </div>
            </div>
    
            <div class="col-md-3">
                <div class="card stat-card stat-red shadow-sm border-0 rounded-4 p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle bg-danger me-3">
                            <i class="fas fa-user-slash fa-lg text-white"></i>
                        </div>
                        <div>
                            <h5 class="mb-0" id="deactivatedUsers">{{ $userStats['inactive'] }}</h5>
                            <small class="text-muted">Deactivated Users</small>
                        </div>
                    </div>
                </div>
            </div>
    
            <div class="col-md-3">
                <div class="card stat-card stat-yellow shadow-sm border-0 rounded-4 p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle bg-warning me-3">
                            <i class="fas fa-user-shield fa-lg text-white"></i>
                        </div>
                        <div>
                            <h5 class="mb-0" id="adminUsers">{{ $userStats['admins'] }}</h5>
                            <small class="text-muted">Admin Users</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
        <!-- FILTERS -->
        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <select id="roleFilter" class="form-select">
                    <option value="all">All Roles</option>
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                    <option value="super_admin">Super Admin</option>
                </select>
            </div>
    
            <div class="col-md-4">
                <select id="statusFilter" class="form-select">
                    <option value="all">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
    
            <div class="col-md-4">
                <input type="text" id="userSearch" class="form-control" placeholder="Search by name or email">
            </div>
        </div>
    
        <!-- TABLE -->
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                      <th>#</th>
                      <th>Name</th>
                      <th>Email</th>
                      <th>Role</th>
                      <th>Status</th>
                      <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody id="userTableBody"></tbody>
            </table>
        </div>
        <!-- PAGINATION -->
        <nav><ul class="pagination justify-content-center" id="userPagination"></ul></nav>
        </div>
    
        <!-- DEACTIVATED USERS VIEW -->
        <div id="deactivated-users-view" style="display:none;">
    
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold"><i class="fas fa-user-slash text-danger me-2"></i>Deactivated Accounts </h4>
                    <p class="text-muted mb-0"> Users that are currently disabled </p>
                </div>
                <button class="btn btn-outline-primary" id="backToUsersBtn"><i class="fas fa-arrow-left me-1"></i> Back </button>
            </div>
        
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th> 
                        </tr>
                    </thead>
                    <tbody id="deactivatedUserTableBody"></tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const USERS_URL = "/super-admin/users";
    const PER_PAGE = 5;

    let users = [];
    let filteredUsers = [];
    let currentPage = 1;

    const tableBody = document.getElementById("userTableBody");
    const pagination = document.getElementById("userPagination");

    const searchInput = document.getElementById("userSearch");
    const roleFilter = document.getElementById("roleFilter");
    const statusFilter = document.getElementById("statusFilter");

    const activeView = document.getElementById("active-users-view");
    const deactivatedView = document.getElementById("deactivated-users-view");
    const deactivatedTableBody = document.getElementById("deactivatedUserTableBody");

    // FETCH USERS
    async function fetchUsers() {
        const res = await fetch(USERS_URL);
        users = await res.json();
        filteredUsers = users.filter(u => u.status.toLowerCase() === "active");
        render();
        updateStats();
    }

    // RENDER
    function render() {
        renderTable();
        renderPagination();
    }

    function renderTable() {
        tableBody.innerHTML = "";
        const start = (currentPage - 1) * PER_PAGE;
        const pageUsers = filteredUsers.slice(start, start + PER_PAGE);

        if (!pageUsers.length) {
            tableBody.innerHTML = `<tr>
                <td colspan="6" class="text-center text-muted">No users found</td>
            </tr>`;
            return;
        }

        pageUsers.forEach((u, i) => {
            tableBody.innerHTML += `
                <tr>
                    <td>${start + i + 1}</td>
                    <td>${u.first_name} ${u.middle_name} ${u.last_name}</td>
                    <td>${u.email}</td>
                    <td><span class="badge bg-${roleColor(u.role)}">${formatRole(u.role)}</span></td>
                    <td><span class="badge bg-success">Active</span></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-info me-1" onclick="viewUser(${u.id})">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-warning me-1" onclick="editUser(${u.id})">
                            <i class="fas fa-pen"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deactivateUser(${u.id})">
                            <i class="fas fa-user-slash"></i>
                        </button>
                    </td>
                </tr>`;
        });
    }

    function renderPagination() {
        pagination.innerHTML = "";
        const pages = Math.ceil(filteredUsers.length / PER_PAGE);
        for (let i = 1; i <= pages; i++) {
            pagination.innerHTML += `<li class="page-item ${i === currentPage ? 'active' : ''}">
                <button class="page-link" onclick="goToPage(${i})">${i}</button>
            </li>`;
        }
    }

    window.goToPage = page => {
        currentPage = page;
        render();
    };

    // FILTERS
    function applyFilters() {
        const search = searchInput.value.toLowerCase();
        const role = roleFilter.value;
        const status = statusFilter.value;

        filteredUsers = users.filter(u => {
            const matchSearch =
                u.first_name.toLowerCase().includes(search) ||
                u.middle_name.toLowerCase().includes(search) ||
                u.last_name.toLowerCase().includes(search) ||
                u.username.toLowerCase().includes(search) ||
                u.email.toLowerCase().includes(search);

            const matchRole = role === "all" || u.role === role;
            const matchStatus = status === "all" || u.status.toLowerCase() === status;

            return matchSearch && matchRole && matchStatus;
        });

        currentPage = 1;
        render();
    }

    searchInput.addEventListener("input", applyFilters);
    roleFilter.addEventListener("change", applyFilters);
    statusFilter.addEventListener("change", applyFilters);

    // VIEW TOGGLE
    document.getElementById("showDeactivatedBtn").onclick = () => {
        activeView.style.display = "none";
        deactivatedView.style.display = "block";
        renderDeactivated();
    };
    document.getElementById("backToUsersBtn").onclick = () => {
        deactivatedView.style.display = "none";
        activeView.style.display = "block";
    };

    function renderDeactivated() {
        deactivatedTableBody.innerHTML = "";
        const list = users.filter(u => u.status.toLowerCase() === "inactive");

        if (!list.length) {
            deactivatedTableBody.innerHTML = `<tr>
                <td colspan="6" class="text-center text-muted">No deactivated users</td>
            </tr>`;
            return;
        }

        list.forEach((u, i) => {
            deactivatedTableBody.innerHTML += `
                <tr>
                    <td>${i + 1}</td>
                    <td>${u.first_name} ${u.middle_name} ${u.last_name}</td>
                    <td>${u.email}</td>
                    <td><span class="badge bg-${roleColor(u.role)}">${formatRole(u.role)}</span></td>
                    <td><span class="badge bg-secondary">Inactive</span></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-success" onclick="activateUser(${u.id})">
                            <i class="fas fa-user-check me-1"></i> Activate
                        </button>
                    </td>
                </tr>`;
        });
    }

    // UTILITIES
    function formatRole(role) {
        return role.replace("_", " ").toUpperCase();
    }
    function roleColor(role) {
        if (role === "super_admin") return "danger";
        if (role === "admin") return "primary";
        return "secondary";
    }
    function updateStats() {
        document.getElementById("totalUsers").textContent = users.length;
        document.getElementById("activeUsers").textContent = users.filter(u => u.status.toLowerCase() === "active").length;
        document.getElementById("deactivatedUsers").textContent = users.filter(u => u.status.toLowerCase() === "inactive").length;
        document.getElementById("adminUsers").textContent = users.filter(u => u.role.toLowerCase() === "admin").length;
    }

    // MODAL FUNCTIONS
    window.viewUser = function(id) {
        const user = users.find(u => u.id === id);
        if (!user) return;

        document.getElementById('viewUserName').textContent = `${user.first_name} ${user.middle_name} ${user.last_name}`;
        document.getElementById('viewUserStatus').textContent = user.status;
        document.getElementById('viewUserId').textContent = `#${user.id}`;
        document.getElementById('viewUserEmail').textContent = user.email;
        document.getElementById('viewUserRole').textContent = user.role.toUpperCase();
        document.getElementById('viewUserCreated').textContent = new Date(user.created_at).toLocaleDateString();

        new bootstrap.Modal(document.getElementById('viewUserModal')).show();
    };

    window.editUser = function(id) {
        const user = users.find(u => u.id === id);
        if (!user) return;
    
        console.log(user); // debug
    
        // Safe display function
        function safeField(value) {
            return value && value.toString().trim() !== '' ? value : '-';
        }
    
        // Map modal fields to DB keys
        const fieldMap = {
            FirstName: 'first_name',
            MiddleName: 'middle_name',
            LastName: 'last_name',
            Username: 'username',
            Email: 'email',
            Contact: 'contact',
            Avatar: 'avatar',
            Street: 'street',
            Barangay: 'barangay',
            MunicipalityCity: 'municipality_city',
            Province: 'province'
        };
    
        // Fill input fields
        for (const [field, key] of Object.entries(fieldMap)) {
            const el = document.getElementById(`edit${field}`);
            if (el) el.value = safeField(user[key]);
        }
    
    // Avatar preview with gradient initials if no avatar
    const avatarPreview = document.getElementById('editAvatarPreview');
    if (avatarPreview) {
        if (user.avatar && user.avatar.trim() !== '') {
            // If user has uploaded avatar, show it
            avatarPreview.style.backgroundImage = 'none';
            avatarPreview.style.backgroundColor = 'transparent';
            avatarPreview.textContent = '';
            avatarPreview.innerHTML = `<img src="${user.avatar}" alt="Avatar" class="rounded-circle" style="width:100%; height:100%; object-fit:cover;">`;
        } else {
            // Generate initials
            const initials = `${user.first_name?.[0] || ''}${user.last_name?.[0] || ''}`.toUpperCase();
            avatarPreview.textContent = initials;
    
            // Generate gradient color based on user's name hash
            const colors = [
                '#F28B82','#FBBC04','#FFF475','#CCFF90','#A7FFEB',
                '#CBF0F8','#AECBFA','#D7AEFB','#FDCFE8','#E6C9A8'
            ];
            const hash = Array.from(user.first_name + user.last_name).reduce((acc, char) => acc + char.charCodeAt(0), 0);
            const color1 = colors[hash % colors.length];
            const color2 = colors[(hash + 3) % colors.length];
    
            avatarPreview.style.backgroundImage = `linear-gradient(135deg, ${color1}, ${color2})`;
            avatarPreview.style.display = 'flex';
            avatarPreview.style.alignItems = 'center';
            avatarPreview.style.justifyContent = 'center';
        }
    }


    document.getElementById('editUserRole').value = safeField(user.role);

    const roleCards = document.querySelectorAll('.role-card');
    const saveBtn = document.getElementById('saveUserChanges');

    // Highlight role
    roleCards.forEach(c => c.classList.remove('active', 'disabled'));
    const roleCard = document.querySelector(`.role-card[data-role="${user.role}"]`);
    if(roleCard) roleCard.classList.add('active');

    // RESTRICT EDITING BASED ON RULES
    const alwaysReadonly = ['editFirstName','editMiddleName','editLastName','editUsername','editEmail','editAvatar','editProvince'];
    alwaysReadonly.forEach(f => {
        const el = document.getElementById(f);
        if(el) el.readOnly = true;
    });

    // Only contact, street, barangay, municipality_city can be edited
    ['editContact','editStreet','editBarangay','editMunicipalityCity'].forEach(f => {
        const el = document.getElementById(f);
        if(el) el.readOnly = false;
    });

    // Disable role cards for super_admin users
    if(user.role === 'super_admin'){
        roleCards.forEach(c => c.classList.add('disabled'));
        saveBtn.disabled = true;
    } else {
        roleCards.forEach(c => c.classList.remove('disabled'));
        saveBtn.disabled = false;
    }

    // Role card click handler
    roleCards.forEach(card => {
        card.onclick = () => {
            if(card.classList.contains('disabled')) return;
            roleCards.forEach(c => c.classList.remove('active'));
            card.classList.add('active');
            document.getElementById('editUserRole').value = card.dataset.role;
        };
    });
    
        // Show modal
        new bootstrap.Modal(document.getElementById('editUserModal')).show();
    };


    window.deactivateUser = function(id) {
        const user = users.find(u => u.id === id);
        if(!user) return;
        document.getElementById('deactivateUserName').textContent = `${user.first_name} ${user.middle_name} ${user.last_name}`;
        document.getElementById('confirmDeactivateUser').dataset.userId = user.id;
        new bootstrap.Modal(document.getElementById('deactivateUserModal')).show();
    };

    document.getElementById('confirmDeactivateUser').addEventListener('click', async function(){
        const userId = this.dataset.userId;
        try {
            const res = await fetch(`${USERS_URL}/${userId}/deactivate`, {
                method:'PATCH',
                headers:{'Content-Type':'application/json'},
            });
            if(!res.ok) throw new Error('Failed to deactivate user');

            users = users.map(u => u.id==userId ? {...u, status:'inactive'} : u);
            filteredUsers = users.filter(u => u.status.toLowerCase() === 'active');
            render(); renderDeactivated(); updateStats();

            bootstrap.Modal.getInstance(document.getElementById('deactivateUserModal')).hide();
            activeView.style.display = "none"; deactivatedView.style.display = "block";
        } catch(err){ alert(err.message); }
    });

    window.activateUser = function(id){
        const user = users.find(u => u.id===id);
        if(!user) return;
        document.getElementById('activateUserName').textContent = `${user.first_name} ${user.middle_name} ${user.last_name}`;
        document.getElementById('confirmActivateUser').dataset.userId = id;
        new bootstrap.Modal(document.getElementById('activateUserModal')).show();
    };

    // INIT
    fetchUsers();
});
</script>



<!-- SUPER ADMIN BENEFITS SECTION -->
<div id="benefits-section" class="content-section" style="display:none;">
    <div class="admin-container d-flex flex-column">
        <div>
            <h3 class="fw-bold mb-1 mt-2">Benefits & Schedule Management</h3>
            <p class="text-muted">Manage beneficiaries, payout schedules, and monitor assistance status across all barangays.</p>
        </div>

        <!-- STATS SUMMARY -->
        <div class="row g-3 mb-4" id="superStats">
            <div class="col-md-3">
                <div class="card stat-card stat-blue shadow-sm border-0 rounded-4 p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle bg-primary me-3">
                            <i class="fas fa-users fa-lg text-white"></i>
                        </div>
                        <div>
                            <h5 class="mb-0" id="superTotalBeneficiaries">25</h5>
                            <small class="text-muted">Total Beneficiaries</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card stat-card stat-green shadow-sm border-0 rounded-4 p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle bg-success me-3">
                            <i class="fas fa-hourglass-half fa-lg text-white"></i>
                        </div>
                        <div>
                            <h5 class="mb-0" id="superPendingBeneficiaries">0</h5>
                            <small class="text-muted">Pending</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card stat-card stat-yellow shadow-sm border-0 rounded-4 p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle bg-warning me-3">
                            <i class="fas fa-hand-holding fa-lg text-white"></i>
                        </div>
                        <div>
                            <h5 class="mb-0" id="superReceivedBeneficiaries">0</h5>
                            <small class="text-muted">Received</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card stat-card stat-red shadow-sm border-0 rounded-4 p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle bg-danger me-3">
                            <i class="fas fa-map-marker-alt fa-lg text-white"></i>
                        </div>
                        <div>
                            <h5 class="mb-0" id="superScheduledBarangays">4</h5>
                            <small class="text-muted">Scheduled Barangays</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex">
            <!-- LEFT PANEL: Barangay List -->
            <div class="col-left">
                <h4 class="section-title fw-bold mb-2 mt-2">🏘️ Barangays in General Trias</h4>
                <small style="color:#fff;">Click a barangay to view its beneficiaries</small>
                <div class="row mt-3" id="barangay-list-container"></div>
            </div>

            <!-- RIGHT PANEL -->
            <div class="col-right">
                <h4 id="category-title" class="fw-bold mb-3">Schedule Payout per Barangay</h4>

                <!-- Schedule Cards -->
                <div id="schedule-panel">
                    <div class="schedule-card-grid" id="schedule-card-container"></div>
            
                <nav aria-label="Schedule Pagination" class="mt-3"><ul class="pagination justify-content-center" id="schedule-pagination"></ul>/nav>
            
                </div>

                <!-- Beneficiaries Table -->
                <div id="barangay-beneficiaries-panel" style="display:none;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <button class="btn btn-sm btn-secondary" id="back-to-schedule">← Back to Schedule</button>
                    </div>

                <!-- Toolbar -->
                <div class="d-flex gap-2 mb-3" id="toolbar">
                    <div style="flex:1; max-width:320px;">
                    <label class="form-label mb-1">Search by Name</label>
                    <input id="global-search" class="form-control form-control-sm" placeholder="Search beneficiaries by name" style="display:none;">
                    </div>
                    <div style="margin-left:auto; display:flex; gap:8px; align-items:end;">
                        <div>
                            <label class="form-label mb-1">Filter by Category</label>
                            <select id="filter-category" class="form-select form-select-sm">
                            <option value="">All</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label mb-1">Filter by Status</label>
                            <select id="filter-status" class="form-select form-select-sm">
                            <option value="">All</option>
                            <option value="pending">Pending</option>
                            <option value="received">Received</option>
                            </select>
                        </div>
                    </div>
                </div>
    
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle" id="beneficiaries-table">
                    <thead class="table-light">
                        <tr>
                        <th>Name</th>
                        <th>Address</th>
                        <th>Auto Benefits</th>
                        <th>Solo Parent Category</th>
                        <th>Status Receive</th>
                        <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="beneficiaries-list"></tbody>
                    </table>
                </div>
    
                <nav aria-label="Beneficiaries Pagination" style="position: sticky; bottom: 0; background: white; padding-top: 5px;">
                    <ul class="pagination" id="beneficiary-pagination"></ul>
                </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let barangays = [];
    let scheduleBarangays = [];
    let selectedBarangayId = null;
    
    
    let currentPage = 1;
    const cardsPerPage = 6;
    
    // On DOM Load
    document.addEventListener("DOMContentLoaded", () => {
        loadBarangays();
        loadScheduledBarangays();
    });

    async function loadBarangays() {
        const container = document.getElementById('barangay-list-container');
        container.innerHTML = `<div class="text-center text-white py-3"><small>Loading barangays...</small></div>`;
    
        try {
            const res = await fetch(`/super-admin/barangays`);
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            let fetchedBarangays = await res.json();
    
            // Ensure undefined/missing barangays are included
            const undefinedBarangays = fetchedBarangays.filter(b => !b.barangay || b.barangay.trim() === '');
            undefinedBarangays.forEach(b => b.barangay = 'Undefined');
    
            barangays = fetchedBarangays;
    
            container.innerHTML = '';
            if (!barangays.length) {
                container.innerHTML = `<small class="text-white">No barangays found</small>`;
                return;
            }
    
            barangays.forEach(b => {
                const div = document.createElement('div');
                div.className = 'col-12';
                div.innerHTML = `
                    <div class="barangay-btn" data-id="${b.id}" data-barangay="${b.barangay}">
    
                        <span>${b.barangay}</span>
                        <span class="barangay-count">${b.total_beneficiaries ?? 0}</span>
                    </div>
                `;
                container.appendChild(div);
            });
    
            attachBarangayClickEvents();
    
        } catch (err) {
            console.error("Failed to load barangays:", err);
            container.innerHTML = `<small class="text-danger">Failed to load barangays</small>`;
        }
    }

    // Highlight Selected Barangay
    function selectBarangay(id, barangay) {
        selectedBarangayId = id;
        document.querySelectorAll('.barangay-btn').forEach(btn => btn.classList.remove('active'));
        const selectedBtn = document.querySelector(`.barangay-btn[data-id='${id}']`);
        if (selectedBtn) selectedBtn.classList.add('active');
    }

    function attachBarangayClickEvents() {
        document.querySelectorAll('.barangay-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const barangay = btn.dataset.barangay; 
                selectBarangay(btn.dataset.id, barangay);
                showBarangayBeneficiaries(barangay);
            });
        });
    }
    
    function showBarangayBeneficiaries(barangay) {
        console.log('Barangay sent to backend:', barangay);
        document.getElementById('schedule-panel').style.display = 'none';
        document.getElementById('barangay-beneficiaries-panel').style.display = 'block';
        document.getElementById('category-title').textContent = `Beneficiaries – ${barangay}`;
        document.getElementById('global-search').style.display = 'block';
    
        const tbody = document.getElementById('beneficiaries-list');
        tbody.innerHTML = `<tr><td colspan="6" class="text-center">Loading...</td></tr>`;
    
        fetch(`/super-admin/solo-parent-beneficiaries-by-barangay/${encodeURIComponent(barangay)}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            tbody.innerHTML = '';
    
            if (!data.length) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-muted text-center">
                            No beneficiaries found
                        </td>
                    </tr>`;
                return;
            }
    
            data.forEach(b => {
    
                // STATUS BADGE
                const statusBadge = b.assistance_status === 'Received'
                    ? `<span class="badge bg-success">Received</span>`
                    : `<span class="badge bg-warning text-dark">Pending</span>`;
    
                // BENEFITS BADGES
                let benefitsHTML = '—';
                try {
                    const benefitsArray = JSON.parse(b.selected_benefits);
                    if (Array.isArray(benefitsArray) && benefitsArray.length > 0) {
                        benefitsHTML = benefitsArray.map(benefit => 
                            `<span class="badge bg-success me-1" style="font-size:0.75rem; padding:0.35em 0.5em;">${benefit}</span>`
                        ).join(' ');
                    } else if (benefitsArray) {
                        benefitsHTML = `<span class="badge bg-success" style="font-size:0.75rem; padding:0.35em 0.5em;">${benefitsArray}</span>`;
                    }
                } catch {
                    if (b.selected_benefits) {
                        benefitsHTML = `<span class="badge bg-success" style="font-size:0.75rem; padding:0.35em 0.5em;">${b.selected_benefits}</span>`;
                    }
                }
    
                // ACTION BUTTON
                const actionBtn = `
                    <button class="btn btn-sm btn-outline-primary"
                        onclick="viewAssistanceHistory(${b.beneficiary_id})">
                        <i class="fas fa-history"></i> View History
                    </button>
                `;
    
                tbody.innerHTML += `
                    <tr>
                        <td>${b.first_name} ${b.last_name}</td>
                        <td>${b.street}, ${b.barangay}, ${b.municipality}</td>
                        <td>${benefitsHTML}</td>
                        <td>${b.category}</td>
                        <td>${statusBadge}</td>
                        <td>${actionBtn}</td>
                    </tr>
                `;
            });
        })
        .catch(err => {
            console.error(err);
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-danger text-center">
                        Failed to load data
                    </td>
                </tr>`;
        });
    }
    
    // Load Scheduled Barangays for Right Panel
    async function loadScheduledBarangays() {
        const container = document.getElementById('schedule-card-container');
        container.innerHTML = `<div class="text-center py-3"><small>Loading scheduled payouts...</small></div>`;
    
        try {
            const res = await fetch(`/super-admin/scheduled-barangays`);
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            const schedules = await res.json();
    
            if (!schedules.length) {
                container.innerHTML = `<small class="text-muted">No scheduled barangays found</small>`;
                return;
            }
    
            // Aggregate schedules per barangay
            const allBarangayNames = barangays.map(b => b.barangay);
            scheduleBarangays = allBarangayNames.map(barangay => {
                const item = schedules.find(s => s.barangay === barangay);
                return {
                    barangay: barangay,
                    total_beneficiaries: item?.total_beneficiaries ?? 0,
                    received_count: item?.received_count ?? 0,
                    scheduled_date: item?.scheduled_date ?? '-',
                    scheduled_time: item?.scheduled_time ?? '-',
                    location: item?.location ?? '-',
                    status: item?.status ?? 'Pending'
                };
            });
    
            renderScheduleCards();
    
        } catch (err) {
            console.error("Failed to load scheduled barangays:", err);
            container.innerHTML = `<small class="text-danger">Failed to load scheduled payouts</small>`;
        }
    }
    
    // Generate unique color for each barangay
    function generateBarangayColors(barangays) {
        const colors = {};
        const total = barangays.length;
    
        barangays.forEach((b, index) => {
            const hue = Math.round((index / total) * 360);
            colors[b] = `hsl(${hue}, 70%, 50%)`; 
        });
    
        return colors;
    }
    
    // Render Schedule Cards with Unique Colors
    function renderScheduleCards() {
        const container = document.getElementById('schedule-card-container');
        const pagination = document.getElementById('schedule-pagination');
        container.innerHTML = '';
        pagination.innerHTML = '';
    
        // Generate unique colors for all barangays
        const allBarangayNames = scheduleBarangays.map(s => s.barangay);
        const barangayColors = generateBarangayColors(allBarangayNames);
    
        const start = (currentPage - 1) * cardsPerPage;
        const end = start + cardsPerPage;
        const paginatedData = scheduleBarangays.slice(start, end);
    
        paginatedData.forEach(s => {
            const color = barangayColors[s.barangay];
            const card = document.createElement('div');
            card.className = 'schedule-card';
            card.dataset.barangay = s.barangay;
            card.style.borderLeft = `6px solid ${color}`;
            card.innerHTML = `
                <div class="schedule-header">
                    <h5 style="color:${color}">${s.barangay}</h5>
                    <span class="badge-soft ${s.status === 'Pending' ? 'badge-yellow' : 'badge-green'}">${s.status}</span>
                </div>
                <div class="schedule-body">
                    <div class="schedule-row"><span>No. of Beneficiaries:</span> <strong>${s.total_beneficiaries}</strong></div>
                    <div class="schedule-row"><span>Schedule Date:</span> <strong>${s.scheduled_date} ${s.scheduled_time}</strong></div>
                    <div class="schedule-row"><span>Payout Location:</span> <strong>${s.location}</strong></div>
                    <div class="schedule-row"><span>Beneficiaries Received:</span> <strong>${s.received_count}</strong></div>
                </div>
                <div class="schedule-footer">
                    <button class="btn btn-sm btn-primary view-schedule-btn">View Schedule Details</button>
                </div>
            `;
            container.appendChild(card);
        });
    
        // Pagination
        const totalPages = Math.ceil(scheduleBarangays.length / cardsPerPage);
        for (let i = 1; i <= totalPages; i++) {
            const li = document.createElement('li');
            li.className = `page-item ${i === currentPage ? 'active' : ''}`;
            li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
            li.addEventListener('click', (e) => {
                e.preventDefault();
                currentPage = i;
                renderScheduleCards();
            });
            pagination.appendChild(li);
        }
    
        // Attach modal events
        document.querySelectorAll('.view-schedule-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const barangay = this.closest('.schedule-card').dataset.barangay;
                openScheduleModal(barangay);
            });
        });
    }

    function openScheduleModal(barangay) {
        const modalEl = document.getElementById('barangayScheduleModal');
        const modal = new bootstrap.Modal(modalEl);
    
        // Fetch schedule details for this barangay
        fetch(`/super-admin/payout-schedule-by-barangay/${encodeURIComponent(barangay)}`)
        .then(res => res.json())
        .then(schedule => {
            if (!schedule) throw new Error("No schedule found");
    
            // Update modal schedule info
            document.getElementById('modalBarangayTitle').textContent = `Schedule Details - ${barangay}`;
            document.getElementById('modalBarangayName').textContent = schedule.barangay;
            document.getElementById('modalScheduleDate').textContent = `${schedule.scheduled_date} ${schedule.scheduled_time}`;
            document.getElementById('modalPayoutLocation').textContent = schedule.location;
            document.getElementById('modalTotalBeneficiaries').textContent = schedule.total_beneficiaries ?? 0;
    
            // Fetch beneficiaries for this schedule
            fetch(`/super-admin/solo-parent-beneficiaries-by-barangay/${encodeURIComponent(barangay)}`)
            .then(res => res.json())
            .then(benList => {
                const receivedCount = benList.filter(b => b.assistance_status === 'Received').length;
                document.getElementById('modalBeneficiariesReceived').textContent = receivedCount;
    
                const tbody = document.getElementById('modalBeneficiariesList');
                tbody.innerHTML = '';
                benList.forEach(b => {
                    const receivedStatus = b.assistance_status === 'Received'
                        ? `<span class="text-success"><i class="fas fa-check-circle"></i> Received</span>`
                        : `<span class="text-warning"><i class="fas fa-hourglass-half"></i> Pending</span>`;
                    tbody.innerHTML += `
                        <tr>
                            <td>${b.first_name} ${b.last_name}</td>
                            <td>${b.street}, ${b.barangay}, ${b.municipality}</td>
                            <td>${b.category}</td>
                            <td>${receivedStatus}</td>
                        </tr>`;
                });
    
                modal.show();
            })
            .catch(err => console.error('Failed to fetch beneficiaries:', err));
        })
        .catch(err => console.error('Failed to fetch schedule:', err));
    }
    
    
    document.getElementById('back-to-schedule').addEventListener('click', () => {
        document.getElementById('barangay-beneficiaries-panel').style.display = 'none';
        document.getElementById('schedule-panel').style.display = 'block';
        document.getElementById('category-title').textContent = 'Schedule Payout per Barangay';
        document.getElementById('global-search').style.display = 'none';
    });
    </script>


<!-- REPORTS SECTION -->
<div id="report-section" class="content-section" style="display:none;">
    <div class="admin-container">

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h2 class="fw-bold mb-1"><i class="fas fa-chart-line me-2"></i>Reports & Analytics</h2>
                <p class="text-muted mb-0">Overall Report – General Trias (All Barangays)</p>
            </div>
            <div class="d-flex gap-2">
                <select class="form-select form-select-sm" style="width:120px;">
                    <option>2025</option><option>2024</option><option>2023</option>
                </select>
                <select class="form-select form-select-sm" style="width:120px;">
                    <option value="all">All Months</option>
                    <option>Jan</option><option>Feb</option><option>Mar</option>
                </select>
                <button class="btn btn-dark btn-sm">Apply</button>
            </div>
        </div>

        <!-- STATUS SUMMARY -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card stat-card stat-blue shadow-sm border-0 rounded-4 p-3">
                    <div class="d-flex align-items-center justify-content-center">
                        <div class="icon-circle-big-logo bg-primary me-4">
                            <i class="fas fa-file-alt fa-lg text-white"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold" id="totalApplications">25</h5>
                            <small class="text-muted">Total Applications</small>
                            <p class="small text-success mt-1" id="totalApplicationsPercent">▲ 5% this month</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card stat-card stat-green shadow-sm border-0 rounded-4 p-3">
                    <div class="d-flex align-items-center justify-content-center">
                        <div class="icon-circle-big-logo bg-success me-4">
                            <i class="fas fa-check-circle fa-lg text-white"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold" id="approvedApplications">10</h5>
                            <small class="text-muted">Approved</small>
                            <p class="small text-success mt-1" id="approvedApplicationsPercent">▲ 8% this month</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card stat-card stat-yellow shadow-sm border-0 rounded-4 p-3">
                    <div class="d-flex align-items-center justify-content-center">
                        <div class="icon-circle-big-logo bg-warning me-4">
                            <i class="fas fa-hourglass-half fa-lg text-white"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold" id="pendingApplications">15</h5>
                            <small class="text-muted">Pending</small>
                            <p class="small text-danger mt-1" id="pendingApplicationsPercent">▼ 3% this month</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card stat-card stat-red shadow-sm border-0 rounded-4 p-3">
                    <div class="d-flex align-items-center justify-content-center">
                        <div class="icon-circle-big-logo bg-danger me-4">
                            <i class="fas fa-home fa-lg text-white"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold" id="homeVisits">5</h5>
                            <small class="text-muted">Home Visits</small>
                            <p class="small text-info mt-1" id="homeVisitsPercent">▲ 2% this week</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- MONTHLY PERFORMANCE -->
        <div class="card shadow-sm mb-4 report-group">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Monthly Performance</h6>
                    <small class="text-muted">Trends & approvals (All Barangays)</small>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-danger btn-sm export-group" data-type="pdf"><i class="fas fa-file-pdf"></i></button>
                    <button class="btn btn-outline-success btn-sm export-group" data-type="csv"><i class="fas fa-file-csv"></i></button>
                    <button class="btn btn-outline-secondary btn-sm toggle-group"><i class="fas fa-chevron-up"></i></button>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card shadow-sm h-100 chart-card">
                            <div class="chart-loading-overlay">Loading...</div>
                            <div class="card-header py-2 d-flex justify-content-between">
                                <h6 class="m-0 small">Monthly Applications</h6>
                                <button class="btn btn-sm btn-light fullscreen-btn"><i class="fas fa-expand"></i></button>
                            </div>
                            <div class="card-body"><canvas id="applicationsChart"></canvas></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card shadow-sm h-100 chart-card">
                            <div class="chart-loading-overlay">Loading...</div>
                            <div class="card-header py-2 d-flex justify-content-between">
                                <h6 class="m-0 small">Approval Status</h6>
                                <button class="btn btn-sm btn-light fullscreen-btn"><i class="fas fa-expand"></i></button>
                            </div>
                            <div class="card-body"><canvas id="approvalChart"></canvas></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CATEGORY & BARANGAY -->
        <div class="card shadow-sm mb-4 report-group">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0"><i class="fas fa-map-marked-alt me-2"></i>Category & Location</h6>
                    <small class="text-muted">Distribution overview (All Barangays)</small>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-danger btn-sm export-group" data-type="pdf"><i class="fas fa-file-pdf"></i></button>
                    <button class="btn btn-outline-success btn-sm export-group" data-type="csv"><i class="fas fa-file-csv"></i></button>
                    <button class="btn btn-outline-secondary btn-sm toggle-group"><i class="fas fa-chevron-up"></i></button>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card shadow-sm h-100 chart-card">
                            <div class="chart-loading-overlay">Loading...</div>
                            <div class="card-header py-2 d-flex justify-content-between">
                                <h6 class="m-0 small">Solo Parent Categories</h6>
                                <button class="btn btn-sm btn-light fullscreen-btn"><i class="fas fa-expand"></i></button>
                            </div>
                            <div class="card-body"><canvas id="chartCategory"></canvas></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card shadow-sm h-100 chart-card">
                            <div class="chart-loading-overlay">Loading...</div>
                            <div class="card-header py-2 d-flex justify-content-between">
                                <h6 class="m-0 small">Per Barangay</h6>
                                <button class="btn btn-sm btn-light fullscreen-btn"><i class="fas fa-expand"></i></button>
                            </div>
                            <div class="card-body"><canvas id="barangayChart"></canvas></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- HOME VISITS TRENDS -->
        <div class="card shadow-sm mb-4 report-group">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0"><i class="fas fa-house-user me-2"></i>Home Visits Trends</h6>
                    <small class="text-muted">Weekly visits overview</small>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-danger btn-sm export-group" data-type="pdf"><i class="fas fa-file-pdf"></i></button>
                    <button class="btn btn-outline-success btn-sm export-group" data-type="csv"><i class="fas fa-file-csv"></i></button>
                    <button class="btn btn-outline-secondary btn-sm toggle-group"><i class="fas fa-chevron-up"></i></button>
                </div>
            </div>
            <div class="card-body">
                <div class="card shadow-sm chart-card">
                    <div class="chart-loading-overlay">Loading...</div>
                    <canvas id="homeVisitChart"></canvas>
                </div>
            </div>
        </div>

        <!-- SOLO PARENT GIS MAP -->
        <div class="card shadow-sm mb-4 report-group">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Solo Parent GIS Map</h6>
                    <small class="text-muted">Heatmap & markers (All Barangays)</small>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-secondary btn-sm toggle-group"><i class="fas fa-chevron-up"></i></button>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <button id="toggleHeatmap" class="btn btn-danger btn-sm"> Heatmap</button>
                    <button id="toggleMarkers" class="btn btn-primary btn-sm"> Markers</button>
                </div>
                <div id="gisMap" style="height:450px;" class="rounded"></div>
            </div>
        </div>

        <!-- EXPORT -->
        <div class="card shadow-sm mb-5">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-file-export me-2"></i>Export Reports</h6>
            </div>
            <div class="card-body d-flex gap-2 flex-wrap">
                <button class="btn btn-danger"><i class="fas fa-file-pdf"></i> PDF</button>
                <button class="btn btn-success"><i class="fas fa-file-excel"></i> Excel</button>
                <button class="btn btn-dark"><i class="fas fa-file-csv"></i> CSV</button>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>

<script>
document.addEventListener("DOMContentLoaded", () => {

    // HELPERS
    const calcPercent = (current, previous) => previous === 0 ? 100 : ((current - previous) / previous * 100).toFixed(1);
    const showLoading = (canvasId, show = true) => {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return;
        const overlay = canvas.closest(".chart-card")?.querySelector(".chart-loading-overlay");
        if (overlay) overlay.style.display = show ? "flex" : "none";
    };

    let fullscreenChartInstance = null;

    const addBadge = (card, percent, label = '') => {
        let badge = card.querySelector('.badge');
        if (!badge) {
            badge = document.createElement('span');
            badge.className = 'badge mb-2';
            badge.style.fontWeight = 'bold';
            badge.style.padding = '0.5em 0.7em';
            badge.style.display = 'inline-block';
            card.prepend(badge);
        }
        badge.style.backgroundColor = percent >= 0 ? '#198754' : '#dc3545';
        badge.style.color = '#fff';
        badge.innerText = `${percent >= 0 ? '▲' : '▼'} ${Math.abs(percent)}% ${label}`;
    };

    // COLLAPSE / EXPAND 
    document.querySelectorAll(".toggle-group").forEach(btn => {
        btn.onclick = () => {
            const body = btn.closest(".report-group").querySelector(".card-body");
            body.classList.toggle("d-none");
            btn.innerHTML = body.classList.contains("d-none")
            ? '<i class="fas fa-chevron-down"></i>'
            : '<i class="fas fa-chevron-up"></i>';
        };
    });

    // FULLSCREEN
    document.querySelectorAll(".fullscreen-btn").forEach(btn => {
        btn.onclick = () => {
            const card = btn.closest(".chart-card");
            const canvas = card.querySelector("canvas");
            const title = card.querySelector("h6")?.innerText || '';
            document.getElementById("fullscreenTitle").innerText = title;
    
            if (fullscreenChartInstance) fullscreenChartInstance.destroy();
            
            const originalChart = Chart.getChart(canvas);
            if (!originalChart) return;
            
            const ctx = document.getElementById("fullscreenChart").getContext("2d");
            fullscreenChartInstance = new Chart(ctx, {
                type: originalChart.config.type,
                data: JSON.parse(JSON.stringify(originalChart.config.data)),
                options: JSON.parse(JSON.stringify(originalChart.config.options))
            });

            new bootstrap.Modal(document.getElementById("chartFullscreenModal")).show();
        };
    });

    // EXPORT
    document.querySelectorAll(".export-group").forEach(btn => {
        btn.onclick = () => alert(`Export ${btn.dataset.type.toUpperCase()} (hook to backend)`);
    });

    // MONTHLY PERFORMANCE WITH BADGES
    showLoading("applicationsChart", true);
    showLoading("approvalChart", true);

    fetch("/super-admin/reports/monthly-performance")
        .then(res => res.json())
        .then(data => {
    
        // Applications Chart
        const appCtx = document.getElementById("applicationsChart")?.getContext("2d");
        const appCard = document.getElementById("applicationsChart")?.closest(".chart-card");
        if (appCtx && appCard) {
            const totals = data.applications;
            const last = totals[totals.length - 1] || 0;
            const prev = totals[totals.length - 2] || 0;
            addBadge(appCard, calcPercent(last, prev), 'this month');

            new Chart(appCtx, {
              type: "line",
              data: {
                labels: data.months,
                datasets: [{ label: "Applications", data: totals, borderColor: "#0d6efd", backgroundColor: "rgba(13,110,253,0.2)", fill: true, tension: 0.3 }]
              },
              options: { responsive: true, plugins: { legend: { display: false } } }
            });
        }
        
        // Approval Chart
        const approvalCtx = document.getElementById("approvalChart")?.getContext("2d");
        const approvalCard = document.getElementById("approvalChart")?.closest(".chart-card");
            if (approvalCtx && approvalCard) {
                const totalApproval = data.approved + data.pending + data.rejected || 0;
                const approvalPercent = totalApproval === 0 ? 0 : ((data.approved / totalApproval) * 100).toFixed(1);
                addBadge(approvalCard, approvalPercent, 'approved this month');
        
                new Chart(approvalCtx, {
                type: "pie",
                data: { labels: ["Approved","Pending","Rejected"], datasets: [{ data: [data.approved, data.pending, data.rejected], backgroundColor: ["#28a745","#ffc107","#dc3545"] }] },
                options: { responsive: true }
            });
        }

        showLoading("applicationsChart", false);
        showLoading("approvalChart", false);
    })
    .catch(err => console.error("Monthly Performance Error:", err));
    
    // CATEGORY & BARANGAY CHARTS WITH BADGES
    showLoading("chartCategory", true);
    showLoading("barangayChart", true);

    fetch("/super-admin/reports/category-location")
        .then(res => res.json())
        .then(data => {
    
            // Category
            const catCtx = document.getElementById("chartCategory")?.getContext("2d");
            const catCard = document.getElementById("chartCategory")?.closest(".chart-card");
            if (catCtx && catCard) {
                const current = data.categories.reduce((a,b)=>a+b.total,0);
                const prev = data.categories.last_week_total || 0;
                addBadge(catCard, calcPercent(current, prev), 'from last week');
    
                new Chart(catCtx, {
                  type: "bar",
                  data: { labels: data.categories.map(c => c.category), datasets: [{ data: data.categories.map(c => c.total), backgroundColor: "#6f42c1" }] },
                  options: { responsive: true, plugins: { legend: { display: false } } }
                });
            }
    
            // Barangay
            const barCtx = document.getElementById("barangayChart")?.getContext("2d");
            const barCard = document.getElementById("barangayChart")?.closest(".chart-card");
            if (barCtx && barCard) {
                const current = data.barangays.reduce((a,b)=>a+b.total,0);
                const prev = data.barangays.last_week_total || 0;
                addBadge(barCard, calcPercent(current, prev), 'from last week');
    
            new Chart(barCtx, {
              type: "bar",
              data: { labels: data.barangays.map(b => b.barangay), datasets: [{ data: data.barangays.map(b => b.total), backgroundColor: "#0d6efd" }] },
              options: { indexAxis: "y", responsive: true, plugins: { legend: { display: false } } }
            });
          }
    
        showLoading("chartCategory", false);
        showLoading("barangayChart", false);
    })
    .catch(err => console.error("Category & Barangay Error:", err));

    // HOME VISITS WITH BADGE
    showLoading("homeVisitChart", true);
    fetch("/super-admin/home-visits/weekly")
        .then(res => res.json())
        .then(data => {
            const ctx = document.getElementById("homeVisitChart")?.getContext("2d");
            const card = document.getElementById("homeVisitChart")?.closest(".chart-card");
            if (!ctx || !card) return;
            
            const last = data.visits[data.visits.length-1] || 0;
            const prev = data.visits[data.visits.length-2] || 0;
            addBadge(card, calcPercent(last, prev), 'this week');
    
            new Chart(ctx, {
                type: "line",
                data: { labels: data.weeks, datasets: [{ label: "Home Visits", data: data.visits, borderColor: "#fd7e14", backgroundColor: "rgba(253,126,20,0.2)", fill: true, tension: 0.3 }] },
                options: { responsive: true, plugins: { legend: { display: true } }, scales: { y: { beginAtZero:true, stepSize:1 } } }
            });
    
            const total = data.visits.reduce((a,b)=>a+b,0);
            document.getElementById("homeVisits").textContent = total;
    
        showLoading("homeVisitChart", false);
    })
    .catch(err => console.error("Home Visits Error:", err));

    // GIS MAP
    const map = L.map("gisMap").setView([14.274,120.912],13);
    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png").addTo(map);
    
    let markersLayer = L.layerGroup().addTo(map);
    let heatLayer = null;
    const locations = [[14.272,120.912],[14.275,120.910],[14.277,120.915]];
        locations.forEach(l=>L.marker(l).addTo(markersLayer));
        
    document.getElementById("toggleMarkers").onclick = () => map.hasLayer(markersLayer) ? map.removeLayer(markersLayer) : map.addLayer(markersLayer);
    document.getElementById("toggleHeatmap").onclick = () => {
        if(heatLayer){ map.removeLayer(heatLayer); heatLayer=null; } 
        else{ heatLayer=L.heatLayer(locations.map(l=>[l[0],l[1],1]),{radius:25}).addTo(map); }
    };

});
</script>
<!-- ANNOUNCEMENT SECTION -->
<div class="content-section" id="announcement-section" style="display:none;">
    <div class="admin-container">

        <div class="row mb-4">
            <div class="col-12">
                <h2 class="h3 fw-bold"> Announcements</h2>
                <p class="text-muted">Post and manage announcements for users.</p>
            </div>
        </div>

        <!-- ANNOUNCEMENTS CARD -->
        <div class="card border-0 shadow-sm">
            <div class="card-body">

                <!-- DYNAMIC ANNOUNCEMENT LIST -->
                <div id="announcementList"></div>

                <!-- EMPTY STATE -->
                <div id="noAnnouncements" class="text-center py-3" style="display:none;">
                    <p class="text-muted m-0">No announcements available.</p>
                </div>

            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {

    const list = document.getElementById('announcementList');
    const empty = document.getElementById('noAnnouncements');

    async function loadAnnouncements() {
        try {
            const response = await fetch('/super-admin/announcements');
            const announcements = await response.json();

            list.innerHTML = "";

            if (!announcements.length) {
                empty.style.display = 'block';
                return;
            }

            empty.style.display = 'none';

            announcements.forEach(ann => {
                const item = document.createElement('div');
                item.className = "single-announcement";

                // Border color based on status
                let borderColor = '#007bff'; // default
                if (ann.status === 'success') borderColor = '#28a745';
                else if (ann.status === 'pending') borderColor = '#ffc107';
                else if (ann.status === 'error') borderColor = '#dc3545';
                item.style.borderLeftColor = borderColor;

                item.innerHTML = `
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <strong>${ann.title}</strong>
                        <span class="badge ${ann.status === 'success' ? 'bg-success' :
                                            ann.status === 'pending' ? 'bg-warning text-dark' :
                                            'bg-secondary'}">${ann.status}</span>
                    </div>

                    <p class="mb-1">${ann.content ?? ""}</p>

                    <div class="d-flex justify-content-between flex-wrap mb-1">
                        <small class="text-muted">Category: ${ann.category ?? "General"}</small>
                        <small class="text-muted">Type: ${ann.type ?? "General"}</small>
                        ${ann.link ? `<small><a href="${ann.link}" target="_blank">View Link</a></small>` : ''}
                    </div>

                    <small class="text-muted">Created: ${timeAgo(ann.created_at)}</small>
                `;

                list.appendChild(item);
            });

        } catch(error) {
            console.error('Error fetching announcements:', error);
        }
    }

    // Auto-refresh every 30 seconds
    setInterval(loadAnnouncements, 30000);

    // Initial load
    loadAnnouncements();

    // TIME AGO formatter
    function timeAgo(dateString) {
        const now = new Date();
        const date = new Date(dateString);
        const seconds = Math.floor((now - date) / 1000);

        if (seconds < 60) return `${seconds} sec ago`;
        const minutes = Math.floor(seconds / 60);
        if (minutes < 60) return `${minutes} min ago`;
        const hours = Math.floor(minutes / 60);
        if (hours < 24) return `${hours} hr ago`;
        const days = Math.floor(hours / 24);
        return `${days} day(s) ago`;
    }

});
</script>

<input type="hidden" id="currentUserId" value="{{ auth()->id() }}">
<input type="hidden" id="adminId" value="{{ auth()->user()->id }}">
<!-- CHAT SECTION -->
<div id="chat-section" class="content-section" style="display:none; position: relative;">
    <div class="admin-container">
        <h3 class="section-title fw-bold mb-1 mt-2">💬 Chat Communication</h3>
        <small style="color:#555;">View and respond to user messages in real time</small>

        <div class="row mt-3 g-0" style="height:600px;">
            <!-- USER LIST -->
            <div class="col-md-4 border-end" style="overflow-y:auto;">
                <div class="chat-search mb-2 p-2">
                    <input type="text" id="userSearch" placeholder="Search users..." class="form-control"/>
                </div>

                <div id="chat-users">
                    @foreach($users as $user)
                        <div class="chat-user d-flex align-items-center p-2 border-bottom" data-user="{{ $user->id }}" style="cursor:pointer; transition: background 0.2s;">
                            <img src="{{ asset('images/avatar.png') }}" alt="{{ $user->username }}" class="rounded-circle me-2" width="40" height="40">
                            <div class="chat-info flex-grow-1">
                                <h6 class="mb-0">{{ $user->username }}</h6>
                                <p class="mb-0 text-truncate" style="max-width:180px; font-size:0.9rem;">{{ $user->last_message ?? 'No messages yet' }}</p>
                                <small class="text-muted">{{ $user->last_message_time ?? '' }}</small>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- CONVERSATION PANEL -->
            <div class="col-md-8 d-flex flex-column" id="chat-conversation">
                <div class="chat-header d-flex align-items-center p-2 border-bottom">
                    <h5 id="conversationWith" class="mb-0">Select a user</h5>
                </div>

                <div class="conversation-inner flex-grow-1 overflow-auto p-3" id="conversation-content" style="background:#f0f2f5;">
                    <p class="text-center text-muted mt-5">Select a user to start chatting</p>
                </div>

                <div class="chat-input input-group p-2 border-top">
                    <input type="text" id="chatInput" class="form-control" placeholder="Type your message..." disabled />
                    <button class="btn btn-primary" id="sendChatBtn" disabled>➤</button>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
const currentUserId = document.getElementById('currentUserId').value;
const adminId = document.getElementById('adminId').value;

let selectedUserId = null;
const usersList = document.getElementById('chat-users');
const conversationContent = document.getElementById('conversation-content');
const conversationWith = document.getElementById('conversationWith');
const chatInput = document.getElementById('chatInput');
const sendBtn = document.getElementById('sendChatBtn');
const userSearch = document.getElementById('userSearch');

// Select user
document.querySelectorAll('.chat-user').forEach(el => {
    el.addEventListener('click', () => {
        selectedUserId = el.dataset.user;
        conversationWith.textContent = el.querySelector('h6').textContent;

        // Highlight the selected user
        document.querySelectorAll('.chat-user').forEach(u => u.classList.remove('active-user'));
        el.classList.add('active-user');

        chatInput.disabled = false;
        sendBtn.disabled = false;
        chatInput.focus();
        fetchMessages();
    });
});

// Search users
userSearch.addEventListener('input', () => {
    const search = userSearch.value.toLowerCase();
    document.querySelectorAll('.chat-user').forEach(u => {
        const name = u.querySelector('h6').textContent.toLowerCase();
        u.style.display = name.includes(search) ? '' : 'none';
    });
});

// Fetch messages
function fetchMessages() {
    if (!selectedUserId) return;
    axios.get(`/admin/chat/messages/${selectedUserId}`)
        .then(res => {
            conversationContent.innerHTML = '';
            if (!res.data.length) {
                conversationContent.innerHTML = '<p class="text-center text-muted mt-5">💬 No messages yet</p>';
                return;
            }
            res.data.forEach(msg => {
                const div = document.createElement('div');
                div.className = msg.sender_id == adminId ? 'text-end mb-2' : 'text-start mb-2';
                div.innerHTML = `
                    <span class="badge ${msg.sender_id == adminId ? 'bg-primary' : 'bg-secondary'} p-2">${msg.message}</span>
                    <small class="d-block text-muted">${formatMessageTime(msg.created_at)}</small>
                `;
                conversationContent.appendChild(div);
            });
            conversationContent.scrollTop = conversationContent.scrollHeight;
        })
        .catch(console.error);
}

// Send message
sendBtn.addEventListener('click', () => {
    const msg = chatInput.value.trim();
    if (!msg || !selectedUserId) return;

    axios.post('/admin/chat/send', {
        message: msg,
        receiver_id: selectedUserId
    }, { headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
    .then(() => {
        chatInput.value = '';
        fetchMessages();
    }).catch(console.error);
});

// Enter key send
chatInput.addEventListener('keypress', e => {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendBtn.click();
    }
});

// Auto-refresh messages every 2 seconds
setInterval(() => { if (selectedUserId) fetchMessages(); }, 2000);

// Format message time
function formatMessageTime(datetime) {
    const dt = new Date(datetime);
    return dt.toLocaleString([], { hour: '2-digit', minute: '2-digit', month: 'short', day: 'numeric' });
}
</script>

<!-- AUDIT LOG SECTION -->
<div id="audit-log-section" class="content-section" style="display:none;">
    <div class="admin-container p-3">

        <!-- Header -->
        <div class="mb-4">
            <h2 class="h3 fw-bold"><i class="fas fa-cogs me-2"></i>Audit Logs</h2>
            <p class="text-muted">Monitor all admin actions across modules in real-time. Hover over actions for details.</p>
        </div>

        <!-- Filters -->
        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <input type="text" id="filterUser" class="form-control" placeholder="Filter by user name">
            </div>
    
            <div class="col-md-4">
                <input type="text" id="filterModule" class="form-control" placeholder="Filter by module">
            </div>
    
            <div class="col-md-4">
                <input type="date" id="filterDate" class="form-control">
            </div>
        </div>


        <!-- Audit Log Cards -->
        <div class="audit-log-cards" id="auditLogCards">
          <!-- Cards will be inserted dynamically -->
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-4">
            <small class="text-muted" id="auditLogCount">Showing 1–4 of 25 entries</small>
            <ul class="pagination mb-0" id="audit-log-pagination">
                <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">Next</a></li>
            </ul>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {

    const auditLogCards = document.getElementById('auditLogCards');
    const paginationEl = document.getElementById('audit-log-pagination');
    const countEl = document.getElementById('auditLogCount');

    const filterUser = document.getElementById('filterUser');
    const filterModule = document.getElementById('filterModule');
    const filterDate = document.getElementById('filterDate');

    const PER_PAGE = 7;
    const AUTO_REFRESH_SECONDS = 15;

    let allLogs = [];
    let filteredLogs = [];
    let currentPage = 1;

    function getStatusBadge(status){
        if(status === 'Success') return '<span class="badge bg-success"><i class="fas fa-check-circle"></i> Success</span>';
        if(status === 'Deleted') return '<span class="badge bg-danger"><i class="fas fa-trash-alt"></i> Deleted</span>';
        if(status === 'Updated') return '<span class="badge bg-warning text-dark"><i class="fas fa-edit"></i> Updated</span>';
        return '<span class="badge bg-info text-dark"><i class="fas fa-eye"></i> Viewed</span>';
    }

    function applyFilters(){
        const user = filterUser.value.toLowerCase();
        const module = filterModule.value.toLowerCase();
        const date = filterDate.value;

        filteredLogs = allLogs.filter(log => {
            const matchUser = log.user.toLowerCase().includes(user);
            const matchModule = log.module.toLowerCase().includes(module);
            const matchDate = !date || log.created_at.startsWith(date);
            return matchUser && matchModule && matchDate;
        });

        currentPage = 1;
        renderAuditLogs();
    }

    function renderAuditLogs(){
        auditLogCards.style.opacity = 0;
        auditLogCards.innerHTML = '';

        const start = (currentPage - 1) * PER_PAGE;
        const pageLogs = filteredLogs.slice(start, start + PER_PAGE);

        pageLogs.forEach(log => {
            const card = document.createElement('div');
            card.className = `audit-card ${log.status.toLowerCase()}`;

            card.innerHTML = `
                <div class="details">
                    <div><i class="fas fa-user user-icon"></i> <strong>${log.user}</strong></div>
                    <div>${log.action} <small class="text-muted">[${log.module}]</small></div>
                    <div class="text-muted small">${new Date(log.created_at).toLocaleString()}</div>
                </div>
                <div class="status">${getStatusBadge(log.status)}</div>
            `;
            auditLogCards.appendChild(card);
        });

        auditLogCards.style.opacity = 1;
        renderPagination();
        updateCount();
    }

    function renderPagination(){
        paginationEl.innerHTML = '';
        const totalPages = Math.ceil(filteredLogs.length / PER_PAGE);

        paginationEl.innerHTML += `
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <button class="page-link" onclick="goToPage(${currentPage - 1})">Previous</button>
            </li>
        `;

        for(let i = 1; i <= totalPages; i++){
            paginationEl.innerHTML += `
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <button class="page-link" onclick="goToPage(${i})">${i}</button>
                </li>
            `;
        }

        paginationEl.innerHTML += `
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <button class="page-link" onclick="goToPage(${currentPage + 1})">Next</button>
            </li>
        `;
    }

    function updateCount(){
        const start = (currentPage - 1) * PER_PAGE + 1;
        const end = Math.min(currentPage * PER_PAGE, filteredLogs.length);
        countEl.textContent = `Showing ${start}–${end} of ${filteredLogs.length} entries`;
    }

    window.goToPage = function(page){
        const totalPages = Math.ceil(filteredLogs.length / PER_PAGE);
        if(page < 1 || page > totalPages) return;
        currentPage = page;
        renderAuditLogs();
    };

    function loadAuditLogs(){
        fetch("{{ route('super.audit.logs') }}")
            .then(res => res.json())
            .then(data => {
                allLogs = data;
                filteredLogs = data;
                renderAuditLogs();
            })
            .catch(err => console.error(err));
    }

    // Auto refresh
    setInterval(loadAuditLogs, AUTO_REFRESH_SECONDS * 1000);

    // Filter listeners
    filterUser.addEventListener('input', applyFilters);
    filterModule.addEventListener('input', applyFilters);
    filterDate.addEventListener('change', applyFilters);

    loadAuditLogs();
});
</script>

<!-- SETTING LOG SECTION -->
<div class="content-section" id="settings-section" style="display:none;">
    <div class="admin-container">

        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="h3 fw-bold"><i class="fas fa-cogs me-2"></i>System Settings</h2>
                <p class="text-muted">Configure system preferences, content, and maintenance options.</p>
            </div>
        </div>

        <!-- Settings Tabs -->
        <ul class="nav nav-tabs mb-4" id="settingsTabs">
            <li class="nav-item"><a class="nav-link active" data-tab="general-settings" href="javascript:void(0)">General</a></li>
            <li class="nav-item"><a class="nav-link" data-tab="security-settings" href="javascript:void(0)">Security</a></li>
            <li class="nav-item"><a class="nav-link" data-tab="maintenance-settings" href="javascript:void(0)">Maintenance</a></li>
            <li class="nav-item"><a class="nav-link" data-tab="content-settings" href="javascript:void(0)">Content</a></li>
            <li class="nav-item"><a class="nav-link" data-tab="faq-settings" href="javascript:void(0)">FAQs</a></li>
        </ul>

    <!-- GENERAL SETTINGS -->
    <div class="settings-tab active" id="general-settings">
        <div class="card shadow-sm rounded-4 p-4 mb-4" style="background: #f0f4f8;">
            <h5 class="fw-bold mb-4"><i class="fas fa-info-circle me-2"></i>General System Information</h5>
    
            <form id="generalSettingsForm" enctype="multipart/form-data">
                @csrf
    
                <div class="mb-3">
                    <label class="form-label">System Brand Name</label>
                    <input type="text" name="system_brand_name" class="form-control"
                           value="{{ $system->system_brand_name ?? '' }}" required>
                </div>
    
                <div class="mb-3">
                    <label class="form-label">System Full Name</label>
                    <input type="text" name="system_full_name" class="form-control"
                           value="{{ $system->system_full_name ?? '' }}" required>
                </div>
    
                <div class="mb-3">
                    <label class="form-label">System Description</label>
                    <textarea name="system_description" class="form-control" rows="3">{{ $system->system_description ?? '' }}</textarea>
                </div>
    
                <div class="mb-3">
                    <label class="form-label">Upload System Logo</label>
                    <input type="file" name="logo" class="form-control">
                </div>
    
                <div class="mb-3">
                    <label class="form-label">Admin Contact Email</label>
                    <input type="email" name="admin_email" class="form-control" value="{{ $system->admin_email ?? '' }}">
                </div>
    
                <div class="mb-3">
                    <label class="form-label">Footer Text</label>
                    <input type="text" name="footer_text" class="form-control" value="{{ $system->footer_text ?? '' }}">
                </div>
    
                <button type="submit" class="btn btn-primary px-4 fw-semibold shadow-sm">
                    <i class="fas fa-save me-1"></i> Save Changes
                </button>
            </form>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('generalSettingsForm');

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch("{{ route('superadmin.system.update') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Update DOM dynamically
                    if (data.system.brand_name) {
                        document.querySelectorAll('.topbar-title').forEach(el => el.textContent = data.system.brand_name);
                        document.querySelector('.navbar-brand .fw-bold').textContent = data.system.brand_name;
                    }

                    if (data.system.full_name) {
                        const heroTitle = document.querySelector('.typing-title');
                        heroTitle.textContent = '';
                        let i = 0;
                        const text = data.system.full_name;
                        function typeHero() {
                            if (i < text.length) {
                                heroTitle.textContent += text.charAt(i);
                                i++;
                                setTimeout(typeHero, 50);
                            }
                        }
                        typeHero();
                    }

                    if (data.system.footer_text) {
                        const footerEl = document.querySelector('footer');
                        if (footerEl) {
                            footerEl.innerHTML = `
                                <div class="text-center">
                                    <p class="mb-0 small">${data.system.footer_text}</p>
                                </div>
                            `;
                        }
                    }

                    // Optionally show success toast/alert
                    alert('System settings updated successfully!');
                } else {
                    alert('Error updating system settings!');
                }
            })
            .catch(err => console.error('Error:', err));
        });
    });
    </script>

    <!-- SECURITY -->
    <div class="settings-tab" id="security-settings" style="display:none;">
        <div class="card shadow-sm rounded-4 p-4 mb-4" style="background: #e8f5e9;">
            <h5 class="fw-bold mb-4"><i class="fas fa-shield-alt me-2"></i>User & Security Settings</h5>

            <div class="mb-3">
                <label class="form-label">Change Password</label>
                <input type="password" class="form-control" placeholder="Enter new password">
            </div>
            <div class="mb-3">
                <label class="form-label">Require Strong Passwords</label>
                <select class="form-select">
                    <option>Enabled</option>
                    <option>Disabled</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Auto Logout After</label>
                <select class="form-select">
                    <option>15 minutes</option>
                    <option>30 minutes</option>
                    <option>1 hour</option>
                    <option>Never</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Allow Multiple Logins</label>
                <select class="form-select">
                    <option>Yes</option>
                    <option>No</option>
                </select>
            </div>

            <button class="btn btn-success px-4 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalSaveSecurity">
                <i class="fas fa-save me-1"></i>Save Security Settings
            </button>
        </div>
    </div>

    <!-- MAINTENANCE -->
    <div class="settings-tab" id="maintenance-settings" style="display:none;">
        <div class="card shadow-sm rounded-4 p-4 mb-4" style="background: #fff3e0;">
            <h5 class="fw-bold mb-4"><i class="fas fa-database me-2"></i>Backup & Maintenance</h5>

            <div class="mb-3">
                <label class="form-label">Manual Database Backup</label><br>
                <button class="btn btn-warning px-4 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalBackup">
                    <i class="fas fa-download me-1"></i>Download Backup
                </button>
            </div>

            <div class="mb-3">
                <label class="form-label">Auto Backup Schedule</label>
                <select class="form-select">
                    <option>Disabled</option>
                    <option>Daily</option>
                    <option>Weekly</option>
                    <option>Monthly</option>
                </select>
            </div>

            <div class="mb-3 d-flex gap-2">
                <button class="btn btn-danger px-4 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalClearCache">
                    <i class="fas fa-broom me-1"></i>Clear Cache
                </button>
                <button class="btn btn-info px-4 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalOptimize">
                    <i class="fas fa-cogs me-1"></i>Optimize System
                </button>
            </div>

            <p class="text-muted mt-3">System Version: v1.0.0</p>
        </div>
    </div>

    <!-- CONTENT -->
    <div class="settings-tab" id="content-settings" style="display:none;">
        <div class="card shadow-sm rounded-4 p-4 mb-4" style="background: #f3e5f5;">
            <h5 class="fw-bold mb-4"><i class="fas fa-file-alt me-2"></i>Content Management</h5>

            <h6 class="fw-semibold"><i class="fas fa-info-circle me-1"></i>About Section</h6>
            <textarea class="form-control mb-3" rows="3" placeholder="About content..."></textarea>
            <button class="btn btn-primary mb-4 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalSaveAbout">
                <i class="fas fa-save me-1"></i>Save About
            </button>

            <h6 class="fw-semibold"><i class="fas fa-newspaper me-1"></i>Articles</h6>
            <input type="text" class="form-control mb-2" placeholder="Article Title">
            <textarea class="form-control mb-2" rows="2" placeholder="Excerpt"></textarea>
            <input type="file" class="form-control mb-2">
            <button class="btn btn-primary mb-4 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalAddArticle">
                <i class="fas fa-plus me-1"></i>Add Article
            </button>

            <h6 class="fw-semibold"><i class="fas fa-image me-1"></i>Gallery</h6>
            <input type="text" class="form-control mb-2" placeholder="Image Title">
            <input type="file" class="form-control mb-2">
            <button class="btn btn-primary fw-semibold" data-bs-toggle="modal" data-bs-target="#modalAddImage">
                <i class="fas fa-plus me-1"></i>Add Image
            </button>
        </div>
    </div>

    <!-- FAQ  -->
    <div class="settings-tab" id="faq-settings" style="display:none;">
        <div class="card shadow-sm rounded-4 p-4 mb-4" style="background:#eef6ff;">

            <h5 class="fw-bold mb-4"><i class="fas fa-question-circle me-2"></i>FAQ Management</h5>

            <!-- ADD FAQ -->
            <div class="mb-4">
                <h6 class="fw-semibold mb-2"><i class="fas fa-plus-circle me-1"></i>Add New FAQ</h6>

                <input type="text" id="newFaqQuestion"class="form-control mb-2" placeholder="Enter question">
                <textarea id="newFaqAnswer" class="form-control mb-2" rows="3" placeholder="Enter answer"></textarea>

                <button class="btn btn-primary fw-semibold" id="addFaqBtn">
                    <i class="fas fa-plus me-1"></i>Add FAQ
                </button>
            </div>

            <hr>
            <!-- FAQ LIST -->
            <h6 class="fw-semibold mb-3"><i class="fas fa-list me-1"></i>Existing FAQs</h6>

            <div id="faqAdminList">
                <!-- FAQ items will be dynamically loaded here -->
            </div>


        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {

        const faqList = document.getElementById('faqAdminList');
        const addBtn = document.getElementById('addFaqBtn');
        const confirmAddBtn = document.getElementById('confirmAddFaqBtn');
        const confirmSaveBtn = document.getElementById('confirmSaveFaqBtn');
        const confirmDeleteBtn = document.getElementById('confirmDeleteFaqBtn');
        const deleteFaqMessage = document.getElementById('deleteFaqMessage');

        let tempQuestion = '', tempAnswer = '';
        let saveCard = null, deleteCard = null;

        const addModal = new bootstrap.Modal(document.getElementById('confirmAddFaqModal'));
        const saveModal = new bootstrap.Modal(document.getElementById('confirmSaveFaqModal'));
        const deleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteFaqModal'));

        // FETCH FAQs
        function loadFaqs() {
            fetch("{{ route('superadmin.faqs.fetch') }}", { headers: { 'Accept': 'application/json' } })
            .then(res => res.json())
            .then(data => {
                faqList.innerHTML = '';
                if (!data.length) {
                    faqList.innerHTML = `<p class="text-muted text-center">No FAQs added yet.</p>`;
                    return;
                }
                data.forEach(faq => faqList.insertAdjacentHTML('beforeend', renderFaqItem(faq)));
                attachEvents();
            })
            .catch(err => console.error('FAQ Fetch Error:', err));
        }

        // RENDER FAQ ITEM
        function renderFaqItem(faq) {
            return `
            <div class="faq-item card p-3 mb-3 shadow-sm rounded-3" data-id="${faq.id}">
                <input type="text" class="form-control mb-2 faq-question" value="${faq.question}">
                <textarea class="form-control mb-2 faq-answer" rows="2">${faq.answer}</textarea>
                <div class="d-flex gap-2 justify-content-end">
                    <button class="btn btn-success btn-sm saveFaq"><i class="fas fa-save"></i> Save</button>
                    <button class="btn btn-danger btn-sm deleteFaq"><i class="fas fa-trash"></i> Delete</button>
                </div>
            </div>`;
        }

        // ADD FAQ
        addBtn.addEventListener('click', () => {
            const question = document.getElementById('newFaqQuestion').value.trim();
            const answer = document.getElementById('newFaqAnswer').value.trim();

            if (!question || !answer) return alert('Question and Answer are required.');
            tempQuestion = question; tempAnswer = answer;
            addModal.show();
        });

        confirmAddBtn.addEventListener('click', () => {
            bootstrap.Modal.getInstance(document.getElementById('confirmAddFaqModal')).hide();
            fetch("{{ route('superadmin.faqs.store') }}", {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}", 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ question: tempQuestion, answer: tempAnswer })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('newFaqQuestion').value = '';
                    document.getElementById('newFaqAnswer').value = '';
                    tempQuestion = ''; tempAnswer = '';
                    loadFaqs();
                    alert('FAQ successfully added!');
                }
            })
            .catch(err => console.error('FAQ Add Error:', err));
        });

        // ATTACH SAVE & DELETE EVENTS
        function attachEvents() {
            // SAVE
            document.querySelectorAll('.saveFaq').forEach(btn => {
                btn.onclick = () => {
                    saveCard = btn.closest('.faq-item');
                    saveModal.show();
                };
            });

            confirmSaveBtn.onclick = () => {
                if (!saveCard) return;
                const id = saveCard.dataset.id;
                const question = saveCard.querySelector('.faq-question').value.trim();
                const answer = saveCard.querySelector('.faq-answer').value.trim();
                if (!question || !answer) return alert('Question and Answer cannot be empty.');

                fetch(`{{ url('/super-admin/faqs') }}/${id}`, {
                    method: 'PUT',
                    headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}", 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ question, answer })
                })
                .then(() => {
                    saveCard.querySelector('.saveFaq').innerHTML = '<i class="fas fa-check"></i> Saved';
                    setTimeout(() => { saveCard.querySelector('.saveFaq').innerHTML = '<i class="fas fa-save"></i> Save'; }, 1000);
                    saveModal.hide();
                })
                .catch(err => console.error('FAQ Update Error:', err));
            };

            // DELETE
            document.querySelectorAll('.deleteFaq').forEach(btn => {
                btn.onclick = () => {
                    deleteCard = btn.closest('.faq-item');
                    const questionText = deleteCard.querySelector('.faq-question').value.trim();
                    deleteFaqMessage.innerHTML = `Are you sure you want to delete this FAQ: <strong>${questionText}</strong>?`;
                    deleteModal.show();
                };
            });

            confirmDeleteBtn.onclick = () => {
                if (!deleteCard) return;
                const id = deleteCard.dataset.id;
                fetch(`{{ url('/super-admin/faqs') }}/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}", 'Accept': 'application/json' }
                })
                .then(() => { deleteCard.remove(); deleteModal.hide(); })
                .catch(err => console.error('FAQ Delete Error:', err));
            };
        }

        loadFaqs();
    });
    </script>

<script>
document.querySelectorAll('#settingsTabs .nav-link').forEach(tab=>{
    tab.addEventListener('click', function(){
        document.querySelectorAll('#settingsTabs .nav-link').forEach(t=>t.classList.remove('active'));
        this.classList.add('active');
        document.querySelectorAll('.settings-tab').forEach(c=>c.style.display='none');
        document.getElementById(this.dataset.tab).style.display='block';
    });
});
</script>


</main>

<!-- General Save -->
<div class="modal fade" id="modalSaveGeneral" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 bg-primary text-white text-center flex-column position-relative">
                <div class="ready-icon mb-2"><i class="fas fa-save"></i></div>
                <h5 class="modal-title fw-bold">Save Changes</h5>
                <button type="button" class="btn-close btn-close-white position-absolute end-0 top-0 m-3" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center px-4">
                <p class="text-muted mb-3">Are you sure you want to save general system changes?</p>
            </div>
            <div class="modal-footer border-0 justify-content-center gap-2 pb-4">
                <button class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary px-4">Confirm</button>
            </div>
        </div>
    </div>
</div>

<!-- Security Save -->
<div class="modal fade" id="modalSaveSecurity" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 bg-success text-white text-center flex-column position-relative">
                <div class="ready-icon mb-2"><i class="fas fa-shield-alt"></i></div>
                <h5 class="modal-title fw-bold">Save Security Settings</h5>
                <button type="button" class="btn-close btn-close-white position-absolute end-0 top-0 m-3" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center px-4">
                <p class="text-muted mb-3">Are you sure you want to save security settings?</p>
            </div>
            <div class="modal-footer border-0 justify-content-center gap-2 pb-4">
                <button class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-success px-4">Confirm</button>
            </div>
        </div>
    </div>
</div>

<!-- Backup -->
<div class="modal fade" id="modalBackup" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 bg-warning text-dark text-center flex-column position-relative">
                <div class="ready-icon mb-2"><i class="fas fa-download"></i></div>
                    <h5 class="modal-title fw-bold">Download Backup</h5>
                    <button type="button" class="btn-close position-absolute end-0 top-0 m-3" data-bs-dismiss="modal"></button>
                </div>
            <div class="modal-body text-center px-4">
                <p class="text-muted mb-3">Do you want to download a manual database backup?</p>
            </div>
            <div class="modal-footer border-0 justify-content-center gap-2 pb-4">
                <button class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-warning px-4">Confirm</button>
            </div>
        </div>
    </div>
</div>

<!-- Clear Cache -->
<div class="modal fade" id="modalClearCache" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 bg-danger text-white text-center flex-column position-relative">
                <div class="ready-icon mb-2"><i class="fas fa-broom"></i></div>
                <h5 class="modal-title fw-bold">Clear Cache</h5>
                <button type="button" class="btn-close btn-close-white position-absolute end-0 top-0 m-3" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center px-4">
                <p class="text-muted mb-3">Are you sure you want to clear the system cache?</p>
            </div>
            <div class="modal-footer border-0 justify-content-center gap-2 pb-4">
                <button class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-danger px-4">Confirm</button>
            </div>
        </div>
    </div>
</div>

<!-- Optimize System -->
<div class="modal fade" id="modalOptimize" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 bg-info text-white text-center flex-column position-relative">
                <div class="ready-icon mb-2"><i class="fas fa-cogs"></i></div>
                <h5 class="modal-title fw-bold">Optimize System</h5>
                <button type="button" class="btn-close btn-close-white position-absolute end-0 top-0 m-3" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center px-4">
                <p class="text-muted mb-3">Do you want to optimize the system?</p>
            </div>
            <div class="modal-footer border-0 justify-content-center gap-2 pb-4">
                <button class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-info px-4">Confirm</button>
            </div>
        </div>
    </div>
</div>

<!-- Save About -->
<div class="modal fade" id="modalSaveAbout" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 bg-primary text-white text-center flex-column position-relative">
                <div class="ready-icon mb-2"><i class="fas fa-save"></i></div>
                <h5 class="modal-title fw-bold">Save About Section</h5>
                <button type="button" class="btn-close btn-close-white position-absolute end-0 top-0 m-3" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center px-4">
                <p class="text-muted mb-3">Are you sure you want to save About section content?</p>
            </div>
            <div class="modal-footer border-0 justify-content-center gap-2 pb-4">
                <button class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary px-4">Confirm</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Article -->
<div class="modal fade" id="modalAddArticle" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 bg-primary text-white text-center flex-column position-relative">
                <div class="ready-icon mb-2"><i class="fas fa-plus"></i></div>
                <h5 class="modal-title fw-bold">Add Article</h5>
                <button type="button" class="btn-close btn-close-white position-absolute end-0 top-0 m-3" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center px-4">
                <p class="text-muted mb-3">Do you want to add this article?</p>
            </div>
            <div class="modal-footer border-0 justify-content-center gap-2 pb-4">
                <button class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary px-4">Confirm</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Image -->
<div class="modal fade" id="modalAddImage" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 bg-primary text-white text-center flex-column position-relative">
                <div class="ready-icon mb-2"><i class="fas fa-plus"></i></div>
                <h5 class="modal-title fw-bold">Add Image</h5>
                <button type="button" class="btn-close btn-close-white position-absolute end-0 top-0 m-3" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center px-4">
                <p class="text-muted mb-3">Do you want to add this image to gallery?</p>
            </div>
            <div class="modal-footer border-0 justify-content-center gap-2 pb-4">
                <button class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary px-4">Confirm</button>
            </div>
        </div>
    </div>
</div>

<!-- Payout Schedule -->
<div class="modal fade" id="payoutScheduleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
        <div class="modal-header text-white" style="background-color: #003366;">
            <h5 class="modal-title">Set Payout Schedule</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="payout-barangay">
            <label>Date</label>
            <input type="date" id="payout-date" class="form-control mb-2">
            <label>Time</label>
            <input type="time" id="payout-time" class="form-control mb-2">
            <label>Location</label>
            <input type="text" id="payout-location" class="form-control mb-2">
        </div>
        <div class="modal-footer justify-content-center">
            <button id="save-payout-btn" class="btn text-white"  style="background-color: #003366;" data-route="{{ route('admin.savePayoutSchedule') }}">Save Schedule</button>
            <button type="button" class="btn btn-secondary fw-semibold" data-bs-dismiss="modal">Cancel</button>
        </div>
        </div>
    </div>
</div>

<!-- Success -->
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
        <div class="modal-header bg-success text-white">
            <h5 class="modal-title"> Success</h5>
            <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <p id="successModalMessage" class="mb-0">Action succeeded.</p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-success" data-bs-dismiss="modal">OK</button>
        </div>
        </div>
    </div>
</div>


<!-- VIEW SCHEDULE MODAL -->
<div class="modal fade" id="barangayScheduleModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">

              <!-- HEADER -->
            <div class="modal-header border-0 bg-primary text-white flex-column position-relative text-center">
                <div class="modal-icon mb-2">
                  <i class="fas fa-calendar-check"></i>
                </div>
                <h5 class="modal-title fw-bold" id="modalBarangayTitle">Barangay Schedule Details</h5>
                <button type="button" class="btn-close btn-close-white position-absolute end-0 top-0 m-3" data-bs-dismiss="modal"></button>
            </div>

            <!-- BODY -->
            <div class="modal-body px-4">
                <div class="row text-center mb-3">
                    <div class="col">
                        <i class="fas fa-map-marker-alt text-primary me-1"></i>
                        <strong>Barangay:</strong> <span id="modalBarangayName"></span>
                    </div>
                    <div class="col">
                        <i class="fas fa-calendar-alt text-success me-1"></i>
                        <strong>Date:</strong> <span id="modalScheduleDate"></span>
                    </div>
                    <div class="col">
                        <i class="fas fa-building text-warning me-1"></i>
                        <strong>Location:</strong> <span id="modalPayoutLocation"></span>
                    </div>
                    <div class="col">
                        <i class="fas fa-users text-danger me-1"></i>
                        <strong>Total Beneficiaries:</strong> <span id="modalTotalBeneficiaries"></span>
                    </div>
                    <div class="col">
                        <i class="fas fa-check-circle text-success me-1"></i>
                        <strong>Received:</strong> <span id="modalBeneficiariesReceived"></span>
                    </div>
                </div>
        
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle">
                        <thead class="table-light">
                          <tr>
                            <th><i class="fas fa-user me-1"></i>Name</th>
                            <th><i class="fas fa-home me-1"></i>Address</th>
                            <th><i class="fas fa-tags me-1"></i>Category</th>
                            <th><i class="fas fa-hand-holding-usd me-1"></i>Assistance Received</th>
                          </tr>
                        </thead>
                        <tbody id="modalBeneficiariesList"></tbody>
                    </table>
                </div>
            </div>

            <!-- FOOTER -->
            <div class="modal-footer border-0 justify-content-center pb-4">
                <button class="btn btn-secondary px-4 fw-semibold" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i>Close</button>
            </div>

        </div>
    </div>
</div>

<!-- VIEW BENEFITS -->
<div class="modal fade" id="benefitsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
        
            <!-- HEADER -->
            <div class="modal-header border-0 bg-success text-white flex-column position-relative text-center">
                <div class="modal-icon mb-2">
                    <i class="fas fa-user-check"></i>
                </div>
                <h5 class="modal-title fw-bold" id="modalBeneficiaryTitle">Beneficiary Details</h5>
                <button type="button" class="btn-close btn-close-white position-absolute end-0 top-0 m-3" data-bs-dismiss="modal"></button>
            </div>
            
            <!-- BODY -->
            <div class="modal-body px-4">
                <div class="row text-center mb-3">
                    <div class="col">
                        <i class="fas fa-id-card text-primary me-1"></i>
                        <strong>ID No:</strong> <span id="modalBeneficiaryID"></span>
                    </div>
                    <div class="col">
                        <i class="fas fa-user text-success me-1"></i>
                        <strong>Name:</strong> <span id="modalBeneficiaryName"></span>
                    </div>
                    <div class="col">
                        <i class="fas fa-home text-warning me-1"></i>
                        <strong>Address:</strong> <span id="modalBeneficiaryAddress"></span>
                    </div>
                    <div class="col">
                        <i class="fas fa-tags text-danger me-1"></i>
                        <strong>Category:</strong> <span id="modalBeneficiaryCategory"></span>
                    </div>
                </div>
            
                <h6 class="fw-semibold mt-3"><i class="fas fa-hand-holding-usd me-1"></i>Benefits to Receive</h6>
                <ul class="list-group" id="modalBeneficiaryBenefits"></ul>
            </div>
            
            <!-- FOOTER -->
            <div class="modal-footer border-0 justify-content-center pb-4">
                <button class="btn btn-secondary px-4 fw-semibold" data-bs-dismiss="modal">
                  <i class="fas fa-times me-1"></i>Close
                </button>
            </div>
        
        </div>
    </div>
</div>

<!-- Download Activities Modal -->
<div class="modal fade" id="downloadActivitiesModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 bg-dark text-white d-flex align-items-center">
                <i class="fas fa-download fa-2x me-2"></i>
                <h5 class="modal-title fw-bold">Download Recent Activities</h5>
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p>Select the format to download recent system activities:</p>
                <div class="d-flex gap-2 justify-content-center">
                  <button class="btn btn-primary"><i class="fas fa-file-pdf me-1"></i> PDF</button>
                  <button class="btn btn-success"><i class="fas fa-file-excel me-1"></i> Excel</button>
                  <button class="btn btn-dark"><i class="fas fa-file-csv me-1"></i> CSV</button>
                </div>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button class="btn btn-secondary px-4 fw-semibold" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i> Close</button>
            </div>
        </div>
    </div>
</div>

<!-- DOWNLOAD ACTIVITIES -->
<div class="modal fade" id="downloadActivitiesModal" tabindex="-1" aria-labelledby="downloadActivitiesLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
        
            <!-- HEADER -->
            <div class="modal-header border-0 bg-dark text-white text-center flex-column position-relative">
                <div class="reject-icon mb-2">
                    <i class="fas fa-download"></i>
                </div>
                <h5 class="modal-title fw-bold" id="downloadActivitiesLabel">Download Recent Activities</h5>
                <button type="button" class="btn-close btn-close-white position-absolute end-0 top-0 m-3" data-bs-dismiss="modal"></button>
            </div>
        
            <!-- BODY -->
            <div class="modal-body text-center px-4">
                <p class="text-muted mb-3">Select the format to download recent system activities:</p>
                <div class="d-flex gap-2 justify-content-center flex-wrap">
                    <button class="btn btn-primary px-4"><i class="fas fa-file-pdf me-1"></i> PDF</button>
                    <button class="btn btn-success px-4"><i class="fas fa-file-excel me-1"></i> Excel</button>
                    <button class="btn btn-dark px-4"><i class="fas fa-file-csv me-1"></i> CSV</button>
                </div>
            </div>
        
            <!-- FOOTER -->
            <div class="modal-footer border-0 justify-content-center gap-2 pb-4">
                <button class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- EXPORT PDF MODAL -->
<div class="modal fade" id="exportPDFModal" tabindex="-1" aria-labelledby="exportPDFLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">

            <div class="modal-header border-0 bg-primary text-white text-center flex-column position-relative">
                <div class="reject-icon mb-2">
                    <i class="fas fa-file-pdf"></i>
                </div>
                <h5 class="modal-title fw-bold" id="exportPDFLabel">Export Report as PDF</h5>
                <button type="button" class="btn-close btn-close-white position-absolute end-0 top-0 m-3" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center px-4">
                <p class="text-muted mb-3">Are you sure you want to export the reports as a PDF file?</p>
            </div>

             <div class="modal-footer border-0 justify-content-center gap-2 pb-4">
                <button class="btn btn-primary px-4"><i class="fas fa-file-pdf me-1"></i> Export PDF</button>
                <button class="btn btn-light px-4" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i> Cancel</button>
            </div>
    
        </div>
    </div>
</div>

<!-- EXPORT EXCEL MODAL -->
<div class="modal fade" id="exportExcelModal" tabindex="-1" aria-labelledby="exportExcelLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">

            <div class="modal-header border-0 bg-success text-white text-center flex-column position-relative">
                <div class="reject-icon mb-2">
                    <i class="fas fa-file-excel"></i>
                </div>
                <h5 class="modal-title fw-bold" id="exportExcelLabel">Export Report as Excel</h5>
                <button type="button" class="btn-close btn-close-white position-absolute end-0 top-0 m-3" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center px-4">
                <p class="text-muted mb-3">Are you sure you want to export the reports as an Excel file?</p>
            </div>

            <div class="modal-footer border-0 justify-content-center gap-2 pb-4">
                <button class="btn btn-success px-4"><i class="fas fa-file-excel me-1"></i> Export Excel</button>
                <button class="btn btn-light px-4" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i> Cancel</button>
            </div>

        </div>
    </div>
</div>

<!-- EXPORT CSV MODAL -->
<div class="modal fade" id="exportCSVModal" tabindex="-1" aria-labelledby="exportCSVLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">

            <div class="modal-header border-0 bg-dark text-white text-center flex-column position-relative">
                <div class="reject-icon mb-2">
                    <i class="fas fa-file-csv"></i>
                </div>
                <h5 class="modal-title fw-bold" id="exportCSVLabel">Export Report as CSV</h5>
                <button type="button" class="btn-close btn-close-white position-absolute end-0 top-0 m-3" data-bs-dismiss="modal"></button>
             </div>

            <div class="modal-body text-center px-4">
                <p class="text-muted mb-3">Are you sure you want to export the reports as a CSV file?</p>
            </div>

            <div class="modal-footer border-0 justify-content-center gap-2 pb-4">
                <button class="btn btn-dark px-4"><i class="fas fa-file-csv me-1"></i> Export CSV</button>
                <button class="btn btn-light px-4" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i> Cancel</button>
            </div>

        </div>
    </div>
</div>

<!-- ADD USER MODAL -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
        
            <!-- HEADER -->
            <div class="modal-header bg-primary text-white rounded-top-4">
                <h5 class="modal-title d-flex align-items-center" id="addUserModalLabel"><i class="fas fa-user-plus me-2"></i> Add New User
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <!-- BODY -->
            <div class="modal-body p-4">
                <form id="addUserForm" novalidate>
            
                    <!-- NAME FIELDS -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">First Name</label>
                            <input type="text" class="form-control" id="firstName" placeholder="Enter first name" required>
                            <div class="invalid-feedback">First name is required.</div>
                        </div>
                
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Middle Name <span class="text-muted">(optional)</span></label>
                            <input type="text" class="form-control" id="middleName" placeholder="Enter middle name">
                        </div>
                
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Last Name</label>
                            <input type="text" class="form-control" id="lastName" placeholder="Enter last name" required>
                            <div class="invalid-feedback">Last name is required.</div>
                        </div>
                    </div>
            
                    <!-- USERNAME PREVIEW -->
                    <div class="mb-3">
                        <small class="text-muted">Username preview: <strong id="usernamePreview">—</strong></small>
                    </div>
            
                    <!-- EMAIL -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" class="form-control" id="userEmail" placeholder="Enter email" required>
                        <div class="invalid-feedback">Please enter a valid email.</div>
                    </div>
            
                    <!-- ROLE SELECTION -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold d-block mb-2">Select Role</label>
                        <div class="d-flex gap-3 flex-wrap">
                          <div class="role-card card flex-fill p-3 text-center shadow-sm" data-role="user">
                            <i class="fas fa-user fa-2x mb-1"></i><br>User
                          </div>
                          <div class="role-card card flex-fill p-3 text-center shadow-sm" data-role="admin">
                            <i class="fas fa-user-shield fa-2x mb-1"></i><br>Admin
                          </div>
                          <div class="role-card card flex-fill p-3 text-center shadow-sm" data-role="super_admin">
                            <i class="fas fa-user-tie fa-2x mb-1"></i><br>Super Admin
                          </div>
                        </div>
                        <input type="hidden" id="userRole">
                        <div class="invalid-feedback d-block mt-1" id="roleError" style="display:none;">
                          Please select a role.
                        </div>
                    </div>
            
                    <!-- ADMIN BARANGAY SELECTION -->
                    <div id="adminBarangayWrapper" class="mb-3 d-none">
                        <label class="form-label fw-semibold">Barangay Assignment <span class="text-danger">*</span></label>
                        <select id="adminBarangay" class="form-select shadow-sm">
                            <option value="">Select barangay</option>
                            @foreach($barangays as $barangay)
                                <option value="{{ $barangay }}">{{ $barangay }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback d-block mt-1" id="barangayError" style="display:none;"> Please assign a barangay to this admin.
                        </div>
                    </div>
            
                    <!-- PASSWORD FIELDS -->
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Password</label>
                            <input type="password" class="form-control" id="userPassword" placeholder="Enter password" required>
                            <div class="invalid-feedback">Minimum 6 characters.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Confirm Password</label>
                            <input type="password" class="form-control" id="userConfirmPassword" placeholder="Confirm password" required>
                            <div class="invalid-feedback">Passwords do not match.</div>
                        </div>
                    </div>
            
                </form>
            </div>
        
            <!-- FOOTER -->
            <div class="modal-footer border-0 pt-0">
                <button class="btn btn-light rounded-3" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary rounded-3" id="saveUserBtn">
                  <i class="fas fa-check me-1"></i> Save User
                </button>
            </div>
        
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {

  // ROLE SELECTION
  document.querySelectorAll('.role-card').forEach(card => {
      card.addEventListener('click', () => {
          document.querySelectorAll('.role-card').forEach(c => c.classList.remove('selected'));
          card.classList.add('selected');

          const role = card.dataset.role;
          document.getElementById('userRole').value = role;
          document.getElementById('roleError').style.display = 'none';

          // Show barangay only for Admin
          const barangayWrap = document.getElementById('adminBarangayWrapper');
          const barangaySelect = document.getElementById('adminBarangay');

          if (role === 'admin') {
              barangayWrap.classList.remove('d-none');
          } else {
              barangayWrap.classList.add('d-none');
              barangaySelect.value = '';
              document.getElementById('barangayError').style.display = 'none';
          }
      });
  });

  // USERNAME PREVIEW
  const firstNameInput = document.getElementById('firstName');
  const lastNameInput = document.getElementById('lastName');
  const usernamePreview = document.getElementById('usernamePreview');

  function updateUsernamePreview() {
      const first = firstNameInput.value.trim();
      const last = lastNameInput.value.trim();
      usernamePreview.textContent = (first && last) ? first.charAt(0).toLowerCase() + last.toLowerCase().replace(/\s+/g, '') : '—';
  }

  firstNameInput.addEventListener('input', updateUsernamePreview);
  lastNameInput.addEventListener('input', updateUsernamePreview);

  // FORM VALIDATION & SUBMIT
  document.getElementById('saveUserBtn').addEventListener('click', function () {
      const form = document.getElementById('addUserForm');
      let valid = true;

      // FIRST & LAST NAME
      [firstNameInput, document.getElementById('lastName')].forEach(input => {
          if (!input.value.trim()) { input.classList.add('is-invalid'); valid = false; }
          else input.classList.remove('is-invalid');
      });

      // EMAIL
      const emailInput = document.getElementById('userEmail');
      const emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,}$/i;
      if (!emailInput.value.trim() || !emailPattern.test(emailInput.value)) {
          emailInput.classList.add('is-invalid'); valid = false;
      } else emailInput.classList.remove('is-invalid');

      // ROLE
      const role = document.getElementById('userRole').value;
      if (!role) { document.getElementById('roleError').style.display = 'block'; valid = false; }

      // ADMIN BARANGAY
      let assignedBarangay = null;
      if (role === 'admin') {
          const barangaySelect = document.getElementById('adminBarangay');
          if (!barangaySelect.value) { document.getElementById('barangayError').style.display = 'block'; valid = false; }
          else { assignedBarangay = barangaySelect.value; document.getElementById('barangayError').style.display = 'none'; }
      }

      // PASSWORD
      const password = document.getElementById('userPassword').value;
      const confirmPassword = document.getElementById('userConfirmPassword').value;
      if (password.length < 6) { document.getElementById('userPassword').classList.add('is-invalid'); valid = false; }
      else document.getElementById('userPassword').classList.remove('is-invalid');

      if (password !== confirmPassword || !confirmPassword) { document.getElementById('userConfirmPassword').classList.add('is-invalid'); valid = false; }
      else document.getElementById('userConfirmPassword').classList.remove('is-invalid');

      if (!valid) return;

      const username = firstNameInput.value.trim().charAt(0).toLowerCase() + lastNameInput.value.trim().toLowerCase().replace(/\s+/g, '');
      const data = {
          first_name: firstNameInput.value.trim(),
          middle_name: document.getElementById('middleName').value.trim() || null,
          last_name: document.getElementById('lastName').value.trim(),
          username: username,
          email: emailInput.value.trim(),
          role: role,
          barangay: assignedBarangay,
          password: password,
          password_confirmation: confirmPassword
      };

      axios.post("{{ route('super.users.store') }}", data, {
          headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
      })
      .then(() => {
          alert('User created successfully!');
          form.reset();
          usernamePreview.textContent = '—';
          document.querySelectorAll('.role-card').forEach(c => c.classList.remove('selected'));
          document.getElementById('adminBarangayWrapper').classList.add('d-none');
          bootstrap.Modal.getInstance(document.getElementById('addUserModal')).hide();
          fetchUsers();
      })
      .catch(err => {
          console.error(err.response?.data);
          alert('Failed to create user. Check validation.');
      });
  });

});
</script>

<div class="modal fade" id="viewUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
        
            <!-- HEADER -->
            <div class="modal-header bg-primary text-white border-0 flex-column position-relative">
                <div class="icon-circle mb-2">
                    <i class="fas fa-id-card"></i>
                </div>
                <h5 class="fw-bold mb-0">User Information</h5>
                <button class="btn-close btn-close-white position-absolute end-0 top-0 m-3" data-bs-dismiss="modal"></button>
            </div>
        
            <!-- BODY -->
            <div class="modal-body p-4">
                <div class="row g-4 align-items-center">
            
                    <!-- LEFT: USER ICON -->
                    <div class="col-md-4 text-center">
                        <div class="user-id-card">
                          <i class="fas fa-user"></i>
                        </div>
                        <h5 class="mt-3 fw-bold" id="viewUserName">Juan Dela Cruz</h5>
                        <span class="badge bg-success mt-1" id="viewUserStatus">Active</span>
                    </div>
                
                    <!-- RIGHT: DETAILS -->
                    <div class="col-md-8">
                        <div class="info-card">
                            <div class="info-row">
                                <i class="fas fa-id-badge"></i>
                                <span><strong>User ID:</strong> <span id="viewUserId">#1023</span></span>
                            </div>
                
                            <div class="info-row">
                                <i class="fas fa-envelope"></i>
                                <span><strong>Email:</strong> <span id="viewUserEmail"></span></span>
                            </div>
                
                            <div class="info-row">
                                <i class="fas fa-user-shield"></i>
                                <span><strong>Role:</strong> <span id="viewUserRole"></span></span>
                            </div>
                
                            <div class="info-row">
                                <i class="fas fa-calendar"></i>
                                <span><strong>Created:</strong> <span id="viewUserCreated"></span></span>
                            </div>
                        </div>
                    </div>
            
                </div>
            </div>
        
            <!-- FOOTER -->
            <div class="modal-footer border-0 justify-content-center pb-4">
                <button class="btn btn-light px-4" data-bs-dismiss="modal">Close</button>
            </div>
        
        </div>
    </div>
</div>

<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
        
            <!-- HEADER -->
            <div class="modal-header bg-warning text-dark border-0 text-center flex-column position-relative">
                <div class="icon-circle warning mb-2">
                    <i class="fas fa-user-pen"></i>
                </div>
                <h5 class="fw-bold mb-0">Edit User Details</h5>
                <small class="text-dark-50">View and update user information</small>
                <button class="btn-close position-absolute end-0 top-0 m-3" data-bs-dismiss="modal"></button>
            </div>
        
            <!-- BODY -->
            <div class="modal-body p-4" style="max-height:500px; overflow-y:auto;">
                <input type="hidden" id="editUserId">
                <input type="hidden" id="editUserRole">
            
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body">
                        <div class="row g-3">
                
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">First Name</label>
                                <input type="text" id="editFirstName" class="form-control form-control-lg" readonly>
                            </div>
                
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Middle Name</label>
                                <input type="text" id="editMiddleName" class="form-control form-control-lg" readonly>
                            </div>
                
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Last Name</label>
                                <input type="text" id="editLastName" class="form-control form-control-lg" readonly>
                            </div>
                
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Username</label>
                                <input type="text" id="editUsername" class="form-control form-control-lg" readonly>
                            </div>
                
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" id="editEmail" class="form-control form-control-lg" readonly>
                            </div>
                
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Contact</label>
                                <input type="text" id="editContact" class="form-control form-control-lg">
                            </div>
                
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Avatar URL</label>
                                <input type="text" id="editAvatar" class="form-control form-control-lg" readonly>
                            </div>
                          
                            <div class="d-flex justify-content-center mb-3">
                                <div id="editAvatarPreview" class="avatar-initial rounded-circle"
                                    style=" width:100px; height:100px; font-size:36px; color:white; display:flex; align-items:center; justify-content:center; user-select:none; text-transform:uppercase; ">
                                    <!-- Initials will be set by JS -->
                                </div>
                            </div>
                
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Street</label>
                                <input type="text" id="editStreet" class="form-control form-control-lg">
                            </div>
                
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Barangay</label>
                                <input type="text" id="editBarangay" class="form-control form-control-lg">
                            </div>
                    
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Municipality/City</label>
                                <input type="text" id="editMunicipalityCity" class="form-control form-control-lg">
                            </div>
                    
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Province</label>
                                <input type="text" id="editProvince" class="form-control form-control-lg" readonly>
                            </div>
                
                        </div>
                    </div>
                </div>
            
                <!-- ROLE SELECTION -->
                <label class="form-label fw-semibold mb-2">Role</label>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="role-card disabled" data-role="user">
                          <i class="fas fa-user"></i>
                          <h6>User</h6>
                          <p>Standard system access</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="role-card disabled" data-role="admin">
                          <i class="fas fa-user-shield"></i>
                          <h6>Admin</h6>
                          <p>Manage users and records</p>
                        </div>
                    </div>
                </div>
            </div>
        
            <!-- FOOTER -->
            <div class="modal-footer border-0 justify-content-center pb-4 gap-2">
                <button class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-warning px-4 fw-semibold" id="saveUserChanges">
                  <i class="fas fa-save me-1"></i> Save Changes
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.role-card').forEach(card => {
    card.addEventListener('click', () => {
    
        // Remove active from all
        document.querySelectorAll('.role-card').forEach(c => c.classList.remove('active'));
    
        // Activate selected
        card.classList.add('active');
    
        // Store role
        document.getElementById('editUserRole').value = card.dataset.role;
    });
});
</script>

<!-- DEACTIVATE USER  -->
<div class="modal fade" id="deactivateUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            
            <!-- HEADER -->
            <div class="modal-header bg-danger text-white border-0 flex-column position-relative">
                <div class="icon-circle danger mb-2">
                    <i class="fas fa-user-slash"></i>
                </div>
                <h5 class="fw-bold mb-0">Deactivate User</h5>
                <button class="btn-close btn-close-white position-absolute end-0 top-0 m-3" data-bs-dismiss="modal"></button>
            </div>
        
            <!-- BODY -->
            <div class="modal-body text-center p-4">
                <p class="text-muted mb-3"> Are you sure you want to deactivate this user? </p>
                <h6 class="fw-bold" id="deactivateUserName"></h6>
                <small class="text-muted">The user will lose system access.</small>
            </div>
        
            <!-- FOOTER -->
            <div class="modal-footer border-0 justify-content-center pb-4">
                <button class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-danger px-4" id="confirmDeactivateUser">
                  <i class="fas fa-ban me-1"></i> Deactivate
                </button>
            </div>
        </div>
    </div>
</div>

<style>

</style>

<!-- ADD FAQ CONFIRM -->
<div class="modal fade" id="confirmAddFaqModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <div class="modal-header bg-light fw-bold">
        <i class="fas fa-plus-circle me-2 text-primary"></i>
        <h5 class="modal-title">Confirm Add FAQ</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body px-4 py-3">
        Are you sure you want to add this FAQ?
      </div>
      <div class="modal-footer border-0">
        <button type="button" class="btn btn-secondary fw-semibold" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary fw-semibold" id="confirmAddFaqBtn">
          <i class="fas fa-check me-1"></i> Add
        </button>
      </div>
    </div>
  </div>
</div>

<!-- SAVE FAQ CONFIRM -->
<div class="modal fade" id="confirmSaveFaqModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <div class="modal-header bg-light fw-bold">
        <i class="fas fa-save me-2 text-success"></i>
        <h5 class="modal-title">Confirm Save FAQ</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body px-4 py-3">
        Are you sure you want to save changes to this FAQ?
      </div>
      <div class="modal-footer border-0">
        <button type="button" class="btn btn-secondary fw-semibold" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-success fw-semibold" id="confirmSaveFaqBtn">
          <i class="fas fa-check me-1"></i> Save
        </button>
      </div>
    </div>
  </div>
</div>

<!-- DELETE FAQ CONFIRM -->
<div class="modal fade" id="confirmDeleteFaqModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <div class="modal-header bg-light fw-bold">
        <i class="fas fa-trash me-2 text-danger"></i>
        <h5 class="modal-title">Confirm Delete FAQ</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body px-4 py-3" id="deleteFaqMessage">
        Are you sure you want to delete this FAQ?
      </div>
      <div class="modal-footer border-0">
        <button type="button" class="btn btn-secondary fw-semibold" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger fw-semibold" id="confirmDeleteFaqBtn">
          <i class="fas fa-trash me-1"></i> Delete
        </button>
      </div>
    </div>
  </div>
</div>

<!-- ================= PDF EXPORT MODAL ================= -->
<div class="modal fade" id="exportPdfModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow">

      <div class="modal-header bg-danger text-white rounded-top-4">
        <h5 class="modal-title fw-bold">
          <i class="fas fa-file-pdf me-2"></i>Export PDF Report
        </h5>
        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body p-4">
        <div class="mb-3">
          <label class="form-label fw-semibold">Year</label>
          <select class="form-select" id="pdfYear">
            <option value="">All Years</option>
            <option>2026</option>
            <option>2025</option>
            <option>2024</option>
            <option>2023</option>
          </select>
        </div>

        <div>
          <label class="form-label fw-semibold">Month</label>
          <select class="form-select" id="pdfMonth">
            <option value="">All Months</option>
            <option value="01">January</option>
            <option value="02">February</option>
            <option value="03">March</option>
            <option value="04">April</option>
            <option value="05">May</option>
            <option value="06">June</option>
            <option value="07">July</option>
            <option value="08">August</option>
            <option value="09">September</option>
            <option value="10">October</option>
            <option value="11">November</option>
            <option value="12">December</option>
          </select>
        </div>
      </div>

      <div class="modal-footer px-4 pb-4">
        <button class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-danger fw-bold" id="downloadPdfBtn">
          <i class="fas fa-download me-1"></i>Download PDF
        </button>
      </div>

    </div>
  </div>
</div>

<!-- ================= CSV EXPORT MODAL ================= -->
<div class="modal fade" id="exportCsvModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow">

      <div class="modal-header bg-success text-white rounded-top-4">
        <h5 class="modal-title fw-bold">
          <i class="fas fa-file-csv me-2"></i>Export CSV / Excel
        </h5>
        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body p-4">
        <div class="mb-3">
          <label class="form-label fw-semibold">Year</label>
          <select class="form-select" id="csvYear">
            <option value="">All Years</option>
            <option>2026</option>
            <option>2025</option>
            <option>2024</option>
            <option>2023</option>
          </select>
        </div>

        <div>
          <label class="form-label fw-semibold">Month</label>
          <select class="form-select" id="csvMonth">
            <option value="">All Months</option>
            <option value="01">January</option>
            <option value="02">February</option>
            <option value="03">March</option>
            <option value="04">April</option>
            <option value="05">May</option>
            <option value="06">June</option>
            <option value="07">July</option>
            <option value="08">August</option>
            <option value="09">September</option>
            <option value="10">October</option>
            <option value="11">November</option>
            <option value="12">December</option>
          </select>
        </div>
      </div>

      <div class="modal-footer px-4 pb-4">
        <button class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-success fw-bold" id="downloadCsvBtn">
          <i class="fas fa-download me-1"></i>Download CSV
        </button>
      </div>

    </div>
  </div>
</div>

<!-- VIEW MODAL -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow-lg rounded-4">

      <!-- HEADER -->
      <div class="modal-header bg-primary text-white border-0 rounded-top">
        <div class="d-flex align-items-center">
          <div class="icon-circle bg-white me-3">
            <i class="fas fa-user fa-lg text-primary"></i>
          </div>
          <h5 class="modal-title fw-bold" id="viewModalLabel">Application Details</h5>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <!-- BODY -->
      <div class="modal-body p-4" id="viewDetails" style="max-height:70vh; overflow-y:auto;">
        <!-- Dynamic content will be inserted here from JS -->
      </div>

      <!-- FOOTER -->
      <div class="modal-footer border-0 justify-content-end">
        <button type="button" class="btn btn-secondary fw-semibold" data-bs-dismiss="modal">
          <i class="fas fa-times me-1"></i> Close
        </button>
      </div>

    </div>
  </div>
</div>

<!-- ================= BENEFICIARY PDF EXPORT MODAL ================= -->
<div class="modal fade" id="beneficiaryPdfExportModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4">
        
      <div class="modal-header bg-danger text-white rounded-top-4">
        <h5 class="modal-title fw-bold">
          <i class="fas fa-file-pdf me-2"></i>
          Export Beneficiary List (PDF)
        </h5>
        
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label fw-semibold">Year</label>
          <select id="beneficiaryPdfYear" class="form-select">
            <option value="all">All Years</option>
            <option>2026</option>
            <option>2025</option>
            <option>2024</option>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold">Month</label>
          <select id="beneficiaryPdfMonth" class="form-select">
            <option value="all">All Months</option>
            <option value="01">January</option>
            <option value="02">February</option>
            <option value="03">March</option>
            <option value="04">April</option>
            <option value="05">May</option>
            <option value="06">June</option>
            <option value="07">July</option>
            <option value="08">August</option>
            <option value="09">September</option>
            <option value="10">October</option>
            <option value="11">November</option>
            <option value="12">December</option>
          </select>
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button id="confirmBeneficiaryPdfExport" class="btn btn-danger fw-semibold">
          <i class="fas fa-download me-1"></i> Export PDF
        </button>
      </div>
    </div>
  </div>
</div>

<!-- ================= BENEFICIARY EXCEL EXPORT MODAL ================= -->
<div class="modal fade" id="beneficiaryExcelExportModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4">
      <div class="modal-header bg-success text-white rounded-top-4">
        <h5 class="modal-title fw-bold">
          <i class="fas fa-file-csv me-2"></i>
          Export Beneficiary List (Excel)
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label fw-semibold">Year</label>
          <select id="beneficiaryExcelYear" class="form-select">
            <option value="all">All Years</option>
            <option>2026</option>
            <option>2025</option>
            <option>2024</option>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold">Month</label>
          <select id="beneficiaryExcelMonth" class="form-select">
            <option value="all">All Months</option>
            <option value="01">January</option>
            <option value="02">February</option>
            <option value="03">March</option>
            <option value="04">April</option>
            <option value="05">May</option>
            <option value="06">June</option>
            <option value="07">July</option>
            <option value="08">August</option>
            <option value="09">September</option>
            <option value="10">October</option>
            <option value="11">November</option>
            <option value="12">December</option>
          </select>
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button id="confirmBeneficiaryExcelExport" class="btn btn-success fw-semibold">
          <i class="fas fa-download me-1"></i> Export Excel
        </button>
      </div>
    </div>
  </div>
</div>

<!-- VIEW BENEFICIARY MODAL -->
<div class="modal fade" id="beneficiaryViewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-sm rounded-3">

            <!-- Modal Header -->
            <div class="modal-header text-white" style="background-color: #003366;">
                <h5 class="modal-title"><i class="bi bi-person-circle"></i> Beneficiary Information</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body px-4 py-3">

                <!-- Personal Info Section -->
                <div class="mb-4 p-3 bg-light rounded-3 shadow-sm">
                    <h6 class="fw-bold mb-3"><i class="bi bi-info-circle"></i> Personal Info</h6>
                    <div class="row mb-2">
                        <div class="col-sm-4 fw-semibold">Name:</div>
                        <div class="col-sm-8" id="beneficiaryViewName">-</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 fw-semibold">Address:</div>
                        <div class="col-sm-8" id="beneficiaryViewAddress">-</div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 fw-semibold">Barangay:</div>
                        <div class="col-sm-8" id="beneficiaryViewBarangay">-</div>
                    </div>
                </div>

                <!-- Status & Category Section -->
                <div class="mb-4 p-3 bg-light rounded-3 shadow-sm">
                    <h6 class="fw-bold mb-3"><i class="bi bi-clipboard-check"></i> Status & Category</h6>
                    <div class="row mb-2">
                        <div class="col-sm-4 fw-semibold">Assistance Status:</div>
                        <div class="col-sm-8" id="beneficiaryViewStatus">-</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 fw-semibold">Category:</div>
                        <div class="col-sm-8" id="beneficiaryViewCategory">-</div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 fw-semibold">Date Added:</div>
                        <div class="col-sm-8" id="beneficiaryViewCreatedAt">-</div>
                    </div>
                </div>

                <!-- Benefits Section -->
                <div class="p-3 bg-light rounded-3 shadow-sm">
                    <h6 class="fw-bold mb-3"><i class="bi bi-gift"></i> Benefits</h6>
                    <div id="beneficiaryViewBenefits" class="d-flex flex-wrap gap-2">
                        <!-- Benefit badges will be inserted here dynamically -->
                        <span class="badge bg-success">-</span>
                    </div>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="modal-footer justify-content-center">
                <button class="btn btn-secondary fw-semibold" data-bs-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>



<!-- DELETE BENEFICIARY MODAL -->
<div class="modal fade" id="beneficiaryDeleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4">

      <!-- Header -->
      <div class="modal-header border-0 bg-danger text-white text-center flex-column position-relative">
        <div class="delete-icon mb-2">
          <i class="fas fa-trash fa-2x"></i>
        </div>
        <h5 class="modal-title fw-bold">Delete Beneficiary</h5>
        <button type="button" class="btn-close btn-close-white position-absolute end-0 top-0 m-3" data-bs-dismiss="modal"></button>
      </div>

      <!-- Body -->
      <div class="modal-body text-center px-4">
        <p class="mb-2">Are you sure you want to delete this beneficiary?</p>
        <p class="small text-muted mb-0">This action cannot be undone.</p>
      </div>

      <!-- Footer -->
      <div class="modal-footer border-0 justify-content-center gap-2 pb-4">
        <button class="btn btn-danger px-4 fw-semibold" id="confirmDeleteBeneficiary">
          <i class="fas fa-trash me-1"></i> Delete
        </button>
        <button class="btn btn-light px-4 fw-semibold" data-bs-dismiss="modal">
          <i class="fas fa-times me-1"></i> Cancel
        </button>
      </div>

    </div>
  </div>
</div>

<!-- ✅ ACTIVATE USER CONFIRMATION MODAL -->
<div class="modal fade" id="activateUserModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">

      <div class="modal-header bg-success text-white">
        <h5 class="modal-title">
          <i class="fas fa-user-check me-2"></i>Activate Account
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body text-center">
        <p class="mb-2">
          Are you sure you want to activate this account?
        </p>
        <h6 class="fw-bold text-success" id="activateUserName"></h6>
      </div>

      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          Cancel
        </button>
        <button type="button" class="btn btn-success" id="confirmActivateUser">
          <i class="fas fa-check me-1"></i> Yes, Activate
        </button>
      </div>

    </div>
  </div>
</div>

<!-- ================= FULLSCREEN MODAL ================= -->
<div class="modal fade" id="chartFullscreenModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-fullscreen">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="fullscreenTitle">Chart</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <canvas id="fullscreenChart" style="width:100%;height:100%;"></canvas>
      </div>
    </div>
  </div>
</div>


<!-- PDF Export Modal -->
<div class="modal fade" id="soloParentPdfExportModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white rounded-top-4">
          <i class="fas fa-file-pdf me-2"></i>
        <h5 class="modal-title">Export Solo Parent Beneficiaries - PDF</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="soloParentPdfYear" class="form-label">Year</label>
          <input type="number" id="soloParentPdfYear" class="form-control" value="{{ now()->year }}">
          
        </div>
        <div class="mb-3">
          <label for="soloParentPdfMonth" class="form-label">Month</label>
          <select id="soloParentPdfMonth" class="form-select">
            <option value="all" selected>All Months</option>
            @for ($m = 1; $m <= 12; $m++)
              <option value="{{ $m }}">{{ DateTime::createFromFormat('!m', $m)->format('F') }}</option>
            @endfor
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" id="confirmSoloParentPdfExport" class="btn btn-danger">Export PDF</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>

<!-- Excel Export Modal -->
<div class="modal fade" id="soloParentExcelExportModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-success text-white rounded-top-4">
          <i class="fas fa-file-csv me-2"></i>
        <h5 class="modal-title">Export Solo Parent Beneficiaries - Excel</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="soloParentExcelYear" class="form-label">Year</label>
          <input type="number" id="soloParentExcelYear" class="form-control" value="{{ now()->year }}">
        </div>
        <div class="mb-3">
          <label for="soloParentExcelMonth" class="form-label">Month</label>
          <select id="soloParentExcelMonth" class="form-select">
            <option value="all" selected>All Months</option>
            @for ($m = 1; $m <= 12; $m++)
              <option value="{{ $m }}">{{ DateTime::createFromFormat('!m', $m)->format('F') }}</option>
            @endfor
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" id="confirmSoloParentExcelExport" class="btn btn-success">Export Excel</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>

<!-- Excel Export Modal -->
<div class="modal fade" id="exportExcelModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-success text-white rounded-top-4">
        <i class="fas fa-file-csv me-2"></i>
        <h5 class="modal-title">Export Solo Parent Beneficiaries - Excel</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div class="mb-3">
          <label for="excelYear" class="form-label">Year</label>
          <input type="number" id="excelYear" class="form-control" value="{{ now()->year }}">
        </div>

        <div class="mb-3">
          <label for="excelMonth" class="form-label">Month</label>
          <select id="excelMonth" class="form-select">
            <option value="all" selected>All Months</option>
            @for ($m = 1; $m <= 12; $m++)
              <option value="{{ $m }}">
                {{ DateTime::createFromFormat('!m', $m)->format('F') }}
              </option>
            @endfor
          </select>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" id="confirmExcelExport" class="btn btn-success">
          Export Excel
        </button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          Cancel
        </button>
      </div>
    </div>
  </div>
</div>


@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

  // 🖼️ System Logo Preview Upload
  const logoInput = document.getElementById("systemLogo");
  if (logoInput) {
    logoInput.addEventListener("change", (e) => {
      const file = e.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = (ev) => {
          document.getElementById("logoPreview").src = ev.target.result;
        };
        reader.readAsDataURL(file);
      }
    });
  }

  // 📁 Toggle Sidebar Submenu
  document.querySelectorAll('.has-submenu .submenu-toggle').forEach(toggle => {
    toggle.addEventListener('click', e => {
      e.stopPropagation();
      const submenu = toggle.nextElementSibling;
      submenu.classList.toggle('show');
      toggle.classList.toggle('active');
      document.querySelectorAll('.has-submenu .submenu-toggle').forEach(other => {
        if (other !== toggle) {
          other.nextElementSibling.classList.remove('show');
          other.classList.remove('active');
        }
      });
    });
  });

  // 🧭 Show Section When Menu Clicked
  document.querySelectorAll('.menu li[data-target]').forEach(item => {
    item.addEventListener('click', e => {
      e.stopPropagation();
      document.querySelectorAll('.content-section').forEach(section => {
        section.style.display = 'none';
      });
      document.querySelectorAll('.menu li').forEach(li => li.classList.remove('active'));

      const targetId = item.getAttribute('data-target');
      const targetSection = document.getElementById(targetId);

      if (targetSection) {
        targetSection.style.display = 'block';
        targetSection.classList.remove('fade-up');
        void targetSection.offsetWidth;
        targetSection.classList.add('fade-up');
      }

      item.classList.add('active');

      if (targetId === 'dashboard-section') {
        targetSection.classList.add('active');
      } else {
        const dashboard = document.getElementById('dashboard-section');
        if (dashboard) dashboard.classList.remove('active');
      }
    });
  });

  // 📉 Sidebar Collapse Toggle
  window.toggleSidebar = function() {
    const sidebar = document.querySelector(".sidebar");
    const mainContent = document.querySelector(".main-content");
    sidebar.classList.toggle("hidden");
    mainContent.classList.toggle("expanded");
  };
});
</script>

@endsection
