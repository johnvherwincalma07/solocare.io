@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endsection

@section('content')
<div id="loadingOverlay" style="display:flex; position:fixed; top:0; left:0; right:0; bottom:0; background:#fcfcfc; z-index:12000; justify-content:center; align-items:center;">
    <img src="{{ asset('images/SCM.gif') }}" alt="Loading..." style="width:120px;height:120px;">
</div>

<header class="admin-topbar">
    <div class="topbar-left">
        <button class="toggle-btn" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
        <img src="{{ asset('images/SC.svg') }}" alt="Logo" class="topbar-logo">
        <div class="topbar-text">
            <h2 class="topbar-title">{{ $system->system_brand_name }}</h2>
            <p class="topbar-subtitle">Administrator - Admin</p>
        </div>
    </div>
    <div class="topbar-right d-flex align-items-center gap-2">
        <div class="dropdown">
            <button class="btn notif-btn position-relative" type="button" id="notifDropdown" data-bs-toggle="dropdown">
                <i class="fas fa-bell"></i>

                @php
                    $totalNotifications = ($newApplicationsCount + $newChatCount) ?? 0;
                @endphp

                @if($totalNotifications > 0)
                    <span class="notif-badge">
                        {{ $totalNotifications }}
                    </span>
                @endif
            </button>

            <ul class="dropdown-menu dropdown-menu-end modern-dropdown shadow-lg p-2" aria-labelledby="notifDropdown">

                <li class="dropdown-header fw-bold text-dark px-2">Notifications</li>
                <li><hr class="dropdown-divider my-1"></li>

                {{-- New Applications --}}
                @forelse($recentApplications as $app)
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2 py-2 modern-item" href="#">
                            <div class="icon-circle bg-primary-subtle text-primary">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <div class="lh-sm">
                                <strong>New Application</strong><br>
                                <small class="text-muted">{{ $app->created_at->diffForHumans() }}</small>
                            </div>
                        </a>
                    </li>
                @empty
                    <li><p class="dropdown-item text-muted text-center py-2">No new applications</p></li>
                @endforelse

                {{-- New Chats --}}
                @forelse($recentChats as $msg)
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2 py-2 modern-item" href="{{ route('admin.dashboard') }}#chat-section">
                            <div class="icon-circle bg-success-subtle text-success">
                                <i class="fas fa-comment"></i>
                            </div>
                            <div class="lh-sm">
                                <strong>{{ $msg->sender->username ?? 'User' }}</strong>: {{ Str::limit($msg->message, 25) }}<br>
                                <small class="text-muted">{{ $msg->created_at->diffForHumans() }}</small>
                            </div>
                        </a>
                    </li>
                @empty
                    <li><p class="dropdown-item text-muted text-center py-2">No new chat messages</p></li>
                @endforelse

            </ul>
        </div>

        <div class="dropdown ms-2">
            <button class="btn notif-btn position-relative" type="button" id="profileDropdown" data-bs-toggle="dropdown">
                <img src="{{ asset('images/avatar.png') }}" class="profile-avatar" alt="Profile">
            </button>
    
            <ul class="dropdown-menu dropdown-menu-end modern-dropdown shadow-lg p-2" aria-labelledby="profileDropdown">
                <li class="dropdown-header fw-bold text-dark px-2">Account</li>
                
                <li><hr class="dropdown-divider my-1"></li>
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2 modern-item" href="#">
                        <div class="icon-circle bg-primary-subtle text-primary"><i class="fas fa-user"></i></div>
                        <span>Profile</span>
                    </a>
                </li>
        
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2 modern-item" href="#">
                        <div class="icon-circle bg-secondary-subtle text-secondary"><i class="fas fa-sliders-h"></i></div>
                        <span>Settings</span>
                    </a>
                </li>
        
                <li><hr class="dropdown-divider my-1"></li>
        
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2 modern-item text-danger" href="{{ route('logout') }}">
                        <div class="icon-circle bg-danger-subtle text-danger"><i class="fas fa-sign-out-alt"></i></div>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</header>

<aside class="sidebar">
    <ul class="menu">
        <li class="active" data-target="dashboard-section"><i class="fas fa-home fa-fw me-2"></i> Dashboard</li>
        <li data-target="attendance-section"><i class="fas fa-check fa-fw me-2"></i> Attendance</li>
        <li class="has-submenu">
            <div class="submenu-toggle">
                <i class="fas fa-history fa-fw me-2"></i> Solo Parent
                <i class="fas fa-caret-down float-end"></i>
            </div>
            <ul class="submenu">
                <li data-target="application-section">Applications</li>
                <li data-target="schedule-submission-section">Schedule Submission</li>
                <li data-target="homevisit-section">Home Visit Scheduled</li>
                <li data-target="send-requirements-section">Ready to Submission</li>
            </ul>
        </li>
        <li data-target="solo-parents-section"><i class="fas fa-list fa-fw me-2"></i> Solo Parent Beneficiary Tracker</li>
        <li data-target="benefits-section"><i class="fas fa-gift fa-fw me-2"></i> Benefits</li>
        <li data-target="announcement-section"><i class="fas fa-bullhorn fa-fw me-2"></i> Announcement</li>
        <li data-target="report-section"><i class="fas fa-chart-bar fa-fw me-2"></i> Report</li>
        <li data-target="chat-section"><i class="fas fa-comments fa-fw me-2"></i> Chat</li>
        <li data-target="settings-section"><i class="fas fa-cogs fa-fw me-2"></i> System Settings</li>
        <li id="logoutBtn"><i class="fas fa-sign-out-alt fa-fw me-2"></i> Logout</li>
    </ul>
</aside>

<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">

            <div class="modal-header border-0 bg-danger text-white text-center flex-column position-relative">
                <div class="logout-icon mb-2"><i class="fas fa-sign-out-alt fa-2x"></i></div>
                <h5 class="modal-title fw-bold" id="logoutModalLabel">Confirm Logout</h5>
                <button type="button" class="btn-close btn-close-white position-absolute end-0 top-0 m-3" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body text-center px-4">
                <p class="text-muted mb-3">Are you sure you want to log out of your account?</p>
            </div>
            
            <div class="modal-footer border-0 justify-content-center gap-2 pb-4">
                <button type="button" class="btn btn-danger px-4 fw-semibold" id="confirmLogout"><i class="fas fa-sign-out-alt me-1"></i> Logout </button>
                <button type="button" class="btn btn-light px-4 fw-semibold" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i> Cancel </button>
            </div>
    
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {

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

    const overlay = document.getElementById('loadingOverlay');
    if (overlay) {
        setTimeout(() => {
            overlay.style.transition = 'opacity 0.5s';
            overlay.style.opacity = 0;
            setTimeout(() => { overlay.style.display = 'none'; }, 500);
        }, 1000);
    }
});
</script>

<!-- MAIN CONTENT -->
<main class="main-content fade-in">

<!-- DASHBOARD SECTION -->
<div id="dashboard-section" class="content-section" style="display: block;">
    <div class="dashboard-container d-flex gap-4">
        <!-- LEFT PANEL -->
        <div class="left-panel">
            <div class="welcome-card d-flex align-items-center gap-3 mb-4 p-3 shadow-sm">
                <i class="fas fa-user-circle fa-3x text-primary"></i>
                <div>
                    <h2 class="fw-bold">Tejero Barangay</h2>
                    <p class="mb-0 fw-semibold" style="color: #ffc107;">General Trias • Barangay Administrator</p>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="card stat-card stat-blue shadow-sm border-0 rounded-4 p-3">
                        <div class="d-flex align-items-center">
                            <div class="icon-circle-logo bg-primary me-3"><i class="fas fa-file-alt fa-lg text-white"></i></div>
                            <div class="text-start">
                                <h5 class="mb-0 text-primary">{{ $totalApplications ?? 0 }}</h5>
                                <small class="text-muted">Total Applications</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card stat-card stat-yellow shadow-sm border-0 rounded-4 p-3">
                        <div class="d-flex align-items-center">
                            <div class="icon-circle-logo bg-warning me-3"><i class="fas fa-hourglass-half fa-lg text-white"></i></div>
                            <div class="text-start">
                                <h5 class="mb-0 text-warning">{{ $pendingApplications ?? 0 }}</h5>
                                <small class="text-muted">Pending Reviews</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card stat-card stat-green shadow-sm border-0 rounded-4 p-3">
                        <div class="d-flex align-items-center">
                            <div class="icon-circle-logo bg-success me-3"><i class="fas fa-users fa-lg text-white"></i></div>
                            <div class="text-start">
                                <h5 class="mb-0 text-success">{{ $totalUsers ?? 0 }}</h5>
                                <small class="text-muted">Total Users</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card stat-card stat-red shadow-sm border-0 rounded-4 p-3">
                        <div class="d-flex align-items-center">
                            <div class="icon-circle-logo bg-danger me-3"><i class="fas fa-user-check fa-lg text-white"></i></div>
                            <div class="text-start">
                                <h5 class="mb-0 text-danger">{{ $activeUsers ?? 0 }}</h5>
                                <small class="text-muted">Active Users</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Charts -->
            <div class="chart-section mt-4">
                <!-- WEEKLY HOME VISIT CALENDAR -->
                <div class="chart-card p-3 shadow-sm rounded">
                    <h4><i class="fas fa-calendar-week text-primary me-1"></i> Weekly Schedule</h4>
                    <p class="text-muted">View schedules for <strong>Barangay Tejero, General Trias</strong></p>
                  
                    <div class="weekly-header d-flex justify-content-between align-items-center mb-2">
                        <button id="prevWeek" class="btn btn-sm btn-outline-secondary">‹ Prev</button>
                            <h6 id="weekLabel" class="fw-bold mb-0"></h6>
                        <button id="nextWeek" class="btn btn-sm btn-outline-secondary">Next ›</button>
                    </div>
                    <div class="weekly-grid" id="weeklyCalendar"></div>
                </div>
            
                <!-- CHARTS IN ONE ROW -->
                <div class="row g-4">
                    <div class="col-md-6">
                        <!-- Approval vs Rejection Chart -->
                        <div class="chart-card p-3 shadow-sm rounded h-100 chart-animate">
                            <h4>Approval vs Rejection Trend</h4>
                            <p class="chart-info text-muted">Barangay Tejero, General Trias</p>
                            <canvas id="approvalTrendChartRight" height="160"></canvas>
                        </div>
                    </div>
                    <!-- Application Submission Trend -->
                    <div class="col-md-6">
                        <div class="chart-card p-3 shadow-sm rounded chart-animate h-100">
                            <h4> Application Submission Trend</h4>
                            <p class="chart-info text-muted">Barangay Tejero, General Trias</p>
                            <canvas id="submissionTrendChart" height="160"></canvas>
                        </div>
                     </div>
                </div>
            </div>
        </div>

        <!-- RIGHT PANEL -->
        <div class="right-panel" style="width: 380px;">
            <div class="card calendar shadow-sm p-3 ">
                <h4 class="fw-bold">Calendar</h4>
                <div class="dash-calendar-container mt-2">
                    <div class="dash-calendar-header d-flex justify-content-between align-items-center mb-2">
                        <button id="dashPrevMonth" class="btn btn-sm btn-outline-secondary">&lt;</button>
                        <h5 id="dashCalendarMonth" class="mb-0"></h5>
                        <button id="dashNextMonth" class="btn btn-sm btn-outline-secondary">&gt;</button>
                    </div>
                    <table class="dash-calendar-table">
                        <thead>
                            <tr>
                                <th>Sun</th>
                                <th>Mon</th>
                                <th>Tue</th>
                                <th>Wed</th>
                                <th>Thu</th>
                                <th>Fri</th>
                                <th>Sat</th>
                            </tr>
                        </thead>
                        <tbody id="dashCalendarBody"></tbody>
                    </table>
                </div>
            </div>

            <div class="card activity shadow-sm p-3">
                <h4 class="fw-bold">Recent Activity</h4>
                <ul class="activity-list mt-3 list-unstyled">
                    @forelse($recentActivities as $activity)
                        <li class="d-flex align-items-start mb-2">
                            <span class="status {{ $activity['type'] }}"></span>
            
                            <div class="ms-2">
                                <strong>{{ $activity['text'] }}</strong><br>
                                <small class="text-muted">
                                    {{ $activity['name'] }} •
                                    {{ \Carbon\Carbon::parse($activity['time'])->diffForHumans() }}
                                </small>
                            </div>
                        </li>
                    @empty
                        <li class="text-muted">No recent system activity</li>
                    @endforelse
                </ul> 
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
    
        const dashBody = document.getElementById("dashCalendarBody");
        const dashMonthLabel = document.getElementById("dashCalendarMonth");
        let dashCurrentDate = new Date();
    
        function renderDashboardCalendar() {
            const year = dashCurrentDate.getFullYear();
            const month = dashCurrentDate.getMonth();
    
            dashMonthLabel.textContent = dashCurrentDate.toLocaleString("en-US", {
                month: "long",
                year: "numeric"
            });
    
            dashBody.innerHTML = "";
            const firstDay = new Date(year, month, 1).getDay();
            const totalDays = new Date(year, month + 1, 0).getDate();
    
            let row = document.createElement("tr");
    
            for (let i = 0; i < firstDay; i++) {
                let cell = document.createElement("td");
                cell.classList.add("dash-empty");
                row.appendChild(cell);
            }
    
            for (let day = 1; day <= totalDays; day++) {
                if (row.children.length === 7) {
                    dashBody.appendChild(row);
                    row = document.createElement("tr");
                }
    
                let cell = document.createElement("td");
                cell.textContent = day;
    
                let today = new Date();
                if (
                    today.getDate() === day &&
                    today.getMonth() === month &&
                    today.getFullYear() === year
                ) {
                    cell.classList.add("today");
                }
    
                row.appendChild(cell);
            }
    
            dashBody.appendChild(row);
        }
    
        document.getElementById("dashPrevMonth").onclick = () => {
            dashCurrentDate.setMonth(dashCurrentDate.getMonth() - 1);
            renderDashboardCalendar();
        };
    
        document.getElementById("dashNextMonth").onclick = () => {
            dashCurrentDate.setMonth(dashCurrentDate.getMonth() + 1);
            renderDashboardCalendar();
        };
    
        renderDashboardCalendar();
    
        const weeklyContainer = document.getElementById("weeklyCalendar");
        const weekLabel = document.getElementById("weekLabel");
        let currentWeek = new Date();
    
        const homeVisits = {
            "2025-12-10": "Home Visit",
            "2025-12-12": "Document submission Schedule",
            "2025-12-14": "Follow-up"
        };
    
        function startOfWeek(date) {
            const d = new Date(date);
            d.setDate(d.getDate() - d.getDay());
            return d;
        }
    
        function renderWeek() {
            weeklyContainer.innerHTML = "";
            const start = startOfWeek(currentWeek);
            const end = new Date(start);
            end.setDate(start.getDate() + 6);
    
            weekLabel.textContent =
                start.toLocaleDateString("en-US", { month: "short", day: "numeric" }) +
                " - " +
                end.toLocaleDateString("en-US", { month: "short", day: "numeric", year: "numeric" });
    
            for (let i = 0; i < 7; i++) {
                const day = new Date(start);
                day.setDate(start.getDate() + i);
    
                const dateKey = day.toISOString().split("T")[0];
                const div = document.createElement("div");
                div.className = "week-day";
    
                if (day.toDateString() === new Date().toDateString()) {
                    div.classList.add("today");
                }
    
                div.innerHTML = `
                    <div class="day-name">${day.toLocaleDateString("en-US", { weekday: "short" })}</div>
                    <div class="fw-bold">${day.getDate()}</div>
                `;
    
                if (homeVisits[dateKey]) {
                    const badge = document.createElement("span");
                    badge.className = "visit-badge";
                    badge.textContent = homeVisits[dateKey];
                    div.appendChild(badge);
                }
    
                weeklyContainer.appendChild(div);
            }
        }
    
        document.getElementById("prevWeek").onclick = () => {
            currentWeek.setDate(currentWeek.getDate() - 7);
            renderWeek();
        };
    
        document.getElementById("nextWeek").onclick = () => {
            currentWeek.setDate(currentWeek.getDate() + 7);
            renderWeek();
        };
    
        renderWeek();
    
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // ACTIVE VS INACTIVE USERS
    new Chart(document.getElementById("usersChart"), {
        type: "doughnut",
        data: { labels: ["Active", "Inactive"], datasets: [{ data: [120, 80], backgroundColor: ["#28a745", "#dc3545"] }] }
    });
    
    // SUBMISSION TREND
    new Chart(document.getElementById("submissionTrendChart"), {
        type: "line",
        data: {
            labels: ["Jul", "Aug", "Sept", "Oct", "Nov", "Dec"],
            datasets: [{ label: "Submissions", data: [0, 0, 0, 0, 10, 25], fill: true }]
        }
    });
    
    // APPROVAL TREND
    new Chart(document.getElementById("approvalTrendChartRight"), {
        type: "bar",
        data: {
            labels: ["Jan", "Feb", "Mar", "Apr", "May"],
            datasets: [
                { label: "Approved", data: [20, 30, 40, 60, 55] },
                { label: "Rejected", data: [5, 10, 7, 12, 9] }
            ]
        }
    });
    
    // USER ACTIVITY TREND
    new Chart(document.getElementById("userTrendChartRight"), {
        type: "line",
        data: {
            labels: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
            datasets: [{ label: "Logins", data: [10, 20, 25, 30, 22, 40, 33] }]
        }
    });
</script>

<!-- ATTENDANCE SECTION -->
<div id="attendance-section" class="content-section" style="display:none;">
    <div class="admin-container">
        
        <div class="d-flex flex-column mb-3">
            <h2 class="fw-bold mb-1"><i class="fas fa-clipboard me-2"></i> Attendance Management</h2>
            <p class="text-muted mb-3">Barangay Tejero, General Trias</p>
        </div>

        <div class="row g-3 mb-4" id="activityStats">
            <div class="col-6 col-md-3">
                <div class="card stat-card stat-blue shadow-sm border-0 rounded-4 p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle-logo bg-primary me-3"><i class="fas fa-chalkboard-teacher fa-lg text-white"></i></div>
                        <div>
                            <h5 class="mb-0" id="seminarsCount">0</h5>
                            <small class="text-muted">Seminars Held</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card stat-card stat-yellow shadow-sm border-0 rounded-4 p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle-logo bg-warning me-3"><i class="fas fa-calendar-alt fa-lg text-white"></i></div>
                        <div>
                            <h5 class="mb-0" id="eventsCount">0</h5>
                            <small class="text-muted">Events Conducted</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card stat-card stat-green shadow-sm border-0 rounded-4 p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle-logo bg-success me-3"><i class="fas fa-handshake fa-lg text-white"></i></div>
                        <div>
                            <h5 class="mb-0" id="meetingsCount">0</h5>
                            <small class="text-muted">Meetings Organized</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card stat-card stat-red shadow-sm border-0 rounded-4 p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle-logo bg-danger me-3"><i class="fas fa-home fa-lg text-white"></i></div>
                        <div>
                            <h5 class="mb-0" id="visitsCount">0</h5>
                            <small class="text-muted">Home Visits</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB NAVIGATION -->
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-2 mt-2">
            <div class="tab-bar d-flex flex-wrap mb-0">
                <button class="tab-btn active me-2" onclick="openTab(event, 'overview')">Overview</button>
                <button class="tab-btn me-2" onclick="openTab(event, 'seminars')">Seminars</button>
                <button class="tab-btn me-2" onclick="openTab(event, 'events')">Events</button>
                <button class="tab-btn me-2" onclick="openTab(event, 'meetings')">Meetings</button>
                <button class="tab-btn me-2" onclick="openTab(event, 'visits')">Visiting Log</button>
            </div>
            <button class="btn btn-primary fw-semibold px-3 mt-md-0" id="recordAttendanceBtn">Create Event</button>
        </div>

        <!-- Overview Tab -->
        <div id="overview" class="tab-content border border-top-0 rounded-bottom p-3" style="display:block;">
            <div class="table-responsive mt-2">
                <table class="admin-table table table-striped table-hover align-middle mb-0" id="attendanceTable">
                    <thead>
                        <tr>
                            <th>Activity Name</th>
                            <th>Date</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Attendees</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        <!-- Seminars Tab -->
        <div id="seminars" class="tab-content border border-top-0 rounded-bottom p-3" style="display:none;">
            <div class="table-responsive mt-2">
                <table class="admin-table table table-striped table-hover align-middle mb-0" id="seminarsTable">
                    <thead>
                        <tr>
                            <th>Activity Name</th>
                            <th>Date</th>
                            <th>Location</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        <!-- Events Tab -->
        <div id="events" class="tab-content border border-top-0 rounded-bottom p-3" style="display:none;">
            <div class="table-responsive mt-2">
                <table class="admin-table table table-striped table-hover align-middle mb-0" id="eventsTable">
                    <thead>
                        <tr>
                            <th>Activity Name</th>
                            <th>Date</th>
                            <th>Location</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        <!-- Meetings Tab -->
        <div id="meetings" class="tab-content border border-top-0 rounded-bottom p-3" style="display:none;">
            <div class="table-responsive mt-2">
                <table class="admin-table table table-striped table-hover align-middle mb-0" id="meetingsTable">
                    <thead>
                        <tr>
                            <th>Activity Name</th>
                            <th>Date</th>
                            <th>Location</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        <!-- Visiting Log Tab -->
        <div id="visits" class="tab-content border border-top-0 rounded-bottom p-3" style="display:none;">
            <div class="table-responsive mt-2">
                <table class="admin-table table table-striped table-hover align-middle mb-0" id="visitsTable">
                    <thead>
                        <tr>
                            <th>Activity Name</th>
                            <th>Date</th>
                            <th>Location</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        window.events = [];
        const createModal = new bootstrap.Modal(document.getElementById('preRegModal'));
        const editModal = new bootstrap.Modal(document.getElementById('editEventModal'));
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteEventModal'));
        const preRegisterModal = new bootstrap.Modal(document.getElementById('preRegisterModal'));
        let currentEventId = null;

        document.getElementById("recordAttendanceBtn").addEventListener("click", function(){
            currentEventId = null;
            document.getElementById("createEventForm").reset();
            createModal.show();
        });

        fetch("{{ route('admin.events.fetch') }}")
            .then(res => res.json())
            .then(res => {
                window.events = res.events || [];
                updateEventTables();
            });

        document.getElementById("createEventForm").addEventListener("submit", function(e){
            e.preventDefault();
            const formData = new FormData(this);

            fetch("{{ route('admin.events.create') }}", {
                method: "POST",
                headers: {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
                body: formData
            })
            .then(res => res.json())
            .then(res => {
                if(res.success){
                    window.events.push(res.event);
                    updateEventTables();
                    createModal.hide();
                    alert("Activity created successfully!");
                } else {
                    alert("Failed to create activity");
                }
            });
        });

        window.editEvent = function(id){
            const ev = window.events.find(e => e.id === id);
            if(!ev) return;

            currentEventId = id;
            const form = document.getElementById('editEventForm');
            form.querySelector("[name='eventId']").value = ev.id;
            form.querySelector("[name='name']").value = ev.name;
            form.querySelector("[name='type']").value = ev.type;
            form.querySelector("[name='date']").value = ev.date;
            form.querySelector("[name='time']").value = ev.time;
            form.querySelector("[name='location']").value = ev.location;

            editModal.show();
        };

        window.deleteEvent = function(id){
            currentEventId = id;
            const ev = window.events.find(e => e.id === id);
            if(!ev) return;

            document.getElementById('deleteEventMessage').textContent =
                `Are you sure you want to delete "${ev.name}"?`;
            deleteModal.show();
        };

        window.preRegisterEvent = function(id){
            currentEventId = id;
            const ev = window.events.find(e => e.id === id);
            if(!ev) return;

            let participantsList = ev.participants && ev.participants.length
                ? ev.participants.map(p => `<li>${p.name} (${p.contact || 'No Contact'})</li>`).join("")
                : "<li>No participants yet.</li>";

            document.getElementById('preRegisterBody').innerHTML = `
                <strong>${ev.name}</strong> (${ev.participants?.length || 0}/${ev.maxParticipants})
                <ul>${participantsList}</ul>
            `;
            preRegisterModal.show();
        };

        document.getElementById('confirmDeleteEvent').addEventListener('click', function(){
            fetch(`/admin/events/delete/${currentEventId}`, {
                method: "DELETE",
                headers: {"X-CSRF-TOKEN": "{{ csrf_token() }}"}
            })
            .then(res => res.json())
            .then(res => {
                if(res.success){
                    window.events = window.events.filter(e => e.id !== currentEventId);
                    updateEventTables();
                    deleteModal.hide();
                    alert("Event deleted successfully!");
                }
            });
        });

        document.getElementById('confirmEditEvent').addEventListener('click', function(){
            const form = document.getElementById('editEventForm');
            const formData = new FormData(form);

            fetch(`/admin/events/update/${currentEventId}`, {
                method: "POST",
                headers: {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
                body: formData
            })
            .then(res => res.json())
            .then(res => {
                if(res.success){
                    const idx = window.events.findIndex(e => e.id === currentEventId);
                    window.events[idx] = res.event;
                    updateEventTables();
                    editModal.hide();
                    alert("Event updated successfully!");
                }
            });
        });

        function updateEventTables(){
            const tableMap = {
                "Overview": document.querySelector("#attendanceTable tbody"),
                "Seminar": document.querySelector("#seminarsTable tbody"),
                "Event": document.querySelector("#eventsTable tbody"),
                "Meeting": document.querySelector("#meetingsTable tbody"),
                "Home Visit": document.querySelector("#visitsTable tbody")
            };

            for(const t in tableMap) tableMap[t].innerHTML = "";

            window.events.forEach(ev => {
                const joined = ev.participants?.length || 0;
                const total = ev.maxParticipants || 0;
                const preRegBtn = `<button class="btn btn-sm btn-success" onclick="preRegisterEvent(${ev.id})">Pre-Register (${joined}/${total})</button>`;
                const editBtn = `<button class="btn btn-sm btn-primary me-1" onclick="editEvent(${ev.id})">Edit</button>`;
                const deleteBtn = `<button class="btn btn-sm btn-danger" onclick="deleteEvent(${ev.id})">Delete</button>`;

                const tr = document.createElement("tr");
                tr.innerHTML = `
                    <td>${ev.name}</td>
                    <td>${ev.date} ${ev.time}</td>
                    <td>${ev.location}</td>
                    <td>${ev.status || 'Open'}</td>
                    <td>${preRegBtn}</td>
                    <td>${editBtn}${deleteBtn}</td>
                `;
                tableMap["Overview"].appendChild(tr);

                if(tableMap[ev.type]) tableMap[ev.type].appendChild(tr.cloneNode(true));
            });

            document.getElementById("seminarsCount").textContent = window.events.filter(e => e.type==="Seminar").length;
            document.getElementById("eventsCount").textContent = window.events.filter(e => e.type==="Event").length;
            document.getElementById("meetingsCount").textContent = window.events.filter(e => e.type==="Meeting").length;
            document.getElementById("visitsCount").textContent = window.events.filter(e => e.type==="Home Visit").length;
        }

        window.openTab = function(evt, tabName) {
            const tabs = document.getElementsByClassName("tab-content");
            for (let tab of tabs) tab.style.display = "none";

            const btns = document.getElementsByClassName("tab-btn");
            for (let btn of btns) btn.classList.remove("active");

            document.getElementById(tabName).style.display = "block";
            evt.currentTarget.classList.add("active");
        };

    });
</script>

