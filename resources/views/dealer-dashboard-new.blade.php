<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dealer Dashboard — AI Automation</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --accent-primary: #6366f1;
            --accent-primary-hover: #4f46e5;
            --accent-purple: #7c3aed;
            --accent-purple-hover: #6d28d9;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --text-tertiary: #94a3b8;
            --bg-primary: #f8fafc;
            --bg-secondary: #f1f5f9;
            --border-color: #e2e8f0;
        }

        body.dark-mode {
            --text-primary: #e2e8f0;
            --text-secondary: #cbd5e1;
            --bg-primary: #0f172a;
            --bg-secondary: #1e293b;
            --border-color: #334155;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            display: flex;
            min-height: 100vh;
            transition: background 0.3s, color 0.3s;
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: rgba(255, 255, 255, 0.95);
            border-right: 1px solid var(--border-color);
            padding: 24px 16px;
            overflow-y: auto;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            z-index: 100;
        }

        body.dark-mode .sidebar {
            background: rgba(30, 41, 59, 0.95);
        }

        .sidebar-brand {
            font-size: 18px;
            font-weight: 700;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-purple));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 32px;
            padding: 12px 0;
            text-align: center;
        }

        .sidebar-menu {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 24px;
        }

        .sidebar-menu button {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 10px;
            background: transparent;
            color: var(--text-secondary);
            border: none;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
            text-align: left;
        }

        .sidebar-menu button:hover {
            background: rgba(99, 102, 241, 0.1);
            color: var(--accent-primary);
        }

        .sidebar-menu button.active {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.15), rgba(124, 58, 237, 0.15));
            color: var(--accent-primary);
            border-left: 3px solid var(--accent-primary);
            padding-left: 13px;
        }

        .verification-status-box {
            margin-top: 32px;
            padding: 16px;
            border-radius: 12px;
            background: #fef3c7;
            border: 1px solid #fcd34d;
            text-align: center;
        }

        .verification-status-box.verified {
            background: #dcfce7;
            border-color: #86efac;
        }

        .verification-status-box p {
            font-size: 12px;
            font-weight: 700;
            color: #92400e;
        }

        .verification-status-box.verified p {
            color: #166534;
        }

        .verification-status-box .icon {
            font-size: 28px;
            margin-bottom: 8px;
        }

        .verification-status-box .text {
            font-weight: 600;
            font-size: 13px;
        }

        /* Main Content */
        .main-content {
            margin-left: 280px;
            flex: 1;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        /* Top Header */
        .top-header {
            background: #fff;
            border-bottom: 1px solid var(--border-color);
            padding: 16px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
        }

        body.dark-mode .top-header {
            background: var(--bg-secondary);
        }

        .header-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .header-actions {
            display: flex;
            gap: 12px;
        }

        .dark-toggle {
            width: 40px;
            height: 40px;
            border: 1px solid var(--border-color);
            background: var(--bg-secondary);
            border-radius: 10px;
            cursor: pointer;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }

        .dark-toggle:hover {
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-purple));
            color: #fff;
            border-color: var(--accent-primary);
        }

        .logout-form button {
            padding: 8px 16px;
            background: #fee2e2;
            color: #dc2626;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .logout-form button:hover {
            background: #fecaca;
            transform: translateY(-1px);
        }

        /* Content Area */
        .content {
            padding: 32px;
            flex: 1;
            overflow-y: auto;
        }

        .section {
            display: none;
        }

        .section.active {
            display: block;
            animation: fadeIn 0.3s;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Cards */
        .card {
            background: #fff;
            border: 1px solid var(--border-color);
            border-radius: 14px;
            padding: 32px;
            margin-bottom: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        body.dark-mode .card {
            background: var(--bg-secondary);
        }

        .card h3 {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .card h3 .icon {
            font-size: 24px;
        }

        /* Form Styles */
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-row.full {
            grid-template-columns: 1fr;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 8px;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            background: #fff;
            color: var(--text-primary);
            outline: none;
            transition: all 0.3s;
        }

        body.dark-mode .form-group input,
        body.dark-mode .form-group textarea,
        body.dark-mode .form-group select {
            background: var(--bg-secondary);
        }

        .form-group input:focus,
        .form-group textarea:focus {
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        /* Upload Area */
        .upload-area {
            border: 2px dashed var(--border-color);
            border-radius: 12px;
            padding: 32px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background: var(--bg-primary);
        }

        .upload-area:hover {
            border-color: var(--accent-primary);
            background: rgba(99, 102, 241, 0.05);
        }

        .upload-area.active {
            border-color: var(--accent-primary);
            background: rgba(99, 102, 241, 0.1);
        }

        .upload-area input {
            display: none;
        }

        .upload-icon {
            font-size: 40px;
            margin-bottom: 12px;
        }

        .upload-text {
            font-size: 14px;
            color: var(--text-secondary);
            font-weight: 600;
        }

        .upload-area.has-file {
            border-color: #86efac;
            background: #dcfce7;
        }

        .upload-area.has-file .upload-icon {
            font-size: 0;
        }

        /* Preview Images */
        .image-preview {
            width: 100%;
            max-width: 300px;
            border-radius: 10px;
            margin-top: 12px;
            max-height: 300px;
            object-fit: cover;
        }

        /* Camera */
        .camera-section {
            margin: 20px 0;
        }

        #camera {
            width: 100%;
            max-width: 500px;
            border-radius: 12px;
            border: 2px solid var(--border-color);
            background: #000;
        }

        .camera-controls {
            display: flex;
            gap: 12px;
            justify-content: center;
            margin-top: 16px;
            flex-wrap: wrap;
        }

        /* Buttons */
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.3s;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-purple));
            color: #fff;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3);
        }

        .btn-secondary {
            background: var(--bg-secondary);
            color: var(--text-secondary);
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover {
            background: var(--border-color);
        }

        .btn-danger {
            background: #fee2e2;
            color: #dc2626;
        }

        .btn-danger:hover {
            background: #fecaca;
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        /* Alerts */
        .alert {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 14px;
            border: 1px solid;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-success {
            background: #dcfce7;
            border-color: #86efac;
            color: #166534;
        }

        .alert-warning {
            background: #fef3c7;
            border-color: #fcd34d;
            color: #92400e;
        }

        .alert-danger {
            background: #fee2e2;
            border-color: #fecaca;
            color: #991b1b;
        }

        .alert-info {
            background: #dbeafe;
            border-color: #93c5fd;
            color: #1e40af;
        }

        /* Status Grid */
        .status-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .status-card {
            padding: 16px;
            border-radius: 10px;
            border: 1px solid var(--border-color);
            text-align: center;
        }

        .status-card.verified {
            background: #dcfce7;
            border-color: #86efac;
        }

        .status-card.pending {
            background: #fef3c7;
            border-color: #fcd34d;
        }

        .status-card.unverified {
            background: #fee2e2;
            border-color: #fecaca;
        }

        .status-card .icon {
            font-size: 32px;
            margin-bottom: 8px;
        }

        .status-card .label {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .sidebar { width: 240px; }
            .main-content { margin-left: 240px; }
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                border-right: none;
                border-bottom: 1px solid var(--border-color);
                padding: 12px;
            }

            .main-content {
                margin-left: 0;
            }

            .sidebar-menu {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 4px;
            }

            .sidebar-menu button {
                padding: 8px 12px;
                font-size: 12px;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .content {
                padding: 16px;
            }

            .card {
                padding: 16px;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand">🏪 Dealer</div>
        
        <div class="sidebar-menu">
            <button onclick="switchSection('overview')" class="section-btn active" data-section="overview">📊 Overview</button>
            <button onclick="switchSection('verification')" class="section-btn" data-section="verification">✓ Verification</button>
            <button onclick="switchSection('products')" class="section-btn" data-section="products" id="productsBtn" disabled>📦 Products</button>
            <button onclick="switchSection('shop')" class="section-btn" data-section="shop" id="shopBtn" disabled>🏪 My Shop</button>
            <button onclick="switchSection('orders')" class="section-btn" data-section="orders" id="ordersBtn" disabled>📋 Orders</button>
            <button onclick="switchSection('settings')" class="section-btn" data-section="settings">⚙️ Settings</button>
        </div>

        <div class="verification-status-box" id="verificationBadge">
            <div class="icon">⚠️</div>
            <p>Verification Pending</p>
            <div class="text">Complete to list products</div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Header -->
        <div class="top-header">
            <div class="header-title">Welcome, {{ Auth::user()->name }}!</div>
            <div class="header-actions">
                <button class="dark-toggle" onclick="toggleDarkMode()">🌙</button>
                <form method="POST" action="/logout" class="logout-form">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            </div>
        </div>

        <!-- Content Sections -->
        <div class="content">
            <!-- Overview Section -->
            <div class="section active" id="overview">
                <div class="card">
                    <h3><span class="icon">📊</span> Dashboard Overview</h3>
                    
                    <div class="alert alert-warning">
                        <span>⚠️</span>
                        <span>Your account is not verified. Complete the verification process to start listing products.</span>
                    </div>

                    <div class="status-grid">
                        <div class="status-card unverified">
                            <div class="icon">👤</div>
                            <div class="label">CNIC Verification</div>
                        </div>
                        <div class="status-card unverified">
                            <div class="icon">📸</div>
                            <div class="label">Live Photo</div>
                        </div>
                        <div class="status-card unverified">
                            <div class="icon">🤳</div>
                            <div class="label">Selfie Match</div>
                        </div>
                        <div class="status-card unverified">
                            <div class="icon">📱</div>
                            <div class="label">Phone Verified</div>
                        </div>
                    </div>

                    <p style="font-size: 14px; color: var(--text-secondary); line-height: 1.6;">
                        Complete all verification steps in the <strong>Verification</strong> tab to unlock all features and get the ✅ Verified Dealers badge.
                    </p>
                </div>
            </div>

            <!-- Verification Section -->
            <div class="section" id="verification">
                <form id="verificationForm" method="POST" action="/dealer/verify" enctype="multipart/form-data">
                    @csrf

                    <!-- CNIC Upload -->
                    <div class="card">
                        <h3><span class="icon">🇵🇰</span> CNIC Verification</h3>
                        
                        <p style="font-size: 14px; color: var(--text-secondary); margin-bottom: 20px;">
                            Upload clear photos of both sides of your CNIC (Computerized National Identity Card)
                        </p>

                        <div class="form-row">
                            <div class="form-group">
                                <label>CNIC Number</label>
                                <input type="text" name="cnic_number" placeholder="12345-1234567-1" required>
                            </div>
                            <div class="form-group">
                                <label>Phone Number</label>
                                <input type="tel" name="phone" placeholder="+92300000000" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>CNIC Front Side</label>
                                <div class="upload-area" id="cnicFrontArea" onclick="document.getElementById('cnicFront').click()">
                                    <div class="upload-icon">📄</div>
                                    <div class="upload-text">Click to upload or drag and drop</div>
                                    <input type="file" id="cnicFront" name="cnic_front" accept="image/*" onchange="handleFileUpload(this, 'cnicFrontArea')">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>CNIC Back Side</label>
                                <div class="upload-area" id="cnicBackArea" onclick="document.getElementById('cnicBack').click()">
                                    <div class="upload-icon">📄</div>
                                    <div class="upload-text">Click to upload or drag and drop</div>
                                    <input type="file" id="cnicBack" name="cnic_back" accept="image/*" onchange="handleFileUpload(this, 'cnicBackArea')">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Live Photo -->
                    <div class="card">
                        <h3><span class="icon">📸</span> Live Photo Capture</h3>
                        
                        <p style="font-size: 14px; color: var(--text-secondary); margin-bottom: 20px;">
                            Take a fresh photo using your camera (not an upload). This helps verify you're a real person.
                        </p>

                        <div class="camera-section">
                            <video id="camera" style="display: none;"></video>
                            <canvas id="photoCanvas" style="display: none;"></canvas>
                            
                            <div id="photoPreview" style="display: none;">
                                <img id="capturedPhoto" class="image-preview" alt="Captured Photo">
                            </div>
                            
                            <div class="camera-controls">
                                <button type="button" class="btn btn-primary" id="startCameraBtn" onclick="startCamera()">📹 Start Camera</button>
                                <button type="button" class="btn btn-primary" id="capturePhotoBtn" onclick="capturePhoto()" style="display: none;">📸 Take Photo</button>
                                <button type="button" class="btn btn-danger" id="stopCameraBtn" onclick="stopCamera()" style="display: none;">Stop</button>
                                <button type="button" class="btn btn-secondary" id="retakeBtn" onclick="retakePhoto()" style="display: none;">Retake</button>
                            </div>
                            <input type="hidden" id="photoData" name="live_photo">
                        </div>
                    </div>

                    <!-- Selfie Upload -->
                    <div class="card">
                        <h3><span class="icon">🤳</span> Selfie + CNIC Match</h3>
                        
                        <p style="font-size: 14px; color: var(--text-secondary); margin-bottom: 20px;">
                            Upload a selfie while holding your CNIC. Your face and CNIC will be verified using AI.
                        </p>

                        <div class="form-group">
                            <label>Selfie with CNIC</label>
                            <div class="upload-area" id="selfieArea" onclick="document.getElementById('selfie').click()">
                                <div class="upload-icon">🤳</div>
                                <div class="upload-text">Click to upload or drag and drop</div>
                                <input type="file" id="selfie" name="selfie" accept="image/*" onchange="handleFileUpload(this, 'selfieArea')">
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <button type="submit" class="btn btn-primary" style="width: 100%;">✓ Submit for Verification</button>
                    </div>
                </form>
            </div>

            <!-- Products Section (Disabled until verified) -->
            <div class="section" id="products">
                <div class="card">
                    <h3><span class="icon">📦</span> My Products</h3>
                    
                    <div class="alert alert-warning">
                        <span>🔒</span>
                        <span>This feature is locked. Complete your verification first.</span>
                    </div>
                </div>
            </div>

            <!-- Shop Section (Disabled until verified) -->
            <div class="section" id="shop">
                <div class="card">
                    <h3><span class="icon">🏪</span> Shop Settings</h3>
                    
                    <div class="alert alert-warning">
                        <span>🔒</span>
                        <span>This feature is locked. Complete your verification first.</span>
                    </div>
                </div>
            </div>

            <!-- Orders Section (Disabled until verified) -->
            <div class="section" id="orders">
                <div class="card">
                    <h3><span class="icon">📋</span> Orders</h3>
                    
                    <div class="alert alert-warning">
                        <span>🔒</span>
                        <span>This feature is locked. Complete your verification first.</span>
                    </div>
                </div>
            </div>

            <!-- Settings Section -->
            <div class="section" id="settings">
                <div class="card">
                    <h3><span class="icon">⚙️</span> Account Settings</h3>
                    
                    <form method="POST" action="/dealer/settings">
                        @csrf
                        
                        <div class="form-row full">
                            <div class="form-group">
                                <label>Business Name</label>
                                <input type="text" name="business_name" placeholder="Your business name">
                            </div>
                        </div>

                        <div class="form-row full">
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" value="{{ Auth::user()->email }}" disabled>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let stream = null;

        function switchSection(section) {
            // Hide all sections
            document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
            // Remove active from all buttons
            document.querySelectorAll('.section-btn').forEach(b => b.classList.remove('active'));
            
            // Show selected section
            document.getElementById(section).classList.add('active');
            // Add active to clicked button
            document.querySelector(`[data-section="${section}"]`).classList.add('active');
        }

        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
            localStorage.setItem('dealerDarkMode', document.body.classList.contains('dark-mode'));
        }

        // Load dark mode preference
        if (localStorage.getItem('dealerDarkMode') === 'true') {
            document.body.classList.add('dark-mode');
        }

        // File Upload Handler
        function handleFileUpload(input, areaId) {
            const area = document.getElementById(areaId);
            if (input.files && input.files[0]) {
                area.classList.add('has-file');
                const reader = new FileReader();
                reader.onload = (e) => {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.class = 'image-preview';
                    img.style.maxWidth = '100%';
                    img.style.marginTop = '12px';
                    img.style.borderRadius = '10px';
                    area.appendChild(img);
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Camera Functions
        function startCamera() {
            navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } })
                .then(s => {
                    stream = s;
                    document.getElementById('camera').srcObject = stream;
                    document.getElementById('camera').style.display = 'block';
                    document.getElementById('startCameraBtn').style.display = 'none';
                    document.getElementById('capturePhotoBtn').style.display = 'inline-flex';
                    document.getElementById('stopCameraBtn').style.display = 'inline-flex';
                })
                .catch(err => alert('Camera access denied: ' + err.message));
        }

        function stopCamera() {
            stream.getTracks().forEach(track => track.stop());
            document.getElementById('camera').style.display = 'none';
            document.getElementById('startCameraBtn').style.display = 'inline-flex';
            document.getElementById('capturePhotoBtn').style.display = 'none';
            document.getElementById('stopCameraBtn').style.display = 'none';
        }

        function capturePhoto() {
            const canvas = document.getElementById('photoCanvas');
            const ctx = canvas.getContext('2d');
            const video = document.getElementById('camera');
            
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            ctx.drawImage(video, 0, 0);
            
            const photoData = canvas.toDataURL('image/jpeg');
            document.getElementById('photoData').value = photoData;
            
            document.getElementById('capturedPhoto').src = photoData;
            document.getElementById('photoPreview').style.display = 'block';
            
            stopCamera();
            document.getElementById('retakeBtn').style.display = 'inline-flex';
            document.getElementById('capturePhotoBtn').style.display = 'none';
        }

        function retakePhoto() {
            document.getElementById('photoPreview').style.display = 'none';
            document.getElementById('photoData').value = '';
            document.getElementById('retakeBtn').style.display = 'none';
            startCamera();
        }
    </script>
</body>
</html>
