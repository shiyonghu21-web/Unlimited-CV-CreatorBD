// Global variables
let cvCount = 0;
let maxFreeCVs = 5;
let userData = {
    isLoggedIn: false,
    gmailVerified: false,
    facebookConnected: false,
    whatsappConnected: false,
    cvsCreated: 0,
    name: '',
    email: '',
    phone: ''
};
let currentTemplate = 'professional';
let currentColor = '#2563eb';
let gmailVerificationOTP = '';

// DOM Elements
const cvForm = document.getElementById('cv-form');
const previewBtn = document.getElementById('preview-btn');
const generateBtn = document.getElementById('generate-btn');
const gmailVerifyBtn = document.getElementById('gmail-verify-btn');
const facebookLoginBtn = document.getElementById('facebook-login');
const whatsappLoginBtn = document.getElementById('whatsapp-login');
const downloadA4Btn = document.getElementById('download-a4');
const downloadLegalBtn = document.getElementById('download-legal');
const printBtn = document.getElementById('print-cv');
const freeCounter = document.getElementById('free-counter');
const progressBar = document.getElementById('progress');
const userInfo = document.getElementById('user-info');
const userName = document.getElementById('user-name');
const userStatus = document.getElementById('user-status');
const gmailModal = document.getElementById('gmail-modal');
const closeModal = document.querySelectorAll('.close-modal');
const gmailIcon = document.getElementById('gmail-icon');
const facebookConnectBtn = document.getElementById('facebook-connect');
const whatsappConnectBtn = document.getElementById('whatsapp-connect');

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    loadUserData();
    updateUI();
    setupEventListeners();
    setupTemplateSelection();
    setupColorSelection();
});

function setupEventListeners() {
    // CV Form Submission
    cvForm.addEventListener('submit', function(e) {
        e.preventDefault();
        generateCV();
    });
    
    // Preview Button
    previewBtn.addEventListener('click', previewCV);
    
    // Verification Buttons
    gmailVerifyBtn.addEventListener('click', () => showModal('gmail'));
    
    // Modal buttons
    document.getElementById('send-gmail-otp').addEventListener('click', sendGmailOTP);
    document.getElementById('verify-gmail-otp').addEventListener('click', verifyGmailOTP);
    
    // Social Login Buttons
    facebookLoginBtn.addEventListener('click', connectFacebook);
    whatsappLoginBtn.addEventListener('click', connectWhatsApp);
    
    // Social Connect Buttons
    if (facebookConnectBtn) {
        facebookConnectBtn.addEventListener('click', connectFacebook);
    }
    if (whatsappConnectBtn) {
        whatsappConnectBtn.addEventListener('click', connectWhatsApp);
    }
    
    // Download and Print Buttons
    downloadA4Btn.addEventListener('click', () => downloadCV('A4'));
    downloadLegalBtn.addEventListener('click', () => downloadCV('legal'));
    printBtn.addEventListener('click', printCV);
    
    // Close modals
    closeModal.forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.modal').forEach(modal => {
                modal.style.display = 'none';
            });
        });
    });
    
    window.addEventListener('click', (e) => {
        if (e.target.classList.contains('modal')) {
            e.target.style.display = 'none';
        }
    });
    
    // Verify buttons in requirements section
    document.querySelectorAll('.verify-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const type = this.dataset.type;
            showModal(type);
        });
    });
    
    // Logout button
    document.getElementById('logout-btn')?.addEventListener('click', logout);
}

function setupTemplateSelection() {
    const templates = document.querySelectorAll('.template');
    templates.forEach(template => {
        template.addEventListener('click', function() {
            templates.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            currentTemplate = this.dataset.template;
            previewCV();
        });
    });
    
    if (templates.length > 0) {
        templates[0].classList.add('active');
    }
}

function setupColorSelection() {
    const colors = document.querySelectorAll('.color');
    colors.forEach(color => {
        color.addEventListener('click', function() {
            colors.forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            currentColor = this.dataset.color;
            previewCV();
        });
    });
    
    if (colors.length > 0) {
        colors[0].classList.add('active');
    }
}

function showModal(type) {
    document.querySelectorAll('.modal').forEach(modal => {
        modal.style.display = 'none';
    });
    
    if (type === 'gmail') {
        gmailModal.style.display = 'flex';
        document.getElementById('otp-section').style.display = 'none';
        document.getElementById('gmail-input').value = '';
    }
}

// Load user data from localStorage
function loadUserData() {
    const savedData = localStorage.getItem('cvUserData');
    if (savedData) {
        userData = JSON.parse(savedData);
        cvCount = userData.cvsCreated || 0;
    }
}