<!-- SOLO PARENT APPLICATION LIST -->
<div id="application-section" class="content-section" style="display:none;">
    <div class="admin-container">
        <h2 class="section-title fw-bold mb-4 mt-2"><i class="fas fa-clipboard-list me-3"></i>Solo Parent Application List</h2>

        <div class="row g-3 mb-4" id="applicationStats">
            <div class="col-md-3">
                <div class="card stat-card stat-blue shadow-sm border-0 rounded-4 p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle-logo bg-primary me-3"><i class="fas fa-file-alt fa-lg text-white"></i></div>
                        <div>
                            <h5 class="mb-0 text-primary">{{ $totalApplications }}</h5>
                            <small class="text-muted">Total Applications</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card stat-yellow shadow-sm border-0 rounded-4 p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle-logo bg-warning me-3"><i class="fas fa-hourglass-half fa-lg text-white"></i></div>
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
                        <div class="icon-circle-logo bg-success me-3"><i class="fas fa-check fa-lg text-white"></i>\</div>
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
                        <div class="icon-circle-logo bg-danger me-3"><i class="fas fa-times fa-lg text-white"></i></div>
                        <div>
                            <h5 class="mb-0 text-danger">{{ $rejectedApplications }}</h5>
                            <small class="text-muted">Rejected</small>
                        </div>
                    </div>
                </div>
            </div>
    
        </div>
        
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-2">
            <h4 class="subtitle fw-bold mb-0">Solo Parent Applications</h4>
            <div class="d-flex gap-2">
                <button id="exportPdfBtn" class="btn btn-sm btn-danger"><i class="fas fa-file-pdf"></i> PDF</button>
                <button id="exportCsvBtn" class="btn btn-sm btn-success"><i class="fas fa-file-csv"></i> CSV</button>
            </div>
        </div>

        <div class="row g-2 align-items-center my-3">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-search"></i></span>
                    <input type="text" id="searchInput" class="form-control border-start-0" placeholder="Search by applicant name...">
                </div>
            </div>

            <div class="col-md-6 d-flex justify-content-end">
                <span class="input-group-text bg-white border-end-0"><i class="fas fa-filter"></i></span>
                <select id="statusFilter" class="form-select me-2">
                    <option value="all">All Status</option>
                    <option value="Pending">Pending</option>
                    <option value="Approved">Approved</option>
                    <option value="Rejected">Rejected</option>
                </select>
            </div>
        </div>

        <div class="table-responsive mt-3">
            <table id="solo-parent-table" class="admin-table table table-striped table-hover align-middle mb-0">
                <thead class="table-primary">
                    <tr>
                        <th class="text-center" style="width:40px;"><input type="checkbox" id="selectAllApplications" title="Select all applications"></th>
                        <th>#</th>
                        <th>Reference No.</th>
                        <th>Applicant Name</th>
                        <th>Address</th>
                        <th>Application Date</th>
                        <th>Solo Parent Category</th>
                        <th>Stage</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>

                <tbody id="applicant-list">
                @foreach($applications as $app)
                    @if(strtolower($app->barangay) === 'tejero')
                    <tr data-details='@json(array_merge($app->toArray(), ["family_composition" => $app->family_composition ? json_decode($app->family_composition, true): [] ]), JSON_HEX_APOS | JSON_HEX_QUOT)' data-application-id="{{ $app->application_id }}" data-files-url="{{ route('admin.application.files', $app->application_id) }}">
                        <!-- Checkbox -->
                        <td class="text-center"><input type="checkbox" class="app-checkbox" value="{{ $app->application_id }}"></td>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $app->reference_no }}</td>
                        <td>{{ $app->last_name }}, {{ $app->first_name }}</td>
                        <td>{{ $app->street }}, {{ $app->barangay }}, {{ $app->municipality }}, {{ $app->province }}</td>
                        <td>{{ $app->created_at->format('Y-m-d') }}</td>
                        <td>{{ $app->category ?? '-' }}</td>
                        <td class="fw-semibold stage-cell
                            @if($app->status == 'Rejected') text-secondary
                            @elseif($app->application_stage == 'HomeVisit') text-warning
                            @elseif($app->application_stage == 'ReadyToProcess') text-primary
                            @elseif($app->application_stage == 'Completed') text-success
                            @else text-primary
                            @endif">
                            @if($app->status == 'Rejected')
                                Review Application
                            @else
                                {{ $app->application_stage
                                    ? ($app->application_stage == 'HomeVisit' ? 'Home Visit'
                                        : ($app->application_stage == 'ReadyToProcess' ? 'Ready to Process'
                                        : ($app->application_stage == 'Completed'
                                            ? 'Completed'
                                            : $app->application_stage)))
                                    : 'Review Application' }}
                            @endif
                            </td>
                
                            <!-- Status -->
                            <td class="fw-semibold status-cell
                                @if($app->status == 'Approved') text-success
                                @elseif($app->status == 'Awaiting Documents') text-warning
                                @elseif($app->status == 'Rejected') text-danger
                                @else text-warning
                                @endif">
                                {{ $app->status ?? 'Pending' }}
                            </td>
                    
                            <!-- Actions -->
                            <td class="text-center">
                                <button type="button" class="btn btn-sm text-white view" style="background-color:#003366;"> View</button>
                                <button type="button" class="btn btn-sm text-white confirmNextStep 
                                    {{ $app->application_stage && $app->application_stage != 'Review Application' ? 'btn-secondary' : 'btn-warning' }}"
                                    {{ $app->application_stage && $app->application_stage != 'Review Application' ? 'disabled' : '' }}
                                    data-app-id="{{ $app->application_id }}"> Next Step
                                </button>
                                <button type="button" class="btn btn-sm btn-danger reject" @if($app->status === 'Rejected' || $app->status === 'Approved') disabled @endif> Reject</button>
                            </td>
                        </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
    
<script>
    document.addEventListener("DOMContentLoaded", () => {
    
        const pdfModal = new bootstrap.Modal(document.getElementById('exportPdfModal'));
        const excelModal = new bootstrap.Modal(document.getElementById('exportExcelModal'));
    
        document.getElementById('exportPdfBtn').addEventListener('click', () => {
            pdfModal.show();
        });
    
        document.getElementById('exportCsvBtn').addEventListener('click', () => {
            excelModal.show();
        });
    
        document.getElementById('confirmPdfExport').addEventListener('click', () => {
            const year = document.getElementById('pdfYear').value;
            const month = document.getElementById('pdfMonth').value;
    
            if (!year || !month) {
                alert('Please select both year and month.');
                return;
            }
    
            window.location.href = `{{ route('solo-parent.export.pdf') }}?year=${year}&month=${month}`;
            pdfModal.hide();
        });
    
        document.getElementById('confirmExcelExport').addEventListener('click', () => {
            const year = document.getElementById('excelYear').value;
            const month = document.getElementById('excelMonth').value;
    
            if (!year || !month) {
                alert('Please select both year and month.');
                return;
            }
    
            window.location.href = `{{ route('solo-parent.export.excel') }}?year=${year}&month=${month}`;
            excelModal.hide();
        });
    
    });

</script>

<script>
    document.addEventListener("DOMContentLoaded", () => {

        const selectAllCheckbox = document.getElementById("selectAllApplications");

        selectAllCheckbox.addEventListener("change", function () {

            const checkboxes = document.querySelectorAll(
                "#applicant-list .app-checkbox"
            );

            checkboxes.forEach(cb => {
                cb.checked = this.checked;
            });
        });

    });
</script>

<!-- SCHEDULE SUBMISSION -->
<div id="schedule-submission-section" class="content-section" style="display:none;">
    <div class="admin-container">
        <h2 class="section-title fw-bold mb-4 mt-2"><i class="fas fa-calendar-check me-3"></i> Schedule Submission of Requirements / Interview in Barangay</h2>
        <p class="text-muted mb-4">
            This section allows the barangay staff to schedule or track the submission of requirements for solo parent applicants. Initial interviews in the barangay can also be recorded here to confirm the applicant's category and eligibility.
        </p>

        
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="row g-2 align-items-center mb-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-search"></i></span>
                            <input type="text" id="homevisit-search" class="form-control border-start-0" placeholder="Search by resident name...">
                        </div>
                    </div>
                    <div class="col-md-6 d-flex justify-content-end">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-map-marker-alt"></i></span>
                        <select id="homevisit-barangay" class="form-select me-2">
                            <option value="all">All Barangays</option>
                            @foreach($barangays as $brgy)
                                <option value="{{ $brgy }}">{{ $brgy }}</option>
                            @endforeach
                        </select>
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-filter"></i></span>
                        <select id="homevisit-status" class="form-select">
                            <option value="all">All Status</option>
                            <option value="Pending">Pending</option>
                            <option value="Scheduled">Scheduled</option>
                            <option value="Completed">Completed</option>
                        </select>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="scheduleTable" class="admin-table table table-striped table-hover align-middle mb-0">
                        <thead class="table-primary">
                            <tr>
                                <th>#</th>
                                <th>Reference No.</th>
                                <th>Applicant Name</th>
                                <th>Category</th>
                                <th>Scheduled Date</th>
                                <th>Address</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($scheduledSubmissions as $sched)
                            <tr data-details='@json(array_merge((array)$sched, ["schedule_req_id" => $sched->schedule_req_id]))'
                                data-status="{{ $sched->status }}"
                                data-date="{{ $sched->scheduled_date ?? '' }}">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $sched->reference_no }}</td>
                                <td>{{ $sched->last_name }}, {{ $sched->first_name }}</td>
                                <td>{{ $sched->category ?? '-' }}</td>
                                <td>
                                    @if($sched->scheduled_date)
                                        {{ \Carbon\Carbon::parse($sched->scheduled_date . ' ' . ($sched->scheduled_time ?? '00:00:00'))->format('F d, Y h:i A') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ trim(($sched->street ?? '') . ', ' . ($sched->barangay ?? '') . ', ' . ($sched->municipality ?? '')) }}</td>
                                <td class="fw-semibold {{ $sched->status == 'Scheduled' ? 'text-purple' : ($sched->status == 'Completed' ? 'text-success' : 'text-secondary') }}">
                                    {{ $sched->status }}
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-success btn-sm schedule-btn" data-id="{{ $sched->schedule_req_id }}"
                                        @if($sched->scheduled_date)
                                            disabled style="background-color: #6c757d; border-color: #6c757d;" title="Already scheduled"
                                        @endif ><i class="fas fa-calendar-alt me-1"></i> Schedule</button>
                                    <button type="button" class="btn btn-primary btn-sm"><i class="fas fa-edit me-1"></i> Edit</button>
                                    <button type="button" class="btn btn-warning btn-sm next-stage-btn text-white" data-id="{{ $sched->schedule_req_id }}"><i class="fas fa-forward me-1"></i> Next Stage</button>
                                    <button type="button" class="btn btn-sm set-category-btn text-white" style="background-color:#003366;
                                            data-id="{{ $sched->schedule_req_id }}">
                                        <i class="fas fa-tags me-1"></i> Set Category
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <button class="btn btn-outline-dark btn-sm" id="prevPage"><i class="fas fa-chevron-left me-1"></i> Prev</button>
                    <span id="pageInfo" class="text-muted"></span>
                    <button class="btn btn-outline-dark btn-sm" id="nextPage">Next <i class="fas fa-chevron-right ms-1"></i></button>
                </div>
            </div>

            <!-- Right Column: Calendar + Legend -->
            <div class="col-lg-5">
                <div class="calendar-container shadow-sm p-3 rounded">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="btn-group btn-group-sm">
                            <button id="btnMonthly" class="btn btn-primary active"><i class="fas fa-calendar-alt me-1"></i> Monthly</button>
                            <button id="btnWeekly" class="btn btn-outline-primary"><i class="fas fa-calendar-week me-1"></i> Weekly</button>
                        </div>
                        <div class="calendar-header d-flex align-items-center gap-2">
                            <button id="prevMonth" class="btn btn-sm btn-outline-secondary"><i class="fas fa-chevron-left"></i></button>
                            <h5 id="calendarMonth" class="mb-0 fw-bold"></h5>
                            <button id="nextMonth" class="btn btn-sm btn-outline-secondary"><i class="fas fa-chevron-right"></i></button>
                        </div>
                    </div>

                    <table class="calendar-table" id="monthlyCalendar">
                        <thead>
                            <tr>
                                <th>Sun</th><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th>
                            </tr>
                        </thead>
                        <tbody id="calendarBody"></tbody>
                    </table>

                    <!-- Weekly Calendar -->
                    <div id="scheduleWeeklyCalendar" class="schedule-weekly-view d-none">
                        <div class="weekly-header d-flex justify-content-between align-items-center mb-2">
                            <button id="schedulePrevWeek" class="btn btn-sm btn-outline-secondary">‹ Prev</button>
                            <h6 id="scheduleWeekLabel" class="fw-bold mb-0"></h6>
                            <button id="scheduleNextWeek" class="btn btn-sm btn-outline-secondary">Next ›</button>
                        </div>
                    
                        <div id="scheduleWeeklyGrid" class="weekly-grid"></div>
                    </div>
                </div>

                <div class="calendar-insights mt-3">
                    <div class="insight-card">
                        <h6 class="fw-bold mb-2"><i class="fas fa-calendar-day me-1 text-primary"></i>Selected Date Overview</h6>
                        <p class="text-muted mb-2" id="selectedDateText"> Click a date on the calendar </p>
                
                        <div class="insight-stats">
                            <div class="stat">
                                <span class="stat-label">Total</span>
                                <span id="dayTotal" class="stat-value">0</span>
                            </div>
                            <div class="stat scheduled">
                                <span class="stat-label">Scheduled</span>
                                <span id="dayScheduled" class="stat-value">0</span>
                            </div>
                            <div class="stat completed">
                                <span class="stat-label">Completed</span>
                                <span id="dayCompleted" class="stat-value">0</span>
                            </div>
                            <div class="stat pending">
                                <span class="stat-label">Pending</span>
                                <span id="dayPending" class="stat-value">0</span>
                            </div>
                        </div>
                        <button class="btn btn-sm btn-outline-primary w-100 mt-3"><i class="fas fa-eye me-1"></i> View Day Details</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
    
        const table = document.getElementById("scheduleTable");
        if (!table) return;
    
        const rows = Array.from(table.querySelectorAll("tbody tr"));
        const prevBtn = document.getElementById("prevPage");
        const nextBtn = document.getElementById("nextPage");
        const pageInfo = document.getElementById("pageInfo");
        const rowsPerPage = 5;
        let currentPage = 1;
    
        function showPage(page) {
            const start = (page - 1) * rowsPerPage;
            const end = start + rowsPerPage;
    
            rows.forEach((row, i) => {
                row.style.display = (i >= start && i < end) ? "" : "none";
            });
    
            const totalPages = Math.max(1, Math.ceil(rows.length / rowsPerPage));
            pageInfo.textContent = `Page ${page} of ${totalPages}`;
            prevBtn.disabled = page <= 1;
            nextBtn.disabled = page >= totalPages;
        }
    
        prevBtn?.addEventListener("click", () => { if (currentPage > 1) showPage(--currentPage); });
        nextBtn?.addEventListener("click", () => { 
            const totalPages = Math.ceil(rows.length / rowsPerPage);
            if (currentPage < totalPages) showPage(++currentPage);
        });
    
        showPage(currentPage);
    
        // FILTERS
        const searchInput = document.getElementById("homevisit-search");
        const barangayFilter = document.getElementById("homevisit-barangay");
        const statusFilter = document.getElementById("homevisit-status");
    
        function applyFilters() {
            const search = searchInput.value.toLowerCase();
            const barangay = barangayFilter.value;
            const status = statusFilter.value;
    
            rows.forEach(row => {
                const name = row.cells[2].innerText.toLowerCase();
                const address = row.cells[5].innerText.toLowerCase();
                const rowStatus = row.cells[6].innerText.trim();
    
                const match =
                    name.includes(search) &&
                    (barangay === "all" || address.includes(barangay.toLowerCase())) &&
                    (status === "all" || rowStatus === status);
    
                row.style.display = match ? "" : "none";
            });
    
            currentPage = 1;
            showPage(currentPage);
        }
    
        searchInput?.addEventListener("input", applyFilters);
        barangayFilter?.addEventListener("change", applyFilters);
        statusFilter?.addEventListener("change", applyFilters);
    
        // CALENDAR TOGGLE
        const btnMonthly = document.getElementById("btnMonthly");
        const btnWeekly = document.getElementById("btnWeekly");
        const monthlyCalendar = document.getElementById("monthlyCalendar");
    
        const weeklyWrapper = document.getElementById("scheduleWeeklyCalendar");
        const weeklyGrid = document.getElementById("scheduleWeeklyGrid");
        const weekLabel = document.getElementById("scheduleWeekLabel");
        const prevWeekBtn = document.getElementById("schedulePrevWeek");
        const nextWeekBtn = document.getElementById("scheduleNextWeek");
    
        if (!weeklyWrapper || !weeklyGrid) console.warn("Weekly calendar elements missing.");
    
        let currentWeekDate = new Date();
    
        function getStartOfWeek(date) {
            const d = new Date(date);
            d.setDate(d.getDate() - d.getDay());
            d.setHours(0,0,0,0);
            return d;
        }
    
        // WEEKLY CALENDAR (7 DAYS)
        function renderWeeklyCalendar() {
            weeklyGrid.innerHTML = "";
    
            const start = getStartOfWeek(currentWeekDate);
            const end = new Date(start);
            end.setDate(start.getDate() + 6);
    
            if (weekLabel) {
                weekLabel.textContent =
                    `${start.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })} – 
                     ${end.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}`;
            }
    
            const today = new Date();
            today.setHours(0,0,0,0);
    
            for (let i = 0; i < 7; i++) {
                const day = new Date(start);
                day.setDate(start.getDate() + i);
    
                let status = "No Schedule";
    
                rows.forEach(row => {
                    const schedDate = row.dataset.date;
                    if (schedDate && new Date(schedDate).toDateString() === day.toDateString()) {
                        status = row.dataset.status || "Scheduled";
                    }
                });
    
                const dayBox = document.createElement("div");
                dayBox.className = "week-day p-2";
    
                // Status colors
                if (status === "Scheduled") dayBox.classList.add("scheduled");
                if (status === "Completed") dayBox.classList.add("completed");
                if (status === "Pending") dayBox.classList.add("pending");
    
                // Highlight today
                if (day.getTime() === today.getTime()) dayBox.classList.add("today");
    
                dayBox.innerHTML = `
                    <div class="day-name fw-bold">${day.toLocaleDateString('en-US', { weekday: 'short' })}</div>
                    <div class="fw-bold">${day.getDate()}</div>
                    <div class="visit-badge">${status}</div>
                `;
                weeklyGrid.appendChild(dayBox);
            }
        }
    
        // BUTTON EVENTS
        btnWeekly?.addEventListener("click", () => {
            btnWeekly.classList.add("btn-primary", "active");
            btnWeekly.classList.remove("btn-outline-primary");
            btnMonthly.classList.remove("btn-primary", "active");
            btnMonthly.classList.add("btn-outline-primary");
    
            monthlyCalendar?.classList.add("d-none");
            weeklyWrapper.classList.remove("d-none");
    
            renderWeeklyCalendar();
        });
    
        btnMonthly?.addEventListener("click", () => {
            btnMonthly.classList.add("btn-primary", "active");
            btnMonthly.classList.remove("btn-outline-primary");
            btnWeekly.classList.remove("btn-primary", "active");
            btnWeekly.classList.add("btn-outline-primary");
    
            weeklyWrapper.classList.add("d-none");
            monthlyCalendar?.classList.remove("d-none");
        });
    
        prevWeekBtn?.addEventListener("click", () => {
            currentWeekDate.setDate(currentWeekDate.getDate() - 7);
            renderWeeklyCalendar();
        });
    
        nextWeekBtn?.addEventListener("click", () => {
            currentWeekDate.setDate(currentWeekDate.getDate() + 7);
            renderWeeklyCalendar();
        });
    
        // Render the current week on page load if weekly is active
        if (!weeklyWrapper.classList.contains("d-none")) renderWeeklyCalendar();
    });
</script>
        
<!-- READY FOR SUBMISSION -->
@php
$requirementsData = [
    "A1. Birth of a child as a consequences of Rape" => [
        "Birth Certificate/s of the child or children",
        "Complaint Affidavit",
        "Sworn affidavit declaring that the solo parent is not cohabiting with a partner or co-parent",
        "Medical Record on the incident of rape",
        "Affidavit of a Barangay Official attesting residency and parental care"
    ],
    "A2. Widow/Widower" => [
        "Birth Certificate/s of the child or children",
        "Marriage Certificate",
        "Death certificate of the spouse",
        "Sworn affidavit declaring that the solo parent is not cohabiting",
        "Barangay Official Affidavit of residency and parental care"
    ],
    "A3. Spouse of person deprived of Liberty (PDL)" => [
        "Birth Certificate/s of the child or children",
        "Marriage Certificate",
        "Certification of Detention or certificate of serving sentence (3+ months)",
        "Sworn affidavit declaring no cohabitation",
        "Barangay Official Affidavit of residency and parental care"
    ],
    "A4. Spouse of person with Disability (PWD)" => [
        "Birth Certificate/s of the child or children",
        "Marriage Certificate or affidavit of cohabitation",
        "Medical record or abstract showing incapacity (3+ months)",
        "Sworn affidavit declaring no cohabitation",
        "Barangay Official Affidavit of residency and parental care"
    ],
    "A5. Due to de facto separation" => [
        "Birth Certificate/s of the child or children",
        "Marriage Certificate or affidavit of the applicant",
        "Affidavit of 2 disinterested persons attesting separation",
        "Sworn affidavit declaring no cohabitation",
        "Barangay Official Affidavit of residency and parental care"
    ],
    "A6. Due to nullity of marriage" => [
        "Birth Certificate/s of the child or children",
        "Marriage Certificate",
        "Judicial decree of nullity or annulment",
        "Sworn affidavit declaring no cohabitation",
        "Barangay Official Affidavit of residency and parental care"
    ],
    "A7. Abandoned" => [
        "Birth Certificate/s of the child or children",
        "Marriage Certificate or affidavit",
        "Affidavit of Two disinterested persons attesting abandonment",
        "Sworn affidavit declaring no cohabitation",
        "Barangay Official Affidavit of residency and parental care"
    ],
    "B. Spouse of the OFW/Relative of the OFW" => [
        "Birth Certificate/s of the child or children",
        "Marriage Certificate",
        "Overseas Employment Certificate (OEC)",
        "Passport stamps showing 12 months continuous stay abroad",
        "Employment Contract",
        "Sworn affidavit declaring no cohabitation",
        "Barangay Official Affidavit of residency and parental care"
    ],
    "C. Unmarried mother/father who keeps and rears his/her child/children" => [
        "Birth Certificate/s of the child or children",
        "CENOMAR",
        "Sworn affidavit declaring no cohabitation",
        "Barangay Official Affidavit of residency and parental care"
    ],
    "D. Legal guardian, adoptive or foster parents who solely provides parental care and support to a child/children/dependent" => [
        "Birth Certificate/s of the child or children",
        "Proof of guardianship, foster care, or adoption",
        "Sworn affidavit declaring no cohabitation",
        "Barangay Official Affidavit of residency and parental care"
    ],
    "E. Any relative within the fourth (4th) civil degree of consanguinity or affinity" => [
        "Birth Certificate/s of the child or children",
        "Death Certificate, incapacity document, or judicial declaration of absence",
        "Sworn affidavit declaring no cohabitation",
        "Barangay Official Affidavit of residency and parental care"
    ],
    "F. Pregnant woman who provides sole parental care and support to her unborn child or children" => [
        "Medical Record of Pregnancy",
        "Sworn affidavit declaring no cohabitation",
        "Barangay Official Affidavit of residency"
    ]
];

// Short names for badges
$shortNames = [
    "Birth Certificate/s of the child or children" => "Birth Cert",
    "Complaint Affidavit" => "Comp. Affidavit",
    "Sworn affidavit declaring that the solo parent is not cohabiting with a partner or co-parent" => "Sworn Affidavit",
    "Sworn affidavit declaring no cohabitation" => "Sworn Affidavit",
    "Barangay Official Affidavit of residency and parental care" => "Barangay Affidavit",
    "Medical Record on the incident of rape" => "Med. Record",
    "Proof of guardianship, foster care, or adoption" => "Guardianship Proof",
    "CENOMAR" => "CENOMAR",
    "Marriage Certificate" => "Marriage Cert",
    "Death certificate of the spouse" => "Death Cert",
    "Certification of Detention or certificate of serving sentence (3+ months)" => "Detention Cert",
    "Medical record or abstract showing incapacity (3+ months)" => "Med. Record",
    "Affidavit of 2 disinterested persons attesting separation" => "Affidavit of Separation",
    "Judicial decree of nullity or annulment" => "Nullity Decree",
    "Affidavit of Two disinterested persons attesting abandonment" => "Affidavit of Abandonment",
    "Overseas Employment Certificate (OEC)" => "OEC",
    "Passport stamps showing 12 months continuous stay abroad" => "Passport Stamps",
    "Employment Contract" => "Employment Contract",
    "Death Certificate, incapacity document, or judicial declaration of absence" => "Death/Absence Cert",
    "Medical Record of Pregnancy" => "Pregnancy Record"
];
@endphp
    
<script>
    const requirementsData = @json($requirementsData);

    document.addEventListener("DOMContentLoaded", () => {
        const setCategoryModal = new bootstrap.Modal(document.getElementById('setCategoryModal'));
        let currentScheduleId = null;
        let selectedCategory = null;

        const categoryStep = document.getElementById("categorySelectionStep");
        const docsStep = document.getElementById("categoryDocsStep");
        const docsPreview = document.getElementById("categoryDocsPreview");
        const selectedCategoryName = document.getElementById("selectedCategoryName");
        const backBtn = document.getElementById("backToCategories");
        const confirmBtn = document.getElementById("confirmCategory");

        // Open modal when clicking Set Category button
        document.querySelectorAll(".set-category-btn").forEach(btn => {
            btn.addEventListener("click", () => {
                currentScheduleId = btn.dataset.id;
                selectedCategory = null;

                // Show category selection step
                categoryStep.style.display = "block";
                docsStep.style.display = "none";

                setCategoryModal.show();
            });
        });

        // When a category is clicked
        document.querySelectorAll(".category-option").forEach(option => {
            option.addEventListener("click", () => {
                selectedCategory = option.textContent;

                // Show documents
                const docs = requirementsData[selectedCategory] || [];
                docsPreview.innerHTML = docs.map(doc => `<li>${doc}</li>`).join('');
                selectedCategoryName.textContent = selectedCategory;

                // Switch steps
                categoryStep.style.display = "none";
                docsStep.style.display = "block";
            });
        });

        // Back button
        backBtn.addEventListener("click", () => {
            categoryStep.style.display = "block";
            docsStep.style.display = "none";
        });

        // Confirm button
        confirmBtn.addEventListener("click", () => {
            if (!selectedCategory) return;

            // Update table Category column immediately
            const row = document.querySelector(`tr[data-details*='"schedule_req_id":${currentScheduleId}']`);
            if (row) {
                row.cells[3].textContent = selectedCategory; // Category
            }

            // Optional: AJAX to backend
            /*
            fetch('/admin/set-category', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json', 
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' 
                },
                body: JSON.stringify({ schedule_req_id: currentScheduleId, category: selectedCategory })
            }).then(res => res.json())
              .then(data => console.log(data));
            */

            setCategoryModal.hide();
        });
    });
</script>

