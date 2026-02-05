@extends('layouts.app')

@section('title', 'Solo Care - Home')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/style.css') }}">

@endsection

@section('content')
    <div id="loadingOverlay" style="display:flex; position: fixed; top:0; left:0; right:0; bottom:0; background:#fcfcfc; z-index:12000; justify-content:center; align-items:center;">
      <img src="{{ asset('images/SCM.gif') }}" alt="Loading..." style="width:120px;height:120px;">
    </div>

    <script>
    window.addEventListener('load', function() {
        const overlay = document.getElementById('loadingOverlay');
        if (overlay) {
            setTimeout(() => {
                overlay.style.display = 'none';
            }, 3000);
        }
    });
    </script>

    <!-- HOME SECTION -->
    <div id="home" class="data-section active-section">
        <div class="hero d-flex align-items-center">
            <div class="hero-overlay">
                <span class="circle" style="top: 10%; left: 5%; width: 40px; height: 40px;"></span>
                <span class="circle" style="top: 60%; left: 20%; width: 20px; height: 20px;"></span>
                <span class="square" style="top: 30%; left: 50%; width: 30px; height: 30px;"></span>
                <span class="triangle" style="top: 80%; left: 70%;"></span>
            </div>
            
            <div class="hero-content">
                <div class="row align-items-center">
    
                <div class="col-lg-6 text-center text-lg-start hero-text">
                    <h1 class="fw-bold text-white mb-3 typing-title">
                    </h1>
                    <p class="text-light lead mb-4">
                    Streamlining solo parent registration, verification, and assistance distribution
                    through digital transformation in partnership with the City Social Welfare
                    and Development Office.
                    </p>
        
                    <div class="d-flex gap-3">
            
                        <button class="btn btn-outline-light btn-lg px-4 rounded-pill" data-bs-toggle="modal" data-bs-target="#registerModal">
                            Register
                        </button>
                        
                        <button class="btn btn-outline-light btn-lg px-4 rounded-pill" data-bs-toggle="modal" data-bs-target="#registerModal">
                            Learn More 
                        </button>
                    </div>
                </div>
    
                <div class="col-lg-6 mt-5 mt-lg-0 hero-carousel">
                    <div id="heroCarousel" class="carousel slide shadow-lg rounded-4 overflow-hidden" data-bs-ride="carousel">
                    <div class="carousel-inner">
        
                        <div class="carousel-item active">
                        <img src="{{ asset('images/money.jpg') }}" class="d-block w-100" alt="Subsidy">
                        <div class="carousel-caption text-start">
                            <h5>Government Subsidy for Solo Parents 2024</h5>
                            <p>Eligible solo parents may receive ₱1,000 monthly assistance under RA 11861.</p>
                            <a href="#" class="btn btn-warning btn-sm fw-bold rounded-pill">Read more →</a>
                        </div>
                        </div>
        
                        <div class="carousel-item">
                        <img src="{{ asset('images/college.jpg') }}" class="d-block w-100" alt="Scholarships">
                        <div class="carousel-caption text-start">
                            <h5>Free Educational Programs for Solo Parent Children</h5>
                            <p>Scholarships and skills training are available through TESDA and DSWD.</p>
                            <a href="#" class="btn btn-warning btn-sm fw-bold rounded-pill">Learn more →</a>
                        </div>
                        </div>
        
                        <div class="carousel-item">
                        <img src="{{ asset('images/pregnant.jpg') }}" class="d-block w-100" alt="Leave Benefits">
                        <div class="carousel-caption text-start">
                            <h5>7-Day Parental Leave Benefits</h5>
                            <p>Solo parents working in companies are entitled to special leave benefits.</p>
                            <a href="#" class="btn btn-warning btn-sm fw-bold rounded-pill">Find out how →</a>
                        </div>
                        </div>
                    </div>
        
                    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                    </div>
                </div>
    
                </div>
        </div>
    </div>
    

    @if(session('register_success'))
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const registerSuccessModalEl = document.getElementById('registerSuccessModal');
        const registerSuccessModal = new bootstrap.Modal(registerSuccessModalEl);
        registerSuccessModal.show();
    
        window.openLogin = function() {
            const registerSuccessInstance = bootstrap.Modal.getInstance(registerSuccessModalEl);
            if(registerSuccessInstance) registerSuccessInstance.hide();

            const loginModalEl = document.getElementById('loginModal');
            const loginModal = new bootstrap.Modal(loginModalEl);
            loginModal.show();
        };
    });
    </script>
    @endif
        
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('registerForm');
        
            const passwordInput = form.password;
            const confirmInput = form.confirm_password;
            const emailInput = form.email;
            const usernameInput = form.username;
        
            const passwordHint = document.getElementById('passwordHint');
            const confirmHint = document.getElementById('confirmHint');
        
            const pwLength = document.getElementById('pw-length');
            const pwUpper = document.getElementById('pw-uppercase');
            const pwLower = document.getElementById('pw-lowercase');
            const pwNumber = document.getElementById('pw-number');
            const pwSpecial = document.getElementById('pw-special');
            const pwMatch = document.getElementById('pw-match');
        
            // Create hint boxes for username and email
            const usernameHint = document.createElement('div');
            usernameHint.className = 'password-hint';
            usernameHint.innerHTML = '<span id="username-status" class="invalid"></span>';
            usernameInput.parentElement.appendChild(usernameHint);
            const usernameStatus = document.getElementById('username-status');
        
            const emailHint = document.createElement('div');
            emailHint.className = 'password-hint';
            emailHint.innerHTML = '<span id="email-status" class="invalid"></span>';
            emailInput.parentElement.appendChild(emailHint);
            const emailStatus = document.getElementById('email-status');
        
            // Show password hints
            passwordInput.addEventListener('focus', () => passwordHint.classList.add('show'));
            passwordInput.addEventListener('blur', () => passwordHint.classList.remove('show'));
            confirmInput.addEventListener('focus', () => confirmHint.classList.add('show'));
            confirmInput.addEventListener('blur', () => confirmHint.classList.remove('show'));
        
            passwordInput.addEventListener('input', validatePassword);
            confirmInput.addEventListener('input', validateMatch);
        
            // Live Username & Email Check
            function checkAvailability(url, value, inputEl, statusEl, availableMsg, takenMsg) {
                if(value.trim() === ''){
                    inputEl.classList.remove('is-invalid','is-valid');
                    statusEl.textContent = '';
                    statusEl.className = 'invalid';
                    return;
                }
                fetch(url, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ value: value })
                })
                .then(res => res.json())
                .then(data => {
                    if(data.exists){
                        inputEl.classList.add('is-invalid');
                        inputEl.classList.remove('is-valid');
                        statusEl.textContent = takenMsg;
                        statusEl.className = 'invalid';
                    } else {
                        inputEl.classList.add('is-valid');
                        inputEl.classList.remove('is-invalid');
                        statusEl.textContent = availableMsg;
                        statusEl.className = 'valid';
                    }
                });
            }
        
            function debounce(fn, delay) {
                let timer;
                return function(...args) {
                    clearTimeout(timer);
                    timer = setTimeout(() => fn.apply(this, args), delay);
                };
            }
        
            // Username
            usernameInput.addEventListener('focus', () => {
                usernameInput.classList.add('is-invalid');
                usernameStatus.textContent = '';
                usernameStatus.className = 'invalid';
                usernameHint.classList.add('show');
            });
        
            usernameInput.addEventListener('input', debounce(() => {
                checkAvailability(
                    "{{ route('check.username') }}",
                    usernameInput.value,
                    usernameInput,
                    usernameStatus,
                    "Username is available.",
                    "Username is already taken."
                );
            }, 500));
        
            // Email
            emailInput.addEventListener('focus', () => {
                emailInput.classList.add('is-invalid');
                emailStatus.textContent = '';
                emailStatus.className = 'invalid';
                emailHint.classList.add('show');
            });
        
            emailInput.addEventListener('input', debounce(() => {
                checkAvailability(
                    "{{ route('check.email') }}",
                    emailInput.value,
                    emailInput,
                    emailStatus,
                    "Email is available.",
                    "Email is already taken."
                );
        
            }, 500));
        
            // Password Validation
            function validatePassword() {
                const val = passwordInput.value;
                pwLength.classList.toggle('valid', val.length >= 8); pwLength.classList.toggle('invalid', val.length < 8);
                pwUpper.classList.toggle('valid', /[A-Z]/.test(val)); pwUpper.classList.toggle('invalid', !/[A-Z]/.test(val));
                pwLower.classList.toggle('valid', /[a-z]/.test(val)); pwLower.classList.toggle('invalid', !/[a-z]/.test(val));
                pwNumber.classList.toggle('valid', /\d/.test(val)); pwNumber.classList.toggle('invalid', !/\d/.test(val));
                pwSpecial.classList.toggle('valid', /[!@#$%^&*]/.test(val)); pwSpecial.classList.toggle('invalid', !/[!@#$%^&*]/.test(val));
                validateMatch();
            }
        
            function validateMatch() {
                const match = passwordInput.value === confirmInput.value && passwordInput.value !== "";
                pwMatch.textContent = match ? "Passwords match" : "Passwords must match.";
                pwMatch.className = match ? "valid" : "invalid";
            }
        
            // Form Submission
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                let valid = true;
        
                const requiredFields = form.querySelectorAll('input[required]');
                requiredFields.forEach(field => {
                    const feedback = field.parentElement.querySelector('.password-hint')?.querySelector('span') || field.nextElementSibling;
                    if (!field.value.trim()) {
                        field.classList.add('is-invalid');
                        if (feedback) feedback.textContent = "This field is required.";
                        valid = false;
                    }
                });
        
                // Password rules
                const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*]).{8,}$/;
                if (!passwordPattern.test(passwordInput.value)) {
                    passwordInput.classList.add('is-invalid');
                    valid = false;
                }
        
                // Confirm password
                if (passwordInput.value !== confirmInput.value) {
                    confirmInput.classList.add('is-invalid');
                    valid = false;
                }
        
                // Final check for username/email being red (taken)
                if(usernameInput.classList.contains('is-invalid') || emailInput.classList.contains('is-invalid')) {
                    valid = false;
                }
        
                if(valid) form.submit();
            });
        });
        </script>
    
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const titleText = @json($system->system_full_name);
            const titleEl = document.querySelector('.typing-title');
            let index = 0;
    
            function typeTitle() {
                if (index < titleText.length) {
                    titleEl.textContent += titleText.charAt(index);
                    index++;
                    setTimeout(typeTitle, 70);
                }
            }
    
            if (titleText) {
                typeTitle();
            }
        });
    </script>
    
        <div id="about-home" style="padding:70px 0;">
            <div class="container" style="margin:auto;">
                <div class="text-center mb-5">
                    <h2 style="color:#2f2f2f;" class="fw-bold">About Solo Parent Act (RA 11861)</h2>
                    <p style="color:#555; font-size:18px; max-width:1000px; margin:auto;"> The Expanded Solo Parents Welfare Act (Republic Act No. 11861) strengthens the support, rights, and welfare of solo parents in the Philippines. It expands benefits, eligibility, and access to government services.</p>
                </div>
        
                <!-- CARD 1 -->
                <div class="about-row left">
                    <div class="about-image">
                        <div class="shape-bg"></div>
                        <img src="{{ asset('images/law.png') }}" alt="About RA 11861" class="floating-img">
                    </div>
                    <div class="about-text card shadow-sm p-4">
                        <h4>About RA 11861</h4>
                        <p>
                        Republic Act No. 11861, or the Expanded Solo Parents Welfare Act, ensures that solo parents receive adequate government assistance, flexible work arrangements, and protection from discrimination, while promoting their well-being and empowerment.
                        </p>
                    </div>
                </div>
        
                <!-- CARD 2 -->
                <div class="about-row right">
                    <div class="about-image">
                        <div class="shape-bg"></div>
                        <img src="{{ asset('images/mama.png') }}" alt="Qualified Solo Parents" class="floating-img">
                    </div>
                    <div class="about-text card shadow-sm p-4">
                        <h4>Who are Qualified as Solo Parents?</h4>
                        <ul>
                        <li>Women who give birth as a result of rape or other crimes against chastity and who raise the child.</li>
                        <li>Parents who became solo due to death, detention, incapacity, or abandonment.</li>
                        <li>Unmarried mothers or fathers who choose to raise their child.</li>
                        <li>Any person solely providing parental care and support.</li>
                        <li>Legal guardians, adoptive, or foster parents solely providing care.</li>
                        </ul>
                    </div>
                </div>
    
                <!-- CARD 3 -->
                <div class="about-row left">
                    <div class="about-image">
                        <div class="shape-bg"></div>
                        <img src="{{ asset('images/help.png') }}" alt="Solo Parent Benefits" class="floating-img">
                    </div>
                    <div class="about-text card shadow-sm p-4">
                        <h4>Benefits for Solo Parents</h4>
                        <ul>
                        <li>Up to 7 days parental leave per year for employed solo parents.</li>
                        <li>Flexible work schedule and protection from discrimination.</li>
                        <li>Educational assistance for children.</li>
                        <li>Health and medical support programs.</li>
                        <li>Livelihood, counseling, and government aid.</li>
                        <li>Discounts and VAT exemption on baby essentials (for low-income solo parents).</li>
                        </ul>
                    </div>
                </div>
    
                <!-- CARD 4 - REQUIREMENTS -->
                <div class="about-row right">
                    <div class="about-image">
                        <div class="shape-bg"></div>
                        <img src="{{ asset('images/file-folder.png') }}" alt="Requirements" class="floating-img">
                    </div>
                    <div class="about-text card shadow-sm p-4 req-card">
                        <h4>Requirements for Each Category</h4>
                        <p>Click each category to expand the list of requirements:</p>
        
                    <div class="req-container">
                        <div class="req-grid">
                        <!-- Left Column -->
                        <div class="req-col">
                        <div class="req-item"><button class="req-toggle">1. As a Consequence of Rape</button><div class="req-content"><p>• Birth Certificate/s of the child<br>• Complaint Affidavit<br>• Sworn affidavit declaring not cohabiting<br>• Medical Record<br>• Barangay Affidavit<br>• 2 pcs 1x1 ID picture</p></div></div>
                        <div class="req-item"><button class="req-toggle">2. Death of the Spouse</button><div class="req-content"><p>• Death Certificate<br>• Barangay certification stating solo parenting responsibility</p></div></div>
                        <div class="req-item"><button class="req-toggle">3. Detained Spouse</button><div class="req-content"><p>• Certificate of Detention<br>• Court or Police record<br>• Barangay certification of solo parenting</p></div></div>
                        <div class="req-item"><button class="req-toggle">4. Physical or Mental Incapacity of Spouse</button><div class="req-content"><p>• Medical Certificate from government hospital<br>• Barangay certification<br>• Valid ID</p></div></div>
                        <div class="req-item"><button class="req-toggle">5. Legal Separation / Annulment</button><div class="req-content"><p>• Court Decision<br>• Barangay certification stating custody of children<br>• 2 pcs ID picture</p></div></div>
                        <div class="req-item"><button class="req-toggle">6. Abandonment by Spouse</button><div class="req-content"><p>• Barangay or Police blotter<br>• Sworn affidavit<br>• Certification of non-cohabitation</p></div></div>
                        </div>
        
                        <!-- Right Column -->
                        <div class="req-col">
                        <div class="req-item"><button class="req-toggle">7. Unmarried Mother/Father</button><div class="req-content"><p>• Birth Certificate of child<br>• CENOMAR<br>• Affidavit of Solo Parenting</p></div></div>
                        <div class="req-item"><button class="req-toggle">8. Legal Guardian</button><div class="req-content"><p>• Court Appointment as Guardian<br>• Barangay certification of custody</p></div></div>
                        <div class="req-item"><button class="req-toggle">9. Foster or Adoptive Parent</button><div class="req-content"><p>• DSWD Certification<br>• Adoption papers<br>• Barangay certification</p></div></div>
                        <div class="req-item"><button class="req-toggle">10. Spouse Working Abroad (6+ Months)</button><div class="req-content"><p>• POEA/Company Certification<br>• Passport/Travel records<br>• Barangay certification of solo responsibility</p></div></div>
                        <div class="req-item"><button class="req-toggle">11. Abandoned by Partner (Unmarried)</button><div class="req-content"><p>• Barangay blotter<br>• Sworn affidavit<br>• Certification of non-cohabitation for at least 1 year</p></div></div>
                        <div class="req-item"><button class="req-toggle">12. Other Circumstances (Court Declaration)</button><div class="req-content"><p>• Court order or certification<br>• Barangay certification<br>• Valid ID & affidavit</p></div></div>
                        </div>
                    </div>
                    </div>
        
                    </div>
                </div>
                
    
            </div>
        </div>
        
        <script>
        document.addEventListener("DOMContentLoaded", function() {
            const toggles = document.querySelectorAll(".req-toggle");
        
            toggles.forEach(btn => {
                btn.addEventListener("click", function() {
                    const content = this.nextElementSibling;
                    const isVisible = content.style.maxHeight && content.style.maxHeight !== "0px";
        
                    if (isVisible) {
                        // Slide up (hide)
                        content.style.maxHeight = "0";
                        content.style.padding = "0 1rem";
                    } else {
                        // Slide down (show)
                        content.style.maxHeight = content.scrollHeight + "px";
                        content.style.padding = "0.5rem 1rem";
                    }
                });
            });
        });
        </script>

        
        <div id="articles-home" class="py-5">
            <div class="container">
                <div class="text-center mb-5">
                    <h2 class="fw-bold">Articles & Updates</h2>
                    <p style="color:#555; font-size:18px; margin-bottom: 10px;"> Stay informed about the latest news, programs, and benefits for solo parents in the Philippines.</p>
                </div>
            
                <div class="articles-row row g-3">
                    <div class="col-12">
                        <div class="card flex-row shadow-sm article-card align-items-center p-3">
                            <div class="article-img-wrapper">
                                <img src="{{ asset('images/nologo.png') }}" alt="Monthly Cash Assistance" class="article-img">
                            </div>
                              <div class="card-body p-0">
                                <h5 class="card-title">Monthly Cash Assistance for Solo Parents</h5>
                                <p class="card-text mb-2"> The government provides ₱1,000 monthly cash aid to qualified solo parents under RA 11861.</p>
                                <a href="https://lawphil.net/statutes/repacts/ra2022/ra_11861_2022.html" target="_blank" class="btn btn-sm btn-primary">Read More</a>
                            </div>
                        </div>
                    </div>
    
                    <div class="col-12">
                        <div class="card flex-row shadow-sm article-card align-items-center p-3">
                            <div class="article-img-wrapper">
                                <img src="{{ asset('images/nologo.png') }}" alt="Scholarships & TESDA Programs" class="article-img">
                            </div>
                            <div class="card-body p-0">
                                <h5 class="card-title">Scholarships & TESDA Programs</h5>
                                <p class="card-text mb-2"> Learn how solo parent children can access scholarships and training programs to build their future.</p> 
                                <a href="https://pia.gov.ph/features/benefits-that-matter-for-solo-parents" target="_blank" class="btn btn-sm btn-primary">Learn More</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <div class="card flex-row shadow-sm article-card align-items-center p-3">
                            <div class="article-img-wrapper me-3">
                                <img src="{{ asset('images/nologo.png') }}" alt="7-Day Parental Leave Explained" class="article-img">
                            </div>
                            <div class="card-body p-0">
                                <h5 class="card-title">7-Day Parental Leave Explained</h5>
                                <p class="card-text mb-2"> Employed solo parents are entitled to 7 days of paid leave each year. Here’s how to apply.</p>
                                <a href="https://elibrary.judiciary.gov.ph/thebookshelf/showdocs/2/95472" target="_blank" class="btn btn-sm btn-primary">Find Out</a>
                            </div>
                        </div>
                    </div>
                </div>
                <button id="load-more-articles" class="btn btn-primary mt-4">View More Articles</button>
            </div>
        </div>
        

        <div id="gallery-home" class="py-5">
            <div class="container">
        
                <div class="text-center mb-5">
                    <h2 class="fw-bold">Gallery</h2>
                    <p style="color:#555; font-size:18px; margin-bottom: 10px;">A glimpse of our activities, programs, and events for solo parents in the City of General Trias.</p>
                </div>
                
                <div class="gallery-wrapper">
                    <div class="gallery-carousel">
                        <div class="gallery-item">
                            <img src="{{ asset('images/image1.png') }}" alt="Community Outreach">
                            <h6>Community Outreach Program</h6>
                            <p>Providing aid and support to solo parent beneficiaries.</p>
                        </div>
        
                        <div class="gallery-item">
                            <img src="{{ asset('images/image2.png') }}" alt="Orientation Seminar">
                            <h6>Solo Parent Orientation Seminar</h6>
                            <p>Educating solo parents on their rights and available assistance.</p>
                        </div>
        
                        <div class="gallery-item">
                            <img src="{{ asset('images/image3.png') }}" alt="Financial Aid">
                            <h6>Distribution of Financial Aid</h6>
                            <p>Supporting families through cash and in-kind assistance.</p>
                        </div>
        
                        <div class="gallery-item">
                            <img src="{{ asset('images/image4.png') }}" alt="Health Check Program">
                            <h6>Health Check Program</h6>
                            <p>Free medical consultations and services for solo parents.</p>
                        </div>
        
                        <div class="gallery-item">
                            <img src="{{ asset('images/image5.png') }}" alt="Training Workshop">
                            <h6>Livelihood Training Workshop</h6>
                            <p>Enhancing livelihood and employability skills.</p>
                        </div>
        
                        <div class="gallery-item">
                            <img src="{{ asset('images/image.png') }}" alt="Family Day Celebration">
                            <h6>Solo Parent Family Day</h6>
                            <p>Celebrating unity and strength among solo parent families.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div id="process-home" class="py-5">
            <div class="container">
                
                <div class="text-center mb-5">
                    <h2 class="fw-bold">Application Process for Eligible Solo Parent</h2>
                    <p style="color:#555; font-size:18px; margin-bottom: 10px;"> Our streamlined process ensures that eligible solo parents can access assistance quickly and efficiently. Follow these simple steps to get the support your family needs.</p>
                </div>
        
                <div class="process-row row g-4 justify-content-center">
                    <div class="col-md-4 col-sm-6 process-col">
                        <div class="card shadow-sm border-0 text-center h-100 process-card">
                            <div class="card-body">
                                <div class="step-circle mb-3">1</div>
                                <h5 class="fw-bold text-primary">Online Application</h5>
                                <p class="small text-muted">Fill out the application form through our online portal.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 process-col">
                        <div class="card shadow-sm border-0 text-center h-100 process-card">
                            <div class="card-body">
                                <div class="step-circle mb-3">2</div>
                                <h5 class="fw-bold text-primary">Upload Documents</h5>
                                <p class="small text-muted">Attach the necessary supporting documents for verification.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 process-col">
                        <div class="card shadow-sm border-0 text-center h-100 process-card">
                            <div class="card-body">
                                <div class="step-circle mb-3">3</div>
                                <h5 class="fw-bold text-primary">Staff Verification</h5>
                                <p class="small text-muted">Our staff will review your application and validate your details.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 process-col">
                        <div class="card shadow-sm border-0 text-center h-100 process-card">
                            <div class="card-body">
                                <div class="step-circle mb-3">4</div>
                                <h5 class="fw-bold text-primary">Home Visitation</h5>
                                <p class="small text-muted">Eligibility verification in progress.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 process-col">
                        <div class="card shadow-sm border-0 text-center h-100 process-card">
                            <div class="card-body">
                                <div class="step-circle mb-3">5</div>
                                <h5 class="fw-bold text-primary">Application Approval</h5>
                                <p class="small text-muted">Once verified, your application will be approved by the barangay.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 process-col">
                        <div class="card shadow-sm border-0 text-center h-100 process-card">
                            <div class="card-body">
                                <div class="step-circle mb-3">6</div>
                                <h5 class="fw-bold text-primary">Access Benefits</h5>
                                <p class="small text-muted">Log in to your account to view and access available benefits.</p>
                            </div>
                        </div>
                    </div>
                      
                </div>
            </div>
        </div>
    
        <script>
            const processCards = document.querySelectorAll('.process-card');
            
            function revealOnScroll() {
              const windowHeight = window.innerHeight;
              processCards.forEach(card => {
                const elementTop = card.getBoundingClientRect().top;
                if(elementTop < windowHeight - 100) {
                  card.classList.add('visible');
                }
              });
            }
            
            window.addEventListener('scroll', revealOnScroll);
            window.addEventListener('load', revealOnScroll);
        </script>
    
        <div id="track-home" class="py-5">
            <div class="container">
                
                <div class="text-center mb-5">
                    <h2 class="fw-bold">Track Application</h2>
                    <p style="color:#555; font-size:18px; margin-bottom: 10px;"> Track your application form</p>
                </div>
                
                <div class="card p-4 p-md-5 shadow-sm border-0" style="border-radius: 15px;">
                    <div class="row align-items-start">
    
                <!-- 🔹 LEFT SIDE - SEARCH FORM -->
                <div class="col-md-5">
                <h3 class="mb-4 fw-bold text-primary">
                    <i class="fas fa-search me-2"></i> Track Application
                </h3>
    
                <label for="trackingId" class="form-label fw-semibold">Reference Number</label>
                <input type="text" id="trackingId" class="form-control mb-2"
                    placeholder="Enter your reference number here"/>
    
                <small class="text-muted d-block mb-3">
                    ✅ Example: <strong>SP-2024-00001</strong> &nbsp;&nbsp;❌ Wrong: <strong>12345</strong>
                </small>
    
                <button id="trackBtn" class="btn btn-primary w-100 fw-bold py-2">
                    <i class="fas fa-paper-plane me-2"></i> Track Application Status
                </button>

                <div id="trackNotice" class="alert alert-danger mt-3 d-none" role="alert">
                    ⚠️ Invalid Reference Number. Please check and try again.
                </div>
                </div>
  
                <div class="col-md-1 d-none d-md-flex justify-content-center">
                <div style="width: 2px; height: 100%; background: #dee2e6;"></div>
                </div>
    
                <!-- 🔹 RIGHT SIDE - TIMELINE -->
                <div class="col-md-6 mt-5 mt-md-0">
                <h5 class="mb-4 fw-bold text-secondary"><i class="fas fa-clipboard-list me-2"></i> Application Process Timeline</h5>
    
                    <ul class="list-unstyled timeline">
                    <li class="fade-step">
                        <span class="dot"><i class="fas fa-file-alt"></i></span>
                        <div>
                            <strong>Application Submitted</strong>
                            <p class="small text-muted">Your application has been received.</p>
                        </div>
                    </li>
                    <li class="fade-step">
                        <span class="dot"><i class="fas fa-search"></i></span>
                        <div>
                            <strong>Document Review</strong>
                            <p class="small text-muted">Documents are being verified.</p>
                        </div>
                    </li>
                    <li class="fade-step">
                        <span class="dot"><i class="fas fa-user-check"></i></span>
                        <div>
                            <strong>Background Check</strong>
                            <p class="small text-muted">Eligibility verification in progress.</p>
                        </div>
                    </li>
                    <li class="fade-step">
                        <span class="dot"><i class="fas fa-cogs"></i></span>
                        <div>
                            <strong>Approval Processing</strong>
                            <p class="small text-muted">Final approval by authorized personnel.</p>
                        </div>
                    </li>
                    <li class="fade-step">
                        <span class="dot"><i class="fas fa-id-card"></i></span>
                        <div>
                            <strong>ID Generation</strong>
                            <p class="small text-muted">Solo Parent ID is being prepared.</p>
                        </div>
                    </li>
                    <li class="fade-step">
                        <span class="dot"><i class="fas fa-box-open"></i></span>
                        <div>
                            <strong>Ready for Pickup</strong>
                            <p class="small text-muted">Your Solo Parent ID is ready.</p>
                        </div>
                    </li>
                </ul>
                </div>
            </div>
            </div>
        </div>
        </div>
        
        <div id="faq-home" class="py-5">
            <div class="mb-5  text-center">
                <h2 class="fw-bold ">Frequently Asked Questions</h2>
                <p style="color:#555; max-width:1000px; font-size:18px; margin-bottom: 10px; margin:auto;" >
                    Here are some of the most common questions about the Solo Parent Act and the Solo Parent Identification Card (SPIC). Click each question below to view the answer.
                </p>
            </div>

    
            <div class="container bg-white shadow-lg p-5" style="border-radius: 10px;">
                <div class="accordion" id="faqHomeAccordion" style="margin: auto;"></div>
            </div>
        </div>
    
        <div id="contact-home">
            <div class="container">
                <div class="text-center mb-5">
                    <h2 class="fw-bold">Contact Us</h2>
                    <p style="color:#555; font-size:18px; margin-bottom: 10px;"> For assistance, inquiries, or technical support, you can reach us through the following channels: </p>
                </div>
                
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="card shadow-sm h-100 text-center border-0">
                            <div class="card-body p-4">
                                <div class="mb-3 fs-2 text-primary">📍</div>
                                <h5 class="fw-bold">Office Address</h5>
                                <p class="text-muted small mb-0">
                                    City Social Welfare & Development Office <br>
                                    2nd Floor, City Hall of General Trias, Cavite
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card shadow-sm h-100 text-center border-0">
                            <div class="card-body p-4">
                                <div class="mb-3 fs-2 text-success">📧</div>
                                <h5 class="fw-bold">Email Us</h5>
                                <p class="text-muted small mb-1">For technical support:</p>
                                <a href="mailto:support.solocare@gmail.com" class="d-block text-decoration-none fw-semibold"> support.solocare@gmail.com</a>
                                <p class="text-muted small mb-1 mt-2">General inquiries:</p>
                                <a href="mailto:www.solocaresystem.com" class="d-block text-decoration-none fw-semibold">
                                    www.solocaresystem.com
                                </a>
                            </div>
                        </div>
                    </div>
    
                    <div class="col-md-4">
                        <div class="card shadow-sm h-100 text-center border-0">
                            <div class="card-body p-4">
                                <div class="mb-3 fs-2 text-danger">📞</div>
                                <h5 class="fw-bold">Hotline</h5>
                                <p class="text-muted small mb-1">CSWD Assistance Line</p>
                                <p class="fw-semibold mb-2">(02) 8-CSWD-123</p>
                                <p class="text-muted small">Monday – Friday, 8:00 AM – 5:00 PM</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
    </div>

    <!-- ABOUT SECTION -->
    <div id="about" class="data-section" style="display:none; padding:70px 0;">
            <div class="container" style="max-width:1200px; margin:auto;">
        
                <div class="text-center mb-5 mt-5">
                    <h2 style="color:#2f2f2f;" class="fw-bold">About Solo Parent Act (RA 11861)</h2>
                    <p style="color:#555; font-size:18px; max-width:1000px; margin:auto;"> The Expanded Solo Parents Welfare Act (Republic Act No. 11861) strengthens the support, rights, and welfare of solo parents in the Philippines. It expands benefits, eligibility, and access to government services.</p>
                </div>
    
                <!-- CARD 1 -->
                <div class="about-row left">
                  <div class="about-image">
                    <div class="shape-bg"></div>
                    <img src="{{ asset('images/law.png') }}" alt="About RA 11861" class="floating-img">
                  </div>
                  <div class="about-text card shadow-sm p-4">
                    <h4>About RA 11861</h4>
                    <p>
                      {!! $aboutContent ?? 'Republic Act No. 11861, or the Expanded Solo Parents Welfare Act, ensures that solo parents receive adequate government assistance, flexible work arrangements, and protection from discrimination, while promoting their well-being and empowerment.' !!}
                    </p>
                  </div>
                </div>
        
                <!-- CARD 2 -->
                <div class="about-row right">
            <div class="about-image">
                <div class="shape-bg"></div>
                <img src="{{ asset('images/mama.png') }}" alt="Qualified Solo Parents" class="floating-img">
            </div>
            <div class="about-text card shadow-sm p-4">
                <h4>Who are Qualified as Solo Parents?</h4>
                <div>
                    {!! $about->content_qualified ?? '
                        <ul>
                            <li>Women who give birth as a result of rape or crimes against chastity.</li>
                            <li>Parents who became solo due to death, detention, incapacity, or abandonment.</li>
                            <li>Unmarried mothers or fathers raising their child alone.</li>
                            <li>Any person solely providing parental care.</li>
                            <li>Legal guardian, adoptive or foster parent providing full care.</li>
                        </ul>
                    ' !!}
                </div>
            </div>
        </div>
        
                <!-- CARD 3  -->
                <div class="about-row left">
            <div class="about-image">
                <div class="shape-bg"></div>
                <img src="{{ asset('images/help.png') }}" alt="Solo Parent Benefits" class="floating-img">
            </div>
            <div class="about-text card shadow-sm p-4">
                <h4>Benefits for Solo Parents</h4>
                <div>
                    {!! $about->content_benefits ?? '
                        <ul>
                            <li>7 days parental leave per year.</li>
                            <li>Flexible work schedule.</li>
                            <li>Educational assistance for children.</li>
                            <li>Medical and health support.</li>
                            <li>Livelihood and government aid.</li>
                            <li>Discounts on baby essentials (for low-income solo parents).</li>
                        </ul>
                    ' !!}
                </div>
            </div>
        </div>
        
                <!-- CARD 4 -->
                <div class="about-row right">
              <div class="about-image">
                <div class="shape-bg"></div>
                <img src="{{ asset('images/file-folder.png') }}" alt="Requirements" class="floating-img">
              </div>
              <div class="about-text card shadow-sm p-4 req-card">
                <h4>Requirements for Each Category</h4>
                <p>Click each category to expand the list of requirements:</p>
        
                <div class="req-container">
                  <div class="req-grid">
                    <!-- Left Column -->
                    <div class="req-col">
                      @foreach($requirementsLeft as $req)
                      <div class="req-item">
                        <button class="req-toggle">{{ $req['title'] }}</button>
                        <div class="req-content">
                          <p>{!! $req['content'] !!}</p>
                        </div>
                      </div>
                      @endforeach
                    </div>
        
                    <!-- Right Column -->
                    <div class="req-col">
                      @foreach($requirementsRight as $req)
                      <div class="req-item">
                        <button class="req-toggle">{{ $req['title'] }}</button>
                        <div class="req-content">
                          <p>{!! $req['content'] !!}</p>
                        </div>
                      </div>
                      @endforeach
                    </div>
                  </div>
                </div>
        
              </div>
            </div>
        
          </div>
        </div>

    <script>
    document.querySelectorAll('.req-toggle').forEach(btn=>{
      btn.addEventListener('click', ()=>{
        btn.classList.toggle('active');
        btn.nextElementSibling.classList.toggle('show');
      });
    });
    </script>
    
    <!-- ARICLES SECTION -->
    <div id="articles" class="data-section py-5" style="display:none;">
        <div class="container">
            <div class="text-center mb-5 mt-5">
                <h2 class="fw-bold">Articles & Updates</h2>
                <p style="color:#555; font-size:18px; margin-bottom: 10px;"> Stay informed about the latest news, programs, and benefits for solo parents in the Philippines.</p>
            </div>
    
                <div class="row g-4">
                    <!-- Article 1 -->
                    <div class="col-md-4">
                        <div class="card shadow-sm h-100 border-0">
                            <img src="{{ asset('images/nologo.png') }}" class="card-img-top" alt="Cash Assistance">
                            <div class="card-body">
                                <h5 class="fw-bold">Monthly Cash Assistance for Solo Parents</h5>
                                <p class="text-muted small">
                                    The government provides ₱1,000 monthly cash aid to qualified solo parents under RA 11861.
                                </p>
                                <a href="#" class="btn btn-sm btn-primary">Read More</a>
                            </div>
                        </div>
                    </div>
    
                    <!-- Article 2 -->
                    <div class="col-md-4">
                        <div class="card shadow-sm h-100 border-0">
                            <img src="{{ asset('images/nologo.png') }}" class="card-img-top" alt="Scholarships">
                            <div class="card-body">
                                <h5 class="fw-bold">Scholarships & TESDA Programs</h5>
                                <p class="text-muted small">
                                    Learn how solo parent children can access scholarships and training programs to build their future.
                                </p>
                                <a href="#" class="btn btn-sm btn-primary">Learn More</a>
                            </div>
                        </div>
                    </div>
    
                    <!-- Article 3 -->
                    <div class="col-md-4">
                        <div class="card shadow-sm h-100 border-0">
                            <img src="{{ asset('images/nologo.png') }}" class="card-img-top" alt="Leave Benefits">
                            <div class="card-body">
                                <h5 class="fw-bold">7-Day Parental Leave Explained</h5>
                                <p class="text-muted small">
                                    Employed solo parents are entitled to 7 days of paid leave each year. Here’s how to apply.
                                </p>
                                <a href="#" class="btn btn-sm btn-primary">Find Out</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const loadMoreBtn = document.getElementById("load-more-articles");
        const articlesSection = document.getElementById("articles");
    
        loadMoreBtn.addEventListener("click", function() {
            // Show the articles section
            articlesSection.style.display = "block";
    
            // Optionally hide the current section if needed
            // document.getElementById("home").style.display = "none";
    
            // If you have a single-page nav system, you can trigger the "active" section
            // Example:
            document.querySelectorAll('.data-section').forEach(sec => {
                sec.style.display = 'none'; // hide all sections
            });
            articlesSection.style.display = 'block'; // show articles section
        });
    });
    </script>

    <!-- GALLERY SECTION -->
    <div id="gallery" class="data-section py-5" style="display:none;">
        <div class="container">
                <div class="text-center mb-5">
                    <h2 class="fw-bold">Gallery</h2>
                    <p style="color:#555; font-size:18px; margin-bottom: 10px;">A glimpse of our activities, programs, and events for solo parents in the City of General Trias.</p>
                </div>
    
            <div class="gallery-wrapper">
                <button class="gallery-btn left" id="prevBtn">&#10094;</button>
                <div class="gallery-carousel">
                    <div class="gallery-item">
                        <img src="{{ asset('images/image1.png') }}" alt="Community Outreach">
                        <h6>Community Outreach Program</h6>
                        <p>Providing aid and support to solo parent beneficiaries.</p>
                    </div>
    
                    <div class="gallery-item">
                        <img src="{{ asset('images/image2.png') }}" alt="Orientation Seminar">
                        <h6>Solo Parent Orientation Seminar</h6>
                        <p>Educating solo parents on their rights and available assistance.</p>
                    </div>
    
                    <div class="gallery-item">
                        <img src="{{ asset('images/image3.png') }}" alt="Financial Aid">
                        <h6>Distribution of Financial Aid</h6>
                        <p>Supporting families through cash and in-kind assistance.</p>
                    </div>
    
                    <div class="gallery-item">
                        <img src="{{ asset('images/image4.png') }}" alt="Health Check Program">
                        <h6>Health Check Program</h6>
                        <p>Free medical consultations and services for solo parents.</p>
                    </div>
    
                    <div class="gallery-item">
                        <img src="{{ asset('images/image5.png') }}" alt="Training Workshop">
                        <h6>Livelihood Training Workshop</h6>
                        <p>Enhancing livelihood and employability skills.</p>
                    </div>
    
                    <div class="gallery-item">
                        <img src="{{ asset('images/image.png') }}" alt="Family Day Celebration">
                        <h6>Solo Parent Family Day</h6>
                        <p>Celebrating unity and strength among solo parent families.</p>
                    </div>
                </div>
                <button class="gallery-btn right" id="nextBtn">&#10095;</button>
            </div>
        </div>
    </div>

    <script>
    function updateGallery() {
        const isDesktopLikeMobile = window.innerWidth >= 769;
    
        items.forEach(item => {
            item.classList.remove('left', 'center', 'right', 'hidden');
            item.style.transform = 'none';
            item.style.opacity = '1';
        });
    
        // Stop carousel behavior on desktop
        if (isDesktopLikeMobile) return;
    }
    
    </script>

    <!-- APPLICATION PROCESS -->
    <div id="process" class="data-section py-5" style="display:none;">
        <div class="container">
            <div class="text-center mb-5 mt-5">
                <h2 class="fw-bold">Application Process for Eligible Solo Parent</h2>
                <p style="color:#555; font-size:18px; margin-bottom: 10px;"> Our streamlined process ensures that eligible solo parents can access assistance quickly and efficiently. Follow these simple steps to get the support your family needs.</p>
            </div>
    
            <div class="row g-4 justify-content-center">
                <!-- Step 1 -->
                <div class="col-md-4 col-sm-6">
                    <div class="card shadow-sm border-0 text-center h-100 process-card">
                        <div class="card-body">
                            <div class="step-circle mb-3">1</div>
                            <h5 class="fw-bold text-primary">Online Application</h5>
                            <p class="small text-muted">Fill out the application form through our online portal.</p>
                        </div>
                    </div>
                </div>
    
                <!-- Step 2 -->
                <div class="col-md-4 col-sm-6">
                    <div class="card shadow-sm border-0 text-center h-100 process-card">
                        <div class="card-body">
                            <div class="step-circle mb-3">2</div>
                            <h5 class="fw-bold text-primary">Upload Documents</h5>
                            <p class="small text-muted">Attach the necessary supporting documents for verification.</p>
                        </div>
                    </div>
                </div>
    
                <!-- Step 3 -->
                <div class="col-md-4 col-sm-6">
                    <div class="card shadow-sm border-0 text-center h-100 process-card">
                        <div class="card-body">
                            <div class="step-circle mb-3">3</div>
                            <h5 class="fw-bold text-primary">Staff Verification</h5>
                            <p class="small text-muted">Our staff will review your application and validate your details.</p>
                        </div>
                    </div>
                </div>
    
                <!-- Step 4 -->
                <div class="col-md-4 col-sm-6">
                    <div class="card shadow-sm border-0 text-center h-100 process-card">
                        <div class="card-body">
                            <div class="step-circle mb-3">4</div>
                            <h5 class="fw-bold text-primary">Background Check</h5>
                            <p class="small text-muted">Eligibility verification in progress.</p>
                        </div>
                    </div>
                </div>
    
                <!-- Step 5 -->
                <div class="col-md-4 col-sm-6">
                    <div class="card shadow-sm border-0 text-center h-100 process-card">
                        <div class="card-body">
                            <div class="step-circle mb-3">5</div>
                            <h5 class="fw-bold text-primary">Account Creation</h5>
                            <p class="small text-muted">Your SoloCare account will be created for tracking benefits.</p>
                        </div>
                    </div>
                </div>
    
                <!-- Step 6 -->
                <div class="col-md-4 col-sm-6">
                    <div class="card shadow-sm border-0 text-center h-100 process-card">
                        <div class="card-body">
                            <div class="step-circle mb-3">6</div>
                            <h5 class="fw-bold text-primary">Access Benefits</h5>
                            <p class="small text-muted">Log in to your account to view and access available benefits.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- TRACK APPLICATION -->
    <div id="track" class="data-section py-5" style="display:none;">
      <div class="container">
        <div class="card p-4 p-md-5 shadow-sm border-0 mt-5" style="border-radius: 15px;">
          <div class="row align-items-start">
    
            <!-- 🔹 LEFT SIDE - SEARCH FORM -->
            <div class="col-md-5">
              <h3 class="mb-4 fw-bold text-primary">
                <i class="fas fa-search me-2"></i> Track Application
              </h3>
    
              <label for="trackingId" class="form-label fw-semibold">Reference Number</label>
              <input type="text" id="trackingId" class="form-control mb-2"
                placeholder="Enter your reference number here"/>
    
              <small class="text-muted d-block mb-3">
                ✅ Example: <strong>SP-2024-00001</strong> &nbsp;&nbsp;❌ Wrong: <strong>12345</strong>
              </small>
    
              <button id="trackBtn" class="btn btn-primary w-100 fw-bold py-2">
                <i class="fas fa-paper-plane me-2"></i> Track Application Status
              </button>
    
              <!-- ⚠️ Notice Message -->
              <div id="trackNotice" class="alert alert-danger mt-3 d-none" role="alert">
                ⚠️ Invalid Reference Number. Please check and try again.
              </div>
            </div>
    
            <!-- 🔸 DIVIDER -->
            <div class="col-md-1 d-none d-md-flex justify-content-center">
              <div style="width: 2px; height: 100%; background: #dee2e6;"></div>
            </div>
    
            <!-- 🔹 RIGHT SIDE - TIMELINE -->
            <div class="col-md-6 mt-5 mt-md-0">
              <h5 class="mb-4 fw-bold text-secondary">
                <i class="fas fa-clipboard-list me-2"></i> Application Process Timeline
              </h5>
    
              <ul class="list-unstyled timeline">
                <li class="fade-step">
                  <span class="dot"><i class="fas fa-file-alt"></i></span>
                  <div>
                    <strong>Application Submitted</strong>
                    <p class="small text-muted">Your application has been received.</p>
                  </div>
                </li>
                <li class="fade-step">
                  <span class="dot"><i class="fas fa-search"></i></span>
                  <div>
                    <strong>Document Review</strong>
                    <p class="small text-muted">Documents are being verified.</p>
                  </div>
                </li>
                <li class="fade-step">
                  <span class="dot"><i class="fas fa-user-check"></i></span>
                  <div>
                    <strong>Background Check</strong>
                    <p class="small text-muted">Eligibility verification in progress.</p>
                  </div>
                </li>
                <li class="fade-step">
                  <span class="dot"><i class="fas fa-cogs"></i></span>
                  <div>
                    <strong>Approval Processing</strong>
                    <p class="small text-muted">Final approval by authorized personnel.</p>
                  </div>
                </li>
                <li class="fade-step">
                  <span class="dot"><i class="fas fa-id-card"></i></span>
                  <div>
                    <strong>ID Generation</strong>
                    <p class="small text-muted">Solo Parent ID is being prepared.</p>
                  </div>
                </li>
                <li class="fade-step">
                  <span class="dot"><i class="fas fa-box-open"></i></span>
                  <div>
                    <strong>Ready for Pickup</strong>
                    <p class="small text-muted">Your Solo Parent ID is ready.</p>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- FAQ Section -->
    <div id="faq" class="data-section py-5" style="display:none;">
        <div class="text-center mb-4 mt-5">
            <h1 style="font-weight:700; color:#2f2f2f;">Frequently Asked Questions</h1>
            <p style="color:#555; max-width:1000px; margin:auto; font-size:20px;">
                Here are some of the most common questions about the Solo Parent Act and the Solo Parent Identification Card (SPIC).
                Click each question below to view the answer.
            </p>
        </div>
    
        <div class="container bg-white shadow-lg p-5" style="border-radius: 10px;">
            <div class="accordion" id="faqAccordion" style="margin: auto;">
    
    
            </div>
        </div>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', () => {
    
        const faqAccordion = document.getElementById('faqAccordion'); 
        const faqHomeAccordion = document.getElementById('faqHomeAccordion');
    
        // Exit if neither exists
        if (!faqAccordion && !faqHomeAccordion) return;
    
        fetch("{{ route('faqs.public') }}")
            .then(res => res.json())
            .then(faqs => {
    
                // FAQ SECTION
                if (faqAccordion) {
                    faqAccordion.innerHTML = '';
    
                    if (!faqs.length) {
                        faqAccordion.innerHTML = `<p class="text-muted text-center">No FAQs available.</p>`;
                    } else {
                        faqs.forEach((faq, index) => {
                            if (faq.is_active) {
                                faqAccordion.insertAdjacentHTML('beforeend', `
                                    <div class="accordion-item mb-3 border-0 rounded shadow-sm">
                                        <h2 class="accordion-header" id="heading${index}">
                                            <button class="accordion-button collapsed fw-semibold"
                                                    type="button"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#faq${index}"
                                                    aria-expanded="false"
                                                    aria-controls="faq${index}">
                                                ${faq.question}
                                            </button>
                                        </h2>
    
                                        <div id="faq${index}" class="accordion-collapse collapse"
                                             data-bs-parent="#faqAccordion">
                                            <div class="accordion-body bg-light" style="border-left:4px solid #003366;">
                                                ${faq.answer}
                                            </div>
                                        </div>
                                    </div>
                                `);
                            }
                        });
                    }
                }
    
                if (faqHomeAccordion) {
                    faqHomeAccordion.innerHTML = '';
    
                    if (!faqs.length) {
                        faqHomeAccordion.innerHTML = `<p class="text-muted text-center">No FAQs available.</p>`;
                    } else {
                        faqs.forEach((faq, index) => {
                            if (faq.is_active) {
                                faqHomeAccordion.insertAdjacentHTML('beforeend', `
                                    <div class="accordion-item mb-3 border-0 rounded shadow-sm">
                                        <h2 class="accordion-header" id="homeHeading${index}">
                                            <button class="accordion-button collapsed fw-semibold"
                                                    type="button"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#homeFaq${index}"
                                                    aria-expanded="false"
                                                    aria-controls="homeFaq${index}">
                                                ${faq.question}
                                            </button>
                                        </h2>
    
                                        <div id="homeFaq${index}" class="accordion-collapse collapse"
                                             data-bs-parent="#faqHomeAccordion">
                                            <div class="accordion-body">
                                                ${faq.answer}
                                            </div>
                                        </div>
                                    </div>
                                `);
                            }
                        });
                    }
                }
    
            })
            .catch(err => {
                console.error('FAQ Load Error:', err);
                if (faqAccordion) faqAccordion.innerHTML = `<p class="text-danger text-center">Failed to load FAQs.</p>`;
                if (faqHomeAccordion) faqHomeAccordion.innerHTML = `<p class="text-danger text-center">Failed to load FAQs.</p>`;
            });
    
    });
    </script>

    <!-- CONTACT -->
    <div id="contact" class="data-section py-5" style="display:none;">

        <div class="text-center mb-5 mt-5">
            <h2 class="fw-bold">Contact Us</h2>
            <p style="color:#555; font-size:18px; margin-bottom: 10px;"> For assistance, inquiries, or technical support, you can reach us through the following channels: </p>
        </div>

        <div class="row g-4 align-items-stretch justify-content-center">
            <div class="col-md-3">
                <div class="card shadow-sm h-100 text-center border-0">
                    <div class="card-body p-4">
                        <div class="mb-3 fs-2 text-primary">📍</div>
                        <h5 class="fw-bold">Office Address</h5>
                        <p class="text-muted small mb-0">
                            OFFICE OF THE CITY SOCIAL WELFARE AND DEVELOPMENT <br>
                            General Trias, Cavite
                        </p>
                    </div>
                </div>
            </div>

            <!-- Contact Card: Email -->
            <div class="col-md-3">
                <div class="card shadow-sm h-100 text-center border-0">
                    <div class="card-body p-4">
                        <div class="mb-3 fs-2 text-success">📧</div>
                        <h5 class="fw-bold">Email Us</h5>
                        <p class="text-muted small mb-1">For technical support:</p>
                        <a href="mailto:support.solocare@gmail.com" class="d-block text-decoration-none fw-semibold">
                        support.solocare@gmail.com
                        </a>
                        <p class="text-muted small mb-1 mt-2">General inquiries:</p>
                        <a href="mailto:www.solocaresystem.com" class="d-block text-decoration-none fw-semibold">
                            www.solocaresystem.com
                        </a>
                    </div>
                </div>
            </div>

            <!-- Contact Card: Hotline -->
            <div class="col-md-3">
                <div class="card shadow-sm h-100 text-center border-0">
                    <div class="card-body p-4">
                        <div class="mb-3 fs-2 text-danger">📞</div>
                        <h5 class="fw-bold">Hotline</h5>
                        <p class="text-muted small mb-1">CSWD Assistance Line</p>
                        <p class="fw-semibold mb-2">046 238 2908</p>
                        <p class="text-muted small">Monday 8:00 AM – 4:00 PM and<br>
                            Tuesday – Friday 8:00 AM – 5:00 PM

                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>
    </div>

    <!-- FOOTER -->
    <footer class="text-white text-center text-lg-start mt-5" style="background-color: #003366;">
        <div class="container py-5">
            <div class="row gy-4 align-items-start">
        
                <div class="col-md-6 d-flex align-items-center justify-content-center justify-content-md-start">
                    <img src="images/SC.svg" alt="Logo" class="me-3" style="width:60px; height:60px;">
                    <div class="text-start">
                      <h5 class="fw-bold mb-1">SOLO CARE</h5>
                      <p class="mb-0 small">
                        Solo Parent Information and Assistance System<br>
                        Empowering families through digital transformation.
                      </p>
                    </div>
                </div>
        
                <div class="col-md-6">
                    <div class="row">
        
                        <!-- Quick Links -->
                        <div class="col-6 text-start">
                            <h6 class="fw-bold mb-3 text-uppercase">Quick Links</h6>
                            <ul class="list-unstyled mb-0">
                                <li>
                                    <a href="#" class="link-light text-decoration-none d-block mb-2" data-bs-toggle="modal" data-bs-target="#loginModal"> Login </a>
                                </li>
                                <li>
                                    <a href="#" class="link-light text-decoration-none d-block" data-bs-toggle="modal" data-bs-target="#registerModal"> Register </a>
                                </li>
                            </ul>
                        </div>
            
                        <!-- Contact Info -->
                        <div class="col-6 text-start">
                            <h6 class="fw-bold mb-3 text-uppercase">Contact</h6>
                            <p class="small mb-1">For technical support:</p>
                            <p class="small mb-1">📧 support@solocare.gov.ph</p>
                            <p class="small">📞 (02) 8-CSWD-123</p>
                        </div>
                    </div>
                </div>
            </div>
    
            <hr class="border-light opacity-25 my-4">
    
            <div class="text-center">
              <p class="mb-0 small">&copy; 2025 Solo Parent Information and Assistance System</p>
              <small class="text-light opacity-75">City Social Welfare and Development Office</small>
            </div>
        </div>
    </footer>

    <!-- REGISTER MODAL -->
    <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 overflow-hidden">
    
                <div class="modal-header text-white border-0">
                    <h5 class="modal-title fw-bold" id="registerModalLabel">📝 Create Your SoloCare Account</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
    
                <div class="modal-body p-4">
                    <form id="registerForm" action="{{ route('register') }}" method="POST" novalidate>
                        @csrf
                        <div class="row g-3">
    
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Last Name <span class="required">*</span></label>
                                <input type="text" class="form-control form-control-lg rounded-3" name="last_name" placeholder="Enter your last name" required>
                                <div class="invalid-feedback"></div>
                            </div>
    
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">First Name <span class="required">*</span></label>
                                <input type="text" class="form-control form-control-lg rounded-3" name="first_name" placeholder="Enter your first name" required>
                                <div class="invalid-feedback"></div>
                            </div>
    
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Middle Name</label>
                                <input type="text" class="form-control form-control-lg rounded-3" name="middle_name" placeholder="Enter your middle name">
                            </div>
    
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Username <span class="required">*</span></label>
                                <input type="text" class="form-control form-control-lg rounded-3 @error('username') is-invalid @enderror"
                                    name="username" value="{{ old('username') }}" placeholder="Enter your username" required>
                                <div class="invalid-feedback js-feedback"></div>
                            </div>
    
                            <div class="col-md-6 position-relative">
                                <label class="form-label fw-semibold">Password <span class="required">*</span></label>
                                <input type="password" class="form-control form-control-lg rounded-3" name="password" placeholder="Enter password" required>
                                <div class="invalid-feedback"></div>
                                <div id="passwordHint" class="password-hint">
                                    <ul class="mb-0">
                                        <li id="pw-length" class="invalid">At least 8 characters</li>
                                        <li id="pw-uppercase" class="invalid">At least one uppercase letter</li>
                                        <li id="pw-lowercase" class="invalid">At least one lowercase letter</li>
                                        <li id="pw-number" class="invalid">At least one number</li>
                                        <li id="pw-special" class="invalid">At least one special character (!@#$%^&*)</li>
                                    </ul>
                                </div>
                            </div>
    
                            <div class="col-md-6 position-relative">
                                <label class="form-label fw-semibold">Confirm Password <span class="required">*</span></label>
                                <input type="password" class="form-control form-control-lg rounded-3" name="confirm_password" placeholder="Re-enter password" required>
                                <div class="invalid-feedback"></div>
                                <div id="confirmHint" class="password-hint">
                                    <span id="pw-match" class="invalid">Passwords must match.</span>
                                </div>
                            </div>
    
                            <div class="col-md-6 position-relative">
                                <label class="form-label fw-semibold">Email Address <span class="required">*</span></label>
                                <input type="email" class="form-control form-control-lg rounded-3 @error('email') is-invalid @enderror"
                                    name="email" value="{{ old('email') }}" placeholder="example@gmail.com" required>
                                <div class="password-hint"><span id="email-status" class="invalid"></span></div>
    
                            </div>
    
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Contact Number <span class="required">*</span></label>
                                <input type="text" class="form-control form-control-lg rounded-3" name="contact" placeholder="09XXXXXXXXX" required pattern="09\d{9}">
                                <div class="invalid-feedback"></div>
                            </div>
    
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Street <span class="required">*</span></label>
                                <input type="text" class="form-control form-control-lg rounded-3" name="street" placeholder="Street, subdivision, etc." required>
                                <div class="invalid-feedback"></div>
                            </div>
    
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Barangay <span class="required">*</span></label>
                                <input type="text" class="form-control form-control-lg rounded-3" name="barangay" placeholder="Enter your barangay" required>
                                <div class="invalid-feedback"></div>
                            </div>
    
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Municipality / City <span class="required">*</span></label>
                                <input type="text" class="form-control form-control-lg rounded-3" name="municipality_city" placeholder="Enter your municipality or city" required>
                                <div class="invalid-feedback"></div>
                            </div>
    
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Province <span class="required">*</span></label>
                                <input type="text" class="form-control form-control-lg rounded-3" name="province" placeholder="Enter your province" required>
                                <div class="invalid-feedback"></div>
                            </div>
    
                        </div>
                    </form>
                </div>

                <div class="modal-footer border-0 d-flex justify-content-between">
                    <p class="mb-0 small text-muted">Already have an account?
                        <a href="#" onclick="openLogin()" class="fw-semibold text-primary text-decoration-none">Login here</a>
                    </p>
                    <button type="submit" form="registerForm" class="btn btn-primary btn-lg px-4 rounded-3 fw-bold">Register</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="registerSuccessModal" tabindex="-1" aria-labelledby="registerSuccessModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold" id="registerSuccessModalLabel">🎉 Registration Successful!</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <p class="mb-4">Your SoloCare account has been successfully created.</p>
                    <button type="button" class="btn btn-primary btn-lg fw-bold" onclick="openLogin()">Go to Login</button>
                </div>
            </div>
    </div>
</div>

@section('scripts')
<script>
    const items = document.querySelectorAll('.gallery-item');
    let current = 0;
    
    function updateGallery() {
        const isMobile = window.innerWidth <= 768;
    
        items.forEach((item, i) => {
            item.className = 'gallery-item'; // reset
            item.style.zIndex = '';
            item.style.opacity = '';
            if (!isMobile) {
                const offset = (i - current + items.length) % items.length;
    
                if (offset === 0) item.classList.add('center');
                else if (offset === 1) item.classList.add('right');
                else if (offset === items.length - 1) item.classList.add('left');
                else item.style.display = 'none'; // hide others
            } else {
                item.style.display = 'block'; // all visible on mobile
                item.style.transform = 'none';
                item.style.opacity = '1';
            }
        });
    }
    
    // Buttons
    document.getElementById('nextBtn')?.addEventListener('click', () => {
        current = (current + 1) % items.length;
        updateGallery();
    });
    document.getElementById('prevBtn')?.addEventListener('click', () => {
        current = (current - 1 + items.length) % items.length;
        updateGallery();
    });
    
    window.addEventListener('resize', updateGallery);
    updateGallery();

    // Track Button
    const trackBtn = document.getElementById('trackBtn');
    if(trackBtn){
        trackBtn.addEventListener('click', function(){
            const reference = document.getElementById('trackingId').value.trim();
            const notice = document.getElementById('trackNotice');

            if(!reference){
                notice.textContent = "Please enter a reference number.";
                notice.classList.remove('d-none');
                return;
            }

            fetch("{{ route('applications.track') }}?reference_no=" + reference)
            .then(res => {
                if(!res.ok) throw new Error('Not found');
                return res.text();
            })
            .then(data => {
                document.querySelector('#track .timeline').innerHTML = data;
                notice.classList.add('d-none');
            })
            .catch(err => {
                notice.textContent = "Invalid Reference Number. Please check and try again.";
                notice.classList.remove('d-none');
            });
        });
    }

    // Register / Login modal functions
    window.openRegister = function() {
        closeLogin?.();
        const registerModal = new bootstrap.Modal(document.getElementById('registerModal'));
        registerModal.show();
    }

    window.closeRegister = function() {
        const registerModal = bootstrap.Modal.getInstance(document.getElementById('registerModal'));
        registerModal?.hide();
    }

    // Password reset success modal
    @if(session('reset_success'))
    let resetModal = new bootstrap.Modal(document.getElementById('successModal'));
    resetModal.show();

    let countdownEl = document.getElementById('countdown');
    let backHomeBtn = document.getElementById('backHomeBtn');
    let countdown = 5;
    let interval = setInterval(() => {
        countdown--;
        if(countdown > 0) countdownEl.textContent = countdown;
        else {
            clearInterval(interval);
            window.location.href = backHomeBtn.href;
        }
    }, 1000);
    @endif

    // Logout button
    const logoutBtn = document.getElementById('logoutBtn');
    if(logoutBtn){
        logoutBtn.addEventListener('click', function(){
            document.getElementById('loadingOverlay').style.display = 'flex';
            document.getElementById('logoutForm')?.submit();
        });
    }
</script>

@endsection