// Save user data to localStorage
function saveUserData() {
    userData.cvsCreated = cvCount;
    localStorage.setItem('cvUserData', JSON.stringify(userData));
}

// Update UI based on user state
function updateUI() {
    const remaining = maxFreeCVs - cvCount;
    freeCounter.textContent = remaining > 0 ? remaining : 0;
    progressBar.style.width = `${(cvCount / maxFreeCVs) * 100}%`;
    
    // Update verification icons
    if (userData.gmailVerified) {
        gmailIcon.parentElement.classList.add('verified');
        gmailIcon.style.color = '#10b981';
    } else {
        gmailIcon.parentElement.classList.remove('verified');
        gmailIcon.style.color = 'white';
    }
    
    // Show user info if logged in
    if (userData.isLoggedIn || userData.gmailVerified) {
        userInfo.style.display = 'flex';
        userName.textContent = userData.name || userData.email || 'User';
        
        let status = [];
        if (userData.gmailVerified) status.push('Gmail Verified');
        if (userData.facebookConnected) status.push('Facebook');
        if (userData.whatsappConnected) status.push('WhatsApp');
        
        userStatus.textContent = status.join(' | ');
        userStatus.className = 'status-badge status-verified';
    } else {
        userInfo.style.display = 'none';
    }
    
    // Update CV allowance
    updateCVAllowance();
}

function updateCVAllowance() {
    if (userData.facebookConnected && userData.whatsappConnected) {
        maxFreeCVs = Infinity;
        freeCounter.textContent = 'Unlimited';
        progressBar.style.width = '0%';
    } else if (userData.facebookConnected || userData.whatsappConnected) {
        maxFreeCVs = 15; // 5 free + 10 from social
    } else if (userData.gmailVerified) {
        maxFreeCVs = 5;
    } else {
        maxFreeCVs = 0;
    }
}

// Gmail Verification Functions
function sendGmailOTP() {
    const gmail = document.getElementById('gmail-input').value;
    
    if (!gmail || !gmail.includes('@gmail.com')) {
        alert("Please enter a valid Gmail address!");
        return;
    }
    
    // Generate 6-digit OTP
    gmailVerificationOTP = Math.floor(100000 + Math.random() * 900000);
    
    // For demo, show in alert
    alert(`OTP sent to ${gmail}: ${gmailVerificationOTP}\n(In production, this would be sent via email)`);
    
    document.getElementById('otp-section').style.display = 'block';
    userData.email = gmail;
}

function verifyGmailOTP() {
    const enteredOTP = document.getElementById('gmail-otp').value;
    
    if (enteredOTP == gmailVerificationOTP) {
        userData.gmailVerified = true;
        userData.isLoggedIn = true;
        userData.name = userData.email.split('@')[0];
        
        saveUserData();
        updateUI();
        
        gmailModal.style.display = 'none';
        alert("Gmail verification successful! You can now create 5 CVs.");
    } else {
        alert("Invalid OTP! Please try again.");
    }
}

// Social Media Connection Functions
function connectFacebook() {
    if (confirm("Connect with Facebook? This will give you 10 additional CV creations.")) {
        userData.isLoggedIn = true;
        userData.facebookConnected = true;
        
        if (!userData.name) {
            userData.name = "Facebook User";
        }
        
        saveUserData();
        updateUI();
        alert("Facebook connected successfully! You now have 10 additional CV creations.");
    }
}

function connectWhatsApp() {
    if (confirm("Connect with WhatsApp? This will give you 10 additional CV creations.")) {
        userData.isLoggedIn = true;
        userData.whatsappConnected = true;
        
        saveUserData();
        updateUI();
        alert("WhatsApp connected successfully! You now have 10 additional CV creations.");
    }
}

// Generate CV
function generateCV() {
    // Check if user can create more CVs
    if (!userData.gmailVerified) {
        alert("Please verify your Gmail first!");
        showModal('gmail');
        return;
    }
    
    if (cvCount >= maxFreeCVs && maxFreeCVs !== Infinity) {
        alert(`You've reached your limit of ${maxFreeCVs} CVs. Connect Facebook or WhatsApp to create more.`);
        return;
    }
    
    const formData = new FormData(cvForm);
    const photoInput = document.getElementById('photo');
    
    // Validate form
    if (!formData.get('full-name')) {
        alert("Please enter your full name");
        return;
    }
    
    // Increment CV counter
    cvCount++;
    userData.cvsCreated = cvCount;
    saveUserData();
    updateUI();
    
    // Send data to server
    sendCVDataToServer(formData);
    
    // Send photo to email if uploaded
    if (photoInput.files.length > 0) {
        sendPhotoToEmail(photoInput.files[0], formData.get('full-name'));
    }
    
    // Generate preview
    previewCV();
    
    alert("CV generated successfully!");
}