<!-- HOME VISIT SECTION -->
<div id="homevisit-section" class="content-section" style="display:none;">
    <div class="admin-container">
        <h2 class="section-title fw-bold mb-4 mt-2"><i class="fas fa-house-user me-2"></i> Home Visit Records</h2>

        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card stat-card stat-blue shadow-sm border-0 rounded-4 p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle-logo bg-primary me-3"><i class="fas fa-home fa-lg text-white"></i></div>
                        <div>
                            <h5 class="mb-0" id="homevisit-total">{{ $totalHomeVisits }}</h5>
                            <small class="text-muted">Total Home Visits</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card stat-card stat-yellow shadow-sm border-0 rounded-4 p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle-logo bg-warning me-3"><i class="fas fa-hourglass-half fa-lg text-white"></i></div>
                        <div>
                            <h5 class="mb-0 text-warning" id="homevisit-pending">{{ $pendingCount }}</h5>
                            <small class="text-muted">Pending</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card stat-card stat-red shadow-sm border-0 rounded-4 p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle-logo bg-danger me-3"><i class="fas fa-calendar-alt fa-lg text-white"></i></div>
                        <div>
                            <h5 class="mb-0 text-purple" id="homevisit-scheduled">{{ $scheduledCount }}</h5>
                            <small class="text-muted">Scheduled</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card stat-card stat-green shadow-sm border-0 rounded-4 p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle-logo bg-success me-3"><i class="fas fa-check fa-lg text-white"></i></div>
                        <div>
                            <h5 class="mb-0 text-success" id="homevisit-completed">{{ $completedCount }}</h5>
                            <small class="text-muted">Completed</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Left Column-->
            <div class="col-lg-7">
                <div class="row g-2 align-items-center mb-3">
                    <!-- Search bar left -->
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-search"></i></span>
                            <input type="text" id="homevisit-search" class="form-control border-start-0" placeholder="Search by resident name...">
                        </div>
                    </div>
                    <!-- Filters right -->
                    <div class="col-md-6 d-flex justify-content-end">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-map-marker-alt"></i></span>
                        <select id="homevisit-barangay" class="form-select me-2">
                            <option value="all">All Barangays</option>
                                @foreach($barangays as $brgy)
                                    <option value="{{ $brgy }}">{{ $brgy }}</option>
                                @endforeach
                        </select>
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-filter"></i></span>
                        <select id="homevisit-status" class="form-select">
                            <option value="all">All Status</option>
                            <option value="Pending">Pending</option>
                            <option value="Scheduled">Scheduled</option>
                            <option value="Completed">Completed</option>
                        </select>
                    </div>
                </div>

                <!-- Table -->
                <div class="table-responsive mt-2">
                    <table id="home-visit-table" class="table table-striped table-hover align-middle">
                        <thead class="table-primary">
                            <tr>
                                <th>#</th>
                                <th>Reference No.</th>
                                <th>Resident Name</th>
                                <th>Barangay</th>
                                <th>Schedule</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                    <tbody id="homevisit-tbody">
                        @foreach($homeVisits as $visit)
                            @php
                                $formattedDate = $visit->visit_date
                                    ? \Carbon\Carbon::parse($visit->visit_date)->format('F j, Y')
                                    : '-';
                                $formattedTime = $visit->visit_time
                                    ? \Carbon\Carbon::parse($visit->visit_time)->format('g:i A')
                                    : '';
                                $scheduleDisplay = $formattedDate !== '-' ? $formattedDate . ' ' . $formattedTime : '-';
                            @endphp
                            <tr data-visit='@json($visit)'>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $visit->reference_no }}</td>
                                <td>{{ $visit->last_name }}, {{ $visit->first_name }}</td>
                                <td>{{ trim($visit->street . ', ' . $visit->barangay . ', ' . $visit->municipality, ', ') }}</td>
                                <td>{{ $scheduleDisplay }}</td>
                                <td>{{ $visit->category ?? '-' }}</td>
                                <td class="fw-semibold {{ $visit->status_class ?? '' }}">
                                    {{ $visit->visit_status ?? 'Pending' }}
                                </td>
                                <td class="text-center">
                                    <button pe="button" class="btn btn-sm text-white scheduleVisit" style="background-color: #003366;" data-id="{{ $visit->visit_id }}">Schedule</button>
                                    <button class="btn btn-warning btn-sm text-white nextStepReady" data-id="{{ $visit->visit_id }}">Next Step</button>
                                    <button class="btn btn-danger btn-sm rejectHomeVisit" data-id="{{ $visit->visit_id }}">Reject</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>


                    </table>
                </div>
            </div>

        <!-- Right Column: Calendar -->
        <div class="col-lg-5">
            <div class="calendar-container shadow-sm p-3 rounded">
        
                <!-- VIEW TOGGLE + NAV -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <!-- Toggle Buttons -->
                    <div class="btn-group btn-group-sm">
                        <button id="homeBtnMonthly" class="btn btn-primary active"><i class="fas fa-calendar-alt me-1"></i> Monthly
                        </button>
                        <button id="homeBtnWeekly" class="btn btn-outline-primary"><i class="fas fa-calendar-week me-1"></i> Weekly
                        </button>
                    </div>
                    <!-- Navigation -->
                    <div class="d-flex align-items-center gap-2">
                        <button id="prevMonthHome" class="btn btn-sm btn-outline-secondary"><i class="fas fa-chevron-left"></i>
                        </button>
                        <h5 id="calendarMonthHome" class="mb-0 fw-bold"></h5>
                        <button id="nextMonthHome" class="btn btn-sm btn-outline-secondary"><i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
    
                <!-- MONTHLY VIEW -->
                <table class="calendar-table" id="homeMonthlyCalendar">
                    <thead>
                        <tr>
                            <th>Sun</th><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th>
                        </tr>
                    </thead>
                    <tbody id="calendarBodyHome"></tbody>
                </table>
        
                <!-- HOME VISIT WEEKLY CALENDAR -->
                <div id="homeWeeklyCalendar" class="schedule-weekly-view d-none">
                    <div class="weekly-header d-flex justify-content-between align-items-center mb-2">
                        <button id="homePrevWeek" class="btn btn-sm btn-outline-secondary">‹ Prev</button>
                        <h6 id="homeWeekLabel" class="fw-bold mb-0"></h6>
                        <button id="homeNextWeek" class="btn btn-sm btn-outline-secondary">Next ›</button>
                    </div>
                    <div id="homeWeeklyGrid" class="weekly-grid"></div>
                </div>
            
                <!-- HOME VISIT CALENDAR INSIGHTS-->
                <div class="calendar-insights mt-3" id="homevisitCalendarInsights">
                    <div class="insight-card">
                        <h6 class="fw-bold mb-2"><i class="fas fa-calendar-day me-1 text-primary"></i>Selected Date Overview</h6>
                        <p class="text-muted mb-2" id="homevisitSelectedDateText"> Click a date on the calendar </p>
        
                        <div class="insight-stats d-flex justify-content-between">
                            <div class="stat text-center">
                                <span class="stat-label d-block">Total</span>
                                <span id="homevisitDayTotal" class="stat-value fw-bold">0</span>
                            </div>
                            <div class="stat scheduled text-center">
                                <span class="stat-label d-block">Scheduled</span>
                                <span id="homevisitDayScheduled" class="stat-value fw-bold">0</span>
                            </div>
                            <div class="stat completed text-center">
                                <span class="stat-label d-block">Completed</span>
                                <span id="homevisitDayCompleted" class="stat-value fw-bold">0</span>
                            </div>
                            <div class="stat pending text-center">
                                <span class="stat-label d-block">Pending</span>
                                <span id="homevisitDayPending" class="stat-value fw-bold">0</span>
                            </div>
                        </div>
        
                        <button class="btn btn-sm btn-outline-primary w-100 mt-3">
                            <i class="fas fa-eye me-1"></i> View Day Details
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
    
        const table = document.getElementById("home-visit-table");
        if (!table) return;
    
        const rows = Array.from(table.querySelectorAll("tbody tr"));
        const prevBtn = document.getElementById("prevPage");
        const nextBtn = document.getElementById("nextPage");
        const pageInfo = document.getElementById("pageInfo");
        const rowsPerPage = 5;
        let currentPage = 1;
    
        function showPage(page) {
            const start = (page - 1) * rowsPerPage;
            const end = start + rowsPerPage;
    
            rows.forEach((row, i) => {
                row.style.display = (i >= start && i < end) ? "" : "none";
            });
    
            const totalPages = Math.max(1, Math.ceil(rows.length / rowsPerPage));
            pageInfo.textContent = `Page ${page} of ${totalPages}`;
            prevBtn.disabled = page <= 1;
            nextBtn.disabled = page >= totalPages;
        }
    
        prevBtn?.addEventListener("click", () => {
            if (currentPage > 1) showPage(--currentPage);
        });
    
        nextBtn?.addEventListener("click", () => {
            const totalPages = Math.ceil(rows.length / rowsPerPage);
            if (currentPage < totalPages) showPage(++currentPage);
        });
    
        showPage(currentPage);
    
        const searchInput = document.getElementById("homevisit-search");
        const barangayFilter = document.getElementById("homevisit-barangay");
        const statusFilter = document.getElementById("homevisit-status");
    
        function applyFilters() {
            const search = searchInput.value.toLowerCase();
            const barangay = barangayFilter.value;
            const status = statusFilter.value;
    
            rows.forEach(row => {
                const name = row.cells[2].innerText.toLowerCase();
                const address = row.cells[3].innerText.toLowerCase(); // correct column for barangay
                const rowStatus = row.cells[6].innerText.trim();
    
                const match =
                    name.includes(search) &&
                    (barangay === "all" || address.includes(barangay.toLowerCase())) &&
                    (status === "all" || rowStatus === status);
    
                row.style.display = match ? "" : "none";
            });
    
            currentPage = 1;
            showPage(currentPage);
        }
    
        searchInput?.addEventListener("input", applyFilters);
        barangayFilter?.addEventListener("change", applyFilters);
        statusFilter?.addEventListener("change", applyFilters);

        const btnMonthly = document.getElementById("homeBtnMonthly");
        const btnWeekly = document.getElementById("homeBtnWeekly");
        const monthlyCalendar = document.getElementById("homeMonthlyCalendar");
        const monthlyBody = document.getElementById("calendarBodyHome");
    
        const weeklyWrapper = document.getElementById("homeWeeklyCalendar");
        const weeklyGrid = weeklyWrapper.querySelector(".weekly-grid");
        const prevWeekBtn = document.getElementById("prevMonthHome");
        const nextWeekBtn = document.getElementById("nextMonthHome");
        const weekLabel = document.getElementById("calendarMonthHome");
    
        let currentWeekDate = new Date();
    
        function getStartOfWeek(date) {
            const d = new Date(date);
            d.setDate(d.getDate() - d.getDay());
            d.setHours(0,0,0,0);
            return d;
        }
    
        function renderHomeWeeklyCalendar() {
            weeklyGrid.innerHTML = "";
    
            const start = getStartOfWeek(currentWeekDate);
            const end = new Date(start);
            end.setDate(start.getDate() + 6);
    
            weekLabel.textContent =
                `${start.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })} – 
                 ${end.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}`;
    
            const today = new Date();
            today.setHours(0,0,0,0);
    
            for (let i = 0; i < 7; i++) {
                const day = new Date(start);
                day.setDate(start.getDate() + i);
    
                let status = "No Schedule";
    
                rows.forEach(row => {
                    const visit = JSON.parse(row.dataset.visit);
                    const schedDate = visit.visit_date;
                    if (schedDate && new Date(schedDate).toDateString() === day.toDateString()) {
                        status = visit.visit_status || "Scheduled";
                    }
                });
    
                const dayBox = document.createElement("div");
                dayBox.className = "week-day";
    
                if (day.getTime() === today.getTime()) {
                    dayBox.classList.add("today");
                }
    
                let badge = '';
                if (status !== "No Schedule") {
                    badge = `<div class="visit-badge">${status}</div>`;
                }
    
                dayBox.innerHTML = `
                    <div class="day-name fw-bold">${day.toLocaleDateString('en-US', { weekday: 'short' })}</div>
                    <div class="fw-bold">${day.getDate()}</div>
                    ${badge}
                `;
                weeklyGrid.appendChild(dayBox);
            }
        }
    
        btnWeekly?.addEventListener("click", () => {
            btnWeekly.classList.add("btn-primary", "active");
            btnWeekly.classList.remove("btn-outline-primary");
            btnMonthly.classList.remove("btn-primary", "active");
            btnMonthly.classList.add("btn-outline-primary");
    
            monthlyCalendar.classList.add("d-none");
            weeklyWrapper.classList.remove("d-none");
    
            renderHomeWeeklyCalendar();
        });
    
        btnMonthly?.addEventListener("click", () => {
            btnMonthly.classList.add("btn-primary", "active");
            btnMonthly.classList.remove("btn-outline-primary");
            btnWeekly.classList.remove("btn-primary", "active");
            btnWeekly.classList.add("btn-outline-primary");
    
            weeklyWrapper.classList.add("d-none");
            monthlyCalendar.classList.remove("d-none");
        });
    
        prevWeekBtn?.addEventListener("click", () => {
            currentWeekDate.setDate(currentWeekDate.getDate() - 7);
            renderHomeWeeklyCalendar();
        });
    
        nextWeekBtn?.addEventListener("click", () => {
            currentWeekDate.setDate(currentWeekDate.getDate() + 7);
            renderHomeWeeklyCalendar();
        });
    
    });
</script>

<!-- READY FOR SUBMISSION === -->
<div id="send-requirements-section" class="content-section" style="display:none;">
    <div class="admin-container">
        <h2 class="section-title fw-bold mb-4 mt-2"><i class="fas fa-file-alt me-3"></i> Ready for Submission</h2>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card stat-card stat-blue shadow-sm border-0 rounded-4 p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle-logo bg-primary me-3"><i class="fas fa-check-circle fa-lg text-white"></i></div>
                        <div class="text-start">
                            <h5 class="mb-0 text-primary" id="submission-total">{{ $readyToProcess->count() }}</h5>
                            <small class="text-muted">Total Ready</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card stat-green shadow-sm border-0 rounded-4 p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle-logo bg-success me-3"><i class="fas fa-thumbs-up fa-lg text-white"></i></div>
                        <div class="text-start">
                            <h5 class="mb-0 text-success" id="submission-approved">25</h5>
                            <small class="text-muted">Approved</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card stat-yellow shadow-sm border-0 rounded-4 p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle-logo bg-warning me-3"><i class="fas fa-hourglass-half fa-lg text-white"></i></div>
                        <div class="text-start">
                            <h5 class="mb-0 text-warning" id="submission-pending">{{ $readyToProcess->where('status','Ready')->count() }}</h5>
                            <small class="text-muted">Pending Verification</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-2 align-items-center mb-3">
            <div class="col-md-6">
                <div class="input-group shadow-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-search"></i></span>
                    <input type="text" id="submission-search" class="form-control border-start-0" placeholder="Search by resident name or reference number...">
                </div>
            </div>

            <div class="col-md-6 d-flex justify-content-end">
                <span class="input-group-text bg-white border-end-0"><i class="fas fa-map-marker-alt"></i></span>
                <select id="submission-barangay" class="form-select me-2">
                    <option value="all">All Barangays</option>
                    @foreach($barangays as $brgy)
                        <option value="{{ $brgy }}">{{ $brgy }}</option>
                    @endforeach
                </select>

                <span class="input-group-text bg-white border-end-0"><i class="fas fa-filter"></i></span>
                <select id="submission-status" class="form-select me-2">
                    <option value="all">All Status</option>
                    <option value="Ready">Ready to Process</option>
                    <option value="Approved">Approved</option>
                    <option value="Pending">Pending Verification</option>
                </select>
            </div>
        </div>

        <div class="table-responsive mt-4">
            <table class="table table-striped table-hover align-middle shadow-sm" id="submission-table">
                <thead class="table-success">
                    <tr>
                        <th><input type="checkbox" id="selectAllReady"></th>
                        <th>#</th>
                        <th>Reference Number</th>
                        <th>Resident Name</th>
                        <th>Address</th>
                        <th>Category</th>
                        <th>Documents</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>

                <tbody id="submission-tbody">
                    @foreach($readyToProcess as $ready)
                        @php
                            $requiredDocs = $requirementsData[$ready->category] ?? [];
                        @endphp
                        <tr data-ready='@json($ready)'>
                            <td><input type="checkbox" class="ready-checkbox" data-id="{{ $ready->ready_process_id }}"></td>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $ready->reference_no }}</td>
                            <td>{{ $ready->last_name }}, {{ $ready->first_name }}</td>
                            <td>
                                {{ $ready->street ? $ready->street . ', ' : '' }}
                                {{ $ready->barangay }}, {{ $ready->municipality }}
                            </td>
                            <td>{{ $ready->category ?? '-' }}</td>
                            <td>
                                @foreach($requiredDocs as $doc)
                                    <span class="badge bg-secondary me-1 mb-1" title="{{ $doc }}">
                                        {{ $shortNames[$doc] ?? $doc }}
                                    </span>
                                @endforeach
                            </td>
                            <td class="fw-semibold text-primary">{{ $ready->status }}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-primary btn-sm me-1 add-beneficiary-btn" data-id="{{ $ready->ready_process_id }}">
                                    <i class="fas fa-user-plus me-1"></i> Add
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    
    
    </div>
</div>
    
<script>
    const checkboxes = document.querySelectorAll('.ready-checkbox');
    const selectAll = document.getElementById('selectAllReady');
    const totalSelected = document.getElementById('total-selected');
    
    selectAll.addEventListener('change', function() {
        checkboxes.forEach(cb => cb.checked = selectAll.checked);
        totalSelected.textContent = selectAll.checked ? checkboxes.length : 0;
    });
    
    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            const selectedCount = document.querySelectorAll('.ready-checkbox:checked').length;
            totalSelected.textContent = selectedCount;
            selectAll.checked = selectedCount === checkboxes.length;
        });
    });
    
    // Search functionality
    document.getElementById('submission-search').addEventListener('input', function() {
        const query = this.value.toLowerCase();
        document.querySelectorAll('#submission-tbody tr').forEach(tr => {
            const name = tr.children[3].textContent.toLowerCase();
            const ref = tr.children[2].textContent.toLowerCase();
            tr.style.display = name.includes(query) || ref.includes(query) ? '' : 'none';
        });
    });
    
    function applyFilters() {
        const barangayFilter = document.getElementById('submission-barangay').value;
        const statusFilter = document.getElementById('submission-status').value;
        const priorityFilter = document.getElementById('submission-priority').value;
    
        document.querySelectorAll('#submission-tbody tr').forEach(tr => {
            const barangay = tr.children[4].textContent;
            const status = tr.children[7].textContent;
            const priority = tr.dataset.priority || 'Normal';
    
            let show = true;
            if (barangayFilter !== 'all' && !barangay.includes(barangayFilter)) show = false;
            if (statusFilter !== 'all' && !status.includes(statusFilter)) show = false;
            if (priorityFilter !== 'all' && priority !== priorityFilter) show = false;
    
            tr.style.display = show ? '' : 'none';
        });
    }
    
    document.getElementById('submission-barangay').addEventListener('change', applyFilters);
    document.getElementById('submission-status').addEventListener('change', applyFilters);
    document.getElementById('submission-priority').addEventListener('change', applyFilters);
</script>
    
