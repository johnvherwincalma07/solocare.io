document.addEventListener("DOMContentLoaded", function () {

    // ==========================
    // Modal Elements
    // ==========================
    const viewModalEl = document.getElementById("viewModal");
    const rejectModalEl = document.getElementById("rejectApplicationModal");
    const rejectHomeVisitModalEl = document.getElementById("rejectHomeVisitModal");
    const viewDetails = document.getElementById("viewDetails");

    const viewModal = viewModalEl ? new bootstrap.Modal(viewModalEl) : null;
    const rejectModal = rejectModalEl ? new bootstrap.Modal(rejectModalEl) : null;
    const rejectHomeVisitModal = rejectHomeVisitModalEl ? new bootstrap.Modal(rejectHomeVisitModalEl) : null;

    let selectedRow = null;
    let selectedHomeVisitRow = null;

    // ==========================
    // Helper Functions
    // ==========================
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

    // ==========================
    // VIEW BUTTON
    // ==========================
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

    // ==========================
    // REJECT APPLICATION
    // ==========================
const confirmRejectBtn = document.getElementById("confirmRejectApplicationBtn");
const rejectReasonInput = document.getElementById("rejectReason");

document.addEventListener("click", (e) => {
    const btn = e.target.closest(".reject");
    if (!btn) return;
    selectedRow = btn.closest("tr");
    if (!selectedRow) return;
    if (rejectModal) rejectReasonInput.value = "";
    if (rejectModal) rejectModal.show();
});

if (confirmRejectBtn) {
    confirmRejectBtn.addEventListener("click", () => {
        if (!selectedRow) return alert("No application selected.");
        const appId = selectedRow.dataset.applicationId;
        if (!appId) return alert("Application ID missing.");
        const reason = rejectReasonInput.value;

        // Show loading spinner
        confirmRejectBtn.classList.add("loading");
        confirmRejectBtn.querySelector(".spinner-border").classList.remove("d-none");
        confirmRejectBtn.querySelector(".btn-text").style.display = "none";

        fetch(REJECT_APPLICATION_URL, { // ✅ Use JS variable, not Blade inside .js
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ application_id: appId, reason })
        })
        .then(res => {
            if (!res.ok) throw new Error("HTTP error " + res.status);
            return res.json();
        })
        .then(data => {
            if (data.success) {
                const statusCell = selectedRow.querySelector(".status-cell");
                if (statusCell) { 
                    statusCell.textContent = "Rejected"; 
                    statusCell.className = "fw-semibold text-danger status-cell"; 
                }
                const stageCell = selectedRow.querySelector(".stage-cell");
                if (stageCell) { 
                    stageCell.textContent = "Review Application"; 
                    stageCell.className = "fw-semibold stage-cell text-primary"; 
                }
                selectedRow.querySelectorAll("button").forEach(b => b.disabled = true);
                if (rejectModal) rejectModal.hide();
                alert("Application rejected successfully!");
            } else {
                alert(data.message || "Failed to reject application.");
            }
        })
        .catch(err => { 
            console.error("❌ Reject request failed:", err); 
            alert("Server error. Please try again."); 
        })
        .finally(() => {
            confirmRejectBtn.classList.remove("loading");
            confirmRejectBtn.querySelector(".spinner-border").classList.add("d-none");
            confirmRejectBtn.querySelector(".btn-text").style.display = "inline-block";
        });
    });
}


    // ==========================
    // HOME VISIT REJECT
    // ==========================
    const confirmRejectHomeVisitBtn = document.getElementById("confirmRejectHomeVisit");
    const rejectMessage = document.getElementById("rejectMessage");

    document.addEventListener("click", (e) => {
        const btn = e.target.closest(".rejectHomeVisit");
        if (!btn) return;
        selectedHomeVisitRow = btn.closest("tr");
        if (!selectedHomeVisitRow) return;

        const visitData = safeParseJSON(selectedHomeVisitRow.dataset.visit);
        if (rejectMessage) rejectMessage.textContent = `Are you sure you want to reject the home visit for ${visitData.last_name || "-"}, ${visitData.first_name || "-"}?`;
        if (rejectHomeVisitModal) rejectReasonInput.value = "";
        if (rejectHomeVisitModal) rejectHomeVisitModal.show();
    });

    if (confirmRejectHomeVisitBtn) {
        confirmRejectHomeVisitBtn.addEventListener("click", () => {
            if (!selectedHomeVisitRow) return alert("No home visit selected.");
            const visitData = safeParseJSON(selectedHomeVisitRow.dataset.visit);
            const visitId = visitData.visit_id;
            if (!visitId) return alert("Home visit ID missing.");
            const reason = rejectReasonInput.value;

            fetch(`/home-visit/reject/${visitId}`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ reason })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const statusCell = selectedHomeVisitRow.querySelector("td:nth-child(7)");
                    if (statusCell) { statusCell.textContent = "Rejected"; statusCell.className = "fw-semibold text-danger"; }
                    if (rejectHomeVisitModal) rejectHomeVisitModal.hide();
                    alert("Home visit rejected successfully!");
                } else alert(data.message || "Failed to reject home visit.");
            })
            .catch(err => { console.error(err); alert("Server error. Please try again."); });
        });
    }

});