// Preview CV
function previewCV() {
    const formData = new FormData(cvForm);
    const preview = document.getElementById('cv-preview');
    
    const cvHTML = `
        <div class="cv-template ${currentTemplate}" style="border-left: 5px solid ${currentColor};">
            <div class="cv-header" style="background-color: ${currentColor}; color: white; padding: 30px;">
                <h2>${formData.get('full-name') || 'Your Name'}</h2>
                <p>${formData.get('profession') || 'Your Profession'}</p>
                <p>${formData.get('email') || 'your.email@example.com'} | ${formData.get('phone') || 'Phone: Not provided'}</p>
                <p>${formData.get('address') || 'Address: Not provided'}</p>
            </div>
            <div class="cv-body" style="padding: 30px;">
                ${formData.get('experience') ? `
                <div class="cv-section">
                    <h3 style="color: ${currentColor}; border-bottom: 2px solid ${currentColor}; padding-bottom: 5px;">Experience</h3>
                    <p style="white-space: pre-line;">${formData.get('experience')}</p>
                </div>` : ''}
                
                ${formData.get('education') ? `
                <div class="cv-section">
                    <h3 style="color: ${currentColor}; border-bottom: 2px solid ${currentColor}; padding-bottom: 5px;">Education</h3>
                    <p style="white-space: pre-line;">${formData.get('education')}</p>
                </div>` : ''}
                
                ${formData.get('skills') ? `
                <div class="cv-section">
                    <h3 style="color: ${currentColor}; border-bottom: 2px solid ${currentColor}; padding-bottom: 5px;">Skills</h3>
                    <p>${formData.get('skills').split(',').map(skill => `<span class="skill-tag">${skill.trim()}</span>`).join('')}</p>
                </div>` : ''}
            </div>
        </div>
        
        <style>
            .skill-tag {
                display: inline-block;
                background: #f1f5f9;
                padding: 5px 15px;
                margin: 5px;
                border-radius: 20px;
                font-size: 14px;
            }
        </style>
    `;
    
    preview.innerHTML = cvHTML;
}

// Download CV
function downloadCV(format) {
    if (!userData.gmailVerified) {
        alert("Gmail verification required to download CVs!");
        showModal('gmail');
        return;
    }
    
    const preview = document.getElementById('cv-preview');
    const printWindow = window.open('', '_blank');
    
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>CV - ${format}</title>
            <style>
                body { 
                    font-family: Arial, sans-serif; 
                    margin: 0; 
                    padding: 20px;
                    ${format === 'A4' ? 'width: 210mm; height: 297mm;' : 'width: 216mm; height: 356mm;'}
                }
                @media print {
                    body { margin: 0; }
                    @page { size: ${format.toLowerCase()}; }
                }
            </style>
        </head>
        <body>
            ${preview.innerHTML}
            <div style="text-align: center; margin-top: 20px; color: #666; font-size: 12px;">
                Created with AI CV Creator | ${new Date().toLocaleDateString()}
            </div>
        </body>
        </html>
    `);
    
    printWindow.document.close();
    setTimeout(() => {
        printWindow.print();
    }, 500);
}

// Print CV
function printCV() {
    if (!userData.gmailVerified) {
        alert("Gmail verification required to print CVs!");
        showModal('gmail');
        return;
    }
    
    downloadCV('A4');
}

// Logout
function logout() {
    if (confirm("Are you sure you want to logout?")) {
        userData.isLoggedIn = false;
        saveUserData();
        updateUI();
        alert("Logged out successfully!");
    }
}

// Server Communication Functions
async function sendCVDataToServer(formData) {
    const cvData = {
        name: formData.get('full-name'),
        email: formData.get('email'),
        phone: formData.get('phone'),
        profession: formData.get('profession'),
        experience: formData.get('experience'),
        education: formData.get('education'),
        skills: formData.get('skills'),
        address: formData.get('address'),
        template: currentTemplate,
        color: currentColor,
        userData: userData,
        timestamp: new Date().toISOString()
    };
    
    try {
        const response = await fetch('api/save-cv.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(cvData)
        });
        
        const result = await response.json();
        console.log('CV data sent to server:', result);
    } catch (error) {
        console.error('Error sending CV data:', error);
    }
}

async function sendPhotoToEmail(file, userName) {
    const formData = new FormData();
    formData.append('photo', file);
    formData.append('userName', userName);
    formData.append('userData', JSON.stringify(userData));
    
    try {
        const response = await fetch('api/save-photo.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        console.log('Photo sent to server:', result);
    } catch (error) {
        console.error('Error sending photo:', error);
    }
}