<!-- SOLO PARENTS SECTION -->
<div id="solo-parents-section" class="content-section" style="display:none;">
    <div class="admin-container">
            
        <div id="beneficiaryMapPanel">
            <h2 class="section-title fw-bold mb-4 mt-2"><i class="fas fa-map-marker-alt me-3"></i>Solo Parent Beneficiary GIS Mapping Tracker</h2>
    
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card stat-card stat-blue shadow-sm border-0 rounded-4 p-3">
                        <div class="d-flex align-items-center">
                            <div class="icon-circle-logo bg-primary me-3"><i class="fas fa-user-friends fa-lg text-white"></i></div>
                            <div>
                                <h5 class="mb-0 text-primary">{{ count($beneficiaries) }}</h5>
                                <small class="text-muted">Total Solo Parents</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card stat-green shadow-sm border-0 rounded-4 p-3">
                        <div class="d-flex align-items-center">
                            <div class="icon-circle-logo bg-success me-3"><i class="fas fa-city fa-lg text-white"></i></div>
                            <div>
                                <h5 class="mb-0 text-success">{{ count($barangays) }}</h5>
                                <small class="text-muted">Registered Barangays</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card stat-yellow shadow-sm border-0 rounded-4 p-3">
                        <div class="d-flex align-items-center">
                            <div class="icon-circle-logo bg-warning me-3"><i class="fas fa-map-marker-alt fa-lg text-white"></i></div>
                            <div>
                                <h5 class="mb-0 text-warning">{{ $highestDensityBarangay ?? 'N/A' }}</h5>
                                <small class="text-muted">Highest Density Barangay</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <!-- MAP -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="fw-bold mb-0">️ Map Visualization</h4>
                            <div class="btn-group">
                                <button id="toggleBeneficiaryHeat" class="btn btn-sm btn-danger"> Heatmap</button>
                                <button id="toggleBeneficiaryMarkers" class="btn btn-sm btn-primary"> Markers</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="beneficiaryGisMap" style="height:450px;" class="rounded"></div>
                        </div>
                    </div>
                </div>
                <!-- BARANGAY DISTRIBUTION -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header"><h4 class="fw-bold mb-0"> Barangay Distribution</h4></div>
                        <div class="card-body overflow-auto" id="beneficiaryBarangayList" style="max-height:450px;">
                            <!-- populated by JS -->
                        </div>
                    </div>
                </div>
                
                <div class="text-end mb-3">
                    <button id="viewAllBeneficiariesBtn" class="btn btn-primary">View All Beneficiaries</button>
                </div>
            </div>
        </div>
            <div id="fullBeneficiaryListPanel" style="display:none;">
                <button id="backToMapBtn" class="btn btn-secondary mb-3">← Back to Map</button>
                <div class="d-flex justify-content-between align-items-center mb-3"><div>
                    <h2 class="fw-bold mb-1"><i class="fas fa-users me-3"></i> Official Solo Parent Beneficiary List</h2>
                    <small class="text-muted">Solo Parent Beneficiary – Barangay Tejero</small>
                </div>
            </div>
            <!-- BENEFICIARY TABLE + EXPORT -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
    
                    <!-- TITLE + EXPORT -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3 class="fw-semibold mb-0"> Solo Parent Beneficiary – Barangay Tejero </h3>
    
                        <div class="d-flex gap-2">
                            <button id="exportBeneficiaryPdfBtn" class="btn btn-sm btn-danger">
                                <i class="fas fa-file-pdf me-1"></i> PDF
                            </button>
                            <button id="exportBeneficiaryExcelBtn" class="btn btn-sm btn-success">
                                <i class="fas fa-file-excel me-1"></i> Excel
                            </button>
                        </div>
                    </div>
    
                    <!-- FILTERS -->
                    <div class="row g-2 mb-3">
                        <div class="col-md-4">
                            <input type="text" id="beneficiarySearch" class="form-control"
                                   placeholder="Search beneficiary name...">
                        </div>
    
                        <div class="col-md-3">
                            <input type="date" id="beneficiaryFilterDate" class="form-control">
                        </div>
    
                        <div class="col-md-3">
                            <select id="beneficiaryFilterBrgy" class="form-select">
                                <option value="all">All Barangays</option>
                                @foreach($barangays as $brgy)
                                    <option value="{{ $brgy }}">{{ $brgy }}</option>
                                @endforeach
                            </select>
                        </div>
    
                        <div class="col-md-2">
                            <button id="beneficiaryReset" class="btn btn-secondary w-100"> Reset </button>
                        </div>
                    </div>
    
                    <!-- TABLE -->
                    <div class="table-responsive">
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
                            @foreach($beneficiaries as $item)
                                @php
                                    $hasBenefits = !empty($item->benefits) && count($item->benefits) > 0;
                                @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->first_name }} {{ $item->last_name }}</td>
                                    <td>{{ $item->barangay }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->date_added)->format('Y-m-d') }}</td>
                                    <td>{{ $item->assistance_status }}</td>
                                    <td>{{ $item->category ?? 'N/A' }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm text-white" style="background:#003366;"> View </button>
                                        <button class="btn btn-sm btn-danger"> Delete </button>
                                        <button class="btn btn-sm {{ $hasBenefits ? 'btn-secondary' : 'btn-warning' }}"
                                                {{ $hasBenefits ? 'disabled' : '' }}>
                                            {{ $hasBenefits ? 'Benefits Set' : 'Choose Benefits' }}
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
</div>
        
<script>
document.addEventListener('DOMContentLoaded', function () {
    const viewAllBtn = document.getElementById('viewAllBeneficiariesBtn');
    const backToMapBtn = document.getElementById('backToMapBtn');
    const mapPanel = document.getElementById('beneficiaryMapPanel');
    const listPanel = document.getElementById('fullBeneficiaryListPanel');

    // Show full list
    viewAllBtn?.addEventListener('click', () => {
        mapPanel.style.display = 'none';
        listPanel.style.display = 'block';
    });

    // Back to map
    backToMapBtn?.addEventListener('click', () => {
        listPanel.style.display = 'none';
        mapPanel.style.display = 'block';
    });

    // Export buttons
    document.getElementById('exportBeneficiaryPdfBtnFull')?.addEventListener('click', () => {
        window.location.href = "{{ route('solo-parent.beneficiary.export.pdf') }}";
    });
    document.getElementById('exportBeneficiaryExcelBtnFull')?.addEventListener('click', () => {
        window.location.href = "{{ route('solo-parent.beneficiary.export.excel') }}";
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    document.getElementById('exportBeneficiaryPdfBtn')
        ?.addEventListener('click', () => {
            window.location.href = "{{ route('solo-parent.beneficiary.export.pdf') }}";
        });

    document.getElementById('exportBeneficiaryExcelBtn')
        ?.addEventListener('click', () => {
            window.location.href = "{{ route('solo-parent.beneficiary.export.excel') }}";
        });

});
</script>

<link rel="stylesheet" href="{{ asset('css/leaflet.css') }}">
<script src="{{ asset('js/admin.js') }}"></script>
<script src="{{ asset('js/leaflet.js') }}"></script>
<script src="{{ asset('js/leaflet-heat.js') }}"></script>
<script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {

    const moveToScheduleModalEl = document.getElementById('moveToScheduleModal');
    const moveToHomeVisitModalEl = document.getElementById('moveToHomeVisitModal');
    const moveToReadyModalEl = document.getElementById('moveToReadyModal');
    const homeVisitScheduleModalEl = document.getElementById('homeVisitScheduleModal');
    const dayScheduleModalEl = document.getElementById('dayScheduleModal');
    const homeVisitDayModalEl = document.getElementById('homeVisitDayModal');
    const successModalEl = document.getElementById('AppSuccessModal');
    const errorModalEl = document.getElementById('AppErrorModal');

    const moveToScheduleModal = moveToScheduleModalEl ? new bootstrap.Modal(moveToScheduleModalEl) : null;
    const moveToHomeVisitModal = moveToHomeVisitModalEl ? new bootstrap.Modal(moveToHomeVisitModalEl) : null;
    const moveToReadyModal = moveToReadyModalEl ? new bootstrap.Modal(moveToReadyModalEl) : null;
    const homeVisitScheduleModal = homeVisitScheduleModalEl ? new bootstrap.Modal(homeVisitScheduleModalEl) : null;
    const dayScheduleModal = dayScheduleModalEl ? new bootstrap.Modal(dayScheduleModalEl) : null;
    const homeVisitDayModal = homeVisitDayModalEl ? new bootstrap.Modal(homeVisitDayModalEl) : null;

    const calendarMonth = document.getElementById("calendarMonth");
    const calendarBody = document.getElementById("calendarBody");

    let today = new Date();
    let currentYear = today.getFullYear();
    let currentMonth = today.getMonth();

    let currentRow = null;

    let scheduledEvents = @json($scheduledSubmissions);
    let events = {};

    scheduledEvents.forEach(sched => {
        if (!sched.scheduled_date || !sched.scheduled_time) return;
        const date = sched.scheduled_date;
        if (!events[date]) events[date] = [];

        const entry = `${sched.last_name}, ${sched.first_name} - ${sched.street || ''}, ${sched.barangay || ''}, ${sched.municipality || ''} - ${sched.status || 'Scheduled'} - ${sched.scheduled_time}`;
        events[date].push(entry);
    });


    let currentYearHome = new Date().getFullYear();
    let currentMonthHome = new Date().getMonth();
    let eventsHome = {};

    const calendarBodyHome = document.getElementById("calendarBodyHome");
    const calendarMonthHome = document.getElementById("calendarMonthHome");
    const homeVisitModalTitle = document.getElementById("homeVisitModalTitle");
    const homeVisitModalBody = document.getElementById("homeVisitModalBody");
    

    function safeParseJSON(str) {
    try { return JSON.parse(str || '{}'); }
    catch { return {}; }
    }

    function getStatusClass(status) {
        switch ((status || '').trim()) {
        case 'Pending': return 'text-warning';
        case 'Scheduled': return 'text-purple';
        case 'Completed': return 'text-success';
        case 'Rejected': return 'text-danger';
        default: return 'text-secondary';
        }
    }

    function getFullAddress(data) {
    return [data.street, data.barangay, data.municipality].filter(Boolean).join(', ');
    }

    function formatTime12(time) {
        if (!time) return "-";
        if (time.toUpperCase().includes("AM") || time.toUpperCase().includes("PM")) return time;

        const [h, m] = time.split(":");
        let hour = parseInt(h, 10);
        const ampm = hour >= 12 ? "PM" : "AM";
        hour = hour % 12 || 12;
        return `${hour}:${m} ${ampm}`;
    }


    function formatDateReadable(dateStr) {
        const d = new Date(dateStr);
        return d.toLocaleDateString("en-US", {
            year: "numeric",
            month: "long",
            day: "2-digit"
        });
    }

    function getFullDateTime(visit){
        return visit.visit_date ? formatDateMonth(visit.visit_date) + ' ' + formatTimeAMPM(visit.visit_time) : '-';
    }
    
    function convertTo24Hour(time12h) {
        if (!time12h) return "";
        if (!time12h.includes("AM") && !time12h.includes("PM")) return time12h;
    
        const [time, modifier] = time12h.split(" ");
        let [hours, minutes] = time.split(":");
    
        if (modifier === "PM" && hours !== "12") hours = String(parseInt(hours) + 12);
        if (modifier === "AM" && hours === "12") hours = "00";
    
        return `${hours.padStart(2, "0")}:${minutes}:00`;
    }
    
    const HOME_VISIT_TIME_SLOTS = ["08:00 AM","09:00 AM","10:00 AM","11:00 AM","12:00 PM","01:00 PM","02:00 PM","03:00 PM","04:00 PM","05:00 PM"];
    
    // TIME GRID RENDER
    function renderHomeVisitTimeGrid() {
        const grid = document.getElementById("homevisit-time-grid");
        const date = document.getElementById("homevisit-schedule-date").value;
    
        if (!grid) return;
    
        if (!date) {
            grid.innerHTML = `<p class="text-muted text-center small">
                Please select a date first
            </p>`;
            return;
        }
    
        grid.innerHTML = "";
    
        HOME_VISIT_TIME_SLOTS.forEach(time => {
            const btn = document.createElement("button");
            btn.type = "button";
            btn.className = "btn btn-outline-primary time-slot-btn";
            btn.textContent = time;
    
            btn.onclick = () => {
                document.getElementById("homevisit-schedule-time").value = time;
                document.getElementById("homevisit-preview-time").textContent = time;
    
                document.querySelectorAll(".time-slot-btn")
                    .forEach(b => b.classList.remove("active"));
    
                btn.classList.add("active");
            };
    
            grid.appendChild(btn);
        });
    }


    function addHomeVisitEvent(date, item) {
    if (!eventsHome[date]) eventsHome[date] = [];
        eventsHome[date].push(item);
    }
    
    function rebindReadyCheckboxes() {
        const checkboxes = document.querySelectorAll('.ready-checkbox');
        const selectAll = document.getElementById('selectAllReady');
        const totalSelected = document.getElementById('total-selected');
        
        checkboxes.forEach(cb => {
            cb.addEventListener('change', () => {
                const count = document.querySelectorAll('.ready-checkbox:checked').length;
                if (totalSelected) totalSelected.textContent = count;
                if (selectAll) selectAll.checked = count === checkboxes.length;
            });
        });
    }


    function loadExistingHomeVisitEvents() {
        eventsHome = {}; // reset

        document.querySelectorAll("#homevisit-tbody tr").forEach(row => {
            const v = safeParseJSON(row.dataset.visit || "{}");
            if (!v.visit_date || !v.visit_time) return;

            const address = getFullAddress(v) || "-";
            const prettyTime = formatTime12(v.visit_time);

            addHomeVisitEvent(
                v.visit_date,
                `${v.last_name}, ${v.first_name} - ${address} - Scheduled - ${prettyTime}`
            );
        });
    }


    function showError(message) {
    if (!errorModalEl) { alert(message); return; }
        const modal = new bootstrap.Modal(errorModalEl);
        errorModalEl.querySelector('.modal-body').innerHTML = message;
        modal.show();
    if (successModalEl) bootstrap.Modal.getInstance(successModalEl)?.hide();
    }

    function showSuccess(message, currentStage = null, nextStage = null, appId = null) {
    if (!successModalEl) { alert(message); return; }
        let msgEl = successModalEl.querySelector('.app-success-message');
        const bodyEl = successModalEl.querySelector('.modal-body');
    if (!msgEl && bodyEl) {
        msgEl = document.createElement('div');
        msgEl.className = 'app-success-message mb-3';
        bodyEl.insertBefore(msgEl, bodyEl.firstChild);
    }
    if (msgEl) msgEl.textContent = message; else alert(message);

    new bootstrap.Modal(successModalEl).show();
    if (currentStage && nextStage && appId) setTimeout(() => animateTimeline(currentStage, nextStage, appId), 120);
    }

    function showScheduleSuccess(message) {
    const modalEl = document.getElementById('ScheduleSuccessModal');
    if (!modalEl) { alert(message); return; }
    const msgEl = modalEl.querySelector('.schedule-success-message');
    if (msgEl) msgEl.textContent = message;
    new bootstrap.Modal(modalEl).show();
    }
    
    function showHomeVisitSuccess(message = "Home Visit scheduled successfully!") {
        const modalEl = document.getElementById("homeVisitSuccessModal");
        if (!modalEl) { alert(message); return; }
    
        const msgEl = modalEl.querySelector("#homeVisitSuccessMessage");
        if (msgEl) msgEl.textContent = message;
    
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    }

    function updateCounter(id, delta) {
    const el = document.getElementById(id);
    if (!el) return;
    el.textContent = Math.max(0, parseInt(el.textContent || 0) + delta);
    }

    function animateTimeline(currentStage, nextStage, appId = null) {
        const steps = Array.from(successModalEl.querySelectorAll('.timeline-step'));
        const dot = successModalEl.querySelector('.current-dot');
        const line = successModalEl.querySelector('.timeline-line');
        if (!dot || !line || !steps.length) return;

        let currentIndex = steps.findIndex(s => s.dataset.step === currentStage);
        let nextIndex = steps.findIndex(s => s.dataset.step === nextStage);

        if (currentIndex < 0) currentIndex = 0;
        if (nextIndex < 0) nextIndex = steps.length - 1;

        steps.forEach(s => s.classList.remove('current'));

        for (let i = 0; i < currentIndex; i++) steps[i].classList.add('completed');

        steps[currentIndex].classList.add('current');

        // Position dot at current step
        const startX = steps[currentIndex].offsetLeft + steps[currentIndex].offsetWidth / 2;
        dot.style.transition = 'none';
        dot.style.left = startX + 'px';

        // Position line at current
        line.style.transition = 'none';
        line.style.width = startX - line.offsetLeft + 'px';

        setTimeout(() => {
            dot.style.transition = 'left 0.6s ease';
            line.style.transition = 'width 0.6s ease';
        }, 50);

        // Animate through each step
        for (let i = currentIndex; i < nextIndex; i++) {
            ((fromIdx, toIdx, t) => {
                setTimeout(() => {
                    const targetX = steps[toIdx].offsetLeft + steps[toIdx].offsetWidth / 2;

                    // Animate dot
                    dot.style.left = targetX + 'px';

                    // Animate line
                    line.style.width = targetX - line.offsetLeft + 'px';

                    // Update steps after short delay
                    setTimeout(() => {
                        steps[fromIdx].classList.add('completed');
                        steps[fromIdx].classList.remove('current');
                        steps[toIdx].classList.add('current');
                    }, 400);
                }, t);
            })(i, i + 1, 600 * (i - currentIndex));
        }

        // Final step
        setTimeout(() => {
            steps[nextIndex].classList.add('completed');
            steps[nextIndex].classList.remove('current');

                const finalX = steps[nextIndex].offsetLeft + steps[nextIndex].offsetWidth / 2;
                line.style.width = finalX - line.offsetLeft + 'px';
            }, 600 * (nextIndex - currentIndex + 1));
        }

        //  (Move to Schedule)
        document.addEventListener('click', e => {
            const btn = e.target.closest('.confirmNextStep');
            if (!btn) return;

            currentRow = btn.closest('tr');

            if (!currentRow || !currentRow.dataset.details) {
                showError("Application data missing.");
                return;
            }

            moveToScheduleModal?.show();
        });

        const confirmMoveBtn = document.getElementById('confirmMoveToScheduleBtn');
        confirmMoveBtn?.addEventListener('click', function () {
            if (!currentRow) return showError("No application selected.");

            const appData = safeParseJSON(currentRow.dataset.details || '{}');
            const appId = appData.application_id || appData.id;
            if (!appId) return showError("Application ID missing.");

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

            fetch("{{ route('application.moveToSchedule') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                },
                body: JSON.stringify({ application_id: appId })
            })
            .then(res => res.json())
            .then(data => {
                if (!data.success) return showError(data.message || "Failed to move application.");

                moveToScheduleModal?.hide();

                const updatedApp = data.application || {};
                const fullAddress = getFullAddress(updatedApp);

                currentRow.dataset.details = JSON.stringify(updatedApp);
                currentRow.querySelector('.stage-cell').textContent = updatedApp.application_stage || "Schedule of Submission";
                const statusCell = currentRow.querySelector('.status-cell');
                if (statusCell) {
                    statusCell.textContent = updatedApp.status || "Awaiting Documents";
                    statusCell.className = "status-cell fw-semibold " + getStatusClass(updatedApp.status || "Awaiting Documents");
                }

                const nextBtn = currentRow.querySelector('.confirmNextStep');
                if (nextBtn) {
                    nextBtn.disabled = true;
                    nextBtn.classList.replace('btn-warning', 'btn-secondary');
                }

            const scheduleBody = document.querySelector('#scheduleTable tbody');
            if (scheduleBody) {
                const newRow = document.createElement('tr');

                const scheduleData = { ...updatedApp, schedule_req_id: data.scheduled.schedule_req_id };
                newRow.dataset.details = JSON.stringify(scheduleData);

                 newRow.innerHTML = `
                  <td>${scheduleBody.children.length + 1}</td>
                  <td>${data.scheduled.reference_no || '-'}</td>
                  <td>${data.scheduled.last_name || ''}, ${data.scheduled.first_name || ''}</td>
                  <td>${data.scheduled.category || '-'}</td>
                  <td>-</td>
                  <td>${data.scheduled.street || '-'}, ${data.scheduled.barangay || '-'}, ${data.scheduled.municipality || '-'}</td>
                  <td class="fw-semibold text-pending">${data.scheduled.status || 'Pending'}</td>
                                                      <td class="text-center">
                                    <button type="button" class="btn btn-success btn-sm schedule-btn" data-id="{{ $sched->schedule_req_id }}"
                                        @if($sched->scheduled_date)
                                            disabled style="background-color: #6c757d; border-color: #6c757d;" title="Already scheduled"
                                        @endif ><i class="fas fa-calendar-alt me-1"></i> Schedule</button>
                                    <button type="button" class="btn btn-primary btn-sm"><i class="fas fa-edit me-1"></i> Edit</button>
                                    <button type="button" class="btn btn-warning btn-sm next-stage-btn text-white" data-id="{{ $sched->schedule_req_id }}"><i class="fas fa-forward me-1"></i> Next Stage</button>
                                </td>
                `;
                scheduleBody.appendChild(newRow);
            }

            updateCounter('pending-total', -1);
            updateCounter('schedule-total', 1);

            showSuccess(data.message || "Application moved successfully!", "Review Application", "Schedule of Submission", appId);

            currentRow = null;
        })
        .catch(err => {
            console.error(err);
            showError("Server error. Please try again.");
        });
    });

    document.querySelector("#scheduleTable")?.addEventListener("click", function(e) {
        const btn = e.target.closest(".schedule-btn");
        if (!btn) return;

        const scheduleId = btn.dataset.id;
        if (!scheduleId) return alert("Schedule ID not found.");

        document.getElementById("schedule-id").value = scheduleId;

        const row = Array.from(document.querySelectorAll("#scheduleTable tbody tr")).find(r => {
            const d = JSON.parse(r.dataset.details || "{}");
            return d.schedule_req_id == scheduleId;
        });

        if (!row) return alert("Row not found.");

        const rowData = JSON.parse(row.dataset.details || "{}");

        // Date & Time
        document.getElementById("schedule-date").value = rowData.scheduled_date || "";
        document.getElementById("schedule-time").value = rowData.scheduled_time || "";

        const fullName = rowData.first_name && rowData.last_name
            ? `${rowData.first_name} ${rowData.last_name}`
            : row.cells[2]?.textContent || "-";
        const fullAddress = rowData.street || rowData.barangay || rowData.municipality
            ? [rowData.street, rowData.barangay, rowData.municipality].filter(Boolean).join(', ')
            : row.cells[5]?.textContent || "-";

        document.getElementById("applicant-name").textContent = fullName;
        document.getElementById("applicant-address").textContent = fullAddress;

        // Update Preview
        document.getElementById("preview-name").textContent = fullName;
        document.getElementById("preview-address").textContent = fullAddress;

        scheduleModal?.show();
    });


    function renderCalendar(year, month) {
        if (!calendarMonth || !calendarBody) return;

        calendarMonth.textContent = new Date(year, month).toLocaleString("default", {
            month: "long",
            year: "numeric"
        });

        const firstDay = new Date(year, month, 1).getDay();
        const lastDate = new Date(year, month + 1, 0).getDate();

        let html = "";
        let day = 1;

        for (let i = 0; i < 6; i++) {
            html += "<tr>";
            for (let j = 0; j < 7; j++) {
                if (i === 0 && j < firstDay) {
                    html += "<td></td>";
                } else if (day > lastDate) {
                    html += "<td></td>";
                } else {
                    const mm = String(month + 1).padStart(2, "0");
                    const dd = String(day).padStart(2, "0");
                    const fullDate = `${year}-${mm}-${dd}`;

                    let eventHtml = "<div class='events'>";
                    if (events[fullDate]) {
                        events[fullDate].forEach(ev => {
                            const parts = ev.split(" - ");
                            const status = (parts[2] || "").toLowerCase();
                            const cls = status === "completed" ? "completed" : status === "pending" ? "pending" : "scheduled";
                            eventHtml += `<span class="event ${cls}">${ev}</span>`;
                        });
                    }
                    eventHtml += "</div>";

                    html += `
                        <td data-date="${fullDate}">
                            <div class="day-number">
                                ${day}
                                ${events[fullDate] ? `<div class="event-count">${events[fullDate].length}</div>` : ""}

                            </div>
                            ${eventHtml}
                        </td>`;
                    day++;
                }
            }
            html += "</tr>";
        }
        calendarBody.innerHTML = html;

        // CLICK TO SHOW DAY MODAL
        calendarBody.querySelectorAll("td[data-date]").forEach(td => {
            td.addEventListener("click", () => {
                const date = td.getAttribute("data-date");
                const modalBody = document.getElementById("modalBody");
                if (!modalBody) return;

                modalBody.innerHTML = "";

                if (events[date] && events[date].length) {
                    events[date].forEach(ev => {
                        let [name, address, status, time] = ev.split(" - ");
                        time = formatTime12(time || "");

                        const card = document.createElement("div");
                        card.classList.add("schedule-card");
                        card.innerHTML = `
                            <div class="person-info"><span class="name">${name || "-"}</span></div>
                            <div class="person-address">${address || "-"}</div>
                            <div class="person-schedule">
                                <span class="time">${time}</span>
                                <span class="status status-${(status || "").toLowerCase()}">${status || "Scheduled"}</span>
                            </div>`;
                        modalBody.appendChild(card);
                    });
                } else {
                    modalBody.innerHTML = `<p class="text-center text-secondary">No one scheduled for this date.</p>`;
                }

                document.getElementById("modalTitle").textContent = `Scheduled People for ${date}`;
                dayScheduleModal?.show();
            });
        });
    }

    document.getElementById("prevMonth")?.addEventListener("click", () => {
        currentMonth--;
        if (currentMonth < 0) { currentMonth = 11; currentYear--; }
        renderCalendar(currentYear, currentMonth);
    });
    
    document.getElementById("nextMonth")?.addEventListener("click", () => {
        currentMonth++;
        if (currentMonth > 11) { currentMonth = 0; currentYear++; }
        renderCalendar(currentYear, currentMonth);
    });
    
    // Add this line here
    renderCalendar(currentYear, currentMonth);


    // MOVE TO HOME VISIT
    document.addEventListener('click', e => {
        const btn = e.target.closest('.next-stage-btn');
        if (!btn) return;
    
        currentRow = btn.closest('tr');
        const appData = safeParseJSON(currentRow.dataset.details || '{}');
    
        const modalBody = document.querySelector('#moveToHomeVisitModal .modal-body');
        if (modalBody) {
            modalBody.innerHTML = `
                Are you sure you want to send
                <strong>${appData.last_name}, ${appData.first_name}</strong>
                to the <strong>Home Visit</strong> stage?
            `;
        }
        moveToHomeVisitModal?.show();
    });

    document.getElementById('confirmMoveToHomeVisitBtn')?.addEventListener('click', function () {
        if (!currentRow) return showError("No application selected.");
    
        const appData = safeParseJSON(currentRow.dataset.details || '{}');
        const scheduleReqId = appData.schedule_req_id || appData.id;
        if (!scheduleReqId) return showError("Schedule ID missing.");
    
        fetch("{{ route('scheduled.moveToHomeVisit') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ schedule_req_id: scheduleReqId })
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success) return showError(data.message || "Failed to move to Home Visit.");
    
            // Remove from Schedule table
            currentRow.remove();
    
            // Insert into Home Visit table
            const tbody = document.getElementById('homevisit-tbody');
            if (tbody && data.homeVisit) {
                const visit = data.homeVisit;
                const rowIndex = tbody.rows.length + 1;
                const address = getFullAddress(visit) || '-';
    
                const formattedSchedule = visit.visit_date
                    ? formatDateReadable(visit.visit_date) + (visit.visit_time ? ' ' + formatTime12(visit.visit_time) : '')
                    : '-';
    
                const row = document.createElement('tr');
                row.dataset.visit = JSON.stringify(visit);
                tbody.appendChild(row);
    
                row.innerHTML = `
                    <td>${rowIndex}</td>
                    <td>${visit.reference_no}</td>
                    <td>${visit.last_name}, ${visit.first_name}</td>
                    <td>${address}</td>
                    <td>${formattedSchedule}</td>
                    <td>${visit.category ?? '-'}</td>
                    <td class="fw-semibold ${visit.visit_status==='Scheduled' ? 'text-purple' : 'text-warning'}">
                        ${visit.visit_status || 'Pending'}
                    </td>
                    <td class="text-center">
                        <button class="btn btn-sm text-white scheduleVisit" style="background-color:#003366;" data-id="${visit.visit_id}" ${visit.visit_status==='Scheduled' ? 'disabled' : ''}>
                            Schedule
                        </button>
                        <button class="btn btn-warning btn-sm text-white nextStepReady" data-id="${visit.visit_id}">Next Step</button>
                        <button class="btn btn-danger btn-sm rejectHomeVisit" data-id="${visit.visit_id}">Reject</button>
                    </td>
                `;
    
                // If already scheduled, add event
                if (visit.visit_date && visit.visit_time) {
                    addHomeVisitEvent(
                        visit.visit_date,
                        `${visit.last_name}, ${visit.first_name} - ${address} - Scheduled - ${formatTime12(visit.visit_time)}`
                    );
                    renderCalendarHome(currentYearHome, currentMonthHome);
                }
            }
            currentRow = null;
            moveToHomeVisitModal?.hide();
    
            animateTimeline('Schedule of Submission', 'Home Visit', appData.id);
            showSuccess(
                "Application moved to Home Visit!",
                "Schedule of Submission",
                "Home Visit",
                appData.id
            );
        })
        .catch(err => {
            console.error(err);
            showError("Server error. Please try again.");
        });
    });

    // OPEN SCHEDULE MODAL
    document.addEventListener("click", e => {
        const btn = e.target.closest(".scheduleVisit");
        if (!btn) return;
        
        const row = btn.closest("tr");
        const visit = safeParseJSON(row.dataset.visit || "{}");
    
    document.getElementById("homevisit-schedule-id").value = visit.visit_id || "";
    document.getElementById("homevisit-schedule-date").value = visit.visit_date || "";
    document.getElementById("homevisit-schedule-time").value = "";
    
    document.getElementById("homevisit-schedule-date")
        .setAttribute("min", new Date().toISOString().split("T")[0]);
    document.getElementById("homevisit-user-name").textContent =
        `${visit.first_name || ""} ${visit.last_name || ""}`.trim();
    document.getElementById("homevisit-user-address").textContent =
        [visit.street, visit.barangay, visit.municipality].filter(Boolean).join(", ");
    document.getElementById("homevisit-preview-date").textContent =
        visit.visit_date ? formatDateReadable(visit.visit_date) : "-";
    document.getElementById("homevisit-preview-time").textContent = "-";
        renderHomeVisitTimeGrid();
        homeVisitScheduleModal?.show();
    });

    // DATE CHANGE → RELOAD GRID
    document.getElementById("homevisit-schedule-date")
    ?.addEventListener("change", () => {
        document.getElementById("homevisit-preview-date").textContent =
            formatDateReadable(event.target.value);
        renderHomeVisitTimeGrid();
    });

    // SAVE SCHEDULE
    document.getElementById("saveHomeVisitScheduleBtn")
        ?.addEventListener("click", () => {
         event.preventDefault();
        const visitId = document.getElementById("homevisit-schedule-id").value;
        const visitDate = document.getElementById("homevisit-schedule-date").value;
        const visitTime12 = document.getElementById("homevisit-schedule-time").value;
        const visitTime24 = convertTo24Hour(visitTime12);
        const row = document.querySelector(`tr[data-visit*='"visit_id":${visitId}']`);
        if (row) {
        const scheduleBtn = row.querySelector(".scheduleVisit");
        if (scheduleBtn) {
            scheduleBtn.disabled = true;
            scheduleBtn.classList.remove("btn-primary");
            scheduleBtn.classList.add("btn-secondary");     
            scheduleBtn.style.backgroundColor = "#6c757d";   
        }
    }

    if (!visitId || !visitDate || !visitTime24)
        return showError("Please select date and time.");
    
        fetch("{{ route('homevisit.saveSchedule') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                visit_id: visitId,
                visit_date: visitDate,
                visit_time: visitTime24
            })
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success) return showError(data.message || "Failed to save.");
            
            const row = document.querySelector(
                `tr[data-visit*='"visit_id":${visitId}']`
            );
            if (!row) return;
            
            const d = safeParseJSON(row.dataset.visit || "{}");
            d.visit_date = visitDate;
            d.visit_time = visitTime24;
            d.visit_status = "Scheduled";
            row.dataset.visit = JSON.stringify(d);
            
            row.children[4].textContent = `${formatDateReadable(visitDate)} ${visitTime12}`;
            row.children[6].textContent = "Scheduled";
            row.children[6].className = "fw-semibold text-purple";
            
            const btn = row.querySelector(".scheduleVisit");
            btn.disabled = true;
            btn.classList.replace("btn-primary", "btn-secondary");
            
            addHomeVisitEvent(
                visitDate,
                `${d.last_name}, ${d.first_name} - ${getFullAddress(d)} - Scheduled - ${visitTime12}`
            );
            
            renderCalendarHome(currentYearHome, currentMonthHome);
            
            homeVisitScheduleModal?.hide();
            
            // Show dedicated Home Visit success modal
            showHomeVisitSuccess("Home Visit scheduled successfully!");
        })
        .catch(() => showError("Server error."));
    });

    // CALENDAR RENDER
    function renderCalendarHome(year, month) {
        calendarMonthHome.textContent = new Date(year, month)
            .toLocaleString("default", { month: "long", year: "numeric" });
    
        const first = new Date(year, month, 1).getDay();
        const last = new Date(year, month + 1, 0).getDate();
    
        let html = "";
        let day = 1;
    
        for (let i = 0; i < 6; i++) {
            html += "<tr>";
            for (let j = 0; j < 7; j++) {
                if (i === 0 && j < first) html += "<td></td>";
                else if (day > last) html += "<td></td>";
                else {
                    const mm = String(month + 1).padStart(2, "0");
                    const dd = String(day).padStart(2, "0");
                    const full = `${year}-${mm}-${dd}`;
                    let items = "";
    
                    if (eventsHome[full]) {
                        eventsHome[full].forEach(ev => {
                            let cls = ev.includes("Scheduled") ? "scheduled" : "pending";
                            items += `<span class="event ${cls}" data-name="${ev}">${ev}</span>`;
                        });
                    }
    
                    html += `
                        <td data-date="${full}">
                            <div class="day-number">
                                ${day}
                                ${eventsHome[full] ? `<div class="event-count">${eventsHome[full].length}</div>` : ""}
                            </div>
                            <div class="events">${items}</div>
                        </td>
                    `;
                    day++;
                }
            }
            html += "</tr>";
        }
        calendarBodyHome.innerHTML = html;
    }

    // CLICK ON CALENDAR DATE TO SHOW HOME VISITS
    calendarBodyHome?.addEventListener("click", e => {
        const td = e.target.closest("td[data-date]");
        if (!td) return;
    
        const date = td.getAttribute("data-date");
        if (!date) return;
    
        homeVisitModalTitle.textContent = `Home Visits for ${formatDateReadable(date)}`;
        homeVisitModalBody.innerHTML = "";
    
        if (!eventsHome[date] || !eventsHome[date].length) {
            homeVisitModalBody.innerHTML = `<p class="text-center text-secondary">No visits scheduled for this day.</p>`;
        } else {
            eventsHome[date].forEach(ev => {
                const parts = ev.split(" - ");
                const name = parts[0] || "-";
                const addr = parts[1] || "-";
                const status = parts[2] || "Pending";
                const time = parts[3] || "";
    
                const card = document.createElement("div");
                card.className = "schedule-card mb-2 p-3 border rounded";
    
                card.innerHTML = `
                    <div class="fw-semibold mb-1">${name}</div>
                    <div class="text-muted small mb-2">${addr}</div>
                    <div class="d-flex justify-content-end align-items-center flex-column">
                        <div class="small text-secondary mb-1"><i class="bi bi-clock"></i> ${formatTime12(time)}</div>
                        <span class="status status-${(status || "").toLowerCase()}">${status}</span>
                    </div>
                `;
    
                homeVisitModalBody.appendChild(card);
            });
        }
    
        homeVisitDayModal?.show();
    });
    
    // MONTH NAVIGATION
    document.getElementById("prevMonthHome")?.addEventListener("click", () => {
        currentMonthHome--;
        if (currentMonthHome < 0) { currentMonthHome = 11; currentYearHome--; }
        renderCalendarHome(currentYearHome, currentMonthHome);
    });
    
    document.getElementById("nextMonthHome")?.addEventListener("click", () => {
        currentMonthHome++;
        if (currentMonthHome > 11) { currentMonthHome = 0; currentYearHome++; }
        renderCalendarHome(currentYearHome, currentMonthHome);
    });
    
    loadExistingHomeVisitEvents();
    renderCalendarHome(currentYearHome, currentMonthHome);

    // NEXT STEP → MOVE TO READY FOR SUBMISSION
    document.addEventListener('click', (e) => {
    const btn = e.target.closest('.nextStepReady');
    if (!btn) return;
    
    currentRow = btn.closest('tr');
    const visitData = safeParseJSON(currentRow.dataset.visit || '{}');
    
    const modalEl = document.getElementById('moveToReadyModal');
    if (!modalEl) return;
    
    const modalBody = modalEl.querySelector('.modal-body');
    modalBody.innerHTML = `
        Are you sure you want to move
        <strong>${visitData.last_name}, ${visitData.first_name}</strong>
        to <strong>Ready for Submission</strong>?
    `;
    
    new bootstrap.Modal(modalEl).show();
    });
    
    // CONFIRM MOVE TO READY
    document.getElementById('confirmMoveToReadyBtn')?.addEventListener('click', function () {
    if (!currentRow) return showError("No Home Visit record selected.");
    
    const visitData = safeParseJSON(currentRow.dataset.visit || '{}');
    const visitId = visitData.visit_id;
    
    if (!visitId) return showError("Visit ID missing.");

    fetch("{{ route('homevisit.moveToReady') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({ visit_id: visitId })
    })
        .then(res => res.json())
        .then(data => {
            if (!data.success)
                return showError(data.message || "Failed to move to Ready for Submission.");
        
            // REMOVE FROM HOME VISIT TABLE
            currentRow.remove();
        
            // ADD TO READY FOR SUBMISSION TABLE
            const tbody = document.getElementById('submission-tbody');
            if (tbody && data.readyRecord) {
        
                const record = data.readyRecord;
                const rowIndex = tbody.rows.length + 1;
        
                const address = [
                    record.street,
                    record.barangay,
                    record.municipality
                ].filter(Boolean).join(', ') || '-';
        
                const row = document.createElement('tr');
                row.dataset.ready = JSON.stringify(record);
        
                row.innerHTML = `
                    <td>
                        <input type="checkbox"
                               class="ready-checkbox"
                               data-id="${record.ready_process_id}">
                    </td>
                    <td>${rowIndex}</td>
                    <td>${record.reference_no || '-'}</td>
                    <td>${record.last_name}, ${record.first_name}</td>
                    <td>${address}</td>
                    <td>${record.category || '-'}</td>
                    <td>-</td>
                    <td class="fw-semibold text-primary">
                        ${record.status || 'Ready'}
                    </td>
                    <td class="text-center">
                        <button type="button"
                                class="btn btn-primary btn-sm add-beneficiary-btn"
                                data-id="${record.ready_process_id}">
                            <i class="fas fa-user-plus me-1"></i> Add
                        </button>
                    </td>
                `;
        
                tbody.appendChild(row);
        
                // UPDATE TOTAL COUNT
                const totalEl = document.getElementById('submission-total');
                if (totalEl) {
                    totalEl.textContent = parseInt(totalEl.textContent || 0) + 1;
                }
        
                // REBIND CHECKBOX EVENTS
                rebindReadyCheckboxes();
            }
        
            // UPDATE TIMELINE
            const timelineStep = document.querySelector(`#timeline-${visitId}`);
            if (timelineStep) {
                timelineStep.classList.remove('text-purple', 'text-warning');
                timelineStep.classList.add('text-success');
                timelineStep.textContent = 'Ready to Process';
            }
        
            currentRow = null;
        
            // CLOSE MODAL
            const modalEl = document.getElementById('moveToReadyModal');
            if (modalEl) bootstrap.Modal.getInstance(modalEl)?.hide();
        
            showSuccess(
                "Record moved to Ready for Submission successfully!",
                "Home Visit",
                "Ready to Process",
                visitId
            );
        })
        .catch(err => {
            console.error(err);
            showError("Server error. Please try again.");
        });
    });

});

