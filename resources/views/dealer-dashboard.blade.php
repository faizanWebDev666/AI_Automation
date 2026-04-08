@extends('layouts.dealer')

@section('title', 'Dealer Dashboard')

@section('content')
    <div class="content-sections">
        <!-- ═══════ OVERVIEW SECTION ═══════ -->
        <div class="section active" id="overview">
            @if($isVerified)
                <div class="verified-hero">
                    <div class="hero-icon">✅</div>
                    <h2>Verified Dealer</h2>
                    <p>You have full access to all dealer features. Start listing your products!</p>
                </div>
            @else
                <div class="card">
                    <h3><span class="icon">📊</span> Dashboard Overview</h3>

                    <div class="alert alert-warning">
                        <span>⚠️</span>
                        <span>Complete your verification to unlock all features and start listing products.</span>
                    </div>

                    <div class="status-grid">
                        <div class="status-card {{ $user->cnic_front_image ? 'verified' : 'unverified' }}">
                            <div class="icon">{{ $user->cnic_front_image ? '✅' : '❌' }}</div>
                            <div class="label">CNIC Upload</div>
                        </div>
                        <div class="status-card {{ $user->live_photo ? 'verified' : 'unverified' }}">
                            <div class="icon">{{ $user->live_photo ? '✅' : '❌' }}</div>
                            <div class="label">Live Photo</div>
                        </div>
                        <div class="status-card {{ ($verificationStatus === 'verified') ? 'verified' : 'unverified' }}">
                            <div class="icon">{{ ($verificationStatus === 'verified') ? '✅' : '❌' }}</div>
                            <div class="label">AI Face Match</div>
                        </div>
                        <div class="status-card {{ $user->phone ? 'verified' : 'unverified' }}">
                            <div class="icon">{{ $user->phone ? '✅' : '❌' }}</div>
                            <div class="label">Phone Linked</div>
                        </div>
                    </div>

                    <p style="font-size: 14px; color: var(--text-secondary); line-height: 1.7;">
                        Go to the <strong>🛡️ Verification</strong> tab to upload your CNIC, take a live photo, and complete the AI-powered face match.
                    </p>
                </div>
            @endif

            <div class="card">
                <h3><span class="icon">📈</span> Quick Stats</h3>
                <div class="status-grid">
                    <div class="status-card" style="background: var(--bg-primary);">
                        <div class="icon">📦</div>
                        <div style="font-size: 28px; font-weight: 800; color: var(--accent-primary);">{{ $propertyCount ?? 0 }}</div>
                        <div class="label">Properties</div>
                    </div>
                    <div class="status-card" style="background: var(--bg-primary);">
                        <div class="icon">📋</div>
                        <div style="font-size: 28px; font-weight: 800; color: var(--accent-primary);">0</div>
                        <div class="label">Orders</div>
                    </div>
                    <div class="status-card" style="background: var(--bg-primary);">
                        <div class="icon">💰</div>
                        <div style="font-size: 28px; font-weight: 800; color: var(--accent-primary);">Rs 0</div>
                        <div class="label">Revenue</div>
                    </div>
                </div>
            </div>

            {{-- ═══════ PLAN STATUS WIDGET ═══════ --}}
            @if(isset($currentPlan))
            <div class="card" style="border-left: 4px solid {{ $currentPlan->slug === 'gold' ? '#f59e0b' : ($currentPlan->slug === 'silver' ? '#6366f1' : '#10b981') }};">
                <h3>
                    <span class="icon">
                        @if($currentPlan->slug === 'gold')
                            🏆
                        @elseif($currentPlan->slug === 'silver')
                            💎
                        @else
                            ✅
                        @endif
                    </span>
                    Your Plan: {{ $currentPlan->name }}
                    @if($currentPlan->slug !== 'free')
                        <span style="background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white; font-size: 10px; font-weight: 700; padding: 3px 10px; border-radius: 999px; margin-left: 8px; letter-spacing: 0.5px;">PREMIUM</span>
                    @endif
                </h3>

                {{-- Listing Usage --}}
                <div style="margin-bottom: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <span style="font-size: 14px; font-weight: 600; color: var(--text-primary);">
                            Listings This Month
                        </span>
                        <span style="font-size: 14px; font-weight: 700; color: var(--accent-primary);">
                            @if($totalAllowed === null)
                                {{ $usedListings ?? 0 }} / ∞
                            @else
                                {{ $usedListings ?? 0 }} / {{ $totalAllowed }}
                            @endif
                        </span>
                    </div>

                    {{-- Progress Bar --}}
                    @php
                        $progressPercent = $totalAllowed ? min(100, round(($usedListings / $totalAllowed) * 100)) : 5;
                        $barColor = $progressPercent >= 90 ? '#ef4444' : ($progressPercent >= 70 ? '#f59e0b' : '#10b981');
                    @endphp
                    <div style="width: 100%; height: 10px; background: var(--bg-secondary); border-radius: 999px; overflow: hidden;">
                        <div style="width: {{ $totalAllowed === null ? '5' : $progressPercent }}%; height: 100%; background: {{ $totalAllowed === null ? 'linear-gradient(135deg, #6366f1, #8b5cf6)' : $barColor }}; border-radius: 999px; transition: width 0.6s ease;"></div>
                    </div>

                    @if($totalAllowed !== null)
                        <p style="font-size: 12px; color: var(--text-tertiary); margin-top: 8px;">
                            @if($remainingListings <= 0)
                                ❌ You've used all your listings this month.
                            @elseif($remainingListings <= 1)
                                ⚠️ Only {{ $remainingListings }} listing remaining!
                            @else
                                {{ $remainingListings }} listings remaining this month
                            @endif
                        </p>
                    @else
                        <p style="font-size: 12px; color: var(--text-tertiary); margin-top: 8px;">
                            ♾️ Unlimited listings with your Gold plan
                        </p>
                    @endif
                </div>

                {{-- Plan Features Summary --}}
                <div style="display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 20px;">
                    <span style="background: {{ $currentPlan->highlighted_listings ? '#eef2ff' : 'var(--bg-secondary)' }}; color: {{ $currentPlan->highlighted_listings ? '#6366f1' : 'var(--text-tertiary)' }}; font-size: 11px; font-weight: 600; padding: 4px 12px; border-radius: 999px;">
                        {{ $currentPlan->highlighted_listings ? '✓' : '✗' }} Highlighted
                    </span>
                    <span style="background: {{ $currentPlan->basic_analytics ? '#eef2ff' : 'var(--bg-secondary)' }}; color: {{ $currentPlan->basic_analytics ? '#6366f1' : 'var(--text-tertiary)' }}; font-size: 11px; font-weight: 600; padding: 4px 12px; border-radius: 999px;">
                        {{ $currentPlan->basic_analytics ? '✓' : '✗' }} Analytics
                    </span>
                    <span style="background: {{ $currentPlan->virtual_tours ? '#eef2ff' : 'var(--bg-secondary)' }}; color: {{ $currentPlan->virtual_tours ? '#6366f1' : 'var(--text-tertiary)' }}; font-size: 11px; font-weight: 600; padding: 4px 12px; border-radius: 999px;">
                        {{ $currentPlan->virtual_tours ? '✓' : '✗' }} Virtual Tours
                    </span>
                    <span style="background: {{ $currentPlan->agency_profile ? '#eef2ff' : 'var(--bg-secondary)' }}; color: {{ $currentPlan->agency_profile ? '#6366f1' : 'var(--text-tertiary)' }}; font-size: 11px; font-weight: 600; padding: 4px 12px; border-radius: 999px;">
                        {{ $currentPlan->agency_profile ? '✓' : '✗' }} Agency Profile
                    </span>
                </div>

                {{-- Upgrade CTA --}}
                @if($currentPlan->slug !== 'gold')
                <a href="{{ route('subscription.plans') }}" style="display: inline-flex; align-items: center; gap: 8px; background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white; padding: 12px 24px; border-radius: 12px; font-size: 14px; font-weight: 600; text-decoration: none; transition: all 0.3s; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 24px rgba(99,102,241,0.3)'" onmouseout="this.style.transform='none';this.style.boxShadow='0 4px 12px rgba(99,102,241,0.2)'">
                    ⚡ Upgrade Plan
                </a>
                @endif
            </div>
            @endif
        </div>

        <!-- ═══════ VERIFICATION SECTION ═══════ -->
        <div class="section" id="verification">
            @if($isVerified)
                <div class="card">
                    <h3><span class="icon">✅</span> Verification Complete</h3>
                    <div class="alert alert-success">
                        <span>🎉</span>
                        <span>Your identity has been verified. You are a <strong>Verified Dealer ✅</strong>.</span>
                    </div>
                    @if($user->verification_notes)
                        <p style="font-size: 13px; color: var(--text-secondary);">{{ $user->verification_notes }}</p>
                    @endif
                </div>
            @elseif($user->isVerificationBanned())
                <!-- Verification Banned -->
                <div class="card">
                    <div style="text-align: center; padding: 40px 20px;">
                        <h2 style="font-size: 48px; margin-bottom: 16px;">🚫</h2>
                        <h3 style="color: #991b1b; margin-bottom: 12px;">Verification Banned</h3>
                        <p style="color: var(--text-secondary); margin-bottom: 20px;">Your email has been banned from verification due to maximum failed attempts.</p>
                        
                        <div style="background-color: #fee2e2; border-left: 4px solid #dc2626; padding: 16px; border-radius: 4px; margin-bottom: 20px; text-align: left;">
                            <p style="font-size: 13px; color: #7f1d1d; margin-bottom: 8px;">
                                <strong>Reason:</strong> {{ $user->verification_ban_reason }}
                            </p>
                            <p style="font-size: 13px; color: #7f1d1d;">
                                <strong>Banned on:</strong> {{ $user->verification_banned_at->format('F j, Y \a\t g:i A') }}
                            </p>
                        </div>

                        <p style="color: var(--text-secondary); margin-bottom: 12px;">
                            You have exceeded the maximum of <strong>5 verification attempts</strong>. This measure is in place to prevent misuse of our platform.
                        </p>

                        <p style="color: var(--text-secondary); margin-bottom: 24px; font-size: 14px;">
                            To restore access, please contact our support team for assistance.
                        </p>

                        <a href="https://support.example.com" target="_blank" class="btn btn-primary" style="display: inline-block;">
                            📧 Contact Support
                        </a>
                    </div>
                </div>
            @elseif($verificationStatus === 'pending')
                <div class="card">
                    <h3><span class="icon">⏳</span> Verification Under Review</h3>
                    <div class="alert alert-info">
                        <span>📝</span>
                        <span>Your documents are being reviewed by our team. This usually takes 24-48 hours.</span>
                    </div>
                    @if($user->verification_failed_attempts > 0)
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px; background-color: #fef3c7; border-radius: 8px; margin-top: 16px; border-left: 4px solid #f59e0b;">
                            <p style="font-size: 13px; color: #92400e;">
                                ⚠️ Previous attempts: <strong>{{ $user->verification_failed_attempts }}</strong>
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Show retry option if there are failed attempts and attempts remaining -->
                @if($user->verification_failed_attempts > 0 && !$user->isVerificationBanned())
                    <!-- Previous Verification Details -->
                    <div class="card">
                        <h3 style="margin-bottom: 16px;">📋 Previous Verification Details</h3>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                            <!-- Previous CNIC -->
                            <div style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; background-color: #f8fafc;">
                                <p style="font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 8px;">CNIC Front</p>
                                @if($user->cnic_front_image)
                                    <div style="width: 100%; height: 120px; background-color: #e2e8f0; border-radius: 6px; display: flex; align-items: center; justify-content: center; margin-bottom: 8px;">
                                        <img src="{{ asset('storage/' . $user->cnic_front_image) }}" style="max-width: 100%; max-height: 100%; border-radius: 4px;" alt="CNIC Front">
                                    </div>
                                    <p style="font-size: 11px; color: #94a3b8;">✅ Uploaded</p>
                                @else
                                    <p style="font-size: 11px; color: #94a3b8;">Not uploaded</p>
                                @endif
                            </div>

                            <!-- Previous Live Photo -->
                            <div style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; background-color: #f8fafc;">
                                <p style="font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 8px;">Live Photo</p>
                                @if($user->live_photo)
                                    <div style="width: 100%; height: 120px; background-color: #e2e8f0; border-radius: 6px; display: flex; align-items: center; justify-content: center; margin-bottom: 8px;">
                                        <img src="{{ asset('storage/' . $user->live_photo) }}" style="max-width: 100%; max-height: 100%; border-radius: 4px;" alt="Live Photo">
                                    </div>
                                    <p style="font-size: 11px; color: #94a3b8;">✅ Uploaded</p>
                                @else
                                    <p style="font-size: 11px; color: #94a3b8;">Not uploaded</p>
                                @endif
                            </div>
                        </div>

                        <div style="background-color: #fee2e2; border-left: 4px solid #dc2626; padding: 12px; border-radius: 4px; margin-bottom: 16px;">
                            <p style="font-size: 13px; color: #7f1d1d; margin-bottom: 8px;">
                                <strong>❌ AI Face Match Failed</strong>
                            </p>
                            <p style="font-size: 12px; color: #991b1b;">
                                The AI detected that the faces in your CNIC and live photo don't match. This could be due to lighting, angles, or facial features not aligning.
                            </p>
                        </div>

                        <div style="display: flex; gap: 12px;">
                            <button type="button" class="btn btn-secondary" onclick="clearPreviousVerification()" style="flex: 1;">
                                🔄 Clear & Try Again
                            </button>
                            <button type="button" class="btn btn-primary" onclick="showPreviousVerificationTips()" style="flex: 1;">
                                💡 Tips for Better Photos
                            </button>
                        </div>
                    </div>
                @endif

            @elseif($verificationStatus !== 'pending')
                <!-- Show form for truly unverified (not pending) -->
                <!-- Verification Status Info -->
                @if($user->verification_failed_attempts > 0)
                    <div class="card">
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 16px; background-color: #fef3c7; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #f59e0b;">
                            <div style="flex: 1;">
                                <p style="font-weight: 600; color: #92400e; margin-bottom: 4px;">⚠️ Verification Failed - Try Again</p>
                                <p style="font-size: 13px; color: #b45309;">
                                    You have used <strong>{{ $user->verification_failed_attempts }} of 5</strong> attempts. 
                                    <strong>{{ $user->getRemainingVerificationAttempts() }} attempts remaining</strong>.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                @if($user->verification_failed_attempts > 0 && ($user->cnic_front_image || $user->live_photo))
                    <!-- Previous Verification Details -->
                    <div class="card">
                        <h3 style="margin-bottom: 16px;">📋 Previous Verification Details</h3>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                            <!-- Previous CNIC -->
                            <div style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; background-color: #f8fafc;">
                                <p style="font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 8px;">CNIC Front</p>
                                @if($user->cnic_front_image)
                                    <div style="width: 100%; height: 120px; background-color: #e2e8f0; border-radius: 6px; display: flex; align-items: center; justify-content: center; margin-bottom: 8px;">
                                        <img src="{{ asset('storage/' . $user->cnic_front_image) }}" style="max-width: 100%; max-height: 100%; border-radius: 4px;" alt="CNIC Front">
                                    </div>
                                    <p style="font-size: 11px; color: #94a3b8;">✅ Uploaded</p>
                                @else
                                    <p style="font-size: 11px; color: #94a3b8;">Not uploaded</p>
                                @endif
                            </div>

                            <!-- Previous Live Photo -->
                            <div style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; background-color: #f8fafc;">
                                <p style="font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 8px;">Live Photo</p>
                                @if($user->live_photo)
                                    <div style="width: 100%; height: 120px; background-color: #e2e8f0; border-radius: 6px; display: flex; align-items: center; justify-content: center; margin-bottom: 8px;">
                                        <img src="{{ asset('storage/' . $user->live_photo) }}" style="max-width: 100%; max-height: 100%; border-radius: 4px;" alt="Live Photo">
                                    </div>
                                    <p style="font-size: 11px; color: #94a3b8;">✅ Uploaded</p>
                                @else
                                    <p style="font-size: 11px; color: #94a3b8;">Not uploaded</p>
                                @endif
                            </div>
                        </div>

                        <div style="background-color: #fee2e2; border-left: 4px solid #dc2626; padding: 12px; border-radius: 4px; margin-bottom: 16px;">
                            <p style="font-size: 13px; color: #7f1d1d; margin-bottom: 8px;">
                                <strong>❌ AI Face Match Failed</strong>
                            </p>
                            <p style="font-size: 12px; color: #991b1b;">
                                The AI detected that the faces in your CNIC and live photo don't match. This could be due to lighting, angles, or facial features not aligning.
                            </p>
                        </div>

                        <div style="display: flex; gap: 12px;">
                            <button type="button" class="btn btn-secondary" onclick="clearPreviousVerification()" style="flex: 1;">
                                🔄 Clear & Try Again
                            </button>
                            <button type="button" class="btn btn-primary" onclick="showPreviousVerificationTips()" style="flex: 1;">
                                💡 Tips for Better Photos
                            </button>
                        </div>
                    </div>
                @endif

                <form id="verificationForm" enctype="multipart/form-data">
                    <!-- Info Card -->
                    <div class="card">
                        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                            <h3 style="color: white; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                                <span>🛡️</span> Start Your Verification
                            </h3>
                            @if($user->verification_failed_attempts > 0)
                                <p style="font-size: 14px; margin-bottom: 8px;">
                                    ✅ Great! You have <strong>{{ $user->getRemainingVerificationAttempts() }} more attempt{{ $user->getRemainingVerificationAttempts() === 1 ? '' : 's' }}</strong> to pass verification.
                                </p>
                                <p style="font-size: 14px; margin-bottom: 8px;">
                                    💡 <strong>Tip:</strong> Make sure your photos are clear, well-lit, and match your actual face.
                                </p>
                            @else
                                <p style="font-size: 14px;">
                                    Complete these 3 steps to become a Verified Dealer and unlock all features!
                                </p>
                            @endif
                        </div>
                    </div>

                    <!-- Step 1: CNIC -->
                    <div class="card">
                        <h3><span class="icon">🇵🇰</span> Step 1: CNIC Verification</h3>
                        <p style="font-size: 14px; color: var(--text-secondary); margin-bottom: 20px;">
                            Upload clear photos of both sides of your CNIC (Computerized National Identity Card).
                        </p>

                        <div class="form-row">
                            <div class="form-group">
                                <label>CNIC Number <span style="color: #ef4444;">*</span></label>
                                <input type="text" name="cnic_number" id="cnicNumber" placeholder="12345-1234567-1" required
                                       pattern="\d{5}-\d{7}-\d{1}" title="Format: 12345-1234567-1">
                            </div>
                            <div class="form-group">
                                <label>Phone Number (linked to CNIC) <span style="color: #ef4444;">*</span></label>
                                <input type="tel" name="phone" id="phoneNumber" placeholder="+923001234567" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>CNIC Front Side <span style="color: #ef4444;">*</span></label>
                                <div class="upload-area" id="cnicFrontArea" onclick="document.getElementById('cnicFront').click()">
                                    <div class="upload-icon">📄</div>
                                    <div class="upload-text">Click to upload front side</div>
                                    <input type="file" id="cnicFront" name="cnic_front" accept="image/*" onchange="handleFileUpload(this, 'cnicFrontArea')" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>CNIC Back Side <span style="color: #ef4444;">*</span></label>
                                <div class="upload-area" id="cnicBackArea" onclick="document.getElementById('cnicBack').click()">
                                    <div class="upload-icon">📄</div>
                                    <div class="upload-text">Click to upload back side</div>
                                    <input type="file" id="cnicBack" name="cnic_back" accept="image/*" onchange="handleFileUpload(this, 'cnicBackArea')" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Live Photo -->
                    <div class="card">
                        <h3><span class="icon">📸</span> Step 2: Live Photo (Camera Only)</h3>
                        <p style="font-size: 14px; color: var(--text-secondary); margin-bottom: 20px;">
                            Take a fresh photo using your camera. <strong>File upload is not allowed</strong> — this ensures you're a real person.
                        </p>

                        <div class="camera-section">
                            <video id="camera" autoplay playsinline style="display: none;"></video>
                            <canvas id="photoCanvas" style="display: none;"></canvas>

                            <div id="photoPreview" style="display: none;">
                                <img id="capturedPhoto" class="image-preview" alt="Captured Photo">
                            </div>

                            <div class="camera-controls">
                                <button type="button" class="btn btn-primary" id="startCameraBtn" onclick="startCamera()">
                                    📹 Open Camera
                                </button>
                                <button type="button" class="btn btn-success" id="capturePhotoBtn" onclick="capturePhoto()" style="display: none;">
                                    📸 Capture
                                </button>
                                <button type="button" class="btn btn-danger" id="stopCameraBtn" onclick="stopCamera()" style="display: none;">
                                    ⏹ Stop
                                </button>
                                <button type="button" class="btn btn-secondary" id="retakeBtn" onclick="retakePhoto()" style="display: none;">
                                    🔄 Retake
                                </button>
                            </div>
                            <input type="hidden" id="photoData" name="live_photo">
                        </div>
                    </div>

                    <!-- Step 3: Selfie + CNIC (Optional Bonus) -->
                    <div class="card">
                        <h3><span class="icon">🤳</span> Step 3: Selfie with CNIC (Optional)</h3>
                        <p style="font-size: 14px; color: var(--text-secondary); margin-bottom: 20px;">
                            Optionally upload a selfie while <strong>holding your CNIC next to your face</strong> for extra verification.
                        </p>

                        <div class="form-group">
                            <label>Selfie with CNIC <span style="color: var(--text-tertiary);">(optional)</span></label>
                            <div class="upload-area" id="selfieArea" onclick="document.getElementById('selfie').click()">
                                <div class="upload-icon">🤳</div>
                                <div class="upload-text">Click to upload selfie holding CNIC</div>
                                <input type="file" id="selfie" name="selfie" accept="image/*" onchange="handleFileUpload(this, 'selfieArea')">
                            </div>
                        </div>

                        <div class="alert alert-info" style="margin-top: 16px;">
                            <span>🤖</span>
                            <span>AI will compare your <strong>CNIC photo</strong> with your <strong>live camera photo</strong> to verify your identity automatically.</span>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="card" style="text-align: center;">
                        <button type="submit" class="btn btn-primary" id="submitVerificationBtn" style="width: 100%; padding: 16px; font-size: 16px;">
                            🛡️ Submit for Verification
                        </button>
                        <p style="font-size: 12px; color: var(--text-tertiary); margin-top: 12px;">
                            All your data is encrypted and securely stored. Verification is instant with AI.
                        </p>
                    </div>
                </form>
            @endif
        </div>

        <!-- ═══════ SHOP SECTION ═══════ -->
        <div class="section" id="shop">
            @if($isVerified)
                <div class="card">
                    <h3><span class="icon">🏪</span> My Shop</h3>
                    <p style="color: var(--text-secondary);">Customize your shop appearance and settings.</p>
                </div>
            @else
                <div class="card">
                    <h3><span class="icon">🔒</span> Shop Locked</h3>
                    <div class="alert alert-warning">
                        <span>⚠️</span>
                        <span>Complete your verification first to access shop settings.</span>
                    </div>
                </div>
            @endif
        </div>

        <!-- ═══════ ORDERS SECTION ═══════ -->
        <div class="section" id="orders">
            @if($isVerified)
                <div class="card">
                    <h3><span class="icon">📋</span> Orders</h3>
                    <p style="color: var(--text-secondary);">Your order management will appear here.</p>
                </div>
            @else
                <div class="card">
                    <h3><span class="icon">🔒</span> Orders Locked</h3>
                    <div class="alert alert-warning">
                        <span>⚠️</span>
                        <span>Complete your verification first to view orders.</span>
                    </div>
                </div>
            @endif
        </div>

        <!-- ═══════ SETTINGS SECTION ═══════ -->
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
                            <input type="email" value="{{ $user->email }}" disabled>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Tips Modal - Always Available -->
    <div id="tipsModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: white; padding: 32px; border-radius: 12px; max-width: 500px; max-height: 80vh; overflow-y: auto; margin: auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="margin: 0;">💡 Tips for Better Face Match</h3>
                <button type="button" onclick="document.getElementById('tipsModal').style.display='none'" style="background: none; border: none; font-size: 24px; cursor: pointer;">✕</button>
            </div>

            <div style="background-color: #f0fdf4; border-left: 4px solid #10b981; padding: 12px; border-radius: 4px; margin-bottom: 16px;">
                <p style="font-size: 13px; color: #065f46; margin: 0;">
                    ℹ️ Face detection compares your CNIC photo with your live camera photo. Better match = higher accuracy!
                </p>
            </div>

            <h4 style="margin-top: 20px; margin-bottom: 12px; color: #1e293b;">📸 For CNIC Photos:</h4>
            <ul style="font-size: 13px; color: #64748b; line-height: 1.8; padding-left: 20px;">
                <li>Place CNIC on white/light background</li>
                <li>Ensure all text is clearly visible</li>
                <li>Avoid glare and shadows on CNIC</li>
                <li>Photo should be straight (not at angle)</li>
                <li>Use good lighting (natural light preferred)</li>
            </ul>

            <h4 style="margin-top: 20px; margin-bottom: 12px; color: #1e293b;">🎥 For Live Photo:</h4>
            <ul style="font-size: 13px; color: #64748b; line-height: 1.8; padding-left: 20px;">
                <li>Face camera directly, look straight</li>
                <li>Use good natural lighting (face front light)</li>
                <li>Remove glasses if possible (or ensure no glare)</li>
                <li>Neutral expression - no extreme angles</li>
                <li>Ensure face takes up 60-70% of photo</li>
                <li>No filters, makeup changes, or face coverings</li>
            </ul>

            <h4 style="margin-top: 20px; margin-bottom: 12px; color: #1e293b;">⚠️ Common Issues:</h4>
            <ul style="font-size: 13px; color: #64748b; line-height: 1.8; padding-left: 20px;">
                <li>❌ Significant beard/facial hair differences</li>
                <li>❌ Heavy makeup (first photo is without makeup)</li>
                <li>❌ Wrong angle or poor lighting</li>
                <li>❌ Extreme expression (smiling vs. neutral)</li>
                <li>❌ Glasses in one photo but not the other</li>
            </ul>

            <button type="button" class="btn btn-primary" onclick="document.getElementById('tipsModal').style.display='none'" style="width: 100%; margin-top: 24px;">
                Got it! Close
            </button>
        </div>
    </div>
    <!-- AI Verification Modal -->
    <div class="ai-overlay" id="aiOverlay">
        <div class="ai-modal" id="aiModal">
            <div class="spinner" id="aiSpinner"></div>
            <h3 id="aiTitle">Verification</h3>
            <p id="aiMessage">Analyzing your documents with AI face recognition...</p>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let stream = null;

        // Handle initial section from URL
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const section = urlParams.get('section');
            if (section) {
                switchSection(section);
            }
        });

        // ═══════ FILE UPLOAD HANDLER ═══════
        function handleFileUpload(input, areaId) {
            const area = document.getElementById(areaId);
            if (input.files && input.files[0]) {
                const existingImg = area.querySelector('.image-preview');
                if (existingImg) existingImg.remove();

                area.classList.add('has-file');
                const reader = new FileReader();
                reader.onload = (e) => {
                    const icon = area.querySelector('.upload-icon');
                    const text = area.querySelector('.upload-text');
                    if (icon) icon.style.display = 'none';
                    if (text) text.textContent = '✅ ' + input.files[0].name;

                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'image-preview';
                    area.appendChild(img);
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // ═══════ CAMERA FUNCTIONS ═══════
        function startCamera() {
            navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user', width: 640, height: 480 } })
                .then(s => {
                    stream = s;
                    const cam = document.getElementById('camera');
                    cam.srcObject = stream;
                    cam.style.display = 'block';
                    cam.play();
                    document.getElementById('startCameraBtn').style.display = 'none';
                    document.getElementById('capturePhotoBtn').style.display = 'inline-flex';
                    document.getElementById('stopCameraBtn').style.display = 'inline-flex';
                })
                .catch(err => {
                    alert('Camera access denied. Please allow camera access to continue.\n\nError: ' + err.message);
                });
        }

        function stopCamera() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }
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

            const photoData = canvas.toDataURL('image/jpeg', 0.9);
            document.getElementById('photoData').value = photoData;
            document.getElementById('capturedPhoto').src = photoData;
            document.getElementById('photoPreview').style.display = 'block';

            stopCamera();
            document.getElementById('startCameraBtn').style.display = 'none';
            document.getElementById('retakeBtn').style.display = 'inline-flex';
        }

        function retakePhoto() {
            document.getElementById('photoPreview').style.display = 'none';
            document.getElementById('photoData').value = '';
            document.getElementById('retakeBtn').style.display = 'none';
            startCamera();
        }

        // ═══════ CLEAR PREVIOUS VERIFICATION ═══════
        function clearPreviousVerification() {
            if (!confirm('⚠️ This will clear all your previous uploads. You can then upload new photos and try verification again.\n\nAre you sure?')) {
                return;
            }

            fetch('{{ route('dealer.clear-verification') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('❌ Error: ' + (data.error || 'Failed to clear verification'));
                }
            })
            .catch(error => {
                alert('❌ Error clearing verification: ' + error.message);
            });
        }

        function showPreviousVerificationTips() {
            const modal = document.getElementById('tipsModal');
            if (modal) modal.style.display = 'flex';
        }

        // ═══════ VERIFICATION FORM SUBMISSION ═══════
        const verifForm = document.getElementById('verificationForm');
        if (verifForm) {
            verifForm.addEventListener('submit', async function(e) {
                e.preventDefault();

                // Validate
                const cnicNumber = document.getElementById('cnicNumber').value;
                const phone = document.getElementById('phoneNumber').value;
                const cnicFront = document.getElementById('cnicFront').files[0];
                const cnicBack = document.getElementById('cnicBack').files[0];
                const livePhoto = document.getElementById('photoData').value;

                if (!cnicNumber || !phone || !cnicFront || !cnicBack || !livePhoto) {
                    alert('Please complete all required fields.');
                    return;
                }

                showAIModal('processing');

                const formData = new FormData(this);

                try {
                    const response = await fetch('{{ route('dealer.verify') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: formData
                    });
                    const data = await response.json();
                    if (data.success) {
                        showAIModal('success', data.message);
                        setTimeout(() => location.reload(), 2000);
                    } else {
                        showAIModal('error', data.error);
                    }
                } catch (error) {
                    showAIModal('error', 'Something went wrong.');
                }
            });
        }

        function showAIModal(type, message = '') {
            const overlay = document.getElementById('aiOverlay');
            const title = document.getElementById('aiTitle');
            const msg = document.getElementById('aiMessage');
            const spinner = document.getElementById('aiSpinner');

            overlay.classList.add('active');
            
            if (type === 'processing') {
                title.textContent = 'Verification Your Documents';
                msg.textContent = 'Analyzing documents...';
                spinner.style.display = 'block';
            } else if (type === 'success') {
                title.textContent = '✓ Successfully Verified';
                msg.textContent = message || 'Verification complete!';
                spinner.style.display = 'none';
                setTimeout(() => overlay.classList.remove('active'), 2000);
            } else {
                title.textContent = '❌ Failed';
                msg.textContent = message || 'Verification failed.';
                spinner.style.display = 'none';
                setTimeout(() => overlay.classList.remove('active'), 3000);
            }
        }
    </script>
@endpush