</script>
    
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
    
        const scheduleModal = new bootstrap.Modal(document.getElementById("scheduleModal"));
        const scheduleForm = document.getElementById("scheduleForm");
        let selectedApplicantId = null;
    
        const dateInput = document.getElementById("schedule-date");
        const addressInput = document.getElementById("barangay-address");
        const timeInput = document.getElementById("schedule-time");
        const timeGrid = document.getElementById("timeGrid");
    
        const saveBtn = document.getElementById("saveScheduleBtn");
        const times = ["08:00 AM","09:00 AM","10:00 AM","11:00 AM","12:00 PM","01:00 PM","02:00 PM","03:00 PM","04:00 PM","05:00 PM"];
    
        times.forEach(t => {
            const card = document.createElement("div");
            card.className = "time-card";
            card.textContent = t;
            card.dataset.time = t;
            card.addEventListener("click", function () {
                document.querySelectorAll(".time-card").forEach(tc => tc.classList.remove("active"));
                card.classList.add("active");
                timeInput.value = t;
                document.getElementById("preview-time").textContent = t;
            });
            timeGrid.appendChild(card);
        });
    

        flatpickr(dateInput, {
            dateFormat: "Y-m-d",
            minDate: "today",
            disableMobile: true,
            onChange: function(selectedDates, dateStr) {
                document.getElementById("preview-date").textContent = dateStr || "-";
            }
        });
    
        document.querySelectorAll(".schedule-btn").forEach(btn => {
            btn.addEventListener("click", function () {
                const tr = this.closest("tr");
                const data = JSON.parse(tr.dataset.details);
    
                selectedApplicantId = data.schedule_req_id;
    
                // Populate modal fields
                document.getElementById("schedule-id").value = data.schedule_req_id;
                document.getElementById("applicant-name").textContent = `${data.last_name}, ${data.first_name}`;
                const fullAddress = `${data.street ?? ''}, ${data.barangay ?? ''}, ${data.municipality ?? ''}`;
                document.getElementById("applicant-address").textContent = fullAddress;
                addressInput.value = fullAddress;
    
                // Reset date/time fields & preview
                dateInput.value = "";
                timeInput.value = "";
                document.getElementById("preview-date").textContent = "-";
                document.getElementById("preview-time").textContent = "-";
                document.getElementById("preview-address").textContent = fullAddress;
    
                // Reset time grid
                document.querySelectorAll(".time-card").forEach(tc => tc.classList.remove("active"));
    
                scheduleModal.show();
            });
        });
    
        // Update preview for address
        addressInput.addEventListener("input", function () {
            document.getElementById("preview-address").textContent = this.value;
        });
    
        // Convert AM/PM → 24-hour
        function convertTo24Hour(time12h) {
            const [time, modifier] = time12h.split(" ");
            let [hours, minutes] = time.split(":");
            hours = parseInt(hours, 10);
            if (modifier === "PM" && hours !== 12) hours += 12;
            if (modifier === "AM" && hours === 12) hours = 0;
            return `${hours.toString().padStart(2, "0")}:${minutes}:00`;
        }
    
        // Save schedule via AJAX
        scheduleForm.addEventListener("submit", function (e) {
            e.preventDefault();
    
            const scheduleId = selectedApplicantId;
            const scheduledDate = dateInput.value;
            let scheduledTime = timeInput.value;
            const address = addressInput.value;
    
            if (!scheduledDate || !scheduledTime || !address) {
                alert("Please select a date, time, and address.");
                return;
            }
    
            // Convert time to 24-hour for database
            if (scheduledTime.includes("AM") || scheduledTime.includes("PM")) {
                scheduledTime = convertTo24Hour(scheduledTime);
            }
    
            // Show loading
            saveBtn.disabled = true;
            saveBtn.querySelector(".btn-text").classList.add("d-none");
            saveBtn.querySelector(".btn-loading").classList.remove("d-none");
    
            fetch("{{ route('scheduled-submissions.store') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    schedule_req_id: scheduleId,
                    scheduled_date: scheduledDate,
                    scheduled_time: scheduledTime,
                    address: address
                })
            })
            .then(res => res.json())
            .then(data => {
                saveBtn.disabled = false;
                saveBtn.querySelector(".btn-text").classList.remove("d-none");
                saveBtn.querySelector(".btn-loading").classList.add("d-none");
    
                if (data.success) {
                    alert("Schedule saved successfully!");
                    location.reload();
                } else {
                    alert(data.message || "Failed to save schedule. Check your inputs.");
                }
            })
            .catch(err => {
                console.error(err);
                alert("An error occurred while saving schedule.");
                saveBtn.disabled = false;
                saveBtn.querySelector(".btn-text").classList.remove("d-none");
                saveBtn.querySelector(".btn-loading").classList.add("d-none");
            });
        });
    
    });
    </script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ADD TO BENEFICIARY FROM READY TO PROCESS
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.add-beneficiary-btn');
        if (!btn) return;

        const recordId = btn.dataset.id;
        const modalEl = document.getElementById('confirmBeneficiaryModal');
        if (!modalEl) return;

        modalEl.dataset.recordId = recordId;

        let modal = bootstrap.Modal.getInstance(modalEl);
        if (!modal) modal = new bootstrap.Modal(modalEl);

        modal.show();
    });

    // CONFIRM ADD TO BENEFICIARY
    document.getElementById('confirmAddBeneficiaryBtn')?.addEventListener('click', function () {
        const btn = this;
        const text = btn.querySelector('.btn-text');
        const loading = btn.querySelector('.btn-loading');

        // Show loading state
        btn.disabled = true;
        text.classList.add('d-none');
        loading.classList.remove('d-none');

        const modalEl = document.getElementById('confirmBeneficiaryModal');
        const recordId = modalEl.dataset.recordId;

        if (!recordId) {
            showError("Record ID missing.");
            btn.disabled = false;
            text.classList.remove('d-none');
            loading.classList.add('d-none');
            return;
        }

        fetch(window.routes.moveToBeneficiary, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": window.csrfToken
            },
            body: JSON.stringify({ ready_process_id: recordId })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            text.classList.remove('d-none');
            loading.classList.add('d-none');

            if (!data.success) return showError(data.message || "Failed to add beneficiary.");

            // Remove from Ready to Process table
            const row = document.querySelector(`tr[data-ready*='"ready_process_id":${recordId}']`);
            if (row) row.remove();

            // Add to Beneficiary Table
            const tbody = document.getElementById('beneficiaryTable');
            if (tbody && data.beneficiary) {
                const record = data.beneficiary;
                const rowEl = document.createElement('tr');
                const rowIndex = tbody.rows.length + 1;
                const dateAdded = record.date_added ? record.date_added.split('T')[0] : '-';
                const hasBenefits = record.benefits && record.benefits.length > 0;

                rowEl.dataset.ready = JSON.stringify(record);
                rowEl.innerHTML = `
                    <td>${rowIndex}</td>
                    <td>${record.first_name || '-'} ${record.last_name || '-'}</td>
                    <td>${record.barangay || '-'}</td>
                    <td>${dateAdded}</td>
                    <td>${record.assistance_status || 'Pending'}</td>
                    <td>${record.category || 'N/A'}</td>
                    <td class="text-center">
                        <button class="btn btn-sm view-details-btn text-white" style="background-color: #003366;" data-id="${record.beneficiary_id}">👁️ View</button>
                        <button class="btn btn-sm btn-danger delete-beneficiary-btn text-white" data-id="${record.beneficiary_id}">🗑️ Delete</button>
                        <button class="btn btn-sm choose-category-benefits-btn text-white ${hasBenefits ? 'btn-secondary' : 'btn-warning'}"
                            data-id="${record.beneficiary_id}"
                            data-category="${record.category || ''}"
                            ${hasBenefits ? 'disabled' : ''}>
                            ${hasBenefits ? 'Benefits Set' : 'Choose Benefits'}
                        </button>
                    </td>
                `;
                tbody.appendChild(rowEl);

                // Update barangay counts / map if needed
                if (record.barangay) {
                    barangayCounts[record.barangay] = (barangayCounts[record.barangay] || 0) + 1;

                    const marker = barangayMarkers[record.barangay];
                    if (marker) {
                        marker.setStyle({ fillColor: getColor(barangayCounts[record.barangay]) });
                        marker.bindTooltip(`${record.barangay}<br>Solo Parents: ${barangayCounts[record.barangay]}`, { direction: "top" });
                        marker.bindPopup(`<b>${record.barangay}</b><br>Solo Parents: ${barangayCounts[record.barangay]}`);
                    }
                }

                renderBarangayList();
            }

            // Update counters
            const readyTotalEl = document.getElementById('submission-total');
            if (readyTotalEl) readyTotalEl.textContent = Math.max(0, parseInt(readyTotalEl.textContent || 0) - 1);

            const beneficiaryTotalEl = document.getElementById('beneficiaryTotalValue');
            if (beneficiaryTotalEl) beneficiaryTotalEl.textContent = parseInt(beneficiaryTotalEl.textContent || 0) + 1;

            // Close modal
            bootstrap.Modal.getInstance(modalEl)?.hide();

            showSuccess("Resident added to Solo Parent Beneficiaries successfully!");
        })
        .catch(err => {
            console.error(err);
            btn.disabled = false;
            text.classList.remove('d-none');
            loading.classList.add('d-none');
            showError("Server error. Please try again.");
        });
    });

});
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const beneficiaryTbody = document.getElementById('beneficiaryTable');
    const barangayList = document.getElementById('beneficiaryBarangayList');

    let map, markersLayer, heatLayer;
    let barangayCounts = {};
    let barangayMarkers = {};
    let markersVisible = true;
    let heatVisible = true;
    let allBeneficiaries = [];

    function getColor(count) {
        if (count > 140) return '#a50026';
        if (count > 120) return '#d73027';
        if (count > 100) return '#fc8d59';
        if (count > 80) return '#fee08b';
        if (count > 50) return '#d9ef8b';
        if (count > 20) return '#91cf60';
        return '#1a9850';
    }

    map = L.map('beneficiaryGisMap', { center: [14.3869, 120.882], zoom: 13 });
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);
    markersLayer = L.layerGroup().addTo(map);

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
            { "type": "Feature", "properties": { "name": "Vibora" }, "geometry": { "type": "Point", "coordinates": [120.8862, 14.3745] } },
            { "type": "Feature", "properties": { "name": "96th" }, "geometry": { "type": "Point", "coordinates": [ 120.5246, 14.2312] } }
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

    function renderBarangayList() {
        barangayList.innerHTML = '';
        barangayData.features.forEach(f => {
            const name = f.properties.name;
            const count = barangayCounts[name] || 0;
            const div = document.createElement('div');
            div.className = 'barangay-item';
            div.style.borderLeft = `6px solid ${getColor(count)}`;
            div.innerHTML = `<strong>${name}</strong><br><small>Solo Parents: ${count}</small>`;
            div.addEventListener('click', () => {
                map.flyTo([f.geometry.coordinates[1], f.geometry.coordinates[0]], 16);
                barangayMarkers[name].openPopup();
            });
            barangayList.appendChild(div);
        });
    }


    async function loadBeneficiaries() {
        try {
            const res = await fetch("{{ route('beneficiaries.index') }}");
            const data = await res.json();
    
            beneficiaryTbody.innerHTML = '';
            barangayCounts = {};
            barangayData.features.forEach(f => barangayCounts[f.properties.name] = 0);
    
    data.beneficiaries.forEach((ben, idx) => {
        const hasBenefits = ben.selected_benefits && ben.selected_benefits.length > 0;
    
        const row = document.createElement('tr');
        row.dataset.id = ben.beneficiary_id;
        row.innerHTML = `
            <td>${idx + 1}</td>
            <td>${ben.first_name} ${ben.last_name}</td>
            <td>${ben.barangay || '-'}</td>
            <td>${ben.date_added ? ben.date_added.split(' ')[0] : '-'}</td>
            <td>${ben.assistance_status || 'N/A'}</td>
            <td>${ben.category || 'N/A'}</td>
            <td class="text-center">
                <button class="btn btn-sm view-details-btn text-white" style="background-color:#003366" data-id="${ben.beneficiary_id}">View</button>
                <button class="btn btn-sm btn-danger delete-beneficiary-btn" data-id="${ben.beneficiary_id}">Delete</button>
                <button
                    class="btn btn-sm choose-category-benefits-btn text-white ${hasBenefits ? 'btn-secondary' : 'btn-warning'}"
                    data-id="${ben.beneficiary_id}"
                    data-category="${ben.category || ''}"
                    ${hasBenefits ? 'disabled' : ''}>
                    ${hasBenefits ? 'Benefits Set' : 'Choose Benefits'}
                </button>
            </td>
        `;
        beneficiaryTbody.appendChild(row);
    
        if (ben.barangay) barangayCounts[ben.barangay] = (barangayCounts[ben.barangay] || 0) + 1;
    });
            renderBarangayList();
            refreshMap();
        } catch (err) {
            console.error(err);
        }
    }

    // VIEW MODAL
    document.addEventListener('click', async e => {
        const btn = e.target.closest('.view-details-btn');
        if (!btn) return;
    
        const id = btn.dataset.id;
        const modalEl = document.getElementById('beneficiaryViewModal');
    
        try {
            const res = await fetch(`/admin/beneficiaries/${id}`);
            const data = await res.json();
            if (!data.success) return alert('Error loading beneficiary');
    
            const ben = data.beneficiary;
    
            modalEl.querySelector('#beneficiaryViewName').textContent = `${ben.first_name} ${ben.last_name}`;
            modalEl.querySelector('#beneficiaryViewAddress').textContent = ben.address || '-';
            modalEl.querySelector('#beneficiaryViewBarangay').textContent = ben.barangay || '-';
            modalEl.querySelector('#beneficiaryViewStatus').textContent = ben.assistance_status || '-';
            modalEl.querySelector('#beneficiaryViewCategory').textContent = ben.category || '-';
            modalEl.querySelector('#beneficiaryViewCreatedAt').textContent = ben.date_added || '-';
    
            // Render benefits as badges
            const benefitsContainer = modalEl.querySelector('#beneficiaryViewBenefits');
            benefitsContainer.innerHTML = '';
            if (ben.benefits && ben.benefits.length > 0) {
                ben.benefits.forEach(b => {
                    const span = document.createElement('span');
                    span.className = 'badge bg-success';
                    span.textContent = b;
                    benefitsContainer.appendChild(span);
                });
            } else {
                const span = document.createElement('span');
                span.className = 'badge bg-secondary';
                span.textContent = '-';
                benefitsContainer.appendChild(span);
            }
    
            new bootstrap.Modal(modalEl).show();
        } catch(err) {
            console.error(err);
            alert('Failed to load beneficiary');
        }
    });

    // DELETE MODAL
    let deleteTargetId = null, deleteTargetRow = null;
    document.addEventListener('click', e => {
        const btn = e.target.closest('.delete-beneficiary-btn');
        if (!btn) return;

        deleteTargetId = btn.dataset.id;
        deleteTargetRow = btn.closest('tr');
        new bootstrap.Modal(document.getElementById('beneficiaryDeleteModal')).show();
    });

    document.getElementById('confirmDeleteBeneficiary').addEventListener('click', async function() {
    if (!deleteTargetId || !deleteTargetRow) return;
    const btn = this; btn.disabled=true; btn.textContent='Deleting...';

    try {
        const res = await fetch(`/admin/beneficiaries/${deleteTargetId}`, {
            method:'DELETE',
            headers:{ 'X-CSRF-TOKEN':'{{ csrf_token() }}' }
        });
        const data = await res.json();
        if (data.success) { deleteTargetRow.remove(); bootstrap.Modal.getInstance(document.getElementById('beneficiaryDeleteModal')).hide(); }
        else alert(data.message || 'Delete failed');
    } catch(err){ console.error(err); alert('Something went wrong!'); }
        btn.disabled=false; btn.textContent='Delete';
        deleteTargetId=null; deleteTargetRow=null;
    });

    const defaultBenefits = [
        "1000 monthly cash subsidy",
        "PhilHealth Coverage",
        "10% + VAT exemption on baby needs",
        "Scholarships for children",
        "Priority in jobs, livelihood, and housing",
        "7 days parental leave + flexible work"
    ];

    // CATEGORY BENEFITS
    const categoryBenefits = {
        "A1. Birth of a child as a consequences of Rape": { recommended: ["Counseling", "Medical Support", "Scholarship"], defaults: defaultBenefits },
        "A2. Widow/Widower": { recommended: ["Livelihood Program", "Housing Assistance"], defaults: defaultBenefits },
        "A3. Spouse of person deprived of Liberty (PDL)": { recommended: ["Livelihood Program", "Legal Aid"], defaults: defaultBenefits },
        "A4. Spouse of person with Disability (PWD)": { recommended: ["Disability Support", "Medical Support"], defaults: defaultBenefits },
        "A5. Due to de facto separation": { recommended: ["Counseling", "Livelihood Program"], defaults: defaultBenefits },
        "A6. Due to nullity of marriage": { recommended: ["Counseling", "Legal Aid"], defaults: defaultBenefits },
        "A7. Abandoned": { recommended: ["Housing Assistance", "Counseling"], defaults: defaultBenefits },
        "B. Spouse of the OFW/Relative of the OFW": { recommended: ["Livelihood Program", "Scholarship"], defaults: defaultBenefits },
        "C. Unmarried mother/father who keeps and rears his/her child/children": { recommended: ["Childcare Support", "Scholarship"], defaults: defaultBenefits },
        "D. Legal guardian, adoptive or foster parents": { recommended: ["Childcare Support", "School Supplies"], defaults: defaultBenefits },
        "E. Any relative within the fourth (4th) civil degree": { recommended: ["Counseling", "Childcare Support"], defaults: defaultBenefits },
        "F. Pregnant woman who provides sole parental care and support to her unborn child or children": { recommended: ["Prenatal Care", "Counseling"], defaults: defaultBenefits }
    };
    
    // CHOOSE BENEFITS MODAL
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.choose-category-benefits-btn');
        if (!btn) return;
    
        const beneficiaryId = btn.dataset.id;
        const category = (btn.dataset.category || "").trim();
    
        const row = btn.closest('tr');
        const beneficiaryName = row.cells[1]?.textContent?.trim() || "-";
        const beneficiaryBarangay = row.cells[2]?.textContent?.trim() || "-";
    
        const modalEl = document.getElementById('chooseBenefitsModal');
        modalEl.dataset.beneficiaryId = beneficiaryId; // store id for save
    
        const modalBody = modalEl.querySelector('.modal-body');
        modalBody.innerHTML = "";

        // BENEFICIARY INFO BOX
        modalBody.innerHTML += `
            <div class="beneficiary-info-box">
                <div><strong>Name:</strong> ${beneficiaryName}</div>
                <div><strong>Barangay:</strong> ${beneficiaryBarangay}</div>
            </div>
        `;
    
        // Extract benefits based on category
        const benefits = categoryBenefits[category] || { recommended: [], defaults: [] };
        // DEFAULT BENEFITS
        if (benefits.defaults.length > 0) {
            modalBody.innerHTML += `
                <div class="benefit-section-title">Default Benefits</div>
            `;
    
            benefits.defaults.forEach(d => {
                modalBody.innerHTML += `
                    <div class="benefit-card">
                        <i class="fa-solid fa-shield-heart"></i>
                        <span>${d}</span>
                    </div>
                `;
            });
        }
    
        // RECOMMENDED BENEFITS (CHECKBOX)
        if (benefits.recommended.length > 0) {
            modalBody.innerHTML += `
                <div class="benefit-section-title mt-3">Recommended Benefits</div>
            `;
    
            benefits.recommended.forEach(r => {
                modalBody.innerHTML += `
                    <label class="benefit-check-row">
                        <input type="checkbox" value="${r}">
                        <i class="fa-solid fa-hand-holding-heart" style="margin-right: 8px;"></i>
    
                        ${r}
                    </label>
                `;
            });
        }
    
        // PRE-CHECK SAVED BENEFITS
        fetch(`/admin/beneficiary-benefits/${beneficiaryId}`)
            .then(res => res.json())
            .then(data => {
                const savedBenefits = data.selected_benefits || [];
                savedBenefits.forEach(b => {
                    const checkbox = modalBody.querySelector(`input[type="checkbox"][value="${b}"]`);
                    if (checkbox) checkbox.checked = true;
                });
            })
            .catch(err => console.error("Pre-check error:", err));
    
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    });

    // SAVE BENEFITS
    document.getElementById('saveBenefitsBtn')?.addEventListener('click', async function () {
        const modalEl = document.getElementById('chooseBenefitsModal');
        if (!modalEl) return;
    
        const modalBody = modalEl.querySelector('.modal-body');
        const beneficiaryId = modalEl.dataset.beneficiaryId;
        if (!beneficiaryId) return alert("Beneficiary ID missing!");
    
        const selectedRecommended = Array.from(
            modalBody.querySelectorAll('input[type="checkbox"]:checked')
        ).map(c => c.value);
    
        const defaults = Array.from(
            modalBody.querySelectorAll('.benefit-card span')
        ).map(s => s.textContent);
    
        const allBenefits = [...defaults, ...selectedRecommended];
    
        this.disabled = true;
        const originalText = this.textContent;
        this.textContent = 'Saving...';
    
        try {
            const res = await fetch("{{ route('admin.beneficiaries.save-benefits') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    beneficiary_id: beneficiaryId,
                    benefits: allBenefits
                })
            });
    
            const data = await res.json();
    
            if (data.success) {
                // hide modal
                bootstrap.Modal.getInstance(modalEl)?.hide();
    
                // update button instantly
                const rowBtn = document.querySelector(`.choose-category-benefits-btn[data-id='${beneficiaryId}']`);
                if (rowBtn) {
                    rowBtn.textContent = 'Benefits Set';
                    rowBtn.classList.remove('btn-warning');
                    rowBtn.classList.add('btn-secondary');
                    rowBtn.disabled = true;
                }
    
                // optional: show global success
                const globalMsgEl = document.getElementById('globalSuccessMessage');
                if (globalMsgEl) globalMsgEl.textContent = 'Benefits saved successfully!';
                const successModalEl = document.getElementById('GlobalSuccessModal');
                if (successModalEl) new bootstrap.Modal(successModalEl).show();
            } else {
                alert('Error: ' + (data.message || "Save failed"));
            }
    
        } catch (err) {
            console.error(err);
            alert('Something went wrong!');
        } finally {
            this.disabled = false;
            this.textContent = originalText;
        }
    });

    loadBeneficiaries();

    function refreshMap() {
        let totalSolo = 0;
        let maxBarangay = { name: '', count: 0 };
    
        Object.keys(barangayMarkers).forEach(name => {
            const marker = barangayMarkers[name];
            const count = barangayCounts[name] || 0;
    
            // Update tooltip & popup
            marker.bindTooltip(`${name}<br>Solo Parents: ${count}`, { direction: "top" });
            marker.bindPopup(`<b>${name}</b><br>Solo Parents: ${count}`);
    
            // Remove existing heat circle
            if (marker.heatCircle) map.removeLayer(marker.heatCircle);
    
            // Add new heat circle if visible
            if (count > 0 && heatVisible) {
                const f = barangayData.features.find(f => f.properties.name === name);
                if (f) {
                    const lat = f.geometry.coordinates[1];
                    const lng = f.geometry.coordinates[0];
    
                    // radius proportional to solo parents
                    const radius = 50 + count * 5;
    
                    const heatCircle = L.circle([lat, lng], {
                        radius: radius,
                        color: getColor(count), // match legend color
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
    
        // Update stats
        document.getElementById('beneficiaryTotalValue').textContent = totalSolo;
        document.getElementById('beneficiaryRegisteredBarangays').textContent = Object.keys(barangayCounts).length;
        document.getElementById('beneficiaryHighestDensity').textContent =
            maxBarangay.count > 0 ? `${maxBarangay.name} (${maxBarangay.count})` : 'None';
    }

    // Toggle heat visibility
    document.getElementById("toggleBeneficiaryHeat")?.addEventListener("click", () => {
heatVisible = !heatVisible;
refreshMap(); // re-render heat circles

const btn = document.getElementById("toggleBeneficiaryHeat");
btn.textContent = heatVisible ? "🔥 Hide Heatmap" : "🔥 Show Heatmap";
});


    document.getElementById("toggleBeneficiaryMarkers")?.addEventListener("click", () => {
        markersVisible = !markersVisible;
        if (markersVisible) { map.addLayer(markersLayer); this.textContent = "🔘 Hide Markers"; }
        else { map.removeLayer(markersLayer); this.textContent = "🔘 Show Markers"; }
    });

    function addMapLegend() {
        const legend = L.control({ position: 'bottomright' });

        legend.onAdd = function(map) {
            const div = L.DomUtil.create('div', 'info legend');
            const grades = [0, 20, 50, 80, 100, 120, 140];
            const labels = [];

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

    const searchInput = document.getElementById('beneficiarySearch');
    const dateInput = document.getElementById('beneficiaryFilterDate');
    const brgySelect = document.getElementById('beneficiaryFilterBrgy');
    const resetBtn = document.getElementById('beneficiaryReset');

    function filterTable() {
        const search = searchInput.value.toLowerCase();
        const date = dateInput.value;
        const brgy = brgySelect.value;
        Array.from(beneficiaryTbody.querySelectorAll('tr')).forEach(row => {
            const name = row.cells[0].textContent.toLowerCase();
            const barangay = row.cells[1].textContent;
            const rowDate = row.cells[2].textContent;
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
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const barangayFilter = document.getElementById('barangayFilter');
    const statusFilter = document.getElementById('statusFilter');
    const tableRows = document.querySelectorAll('#solo-parent-table tbody tr');

    function filterTable() {
        const search = searchInput.value.toLowerCase();
        const barangay = barangayFilter.value.toLowerCase();
        const status = statusFilter.value.toLowerCase();

        tableRows.forEach(row => {
            const name = row.children[1].textContent.toLowerCase();
            const rowBarangay = row.children[2].textContent.toLowerCase();
            const rowStatus = row.children[5].textContent.toLowerCase();

            const matchesSearch = name.includes(search);
            const matchesBarangay = (barangay === 'all') || rowBarangay.includes(barangay);
            const matchesStatus = (status === 'all') || rowStatus.includes(status);

            row.style.display = (matchesSearch && matchesBarangay && matchesStatus) ? '' : 'none';
        });
    }

    searchInput.addEventListener('keyup', filterTable);
    barangayFilter.addEventListener('change', filterTable);
    statusFilter.addEventListener('change', filterTable);
});
</script>

<!-- BENEFITS SECTION -->
<div id="benefits-section" class="content-section" style="display:none;">
    <div class="admin-container d-flex flex-column">
        <div class="mb-3">
            <h2 class="fw-bold mb-1"><i class="fas fa-gift me-3"></i> Benefit Module </h2>
            <small class="text-muted">Barangay Tejero payout scheduling and beneficiary monitoring</small>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card stat-card stat-blue shadow-sm border-0 rounded-4 p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle-logo bg-primary me-3"><i class="fas fa-users fa-lg text-white"></i></div>
                        <div>
                            <h5 class="mb-0 fw-bold" id="total-beneficiaries">2</h5>
                            <small class="text-muted">Total Beneficiaries</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card stat-card stat-yellow shadow-sm border-0 rounded-4 p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle-logo bg-warning me-3"><i class="fas fa-hourglass-half fa-lg text-white"></i></div>
                        <div>
                            <h5 class="mb-0 fw-bold text-warning" id="pending-payouts">0</h5>
                            <small class="text-muted">Pending Payout</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card stat-card stat-green shadow-sm border-0 rounded-4 p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle-logo bg-success me-3"><i class="fas fa-check-circle fa-lg text-white"></i></div>
                        <div>
                            <h5 class="mb-0 fw-bold text-success" id="completed-payouts">0</h5>
                            <small class="text-muted">Completed</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card stat-card stat-red shadow-sm border-0 rounded-4 p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle-logo bg-danger me-3"><i class="fas fa-calendar-day fa-lg text-white"></i></div>
                        <div>
                            <h5 class="mb-0 fw-bold text-danger" id="today-schedules">0</h5>
                            <small class="text-muted">Today’s Schedule</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-3">
            <!-- LEFT PANEL -->
            <div class="col-left">
                <h4 class="fw-bold mb-1">️ Barangay Tejero</h4>
                <small class="opacity-75">Payout & Beneficiary Control</small>
            
                <hr class="text-white">
                <div class="mb-3">
                    <h6>Total Beneficiaries</h6>
                    <h2 class="fw-bold" id="tejero-total">  </h2>
                </div>
        
                <button class="btn btn-light w-100 fw-semibold rounded-pill mb-2" id="openScheduleModalBtn">
                    <i class="fas fa-calendar-plus me-2"></i> Schedule Payout
                </button>
                <button class="btn btn-outline-light w-100 fw-semibold rounded-pill" id="viewBeneficiariesBtn">
                    <i class="fas fa-users me-2"></i> View Beneficiaries
                </button>
            </div>
            <!-- RIGHT PANEL -->
            <div class="col-right flex-fill">
                <div id="benefitScheduleCard" class="mb-4">
                    <h4 class="fw-bold mb-3"><i class="fas fa-hand-holding-usd me-2"></i>Payout Schedule – Barangay Tejero</h4>
                
                    <div id="benefitScheduleCardContainer" class="row g-3">
                        <!-- Dynamic schedule cards will be injected here -->
                    </div>
                </div>
        
                <!-- BENEFICIARIES PANEL -->
                <div id="barangay-beneficiaries-panel" style="display:none;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <button class="btn btn-sm btn-secondary" id="back-to-schedule">← Back to Schedule</button>
                    </div>
                  
                    <h4 class="fw-bold mb-3"><i class="fas fa-hand-holding-usd me-2"></i>Solo Parent Beneficiaries – Barangay Tejero</h4>
        
                    <!-- Toolbar -->
                    <div class="d-flex gap-2 mb-3" id="toolbar">
                        <div style="flex:1; max-width:320px;">
                            <label class="form-label mb-1">Search by Name</label>
                            <input id="global-search" class="form-control form-control-sm" placeholder="Search beneficiaries by name">
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
            
                    <nav aria-label="Beneficiaries Pagination" style="position: sticky; bottom: 0; background: white; padding-top: 5px;"><ul class="pagination" id="beneficiary-pagination"></ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>
    

<script>
document.addEventListener('DOMContentLoaded', () => {

document.getElementById('viewBeneficiariesBtn')?.addEventListener('click', () => {
    document.getElementById('schedule-panel').style.display = 'none';
    document.getElementById('barangay-beneficiaries-panel').style.display = 'block';
    loadTejeroBeneficiaries(); // fetch data from solo_parent_beneficiaries
});


document.getElementById('back-to-schedule')?.addEventListener('click', () => {
    document.getElementById('barangay-beneficiaries-panel').style.display = 'none';
    document.getElementById('schedule-panel').style.display = 'block';
});

});

async function loadTejeroBeneficiaries() {
    try {
        const res = await fetch("/admin/tejero-beneficiaries-json"); // JSON route
        if (!res.ok) throw new Error("Network response was not ok");

        const data = await res.json();
        const tbody = document.getElementById('beneficiaries-list');
        tbody.innerHTML = '';

        if (!data.length) {
            tbody.innerHTML = `<tr><td colspan="6" class="text-center text-muted">No beneficiaries found</td></tr>`;
            return;
        }

        data.forEach(b => {
            let benefits = [];
            if (b.selected_benefits) {
                benefits = Array.isArray(b.selected_benefits)
                    ? b.selected_benefits
                    : JSON.parse(b.selected_benefits); // parse if JSON string
            }

            tbody.insertAdjacentHTML('beforeend', `
                <tr>
                    <td>${b.first_name || '-'} ${b.last_name || '-'}</td>
                    <td>${b.street || '-'}, ${b.municipality || '-'}</td>
                    <td>${benefits.length ? benefits.join(', ') : '-'}</td>
                    <td>${b.category || '-'}</td>
                    <td>
                        <span class="badge ${b.assistance_status === 'Approved' ? 'bg-success' : 'bg-warning'}">
                            ${b.assistance_status || 'Pending'}
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-primary view-details-btn" data-id="123"> View </button>

                    </td>
                </tr>
            `);
        });

    } catch (error) {
        console.error('Failed to load Tejero beneficiaries:', error);
        const tbody = document.getElementById('beneficiaries-list');
        tbody.innerHTML = `<tr><td colspan="6" class="text-center text-danger">Failed to load beneficiaries. Check console.</td></tr>`;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // Listen for click on any "View" button in the beneficiaries table
    document.getElementById('beneficiaries-list').addEventListener('click', (e) => {
        if (e.target.classList.contains('view-details-btn')) {
            // Show the modal
            const modal = new bootstrap.Modal(document.getElementById('historyModal'));
            modal.show();
        }
    });
});

</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<script>
fetch('/admin/tejero-schedule')
.then(res => res.json())
.then(data => {
    const container = document.getElementById('benefitScheduleCardContainer');
    container.innerHTML = '';

    if (!data || data.length === 0) {
        container.innerHTML = `<p class="text-center text-muted">No schedules found</p>`;
        return;
    }

    const schedules = Array.isArray(data) ? data : [data];

    schedules.forEach(item => {
        const isScheduled = item.scheduled_date && item.scheduled_time;

        const card = document.createElement('div');
        card.className = 'col-12 col-md-8 col-lg-12';

        card.innerHTML = `
            <div class="schedule-card">
                <div class="card-header">
                    <i class="fas fa-map-marker-alt me-1 text-primary"></i>
                    ${item.barangay ?? 'Tejero'}
                </div>
                <div class="card-body">
                    <p><strong>Beneficiaries:</strong> ${item.total_beneficiaries ?? 0}</p>
                    <p><strong>Schedule:</strong> ${
                        isScheduled 
                            ? `${item.scheduled_date} ${item.scheduled_time}` 
                            : `<span class="text-muted">Not scheduled</span>`
                    }</p>
                    <p><strong>Location:</strong> ${
                        item.location ? item.location : '<span class="text-muted">Not set</span>'
                    }</p>
                    <p><strong>Received:</strong> ${item.received ?? 0}</p>
                </div>
                <div class="card-actions">
                    <button class="btn btn-sm btn-primary" ${
                        isScheduled ? 'disabled style="opacity:0.6;cursor:not-allowed;"' : `onclick='openScheduleModal(${JSON.stringify(item)})'`
                    }>
                        <i class="fas fa-calendar-plus me-1"></i> Schedule
                    </button>
                    <button class="btn btn-sm btn-warning" onclick='editSchedule(${JSON.stringify(item)})'>
                        <i class="fas fa-edit me-1"></i> Edit
                    </button>
                    <button class="btn btn-sm btn-success" onclick="generateQr('${item.barangay ?? 'Tejero'}', '${item.scheduled_date ?? ''}')">
                        <i class="fas fa-qrcode me-1"></i> QR
                    </button>
                </div>
            </div>
        `;

        container.appendChild(card);
    });

})
.catch(err => {
    console.error('Error fetching schedule:', err);
    document.getElementById('benefitScheduleCardContainer').innerHTML = 
        `<p class="text-center text-danger">Failed to load schedules</p>`;
});

// OPEN PAYOUT MODAL
function openScheduleModal(data) {
    // store selected id (barangay / payout id)
    document.getElementById('payout-barangay').value = data.id ?? '';
    // reset form
    document.getElementById('payoutScheduleForm').reset();
    // reset preview
    document.getElementById('preview-date').textContent = data.scheduled_date ?? '-';
    document.getElementById('preview-time').textContent = data.scheduled_time ?? '-';
    document.getElementById('preview-location').textContent = data.location ?? '-';
    document.getElementById('preview-notify').textContent = '-';
    // remove active states
    document.querySelectorAll('.time-card').forEach(c => c.classList.remove('active'));
    document.querySelectorAll('.notify-card').forEach(c => c.classList.remove('active'));
    
    // show modal
    const modal = new bootstrap.Modal(
        document.getElementById('payoutScheduleModal')
    );
    modal.show();
}
    
// EDIT PAYOUT MODAL
function editSchedule(data) {
    // Fill modal fields
    document.getElementById('edit-payout-barangay').value = data.barangay ?? 'Tejero';
    document.getElementById('edit-payout-date').value = data.scheduled_date ?? '';
    document.getElementById('edit-payout-time').value = data.scheduled_time ?? '';
    document.getElementById('edit-payout-location').value = data.location ?? '';

    // Show modal
    const modal = new bootstrap.Modal(
        document.getElementById('editPayoutModal')
    );
    modal.show();
}
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {

    let currentQrData = {}; // store current QR info

    // Generate QR
    window.generateQr = function(barangay, scheduledDate) {
        currentQrData = { barangay, scheduledDate };

        document.getElementById('qr-barangay-name').textContent = barangay;

        const qrContainer = document.getElementById('qr-code-display');
        qrContainer.innerHTML = '';

        new QRCode(qrContainer, {
            text: JSON.stringify({ barangay, scheduledDate }),
            width: 250,
            height: 250,
            colorDark: "#000000",
            colorLight: "#ffffff"
        });

        new bootstrap.Modal(document.getElementById('qrCodeModal')).show();
    };

    // Print / Download PDF
    const printBtn = document.getElementById('printQrBtn');
    if (printBtn) {
        printBtn.addEventListener('click', () => {
            const qrContainer = document.getElementById('qr-code-display');
            const qrImg = qrContainer.querySelector('img, canvas');
            if (!qrImg) return alert('QR code not generated!');

            // Get QR as data URL
            let qrDataURL = '';
            if (qrImg.tagName === 'IMG') {
                qrDataURL = qrImg.src;
            } else if (qrImg.tagName === 'CANVAS') {
                qrDataURL = qrImg.toDataURL('image/png');
            }

            const barangay = currentQrData.barangay || 'Tejero';
            const scheduledDate = currentQrData.scheduledDate || '-';

            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('p', 'mm', 'a4');

            // HEADER
            doc.setFont('times', 'bold');
            doc.setFontSize(14);
            doc.text('Republic of the Philippines', 105, 20, { align: 'center' });
            doc.setFontSize(13);
            doc.text(`City of General Trias, Cavite`, 105, 28, { align: 'center' });
            doc.setFontSize(12);
            doc.text('City Social Welfare and Development Office', 105, 36, { align: 'center' });

            // Title
            doc.setFontSize(12);
            doc.setFont('times', 'normal');
            doc.text('Official QR Code for Beneficiary Payout', 105, 50, { align: 'center' });

            // Barangay and date
            doc.setFontSize(11);
            doc.text(`Barangay: ${barangay}`, 105, 60, { align: 'center' });
            doc.text(`Scheduled Date: ${scheduledDate}`, 105, 68, { align: 'center' });

            // QR code image
            doc.addImage(qrDataURL, 'PNG', 80, 80, 50, 50);

            // Instructions
            doc.setFontSize(10);
            doc.text('Scan this QR code to mark the benefit as received in the system.', 105, 140, { align: 'center' });

            // Footer / signature
            doc.setFontSize(11);
            doc.text('Prepared By:', 20, 180);
            doc.text('______________________________', 20, 200);
            doc.text('CSWDO Staff / Administrator', 20, 208);

            // Download PDF
            doc.save(`QR_Barangay_${barangay}.pdf`);
        });
    }

});
</script>

<script>
document.addEventListener('DOMContentLoaded', () = >{
    const tejeroTotalEl = document.getElementById('tejero-total');

    // Fetch total beneficiaries for Tejero
    fetch('/admin/tejero-total').then(res = >res.json()).then(data = >{
        tejeroTotalEl.textContent = data.total ? ?0;
    }).
    catch(err = >{
        console.error('Error fetching Tejero total:', err);
        tejeroTotalEl.textContent = 0;]
    });
});
</script>

<!-- ANNOUNCEMENT SECTION -->
<div id="announcement-section" class="content-section" style="display:none;">
    <div class="admin-container">
        <div class="announcement-header-bar d-flex justify-content-between align-items-center mb-3">
            <h2 class="fw-bold mb-3"><i class="fas fa-bullhorn me-3"></i> Announcements</h2>

            <div class="announcement-filters d-flex gap-2">
                <select id="announcementFilter" class="filter-select form-select form-select-sm">
                    <option value="all">All</option>
                    <option value="success">Success</option>
                    <option value="pending">Pending</option>
                    <option value="failed">Failed</option>
                </select>
                <input type="text" class="filter-search form-control form-control-sm" placeholder="Search announcements...">
            </div>
        </div>

        <div class="announcement-list-container" style="max-height: 450px; overflow-y: auto;">
            <div class="announcement-list d-flex flex-column gap-2" id="announcementList">
                <div class="no-announcements text-center text-muted" style="display:none;"><p>No announcements at the moment.</p></div>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {

    const announcementList = document.getElementById('announcementList');
    const filterSelect = document.getElementById('announcementFilter');
    const searchInput = document.querySelector('.filter-search');
    const noAnnouncements = document.querySelector('.no-announcements');

    async function loadAnnouncements() {
        try {
            const response = await fetch('/api/announcements');
            const announcements = await response.json();

            announcementList.querySelectorAll('.announcement-card').forEach(el => el.remove());

            if (!announcements.length) {
                noAnnouncements.style.display = 'block';
                return;
            }

            noAnnouncements.style.display = 'none';

            announcements.forEach(ann => {
                const card = document.createElement('div');
                card.classList.add('announcement-card', 'd-flex', 'justify-content-between', 'align-items-start', 'p-2', 'border', 'rounded');
                card.setAttribute('data-status', ann.status);

                card.innerHTML = `
                    <div class="announcement-content flex-grow-1 me-2">
                        <div class="announcement-header d-flex justify-content-between align-items-center mb-1">
                            <strong>${ann.title}</strong>
                            <span class="status badge ${ann.status.toLowerCase()}">${ann.status.toUpperCase()}</span>
                        </div>
                        <p class="mb-1">${ann.content ?? ""}</p>
                        <small class="text-muted">${timeAgo(ann.created_at)} • ${ann.category ?? "General"} • Auto-generated</small>
                    </div>

                    <div class="announcement-actions d-flex flex-column gap-1">
                        <button class="btn btn-sm btn-outline-success mark-read">Mark as Read</button>
                        <button class="btn btn-sm btn-outline-danger close-btn">✕</button>
                    </div>
                `;

                announcementList.appendChild(card);
            });

            applyFilters();

        } catch(error) {
            console.error('Error fetching announcements:', error);
        }
    }

    setInterval(loadAnnouncements, 30000);
    loadAnnouncements();

    function applyFilters() {
        const filter = filterSelect.value;
        const search = searchInput.value.toLowerCase();

        const cards = document.querySelectorAll('.announcement-card');
        let visibleCount = 0;

        cards.forEach(card => {
            const status = card.getAttribute('data-status');
            const text = card.innerText.toLowerCase();
            const show = (filter === 'all' || status === filter) && text.includes(search);
            card.style.display = show ? 'flex' : 'none';
            if (show) visibleCount++;
        });

        noAnnouncements.style.display = visibleCount === 0 ? 'block' : 'none';
    }

    filterSelect.addEventListener('change', applyFilters);
    searchInput.addEventListener('input', applyFilters);

    document.addEventListener('click', e => {
        if (e.target.classList.contains('mark-read')) {
            e.target.closest('.announcement-card').classList.add('read');
            e.target.remove();
            applyFilters();
        }

        if (e.target.classList.contains('close-btn')) {
            e.target.closest('.announcement-card').remove();
            applyFilters();
        }
    });

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

<!-- REPORTS SECTION -->
<div id="report-section" class="content-section" style="display:none;">
    <div class="admin-container">

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h2 class="fw-bold mb-3"><i class="fas fa-chart-line me-3"></i>Reports & Analytics</h2>
                <p class="text-muted mb-0">Structured insights for decision-making and program monitoring</p>
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

        <!-- SUMMARY -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card stat-card stat-blue shadow-sm border-0 rounded-4 p-3">
                    <div class="d-flex align-items-center justify-content-center">
                        <div class="icon-circle-big-logo bg-primary me-4"><i class="fas fa-file-alt fa-lg text-white"></i></div>
                        <div>
                            <h5 class="mb-0 fw-bold" id="totalApplications">35</h5>
                            <small class="text-muted">Total Applications</small>
                            <p class="small text-success mt-1" id="totalApplicationsPercent">▲ 5% this month</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card stat-card stat-green shadow-sm border-0 rounded-4 p-3">
                    <div class="d-flex align-items-center justify-content-center">
                        <div class="icon-circle-big-logo bg-success me-4"><i class="fas fa-check-circle fa-lg text-white"></i></div>
                        <div>
                            <h5 class="mb-0 fw-bold" id="approvedApplications">25</h5>
                            <small class="text-muted">Approved</small>
                            <p class="small text-success mt-1" id="approvedApplicationsPercent">▲ 8% this month</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card stat-card stat-yellow shadow-sm border-0 rounded-4 p-3">
                    <div class="d-flex align-items-center justify-content-center">
                        <div class="icon-circle-big-logo bg-warning me-4"><i class="fas fa-hourglass-half fa-lg text-white"></i></div>
                        <div>
                            <h5 class="mb-0 fw-bold" id="pendingApplications">10</h5>
                            <small class="text-muted">Pending</small>
                            <p class="small text-danger mt-1" id="pendingApplicationsPercent">▼ 3% this month</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card stat-card stat-red shadow-sm border-0 rounded-4 p-3">
                    <div class="d-flex align-items-center justify-content-center">
                        <div class="icon-circle-big-logo bg-danger me-4"><i class="fas fa-home fa-lg text-white"></i></div>
                        <div>
                            <h5 class="mb-0 fw-bold" id="homeVisits">5</h5>
                            <small class="text-muted">Home Visits</small>
                            <p class="small text-info mt-1" id="homeVisitsPercent">▲ 2% this week</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- DEMOGRAPHICS GROUP -->
        <div class="card shadow-sm mb-4 report-group">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0"><i class="fas fa-users me-2"></i>Solo Parent Demographics</h6>
                    <small class="text-muted">Population breakdown</small>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-danger btn-sm export-group" data-type="pdf"><i class="fas fa-file-pdf"></i></button>
                    <button class="btn btn-outline-success btn-sm export-group" data-type="csv"><i class="fas fa-file-csv"></i></button>
                    <button class="btn btn-outline-secondary btn-sm toggle-group"><i class="fas fa-chevron-up"></i></button>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <!-- Age -->
                    <div class="col-md-3">
                        <div class="card shadow-sm h-100 chart-card">
                            <div class="chart-loading-overlay">Loading...</div>
                                <div class="card-header py-2 d-flex justify-content-between">
                                    <h6 class="m-0 small">Age Groups</h6>
                                    <button class="btn btn-sm btn-light fullscreen-btn"><i class="fas fa-expand"></i></button>
                                </div>
                                <div class="card-body"><canvas id="chartAge"></canvas></div>
                            </div>
                        </div>
                        <!-- Gender -->
                        <div class="col-md-3">
                            <div class="card shadow-sm h-100 chart-card">
                                <div class="chart-loading-overlay">Loading...</div>
                                    <div class="card-header py-2 d-flex justify-content-between">
                                        <h6 class="m-0 small">Gender</h6>
                                        <button class="btn btn-sm btn-light fullscreen-btn"><i class="fas fa-expand"></i></button>
                                    </div>
                                <div class="card-body"><canvas id="chartGender"></canvas></div>
                            </div>
                        </div>
                        <!-- Employment -->
                        <div class="col-md-3">
                            <div class="card shadow-sm h-100 chart-card">
                                <div class="chart-loading-overlay">Loading...</div>
                                    <div class="card-header py-2 d-flex justify-content-between">
                                        <h6 class="m-0 small">Employment</h6>
                                        <button class="btn btn-sm btn-light fullscreen-btn"><i class="fas fa-expand"></i></button>
                                    </div>
                                <div class="card-body"><canvas id="chartEmployment"></canvas></div>
                            </div>
                        </div>
                        <!-- Total Solo Parents -->
                        <div class="col-md-3">
                            <div class="card shadow-sm h-100 chart-card">
                                <div class="chart-loading-overlay">Loading...</div>
                                    <div class="card-header py-2 d-flex justify-content-between">
                                        <h6 class="m-0 small">Total Solo Parents</h6>
                                        <button class="btn btn-sm btn-light fullscreen-btn"><i class="fas fa-expand"></i></button>
                                    </div>
                                <div class="card-body"><canvas id="chartSoloParent"></canvas></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <!-- SOCIO-ECONOMIC ANALYSIS -->
        <div class="card shadow-sm mb-4 report-group">
            <div class="card-header bg-light d-flex justify-content-between">
                <div>
                    <h6 class="mb-0"><i class="fas fa-hand-holding-heart me-2"></i>Socio-Economic Risk Assessment</h6>
                    <small class="text-muted">Identify poorest & priority households</small>
                </div>
                <button class="btn btn-outline-secondary btn-sm toggle-group">\<i class="fas fa-chevron-up"></i></button>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card shadow-sm chart-card">
                            <div class="chart-loading-overlay">Loading...</div>
                            <div class="card-header py-2"><h6 class="m-0 small">Poverty Level Distribution</h6></div>
                            <div class="card-body"><canvas id="povertyChart"></canvas></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card shadow-sm chart-card">
                            <div class="chart-loading-overlay">Loading...</div>
                            <div class="card-header py-2"><h6 class="m-0 small">Primary Assistance Needed</h6></div>
                            <div class="card-body"><canvas id="assistanceChart"></canvas></div>
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
                    <small class="text-muted">Trends & approvals</small>
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
                    <small class="text-muted">Distribution overview</small>
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
        <!-- ACTIVITY LOG -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light d-flex justify-content-between">
                <h6 class="mb-0"><i class="fas fa-history me-2"></i>Recent Activities</h6>
                <button class="btn btn-sm btn-dark"><i class="fas fa-download"></i> CSV </button>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                        <th>Date</th>
                        <th>Activity</th>
                        <th>User</th>
                        <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                        <td>Nov 30, 2025</td>
                        <td>Approved Application</td>
                        <td>Admin</td>
                        <td><span class="badge bg-success">Completed</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- EXPORT -->
        <div class="card shadow-sm mb-5">
            <div class="card-header bg-light"><h6 class="mb-0"><i class="fas fa-file-export me-2"></i>Export Reports</h6></div>
            <div class="card-body d-flex gap-2 flex-wrap">
                <button class="btn btn-danger"><i class="fas fa-file-pdf"></i> PDF</button>
                <button class="btn btn-success"><i class="fas fa-file-excel"></i> Excel</button>
                <button class="btn btn-dark"><i class="fas fa-file-csv"></i> CSV</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded",()=>{

    const calcPercent=(current,lastWeek)=> lastWeek===0 ? 0 : ((current-lastWeek)/lastWeek*100).toFixed(1);
    let fullscreenChartInstance = null;
    
    // COLLAPSE / EXPAND
    document.querySelectorAll(".toggle-group").forEach(btn=>{
        btn.onclick=()=>{
        const body=btn.closest(".report-group").querySelector(".card-body");
        body.classList.toggle("d-none");
        btn.innerHTML=body.classList.contains("d-none")
            ?'<i class="fas fa-chevron-down"></i>'
            :'<i class="fas fa-chevron-up"></i>';
        };
    });

    // FULLSCREEN CHART
    document.querySelectorAll(".fullscreen-btn").forEach(btn=>{
        btn.onclick=()=>{
            const card=btn.closest(".chart-card");
            const chartCanvas=card.querySelector("canvas");
            const title=card.querySelector("h6").innerText;
            document.getElementById("fullscreenTitle").innerText=title;
    
            if(fullscreenChartInstance){
                fullscreenChartInstance.destroy();
                fullscreenChartInstance = null;
            }
    
            const originalChart = Chart.getChart(chartCanvas);
            if(!originalChart) return;
    
            const ctx=document.getElementById("fullscreenChart").getContext("2d");
            fullscreenChartInstance = new Chart(ctx,{
                type: originalChart.config.type,
                data: JSON.parse(JSON.stringify(originalChart.config.data)),
                options: JSON.parse(JSON.stringify(originalChart.config.options))
            });
    
            const modal=new bootstrap.Modal(document.getElementById("chartFullscreenModal"));
            modal.show();
        };
    });

    // EXPORT PLACEHOLDER
    document.querySelectorAll(".export-group").forEach(btn=>{
        btn.onclick=()=>alert(`Export ${btn.dataset.type.toUpperCase()} (hook to backend)`);
    });

    const staticCharts=[
        {id:'chartAge', type:'bar', labels:['18-25','26-35','36+'], data:[300,500,200], color:'#6f42c1'},
        {id:'chartGender', type:'doughnut', labels:['Male','Female'], data:[800,300], color:['#36a2eb','#ff6384']},
        {id:'chartEmployment', type:'pie', labels:['Employed','Unemployed'], data:[600,500], color:['#28a745','#ffc107']},
        {id:'chartSoloParent', type:'doughnut', labels:['Solo Parent'], data:[1000], color:['#ff9f40']}
    ];

    staticCharts.forEach(chart=>{
        const ctx=document.getElementById(chart.id)?.getContext('2d');
        if(!ctx) return;
        const card=document.getElementById(chart.id).closest('.chart-card');
        const overlay=card.querySelector('.chart-loading-overlay');
        overlay.style.display='flex';

        new Chart(ctx,{
            type:chart.type==='line' ? 'line' : chart.type,
            data:{
                labels:chart.labels,
                datasets:[{
                    label:'Data',
                    data:chart.data,
                    backgroundColor:chart.color,
                    borderColor:chart.color,
                    fill:chart.type==='line'
                }]
            },
            options:{responsive:true}
        });
        overlay.style.display='none';
    });

    // SOCIO-ECONOMIC STATIC CHARTS 
    const socioCharts = [
        {
            id: 'povertyChart',
            type: 'bar',
            labels: ['Not Poor','Poor','Poorest of the Poor'],
            data: [42, 31, 17],
            colors: ['#198754','#ffc107','#dc3545']
        },
        {
            id: 'assistanceChart',
            type: 'pie',
            labels: ['Financial','Food','Medical','Education','Housing'],
            data: [28,34,16,12,10],
            colors: ['#0d6efd','#fd7e14','#dc3545','#6f42c1','#20c997']
        }
    ];

    socioCharts.forEach(chart=>{
        const ctx=document.getElementById(chart.id)?.getContext('2d');
        if(!ctx) return;
        const card=document.getElementById(chart.id).closest('.chart-card');
        const overlay=card.querySelector('.chart-loading-overlay');
        overlay.style.display='flex';

        new Chart(ctx,{
            type: chart.type,
            data:{
                labels: chart.labels,
                datasets:[{
                    data: chart.data,
                backgroundColor: chart.colors
            }]
        },
        options:{responsive:true, plugins:{legend:{position:'bottom'}}}
    });

    overlay.style.display='none';
  });

  // DYNAMIC SOLO PARENT CHARTS
  fetch("{{ route('admin.reports.solo-parent-distribution') }}")
    .then(res=>res.json())
    .then(data=>{
        const renderBarChart=(canvasId,dataset,labels,lastWeekData,barColor)=>{
            const ctx=document.getElementById(canvasId).getContext('2d');
            const card=document.getElementById(canvasId).closest('.chart-card');
            const overlay=card.querySelector('.chart-loading-overlay');
            overlay.style.display='flex';
    
            const topValue=Math.max(...dataset);
            const totalPercent = calcPercent(dataset.reduce((a,b)=>a+b,0), lastWeekData.reduce((a,b)=>a+b,0));
            const badge = document.createElement('span');
            badge.className = 'badge mb-2';
            badge.style.backgroundColor = '#0d6efd';
            badge.style.color = '#fff';
            badge.style.fontWeight = 'bold';
            badge.innerText = totalPercent >= 0 ? `▲ ${totalPercent}% this week` : `▼ ${Math.abs(totalPercent)}% this week`;
            card.prepend(badge);

            new Chart(ctx,{
                type:'bar',
                data:{labels:labels,datasets:[{label:'Total Solo Parents',data:dataset,backgroundColor:dataset.map(v=>v===topValue?'#fd7e14':barColor),borderRadius:6}]},
                options:{
                    indexAxis:'y',
                    responsive:true,
                    plugins:{
                        legend:{display:false},
                        tooltip:{callbacks:{label:(ctx)=>`${ctx.raw} (${calcPercent(ctx.raw,lastWeekData[ctx.dataIndex]||ctx.raw)}%)`}}
                        },
                    scales:{x:{beginAtZero:true}}
                    }
                });
            overlay.style.display='none';
        }

        if(document.getElementById('chartCategory')){
            renderBarChart('chartCategory',data.categories.data,data.categories.labels,data.categories.last_week || data.categories.data.map(()=>0),'#0d6efd');
        }
        if(document.getElementById('barangayChart')){
            renderBarChart('barangayChart',data.barangays.data,data.barangays.labels,data.barangays.last_week || data.barangays.data.map(()=>0),'#0d6efd');
        }
    });

    // DYNAMIC MONTHLY PERFORMANCE WITH BADGE 
    const yearSelect = document.querySelector('#report-section select.form-select:nth-child(1)');
    const monthSelect = document.querySelector('#report-section select.form-select:nth-child(2)');
    const applyBtn = document.querySelector('#report-section button.btn-dark');

    const loadMonthlyPerformance = () => {
    const year = yearSelect.value;
    const month = monthSelect.value;

    fetch(`/admin/reports/monthly-performance?year=${year}&month=${month}`)
        .then(res => res.json())
        .then(data => {
        const { months, totals, approval } = data;

        // Applications Chart
        const ctxApp = document.getElementById('applicationsChart').getContext('2d');
        const cardApp = document.getElementById('applicationsChart').closest('.chart-card');
        const overlayApp = cardApp.querySelector('.chart-loading-overlay');
        overlayApp.style.display='flex';

        const prevTotal = totals.reduce((a,b)=>a+b,0) - totals[totals.length-1];
        let badge = cardApp.querySelector('.badge');
        if(!badge){
            badge = document.createElement('span');
            badge.className = 'badge mb-2';
            badge.style.fontWeight = 'bold';
            cardApp.prepend(badge);
        }
        const percentChange = calcPercent(totals[totals.length-1], prevTotal);
        badge.style.backgroundColor = percentChange>=0 ? '#198754' : '#dc3545';
        badge.style.color = '#fff';
        badge.innerText = percentChange>=0 ? `▲ ${percentChange}% this month` : `▼ ${Math.abs(percentChange)}% this month`;

        new Chart(ctxApp,{
            type:'line',
            data:{labels:months,datasets:[{label:'Applications',data:totals,borderColor:'#0d6efd',backgroundColor:'rgba(13,110,253,0.2)',tension:0.4,fill:true}]},
            options:{
                responsive:true,
                plugins:{legend:{display:false},tooltip:{callbacks:{label:ctx=>`${ctx.parsed.y} applications`}}},
                scales:{y:{beginAtZero:true}}
            }
        });
        overlayApp.style.display='none';

        // Approval Pie Chart
        const ctxApproval = document.getElementById('approvalChart').getContext('2d');
        const cardApproval = document.getElementById('approvalChart').closest('.chart-card');
        const overlayApproval = cardApproval.querySelector('.chart-loading-overlay');
        overlayApproval.style.display='flex';

        const totalApproval = approval.approved + approval.pending + approval.rejected || 0;
        const approvalPercent = totalApproval === 0 ? 0 : ((approval.approved / totalApproval) * 100).toFixed(1);

        let approvalBadge = cardApproval.querySelector('.badge');
        if(!approvalBadge){
            approvalBadge = document.createElement('span');
            approvalBadge.className = 'badge mb-2';
            approvalBadge.style.fontWeight = 'bold';
            cardApproval.prepend(approvalBadge);
        }

        approvalBadge.style.backgroundColor = '#0d6efd';
        approvalBadge.style.color = '#fff';
        approvalBadge.innerText = `▲ ${approvalPercent}% approved this month`;

        new Chart(ctxApproval,{
            type:'pie',
            data:{
                labels:['Approved','Pending','Rejected'],
                datasets:[{data:[approval.approved,approval.pending,approval.rejected],backgroundColor:['#28a745','#ffc107','#dc3545']}]
                },
                options:{responsive:true}
            });

            overlayApproval.style.display='none';
        })
        .catch(err=>console.error('Monthly Performance error:', err));
    };

    // Initial load
    loadMonthlyPerformance();
    
    // Apply button
    applyBtn.addEventListener('click', loadMonthlyPerformance);

});
</script>

<input type="hidden" id="currentUserId" value="{{ auth()->id() }}">
<input type="hidden" id="adminId" value="{{ $admin->id }}">

<!-- CHAT SECTION -->
<div id="chat-section" class="content-section" style="display:none; position: relative;">
    <div class="admin-container">
        <h2 class="section-title fw-bold mb-3 mt-2"><i class="fas fa-comments me-3"></i>Support Chat Inbox</h2>
        <small style="color:#555;">View and respond to user messages in real time</small>

        <!-- USER LIST -->
        <div id="chat-users" class="chat-users">
            <div class="chat-search">
            <input type="text" id="userSearch" placeholder="Search users..." />
        </div>
            @foreach($users as $user)
                <div class="chat-user" data-user="{{ $user->id }}">
                    <img src="{{ asset('images/avatar.png') }}" alt="{{ $user->username }}">
                    <div class="chat-info">
                        <h4>{{ $user->username }}</h4>
                        <p>{{ $user->last_message ?? 'No messages yet' }}</p>
                        @if($user->last_message_time)
                            <small class="last-message-time">{{ $user->last_message_time }}</small>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- CONVERSATION -->
        <div id="chat-conversation" class="chat-conversation" style="display:none;">
            <div class="chat-header">
                <button id="backToUsers" class="back-btn">←</button>
                <h4 id="conversationWith"></h4>
            </div>

            <div class="conversation-inner" id="conversation-content"><p class="empty-text"></p></div>

            <div class="chat-input">
                <input type="text" id="chatInput" placeholder="Type your message..." disabled />
                <button id="sendChatBtn" disabled>➤</button>
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
const conversationBox = document.getElementById('chat-conversation');
const conversationContent = document.getElementById('conversation-content');
const conversationWith = document.getElementById('conversationWith');
const chatInput = document.getElementById('chatInput');
const sendBtn = document.getElementById('sendChatBtn');
const backBtn = document.getElementById('backToUsers');

// SELECT USER
document.querySelectorAll('.chat-user').forEach(el => {
    el.addEventListener('click', () => {
        selectedUserId = el.getAttribute('data-user');
        conversationWith.textContent = el.querySelector('h4').textContent;

        // Switch views
        usersList.style.display = 'none';
        conversationBox.style.display = 'flex';
        chatInput.disabled = false;
        sendBtn.disabled = false;
        chatInput.focus();

        fetchMessages();
    });
});

// BACK BUTTON
backBtn.addEventListener('click', () => {
    selectedUserId = null;
    conversationBox.style.display = 'none';
    usersList.style.display = 'block';
    chatInput.disabled = true;
    sendBtn.disabled = true;
    conversationContent.innerHTML = '';

});

// FETCH MESSAGES
function fetchMessages() {
    if (!selectedUserId) return;

    axios.get(`/admin/chat/messages/${selectedUserId}`)
        .then(res => {
            conversationContent.innerHTML = '';

            if (res.data.length === 0) {
                conversationContent.innerHTML = '<p class="empty-text">💬 No messages yet</p>';
            } else {
                res.data.forEach(msg => {
                    const div = document.createElement('div');
                    div.classList.add('message');
                    div.classList.add(msg.sender_id == adminId ? 'admin' : 'user');

                    // Chat bubble
                    const bubble = document.createElement('div');
                    bubble.classList.add('bubble');
                    bubble.textContent = msg.message;

                    // Timestamp
                    const time = document.createElement('small');
                    time.classList.add('message-time');
                    time.textContent = formatMessageTime(msg.created_at);

                    div.appendChild(bubble);
                    div.appendChild(time);
                    conversationContent.appendChild(div);
                });

                // Scroll to bottom
                conversationContent.scrollTop = conversationContent.scrollHeight;

                // Update inbox last message
                const lastMsg = res.data[res.data.length - 1];
                updateInboxLastMessage(selectedUserId, lastMsg.message, formatTimeAgo(lastMsg.created_at));
            }
        })
        .catch(err => console.error(err));
}

// SEND MESSAGE
sendBtn.addEventListener('click', () => {
    const text = chatInput.value.trim();
    if (!text || !selectedUserId) return;

    axios.post('/admin/chat/send', {
        message: text,
        receiver_id: selectedUserId
    }, { headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
    .then(res => {
        chatInput.value = '';
        fetchMessages();
    })
    .catch(err => console.error(err));
});

// ENTER KEY SEND
chatInput.addEventListener('keypress', e => {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendBtn.click();
    }
});

// AUTO REFRESH
setInterval(() => { if (selectedUserId) fetchMessages(); }, 2000);

// UPDATE INBOX LAST MESSAGE\
function updateInboxLastMessage(userId, message, time) {
    const userEl = document.querySelector(`.chat-user[data-user='${userId}']`);
    if (userEl) {
        userEl.querySelector('.chat-info p').textContent = message;
        let timeEl = userEl.querySelector('.chat-info small');
        if (!timeEl) {
            timeEl = document.createElement('small');
            timeEl.className = 'last-message-time';
            userEl.querySelector('.chat-info').appendChild(timeEl);
        }
        timeEl.textContent = time;
    }
}

// FORMAT TIME AGO 
function formatTimeAgo(datetime) {
    const now = new Date();
    const msgTime = new Date(datetime);
    const diff = Math.floor((now - msgTime) / 1000);

    if (diff < 60) return 'Just now';
    if (diff < 3600) return Math.floor(diff / 60) + 'm ago';
    if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
    return Math.floor(diff / 86400) + 'd ago';
}

// FORMAT MESSAGE TIME 
function formatMessageTime(datetime) {
    const msgTime = new Date(datetime);
    const now = new Date();

    const diff = Math.floor((now - msgTime) / 1000); 
    if (diff < 60) return 'Just now';
    if (diff < 3600) return Math.floor(diff / 60) + 'm';
    if (diff < 86400) return Math.floor(diff / 3600) + 'h';

    const options = { hour: '2-digit', minute: '2-digit' };
    if (msgTime.toDateString() === now.toDateString()) {
        return msgTime.toLocaleTimeString([], options);
    } else if (msgTime.toDateString() === new Date(now - 86400000).toDateString()) {
        return 'Yesterday ' + msgTime.toLocaleTimeString([], options);
    } else {
        return msgTime.toLocaleDateString() + ' ' + msgTime.toLocaleTimeString([], options);
    }
}
</script>

<!-- SETTINGS SECTION -->
<div id="settings-section" class="content-section" style="display:none;">
    <div class="admin-container theme-light" id="settingsContainer">
        <h2 class="fw-bold mb-3 mt-2"><i class="fas fa-cogs me-2"></i> Admin Settings </h2>

        <div class="card settings-card shadow-sm border-0">
            <div class="card-body">

                <div class="mb-3">
                    <h5 class="fw-bold">Personal & Dashboard Settings</h5>
                    <p class="small">Adjust your dashboard appearance and notifications</p>
                </div>

                <form id="adminSettingsForm">
                    <!-- Appearance -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Theme</label>
                            <select class="form-select" id="themeSelect" onchange="changeTheme(this.value)">
                                <option value="light">️ Light</option>
                                <option value="dark"> Dark</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Auto Logout (minutes)</label>
                            <input type="number" class="form-control" id="autoLogout" min="5" max="120">
                        </div>
                    </div>

                    <!-- Toggles -->
                    <div class="mb-4">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="enableNotifications">
                            <label class="form-check-label">Enable Email Notifications</label>
                        </div>

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="darkSidebar" onchange="toggleDarkSidebar(this.checked)">
                            <label class="form-check-label">Enable Dark Sidebar</label>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="button" class="btn btn-primary" id="openSaveModal"> Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let autoLogoutTimer;

document.addEventListener('DOMContentLoaded', () => {
    loadAdminSettings();

    const openModalBtn = document.getElementById('openSaveModal');
    const confirmSaveBtn = document.getElementById('confirmSaveChanges');

    // Initialize Bootstrap modal
    const saveModal = new bootstrap.Modal(document.getElementById('saveChangesModal'));

    // Open modal when clicking Save Changes button
    openModalBtn.addEventListener('click', () => {
        saveModal.show();
    });

    // Save settings when confirming in modal
    confirmSaveBtn.addEventListener('click', () => {
        saveAdminSettings();
        saveModal.hide();
    });
});

function changeTheme(theme) {
    const container = document.getElementById('settingsContainer');
    document.body.classList.remove('theme-light', 'theme-dark');
    container.classList.remove('theme-light', 'theme-dark');
    document.body.classList.add(`theme-${theme}`);
    container.classList.add(`theme-${theme}`);
    localStorage.setItem('adminTheme', theme);
}

function toggleDarkSidebar(enabled) {
    const sidebar = document.querySelector('.sidebar');
    if (sidebar) sidebar.classList.toggle('sidebar-dark', enabled);
}

function saveAdminSettings() {
    const settings = {
        theme: themeSelect.value,
        autoLogout: autoLogout.value,
        enableNotifications: enableNotifications.checked,
        darkSidebar: darkSidebar.checked
    };
    localStorage.setItem('adminSettings', JSON.stringify(settings));
    changeTheme(settings.theme);
    toggleDarkSidebar(settings.darkSidebar);
    setupAutoLogout(settings.autoLogout);
    alert("Settings saved successfully!");
}

function loadAdminSettings() {
    const settings = JSON.parse(localStorage.getItem('adminSettings')) || {};
    themeSelect.value = settings.theme || 'light';
    autoLogout.value = settings.autoLogout || 15;
    enableNotifications.checked = settings.enableNotifications || false;
    darkSidebar.checked = settings.darkSidebar || false;
    changeTheme(themeSelect.value);
    toggleDarkSidebar(darkSidebar.checked);
    setupAutoLogout(autoLogout.value);
}

function setupAutoLogout(minutes) {
    if (autoLogoutTimer) clearTimeout(autoLogoutTimer);
    autoLogoutTimer = setTimeout(() => {
        alert("Auto logout triggered");
    }, minutes * 60000);
}
</script>

</main>
<!-- VIEW MODAL -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white border-0 rounded-top">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-white me-3"><i class="fas fa-user fa-lg text-primary"></i></div>
                    <h5 class="modal-title fw-bold" id="viewModalLabel">Application Details</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="viewDetails" style="max-height:70vh; overflow-y:auto;">
                <!-- Dynamic content will be inserted here from JS -->
            </div>
            <div class="modal-footer border-0 justify-content-end">
                <button type="button" class="btn btn-secondary fw-semibold" data-bs-dismiss="modal">
                  <i class="fas fa-times me-1"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MOVE TO SCHEDULE MODAL -->
<div class="modal fade" id="moveToScheduleModal" tabindex="-1" aria-labelledby="moveToScheduleLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title fw-bold" id="moveToScheduleLabel"><i class="fas fa-route me-2"></i> Move to Schedule Submission</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body d-flex flex-column flex-md-row align-items-center gap-3 text-center text-md-start fade-content">
                <i class="fas fa-file-alt fa-3x text-warning"></i>
                <span class="fs-6"> Are you sure you want to move this applicant to the <strong>Schedule Submission</strong> stage?</span>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-warning fw-semibold text-white d-flex align-items-center justify-content-center" id="confirmMoveToScheduleBtn">
                    <span class="spinner-border spinner-border-sm me-2 d-none" id="moveScheduleSpinner"></span>
                    <span id="confirmBtnText">Confirm</span>
                </button>
                <button type="button" class="btn btn-secondary fw-semibold text-white" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
  const confirmBtn = document.getElementById('confirmMoveToScheduleBtn');
  const spinner = document.getElementById('moveScheduleSpinner');
  const btnText = document.getElementById('confirmBtnText');
  const modal = document.getElementById('moveToScheduleModal');
  const fadeContent = modal.querySelector('.fade-content');

  // Animate body content on modal show
  modal.addEventListener('show.bs.modal', () => {
    fadeContent.style.opacity = 0;
    fadeContent.style.transform = 'translateY(10px)';
    setTimeout(() => {
      fadeContent.style.transition = 'all 0.4s ease';
      fadeContent.style.opacity = 1;
      fadeContent.style.transform = 'translateY(0)';
    }, 50);
  });

  // Loading spinner on confirm
  confirmBtn.addEventListener('click', () => {
    spinner.classList.remove('d-none');
    btnText.textContent = 'Processing...';
    confirmBtn.setAttribute('disabled', true);

    setTimeout(() => {
      spinner.classList.add('d-none');
      btnText.textContent = 'Confirm';
      confirmBtn.removeAttribute('disabled');

      const bsModal = bootstrap.Modal.getInstance(modal);
      bsModal.hide();
    }, 1500);
  });
});
</script>

<!-- REJECT APPLICATION MODAL -->
<div class="modal fade" id="rejectApplicationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 bg-danger text-white text-center flex-column position-relative">
                <div class="reject-icon mb-2"><i class="fas fa-user-times"></i></div>
                <h5 class="modal-title fw-bold">Reject Application</h5>
                <button type="button" class="btn-close btn-close-white position-absolute end-0 top-0 m-3" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center px-4">
                <p id="rejectMessage" class="text-muted mb-3"> Are you sure you want to reject this application? </p>
                <textarea id="rejectReason" class="modern-textarea mt-2" rows="3" placeholder="Reason for rejection (optional)"></textarea>
            </div>
            <div class="modal-footer border-0 justify-content-center gap-2 pb-4">
                <button class="btn btn-light px-4" data-bs-dismiss="modal"> Cancel </button>
                <button id="confirmRejectApplicationBtn" class="btn btn-danger px-4">
                    <span class="btn-text">Reject</span>
                    <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

<!-- == SCHEDULE SUBMISSION MODAL == -->
<div class="modal fade" id="scheduleModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form id="scheduleForm" method="POST" action="{{ route('scheduled-submissions.store') }}" class="modal-content modern-modal d-flex flex-column">
            @csrf
            <div class="modal-header bg-primary text-white border-0 flex-shrink-0">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-calendar-check-fill fs-4"></i>
                    <h5 class="modal-title fw-bold mb-0">Schedule Submission</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 py-3 flex-grow-1 overflow-auto">
                <!-- HIDDEN FIELDS -->
                <input type="hidden" name="schedule_req_id" id="schedule-id">
                <!-- APPLICANT INFO -->
                <div class="info-card mb-4">
                    <div class="info-item"><i class="bi bi-person-circle"></i>
                        <div>
                            <small class="text-muted">Applicant Name</small>
                            <div id="applicant-name" class="fw-semibold"></div>
                        </div>
                    </div>
        
                    <div class="info-item"><i class="bi bi-geo-alt-fill"></i>
                        <div>
                            <small class="text-muted">Applicant Address</small>
                            <div id="applicant-address" class="fw-semibold"></div>
                        </div>
                    </div>
                </div>
                <!-- DATE -->
                <div class="mb-4">
                    <label class="form-label fw-semibold"> Select Date</label>
                    <input type="date" name="scheduled_date" id="schedule-date" class="modern-input" placeholder="Choose a date" required>
                </div>
                <!-- TIME -->
                <div class="mb-4">
                    <label class="form-label fw-semibold"> Select Time</label>
                    <div class="time-grid" id="timeGrid"></div>
                    <input type="hidden" name="scheduled_time" id="schedule-time" required>
                </div>
                <!-- BARANGAY ADDRESS -->
                <div class="mb-4">
                    <label class="form-label fw-semibold"> Submission Location</label>
                    <div class="address-card">
                        <i class="bi bi-building"></i>
                        <textarea name="barangay_address" id="barangay-address" class="modern-textarea" rows="2" required></textarea>
                    </div>
                </div>
                <!-- PREVIEW -->
                <div class="preview-card p-3">
                    <h6 class="fw-semibold mb-3"> Submission Preview</h6>
                    <div class="preview-item"><i class="bi bi-person-fill"></i> <span id="preview-name"></span></div>
                    <div class="preview-item"><i class="bi bi-calendar-event-fill"></i> <span id="preview-date">-</span></div>
                    <div class="preview-item"><i class="bi bi-clock-fill"></i> <span id="preview-time">-</span></div>
                    <div class="preview-item"><i class="bi bi-geo-alt-fill"></i> <span id="preview-address"></span></div>
                    <div class="preview-item"><i class="bi bi-bell-fill"></i> <span id="preview-notify">-</span></div>
                </div>
            </div>
            <div class="modal-footer justify-content-center border-0 pb-4 flex-shrink-0">
                <button type="submit" class="btn btn-primary px-4 fw-semibold" id="saveScheduleBtn">
                    <span class="btn-text">Save Schedule</span>
                    <span class="btn-loading d-none"><span class="spinner-border spinner-border-sm me-2"></span> Saving...</span>
                </button>
                <button type="button" class="btn btn-light px-4 fw-semibold" data-bs-dismiss="modal">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- MOVE TO HOME VISIT MODAL -->
<div class="modal fade" id="moveToHomeVisitModal" tabindex="-1" aria-labelledby="moveToHomeVisitLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 bg-warning text-white text-center flex-column position-relative">
                <div class="homevisit-icon mb-2"><i class="fas fa-house-user"></i></div>
                <h5 class="modal-title fw-bold" id="moveToHomeVisitLabel"> Move to Home Visit </h5>
                <button type="button" class="btn-close btn-close-white position-absolute end-0 top-0 m-3" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center px-4">
                <p id="homeVisitMessage" class="text-muted mb-3"> Are you sure you want to send this applicant to the <strong>Home Visit</strong> stage?
                </p>
                <div class="user-preview mb-3"><i class="fas fa-user-circle"></i></div>
                <div class="fw-semibold mb-1" id="moveHomeVisitName">Juan Dela Cruz</div>
                <small class="text-muted mb-2" id="moveHomeVisitAddress">Purok 1, Barangay Sample, Municipality</small>
                <small class="text-muted d-block">This will move the applicant to the next stage of the process.</small>
            </div>
            <div class="modal-footer border-0 justify-content-center gap-2 pb-4">
                <button class="btn btn-light px-4" data-bs-dismiss="modal"> Cancel </button>
                <button id="confirmMoveToHomeVisit" class="btn btn-warning px-4">
                    <span class="btn-text">Confirm</span>
                    <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const confirmBtn = document.getElementById('confirmMoveToHomeVisit');
    const spinner = confirmBtn.querySelector('.spinner-border');
    const btnText = confirmBtn.querySelector('.btn-text');
    const modalEl = document.getElementById('moveToHomeVisitModal');

    confirmBtn.addEventListener('click', () => {
        // Show loading
        confirmBtn.classList.add('loading');
        spinner.classList.remove('d-none');

        setTimeout(() => {
            confirmBtn.classList.remove('loading');
            spinner.classList.add('d-none');
            btnText.textContent = 'Confirm';

            bootstrap.Modal.getInstance(modalEl).hide();
        }, 1500);
    });
});
</script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- HOME VISIT SCHEDULE MODAL -->
<div class="modal fade" id="homeVisitScheduleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form id="homeVisitScheduleForm" class="modal-content">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title fw-bold">Schedule Home Visit</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="homevisit-schedule-time">
                <input type="hidden" id="homevisit-schedule-id">
                <!-- Date Picker -->
                <div class="mb-3">
                    <label class="form-label">Date</label>
                    <input type="date" id="homevisit-schedule-date" class="form-control modern-input" required>
                </div>
                <!-- Time Grid -->
                <div class="mb-3">
                    <label class="form-label">Time</label>
                    <div class="time-grid" id="homevisit-time-grid">
                        <!-- JS will populate time cards here -->
                    </div>
                </div>
                <!-- Preview Info -->
                <div class="preview-card p-3 mt-3">
                    <div class="preview-item"><strong>Name:</strong> <span id="homevisit-user-name">-</span></div>
                    <div class="preview-item"><strong>Address:</strong> <span id="homevisit-user-address">-</span></div>
                    <div class="preview-item"><strong>Date:</strong> <span id="homevisit-preview-date">-</span></div>
                    <div class="preview-item"><strong>Time:</strong> <span id="homevisit-preview-time">-</span></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" id="saveHomeVisitScheduleBtn" class="btn btn-primary">Save Schedule</button>
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
            </div>
        </form>
    </div>
</div>
<!-- HOME VISIT REJECT MODAL -->
<div class="modal fade" id="rejectHomeVisitModal" tabindex="-1" aria-labelledby="rejectHomeVisitLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 bg-danger text-white text-center flex-column position-relative">
                <div class="reject-icon mb-2"><i class="fas fa-house-circle-xmark"></i></div>
                <h5 class="modal-title fw-bold" id="rejectHomeVisitLabel"> Reject Home Visit </h5>
                <button type="button" class="btn-close btn-close-white position-absolute end-0 top-0 m-3" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center px-4">
                <p id="rejectMessage" class="text-muted mb-3"> Are you sure you want to reject this home visit? </p>
                <textarea id="rejectReason" class="form-control modern-textarea" rows="3" placeholder="Optional reason for rejection..."></textarea>
            </div>
            <div class="modal-footer border-0 justify-content-center gap-2 pb-4">
                <button class="btn btn-light px-4" data-bs-dismiss="modal"> Cancel </button>
                <button id="confirmRejectHomeVisit" class="btn btn-danger px-4">
                    <span class="btn-text">Confirm Reject</span>
                    <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MOVE TO READY TO PROCESS MODAL -->
<div class="modal fade" id="moveToReadyModal" tabindex="-1" aria-labelledby="moveToReadyLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 bg-success text-white text-center flex-column position-relative">
                <div class="ready-icon mb-2"><i class="fas fa-user-check"></i></div>
                <h5 class="modal-title fw-bold"> Mark as Ready</h5>
                <button type="button" class="btn-close btn-close-white position-absolute end-0 top-0 m-3" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center px-4">
                <p id="readyMessage" class="text-muted mb-3"> Are you sure you want to mark this record as ready? </p>
                <div class="doc-preview mb-3"><i class="fas fa-file-signature"></i></div>
                <small class="text-muted"> This will move the applicant to the next stage of the process. </small>
            </div>
            <div class="modal-footer border-0 justify-content-center gap-2 pb-4">
                <button class="btn btn-light px-4" data-bs-dismiss="modal"> Cancel </button>
                <button id="confirmMoveToReady" class="btn btn-success px-4">
                    <span class="btn-text">Confirm</span>
                    <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- CONFIRM ADD BENEFICIARY MODAL -->
<div class="modal fade" id="confirmBeneficiaryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modern-confirm-modal">
            <div class="modal-header border-0 justify-content-center">
                <h5 class="modal-title fw-bold">Confirm Action</h5>
                <button type="button" class="btn-close position-absolute end-0 me-3" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pb-4 d-flex align-items-center">
                <div class="confirm-icon flex-shrink-0"><i class="bi bi-person-check-fill"></i></div>
                <div class="ms-3">
                    <p class="confirm-text mb-1"> Are you sure you want to approve this resident as a <strong>Solo Parent Beneficiary</strong>?
                    </p>
                    <small class="text-muted"> This action will add the resident to the official beneficiary list. </small>
                </div>
            </div>
            <div class="modal-footer border-0 justify-content-center pb-4">
                <button type="button" class="btn btn-success confirm-btn" id="confirmAddBeneficiaryBtn">
                    <span class="btn-text"><i class="bi bi-person-plus-fill me-1"></i> Yes, Add Beneficiary</span>
                    <span class="btn-loading d-none"><span class="spinner-border spinner-border-sm me-2"></span> Processing...</span>
                </button>
                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal"> Cancel </button>
            </div>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<!-- FULL DAY SCHEDULE MODAL -->
<div class="modal fade" id="dayScheduleModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
        <div class="modal-header bg-primary text-white">
            <h5 class="modal-title" id="modalTitle">Scheduled People</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" id="modalBody"></div>
        </div>
    </div>
</div>
<!-- HOME VISIT DAY MODAL -->
<div class="modal fade" id="homeVisitDayModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="homeVisitModalTitle">Home Visits</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="homeVisitModalBody">
                <!-- Home visits for the selected day will be inserted here -->
            </div>
        </div>
    </div>
</div>

<!-- CHOOSE BENEFITS Modal -->
<div class="modal fade" id="chooseBenefitsModal" tabindex="-1" aria-labelledby="chooseBenefitsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="chooseBenefitsModalLabel">Choose Benefits</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- JS will insert recommended first, default next -->
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-warning text-white" id="saveBenefitsBtn">Save Benefits</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- VIEW BENEFICIARY MODAL -->
<div class="modal fade" id="beneficiaryViewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-sm rounded-3">
            <div class="modal-header text-white" style="background-color: #003366;">
                <h5 class="modal-title"><i class="bi bi-person-circle"></i> Beneficiary Information</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
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
            <div class="modal-header border-0 bg-danger text-white text-center flex-column position-relative">
                <div class="delete-icon mb-2"><i class="fas fa-trash fa-2x"></i></div>
                <h5 class="modal-title fw-bold">Delete Beneficiary</h5>
                <button type="button" class="btn-close btn-close-white position-absolute end-0 top-0 m-3" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center px-4">
                <p class="mb-2">Are you sure you want to delete this beneficiary?</p>
                <p class="small text-muted mb-0">This action cannot be undone.</p>
            </div>
            <div class="modal-footer border-0 justify-content-center gap-2 pb-4">
                <button class="btn btn-danger px-4 fw-semibold" id="confirmDeleteBeneficiary"><i class="fas fa-trash me-1"></i> Delete </button>
                <button class="btn btn-light px-4 fw-semibold" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i> Cancel </button>
            </div>
        </div>
    </div>
</div>

<!-- DOWNLOAD CONFIRMATION MODAL -->
<div class="modal fade" id="downloadChartsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Download Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body"> Do you want to download the selected chart(s) data as CSV? </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmDownloadChartsBtn">Download CSV</button>
            </div>
        </div>
    </div>
</div>

<!--  PAYOUT SCHEDULE MODAL -->
<div class="modal fade" id="payoutScheduleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form class="modal-content modern-modal" id="payoutScheduleForm">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">Set Payout Schedule</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 py-3">
                <input type="hidden" id="payout-barangay">
                <!-- DATE -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Date</label>
                    <input type="text" id="payout-date" class="form-control modern-input" placeholder="Select payout date" required>
                </div>
                <!-- TIME -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Time</label>
                    <div class="time-grid" id="payout-time-grid"></div>
                    <input type="hidden" id="payout-time" required>
                </div>
                <!-- LOCATION -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Location</label>
                    <input type="text" id="payout-location" class="form-control modern-input" placeholder="Enter payout location" required>
                </div>
                <!-- NOTIFICATIONS -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Notify Beneficiaries</label>
                    <div class="notify-options d-flex gap-3">
                        <div class="notify-card" id="notify-sms">
                            <i class="bi bi-chat-left-text-fill me-2"></i> SMS
                        </div>
                        <div class="notify-card" id="notify-email"><i class="bi bi-envelope-fill me-2"></i> Email</div>
                    </div>
                </div>
                <!-- PREVIEW: PAYOUT SLIP -->
                <div class="preview-card mt-3 p-3 border rounded shadow-sm">
                    <h6 class="fw-semibold mb-3"> Payout Slip Preview</h6>
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-calendar-event-fill text-primary me-2"></i>
                        <span id="preview-date">-</span>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-clock-fill text-primary me-2"></i>
                        <span id="preview-time">-</span>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-geo-alt-fill text-primary me-2"></i>
                        <span id="preview-location">-</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-bell-fill text-primary me-2"></i>
                        <span id="preview-notify">-</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="submit" class="btn btn-primary fw-semibold px-4" id="save-payout-btn" data-route="{{ route('admin.savePayoutSchedule') }}">
                    <span class="btn-text">Save Schedule</span>
                    <span class="btn-loading d-none"><span class="spinner-border spinner-border-sm me-2"></span>Saving...</span>
                </button>
                <button type="button" class="btn btn-secondary fw-semibold px-4" data-bs-dismiss="modal">Cancel</button>
            </div>
        </form>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {

  // DATE PICKER
  flatpickr("#payout-date", {
    dateFormat: "Y-m-d",
    minDate: "today",
    disableMobile: true,
    onChange: updatePreview
  });

  // TIME SELECTION CARDS
  const timeGrid = document.getElementById("payout-time-grid");
  const timeInput = document.getElementById("payout-time");
  const times = ["08:00 AM","09:00 AM","10:00 AM","11:00 AM","12:00 PM","01:00 PM","02:00 PM","03:00 PM","04:00 PM","05:00 PM"];

  times.forEach(time => {
    const card = document.createElement("div");
    card.className = "time-card";
    card.textContent = time;
    card.addEventListener("click", () => {
      document.querySelectorAll(".time-card").forEach(c => c.classList.remove("active"));
      card.classList.add("active");
      timeInput.value = time;
      updatePreview();
    });
    timeGrid.appendChild(card);
  });

  // NOTIFICATION CARD TOGGLE
  const notifySMS = document.getElementById("notify-sms");
  const notifyEmail = document.getElementById("notify-email");
  [notifySMS, notifyEmail].forEach(card => {
    card.addEventListener("click", () => {
      card.classList.toggle("active");
      updatePreview();
    });
  });

  // INPUTS
  const dateInput = document.getElementById("payout-date");
  const locationInput = document.getElementById("payout-location");

  locationInput.addEventListener("input", updatePreview);

  // UPDATE PREVIEW
  function updatePreview() {
    document.getElementById("preview-date").textContent = dateInput.value || "-";
    document.getElementById("preview-time").textContent = timeInput.value || "-";
    document.getElementById("preview-location").textContent = locationInput.value || "-";
    let notifyText = [];
    if (notifySMS.classList.contains("active")) notifyText.push("SMS");
    if (notifyEmail.classList.contains("active")) notifyText.push("Email");
    document.getElementById("preview-notify").textContent = notifyText.join(", ") || "-";
  }

  // SUBMIT WITH LOADING
  const form = document.getElementById("payoutScheduleForm");
  const btn = document.getElementById("save-payout-btn");
  const btnText = btn.querySelector(".btn-text");
  const btnLoading = btn.querySelector(".btn-loading");

  form.addEventListener("submit", (e) => {
    e.preventDefault();
    btn.disabled = true;
    btnText.classList.add("d-none");
    btnLoading.classList.remove("d-none");

    setTimeout(() => {
      btn.disabled = false;
      btnText.classList.remove("d-none");
      btnLoading.classList.add("d-none");

      const modalEl = document.getElementById("payoutScheduleModal");
      const modal = bootstrap.Modal.getInstance(modalEl);
      modal.hide();

      console.log("Payout schedule saved! Notifications sent: " + document.getElementById("preview-notify").textContent);
    }, 1500);
  });

});
</script>

<!-- BENEFITS MODAL -->
<div class="modal fade" id="benefitsModal" tabindex="-1" aria-labelledby="benefitsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content modern-modal">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="benefitsModalLabel">Beneficiary Benefits</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-3 py-3">
                <div class="table-responsive">
                    <table class="table modern-table align-middle text-center mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Benefit</th>
                                <th>Date Given</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody id="benefits-body">
                          <!-- Backend will populate rows dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<!-- PRE-REGISTRATION MODAL -->
<div class="modal fade" id="preRegModal" tabindex="-1" aria-labelledby="preRegModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="preRegModalLabel">Pre-Register Activity</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="createEventForm">
                <div class="modal-body">
                    <!-- Activity Name -->
                    <div class="mb-3">
                        <label class="form-label">Activity Name:</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <!-- Activity Type -->
                    <div class="mb-3">
                        <label class="form-label mb-2">Activity Type:</label>
                        <div class="activity-card-group">
                            <div class="activity-card" data-value="Seminar">
                                <i class="bi bi-easel-fill"></i>
                                <span>Seminar</span>
                            </div>
                            <div class="activity-card" data-value="Event">
                                <i class="bi bi-calendar-event-fill"></i>
                                <span>Event</span>
                            </div>
                            <div class="activity-card" data-value="Meeting">
                                <i class="bi bi-people-fill"></i>
                                <span>Meeting</span>
                            </div>
                            <div class="activity-card" data-value="Home Visit">
                                <i class="bi bi-house-door-fill"></i>
                                <span>Home Visit</span>
                            </div>
                        </div>
                        <input type="hidden" name="type" id="activityType" required>
                    </div>
                    <!-- Date -->
                    <div class="mb-3">
                        <label class="form-label">Date:</label>
                        <input type="date" class="form-control" name="date" required>
                    </div>
                    <!-- Time -->
                    <div class="mb-3">
                        <label class="form-label">Time:</label>
                        <input type="time" class="form-control" name="time" required>
                    </div>
                    <!-- Location -->
                    <div class="mb-3">
                        <label class="form-label">Location:</label>
                        <input type="text" class="form-control" name="location" required>
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="submit" class="btn btn-primary fw-semibold px-4">Save</button>
                    <button type="button" class="btn btn-secondary fw-semibold px-4" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.activity-card').forEach(card => {
    card.addEventListener('click', () => {

        document.querySelectorAll('.activity-card')
            .forEach(c => c.classList.remove('active'));

        card.classList.add('active');
        document.getElementById('activityType').value = card.dataset.value;
    });
});
</script>

<!-- DELETE EVENT MODAL -->
<div class="modal fade" id="deleteEventModal" tabindex="-1" aria-labelledby="deleteEventLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 bg-danger text-white text-center flex-column position-relative">
                <div class="delete-icon mb-2"><i class="fas fa-trash-alt"></i></div>
                <h5 class="modal-title fw-bold" id="deleteEventLabel">Delete Event</h5>
                <button type="button" class="btn-close btn-close-white position-absolute end-0 top-0 m-3" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center px-4">
                <p id="deleteEventMessage" class="text-muted mb-3"> Are you sure you want to delete this activity? </p>
                <div class="event-preview mb-3"><i class="fas fa-calendar-alt"></i></div>
                <div class="fw-semibold mb-1" id="deleteEventName"></div>
                <small class="text-muted mb-2" id="deleteEventDate"></small>
                <small class="text-muted"> This action cannot be undone.</small>
            </div>
            <div class="modal-footer border-0 justify-content-center gap-2 pb-4">
                <button class="btn btn-light px-4" data-bs-dismiss="modal"> Cancel </button>
                <button id="confirmDeleteEventBtn" class="btn btn-danger px-4">
                    <span class="btn-text">Delete</span>
                    <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const confirmBtn = document.getElementById('confirmDeleteEventBtn');
    const spinner = confirmBtn.querySelector('.spinner-border');
    const btnText = confirmBtn.querySelector('.btn-text');
    const modalEl = document.getElementById('deleteEventModal');

    confirmBtn.addEventListener('click', () => {
        // Show loading
        confirmBtn.classList.add('loading');
        spinner.classList.remove('d-none');

        // Simulate processing (replace with real API call)
        setTimeout(() => {
            confirmBtn.classList.remove('loading');
            spinner.classList.add('d-none');
            btnText.textContent = 'Delete';

            bootstrap.Modal.getInstance(modalEl).hide();
        }, 1500);
    });
});
</script>

<!-- PRE-REGISTER EVENT MODAL -->
<div class="modal fade" id="preRegisterModal" tabindex="-1" aria-labelledby="preRegisterLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 bg-success text-white text-center flex-column position-relative">
                <div class="pre-register-icon mb-2"><i class="fas fa-clipboard-list"></i></div>
                <h5 class="modal-title fw-bold" id="preRegisterLabel">Pre-Registered Participants</h5>
                <button type="button" class="btn-close btn-close-white position-absolute end-0 top-0 m-3"data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4" id="preRegisterBody" style="max-height:400px; overflow-y:auto;">
                <!-- Participants list will be dynamically inserted here -->
            </div>
            <div class="modal-footer border-0 justify-content-center pb-4">
                <button class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- EDIT EVENT MODAL -->
<div class="modal fade" id="editEventModal" tabindex="-1" aria-labelledby="editEventLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form id="editEventForm" class="modal-content modern-modal">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"> Edit Activity</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 py-3">
                <input type="hidden" name="eventId" id="editEventId">
                <!-- Activity Name -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Activity Name</label>
                    <input type="text" name="name" class="form-control modern-input" id="editEventName" required>
                </div>
                <!-- Activity Type -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Activity Type</label>
                    <div class="activity-type-grid" id="editActivityTypeGrid">
                        <div class="activity-card" data-value="Seminar"><i class="bi bi-journal-text me-2"></i> Seminar</div>
                        <div class="activity-card" data-value="Event"><i class="bi bi-calendar-event me-2"></i> Event</div>
                        <div class="activity-card" data-value="Meeting"><i class="bi bi-people me-2"></i> Meeting</div>
                        <div class="activity-card" data-value="Home Visit"><i class="bi bi-house-door me-2"></i> Home Visit</div>
                    </div>
                    <input type="hidden" name="type" id="editActivityTypeInput" required>
                </div>
                <!-- Date -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Date</label>
                    <input type="text" name="date" id="editEventDate" class="form-control modern-input" placeholder="Select date" required>
                </div>
                <!-- Time -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Time</label>
                    <div class="time-grid" id="editEventTimeGrid"></div>
                    <input type="hidden" name="time" id="editEventTimeInput" required>
                </div>
                <!-- Location -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Location</label>
                    <input type="text" name="location" class="form-control modern-input" id="editEventLocation" required>
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="submit" class="btn btn-primary fw-semibold px-4" id="confirmEditEventBtn"><span class="spinner-border spinner-border-sm me-2 d-none" id="editEventSpinner"></span> Save Changes </button>
                <button type="button" class="btn btn-secondary fw-semibold px-4" data-bs-dismiss="modal">Cancel</button>
            </div>
        </form>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
  // DATE PICKER
  flatpickr("#editEventDate", {
    dateFormat: "Y-m-d",
    minDate: "today",
    disableMobile: true
  });

  // TIME CARDS
  const times = ["08:00 AM","09:00 AM","10:00 AM","11:00 AM","12:00 PM","01:00 PM","02:00 PM","03:00 PM","04:00 PM","05:00 PM"];
  const timeGrid = document.getElementById("editEventTimeGrid");
  const timeInput = document.getElementById("editEventTimeInput");

  times.forEach(time => {
    const card = document.createElement("div");
    card.className = "time-card";
    card.textContent = time;
    card.addEventListener("click", () => {
      document.querySelectorAll("#editEventTimeGrid .time-card").forEach(c => c.classList.remove("active"));
      card.classList.add("active");
      timeInput.value = time;
    });
    timeGrid.appendChild(card);
  });

  // ACTIVITY TYPE CARD SELECTION
  const typeCards = document.querySelectorAll("#editActivityTypeGrid .activity-card");
  const typeInput = document.getElementById("editActivityTypeInput");
  typeCards.forEach(card => {
    card.addEventListener("click", () => {
      typeCards.forEach(c => c.classList.remove("active"));
      card.classList.add("active");
      typeInput.value = card.dataset.value;
    });
  });

  // PRE-SELECT EXISTING DATA WHEN OPENING MODAL
  const editEventModal = document.getElementById('editEventModal');
  editEventModal.addEventListener('show.bs.modal', event => {
    const button = event.relatedTarget; // Button that triggered the modal
    const eventData = button.dataset; // Example: pass data-* attributes from your table row or backend

    // Fill form fields
    document.getElementById('editEventId').value = eventData.id || '';
    document.getElementById('editEventName').value = eventData.name || '';
    document.getElementById('editEventLocation').value = eventData.location || '';
    document.getElementById('editEventDate')._flatpickr.setDate(eventData.date || '');

    // Preselect activity type
    typeCards.forEach(c => {
      c.classList.remove('active');
      if(c.dataset.value === eventData.type) c.classList.add('active');
    });
    typeInput.value = eventData.type || '';

    // Preselect time
    timeInput.value = eventData.time || '';
    document.querySelectorAll("#editEventTimeGrid .time-card").forEach(c => {
      c.classList.remove('active');
      if(c.textContent === eventData.time) c.classList.add('active');
    });
  });

  // LOADING SPINNER ON SUBMIT
  const editForm = document.getElementById('editEventForm');
  const submitBtn = document.getElementById('confirmEditEventBtn');
  const spinner = document.getElementById('editEventSpinner');

  editForm.addEventListener('submit', e => {
    e.preventDefault();
    spinner.classList.remove('d-none');
    submitBtn.setAttribute('disabled', true);

    // Simulate form submission (replace with actual AJAX)
    setTimeout(() => {
      spinner.classList.add('d-none');
      submitBtn.removeAttribute('disabled');
      // Close modal or show success
      const modal = bootstrap.Modal.getInstance(editEventModal);
      modal.hide();
    }, 1500);
  });

});
</script>

<!-- EDIT PAYOUT MODAL  -->
<div class="modal fade" id="editPayoutModal" tabindex="-1" aria-labelledby="editPayoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 bg-warning text-white text-center flex-column position-relative">
                <div class="modal-icon mb-2"><i class="fas fa-calendar-alt fa-2x"></i></div>
                <h5 class="modal-title fw-bold" id="editPayoutModalLabel">Edit Payout Schedule</h5>
                <button type="button" class="btn-close btn-close-white position-absolute end-0 top-0 m-3" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4">
                <form id="edit-payout-form">
                    <div class="mb-3">
                        <label for="edit-payout-barangay" class="form-label fw-semibold">Barangay</label>
                        <input type="text" id="edit-payout-barangay" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="edit-payout-date" class="form-label fw-semibold">Date</label>
                        <input type="date" id="edit-payout-date" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="edit-payout-time" class="form-label fw-semibold">Time</label>
                        <input type="time" id="edit-payout-time" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="edit-payout-location" class="form-label fw-semibold">Location</label>
                        <input type="text" id="edit-payout-location" class="form-control">
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 justify-content-center gap-2 pb-4">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="save-edit-payout-btn" class="btn btn-warning px-4">
                    <span class="btn-text">Save Changes</span>
                    <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- QR CODE MODAL FOR BENEFICIARY -->
<div class="modal fade" id="qrCodeModal" tabindex="-1" aria-labelledby="qrCodeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 text-center">
            <div class="modal-header border-0 bg-primary text-white flex-column position-relative justify-content-center">
                <div class="qr-icon mb-2"><i class="fas fa-user-check"></i></div>
                <h5 class="modal-title fw-bold" id="qrCodeModalLabel"> QR Code for Beneficiary</h5>
                <span id="qr-barangay-name" class="fw-semibold mt-1 d-block">---</span>
                <button type="button" class="btn-close btn-close-white position-absolute end-0 top-0 m-3" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="max-height:400px; overflow-y:auto;">
                <div class="qr-preview mb-3">
                    <div id="qr-code-display" class="qr-preview mx-auto d-block"></div>
                </div>
                <p id="qr-instruction-text" class="mt-3 fw-semibold"> Scan this QR code to mark the benefit as received. </p>
            </div>
            <div class="modal-footer border-0 justify-content-center pt-3 gap-2">
                <button type="button" class="btn btn-primary px-4" data-bs-dismiss="modal"> Close </button>
                <button type="button" class="btn btn-success px-4" id="printQrBtn"><i class="fas fa-print me-2"></i> Print</button>
            </div>
        </div>
    </div>
</div>

<!-- GLOBAL SUCCESS MODAL -->
<div class="modal fade" id="GlobalSuccessModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Success</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="globalSuccessMessage">Operation completed successfully!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<!-- GLOBAL ERROR MODAL -->
<div class="modal fade" id="AppErrorModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"> Error</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center"> Something went wrong. </div>
            <div class="modal-footer">
                <button class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- APPLICATION SUCCESS MODAL -->
<div class="modal fade" id="AppSuccessModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Solo Parent Application Process TImeline</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div class="timeline-container">
                    <div class="timeline-line"></div>
                    <!-- Dots for each step -->
                    <div class="timeline-step" data-step="Review Application">
                        <div class="timeline-station"></div>
                        <div class="timeline-label">Review Application</div>
                    </div>
                    <div class="timeline-step" data-step="Schedule of Submission">
                        <div class="timeline-station"></div>
                        <div class="timeline-label">Schedule of Submission</div>
                    </div>
                    <div class="timeline-step" data-step="Home Visit">
                        <div class="timeline-station"></div>
                        <div class="timeline-label">Home Visit</div>
                    </div>
                    <div class="timeline-step" data-step="Ready to Process">
                        <div class="timeline-station"></div>
                        <div class="timeline-label">Ready to Process</div>
                    </div>
                    <div class="timeline-step" data-step="Verified Solo Parent">
                        <div class="timeline-station"></div>
                        <div class="timeline-label">Verified Solo Parent</div>
                    </div>
                    <div class="current-dot"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- SCHEDULE SUCCESS MODAL -->
<div class="modal fade" id="ScheduleSuccessModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"> Success</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="schedule-success-message">Schedule saved successfully!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<!--  REPORT MODAL -->
<div class="modal fade" id="reportGenModal" tabindex="-1" aria-labelledby="reportGenModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-primary shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="reportGenModalLabel"><i class="fas fa-file-alt me-2"></i>Generate Report </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                Are you sure you want to generate this report?<br><strong>This will start the report generation process.</strong>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-primary fw-semibold" id="reportGenConfirmBtn"> Yes, Generate </button>
                <button type="button" class="btn btn-secondary fw-semibold" data-bs-dismiss="modal"> Cancel </button>
            </div>
        </div>
    </div>
</div>

<!-- ASSISTANCE HISTORY MODAL -->
<div class="modal fade" id="historyModal" tabindex="-1" aria-labelledby="historyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="historyModalLabel">Assistance History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Program</th>
                            <th>Amount</th>
                            <th>Staff</th>
                            <th>Status</th>
                            <th>Received</th>
                        </tr>
                    </thead>
                    <tbody id="history-body"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- EXPORT MODAL -->
<div class="modal fade" id="exportReportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content export-modal">
            <div class="modal-header export-header">
                <div class="export-icon"><i class="fas fa-file-export"></i></div>
                <h5 class="modal-title" id="exportModalTitle">Export Report</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div class="doc-preview"><i id="exportModalIcon" class="fas fa-file-pdf"></i></div>
                <p id="exportModalMessage" class="text-muted mt-3">Are you sure you want to export this report?</p>
                <small class="text-muted">The file will be generated based on the selected filters.</small>
            </div>
            <div class="modal-footer justify-content-center">
                <button class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                <button id="confirmExportBtn" class="btn btn-dark px-4">
                    <span class="btn-text">Export</span>
                    <span class="spinner-border spinner-border-sm d-none"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
  const modal = new bootstrap.Modal(document.getElementById("exportReportModal"));
  const title = document.getElementById("exportModalTitle");
  const message = document.getElementById("exportModalMessage");
  const icon = document.getElementById("exportModalIcon");
  const confirmBtn = document.getElementById("confirmExportBtn");

  let exportType = "";

  window.openExportModal = function(type) {
    exportType = type;
    if(type === "pdf") {
      title.textContent = "Export PDF Report";
      message.textContent = "Generate a PDF version of this report?";
      icon.className = "fas fa-file-pdf text-danger";
      confirmBtn.className = "btn btn-danger px-4";
    }
    if(type === "excel") {
      title.textContent = "Export Excel Report";
      message.textContent = "Generate an Excel spreadsheet report?";
      icon.className = "fas fa-file-excel text-success";
      confirmBtn.className = "btn btn-success px-4";
    }
    if(type === "csv") {
      title.textContent = "Export CSV Report";
      message.textContent = "Generate a CSV file for data analysis?";
      icon.className = "fas fa-file-csv text-dark";
      confirmBtn.className = "btn btn-dark px-4";
    }
    modal.show();
  };

  confirmBtn.addEventListener("click", function() {
    confirmBtn.classList.add("loading");
    setTimeout(() => {
      confirmBtn.classList.remove("loading");
      modal.hide();
      console.log("Exporting:", exportType);
    }, 1500);
  });
});
</script>

<!--FULLSCREEN MODAL -->
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

<!-- SAVE CHANGES MODAL -->
<div class="modal fade" id="saveChangesModal" tabindex="-1" aria-labelledby="saveChangesLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 bg-primary text-white text-center flex-column position-relative">
                <div class="mb-2"><i class="fas fa-save fa-2x"></i></div>
                <h5 class="modal-title fw-bold" id="saveChangesLabel">Save Changes</h5>
                <button type="button" class="btn-close btn-close-white position-absolute end-0 top-0 m-3" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center px-4">
                <p class="text-muted mb-3">Are you sure you want to save the changes you made in your settings?</p>
            </div>
            <div class="modal-footer border-0 justify-content-center gap-2 pb-4">
                <button type="button" class="btn btn-primary px-4" id="confirmSaveChanges"><i class="fas fa-check me-1"></i> Save Changes </button>
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i> Cancel </button>
            </div>
        </div>
    </div>
</div>

<!-- VIEW MODAL -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white border-0 rounded-top">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-white me-3"><i class="fas fa-user fa-lg text-primary"></i></div>
                    <h5 class="modal-title fw-bold" id="viewModalLabel">Application Details</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="viewDetails" style="max-height:70vh; overflow-y:auto;">
                <!-- Dynamic content will be inserted here from JS -->
            </div>
            <div class="modal-footer border-0 justify-content-end">
                <button type="button" class="btn btn-secondary fw-semibold" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i> Close </button>
            </div>
        </div>
    </div>
</div>

<!-- PDF EXPORT MODAL -->
<div class="modal fade" id="exportPdfModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white rounded-top-4">
                <i class="fas fa-file-pdf me-2"></i>
                <h5 class="modal-title">Export Solo Parent Beneficiaries - PDF</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="pdfYear" class="form-label">Year</label>
                    <input type="number" id="pdfYear" class="form-control" value="{{ now()->year }}">
                </div>
                <div class="mb-3">
                    <label for="pdfMonth" class="form-label">Month</label>
                    <select id="pdfMonth" class="form-select">
                        <option value="all" selected>All Months</option>
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}">{{ DateTime::createFromFormat('!m', $m)->format('F') }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="confirmPdfExport" class="btn btn-danger">Export PDF</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- EXCEL EXPORT MODAL -->
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
                            <option value="{{ $m }}">{{ DateTime::createFromFormat('!m', $m)->format('F') }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="confirmExcelExport" class="btn btn-success">Export Excel</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- SET CATEGORY MODAL -->
<div class="modal fade" id="setCategoryModal" tabindex="-1" aria-labelledby="setCategoryLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color:#003366">
                <h5 class="modal-title" id="setCategoryLabel"><i class="fas fa-tags me-2"></i> Set Solo Parent Category</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Step 1: Select Category -->
                <div id="categorySelectionStep">
                    <p>Select the appropriate category for this solo parent applicant:</p>
                    <div class="row g-2 mb-3">
                        @php
                            $categories = [
                                "A1. Birth of a child as a consequences of Rape",
                                "A2. Widow/Widower",
                                "A3. Spouse of person deprived of Liberty (PDL)",
                                "A4. Spouse of person with Disability (PWD)",
                                "A5. Due to de facto separation",
                                "A6. Due to nullity of marriage",
                                "A7. Abandoned",
                                "B. Spouse of the OFW/Relative of the OFW",
                                "C. Unmarried mother/father who keeps and rears his/her child/children",
                                "D. Legal guardian, adoptive or foster parents",
                                "E. Any relative within the fourth (4th) civil degree",
                                "F. Pregnant woman who provides sole parental care and support to her unborn child or children"
                            ];
                        @endphp
                        @foreach($categories as $category)
                            <div class="col-6">
                                <button type="button" class="btn w-100 category-option text-white" style="background-color:#003366">{{ $category }}</button>
                            </div>
                        @endforeach
                    </div>
                </div>
                <!-- Step 2: Show Documents & Confirm -->
                <div id="categoryDocsStep" style="display:none;">
                    <h6>Required Documents for <span id="selectedCategoryName"></span>:</h6>
                    <ul id="categoryDocsPreview" class="ms-3 text-muted"></ul>
                    <div class="mt-3 text-end">
                        <button type="button" class="btn btn-secondary btn-sm me-2" id="backToCategories">Back</button>
                        <button type="button" class="btn btn-primary btn-sm" id="confirmCategory">Confirm</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- HOME VISIT SCHEDULE SUCCESS MODAL -->
<div class="modal fade" id="homeVisitSuccessModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-sm">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Success</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0" id="homeVisitSuccessMessage">Home Visit scheduled successfully!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>




@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    //️ System Logo Preview Upload
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

    // Toggle Sidebar Submenu
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

    // Show Section When Menu Clicked
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
});
</script>
<script src="https://cdn.jsdelivr.net/npm/qrious@4.0.2/dist/qrious.min.js"></script>
@endsection
