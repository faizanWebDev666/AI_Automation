@extends('layouts.dealer')

@section('title', 'Add New Property')

@section('content')
    {{-- ═══ PLAN LISTING LIMIT BANNER ═══ --}}
    @if(isset($currentPlan))
        @if(!$canAdd)
            {{-- Limit Reached - Block Form --}}
            <div style="background: linear-gradient(135deg, #fee2e2, #fecaca); border: 1px solid #fca5a5; border-radius: 16px; padding: 36px; text-align: center; margin-bottom: 24px;">
                <div style="font-size: 48px; margin-bottom: 16px;">🚫</div>
                <h3 style="font-size: 22px; font-weight: 800; color: #991b1b; margin-bottom: 12px;">Listing Limit Reached</h3>
                <p style="color: #b91c1c; font-size: 15px; margin-bottom: 8px;">
                    You've used all <strong>{{ $totalAllowed }}</strong> listings for this month on your <strong>{{ $currentPlan->name }}</strong>.
                </p>
                <p style="color: #dc2626; font-size: 14px; margin-bottom: 24px;">
                    Upgrade your plan to add more property listings.
                </p>
                <a href="{{ route('subscription.plans') }}" style="display: inline-flex; align-items: center; gap: 8px; background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white; padding: 14px 32px; border-radius: 12px; font-size: 15px; font-weight: 700; text-decoration: none; transition: all 0.3s; box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);">
                    ⚡ Upgrade Your Plan
                </a>
                <a href="{{ route('dealer.properties.index') }}" style="display: inline-flex; align-items: center; gap: 8px; background: transparent; color: #991b1b; border: 1px solid #fca5a5; padding: 14px 32px; border-radius: 12px; font-size: 15px; font-weight: 600; text-decoration: none; margin-left: 12px;">
                    ← Back to Listings
                </a>
            </div>
        @else
            {{-- Usage Info Banner --}}
            @php
                $isNearLimit = $totalAllowed !== null && $remainingListings <= 2;
                $bannerBg = $isNearLimit ? 'linear-gradient(135deg, #fef3c7, #fef08a)' : 'linear-gradient(135deg, #dbeafe, #bfdbfe)';
                $bannerBorder = $isNearLimit ? '1px solid #fcd34d' : '1px solid #93c5fd';
                $bannerColor = $isNearLimit ? '#92400e' : '#1e40af';
                $bannerIcon = $isNearLimit ? '⚠️' : 'ℹ️';
            @endphp
            <div style="background: {{ $bannerBg }}; border: {{ $bannerBorder }}; border-radius: 12px; padding: 16px 20px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span style="font-size: 18px;">{{ $bannerIcon }}</span>
                    <span style="font-size: 14px; color: {{ $bannerColor }}; font-weight: 600;">
                        {{ $currentPlan->name }}:
                        @if($totalAllowed === null)
                            Unlimited listings available
                        @else
                            {{ $remainingListings }} of {{ $totalAllowed }} listings remaining For Your Plan 
                        @endif
                    </span>
                </div>
                @if($currentPlan->slug !== 'gold')
                <a href="{{ route('subscription.plans') }}" style="font-size: 13px; color: #6366f1; font-weight: 600; text-decoration: none;">
                    Upgrade →
                </a>
                @endif
            </div>
        @endif
    @endif

    @if(!isset($canAdd) || $canAdd)
    {{-- ═══ ADD PROPERTY FORM ═══ --}}
    <div id="addPropertyView">
        <form id="propertyForm" enctype="multipart/form-data">
            @csrf
            <!-- Step 1: Basic Info -->
            <div class="card">
                <h3><span class="icon">🏠</span> Basic Information</h3>

                <div class="form-row">
                    <div class="form-group">
                        <label>Property Title <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="title" id="propTitle" placeholder="e.g. 5 Marla House in DHA Phase 5" required maxlength="200">
                    </div>
                    <div class="form-group">
                        <label>Price (PKR) <span style="color:#ef4444;">*</span></label>
                        <input type="number" name="price" id="propPrice" placeholder="5000000" required min="1000">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Property Type <span style="color:#ef4444;">*</span></label>
                        <select name="property_type" id="propType" required>
                            <option value="house">🏠 House</option>
                            <option value="portion">🏘️ Portion</option>
                            <option value="apartment">🏢 Apartment</option>
                            <option value="plot">📐 Plot</option>
                            <option value="commercial">🏪 Commercial</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Listing Type <span style="color:#ef4444;">*</span></label>
                        <select name="listing_type" id="propListingType" required>
                            <option value="sale">🔖 For Sale</option>
                            <option value="rent">🔑 For Rent</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Ownership <span style="color:#ef4444;">*</span></label>
                        <select name="ownership_type" id="propOwnership" required>
                            <option value="owner">👤 Owner</option>
                            <option value="dealer">🏪 Dealer</option>
                            <option value="builder">🏗️ Builder</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Area (Marla) <span style="color:#ef4444;">*</span></label>
                        <input type="number" name="area_marla" id="propMarla" placeholder="5" required min="0.5" max="500" step="0.5">
                    </div>
                </div>
            </div>

            <!-- Step 2: Property Details -->
            <div class="card">
                <h3><span class="icon">🛏️</span> Property Details</h3>

                <div class="form-row">
                    <div class="form-group">
                        <label>Bedrooms <span style="color:#ef4444;">*</span></label>
                        <select name="bedrooms" id="propBedrooms" required>
                            @for($i = 0; $i <= 15; $i++)
                                <option value="{{ $i }}" {{ $i == 2 ? 'selected' : '' }}>{{ $i }} {{ $i == 1 ? 'Bedroom' : 'Bedrooms' }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Bathrooms <span style="color:#ef4444;">*</span></label>
                        <select name="bathrooms" id="propBathrooms" required>
                            @for($i = 0; $i <= 10; $i++)
                                <option value="{{ $i }}" {{ $i == 1 ? 'selected' : '' }}>{{ $i }} {{ $i == 1 ? 'Bathroom' : 'Bathrooms' }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Kitchens <span style="color:#ef4444;">*</span></label>
                        <select name="kitchens" id="propKitchens" required>
                            @for($i = 0; $i <= 5; $i++)
                                <option value="{{ $i }}" {{ $i == 1 ? 'selected' : '' }}>{{ $i }} {{ $i == 1 ? 'Kitchen' : 'Kitchens' }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Floors <span style="color:#ef4444;">*</span></label>
                        <select name="floors" id="propFloors" required>
                            @for($i = 1; $i <= 10; $i++)
                                <option value="{{ $i }}" {{ $i == 1 ? 'selected' : '' }}>{{ $i }} {{ $i == 1 ? 'Floor' : 'Floors' }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Furnished Status <span style="color:#ef4444;">*</span></label>
                        <select name="furnished" id="propFurnished" required>
                            <option value="unfurnished">Unfurnished</option>
                            <option value="semi-furnished">Semi-Furnished</option>
                            <option value="furnished">Fully Furnished</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Contact Phone <span style="color:#ef4444;">*</span></label>
                        <input type="tel" name="contact_phone" id="propPhone" placeholder="+923001234567" required value="{{ Auth::user()->phone }}">
                    </div>
                </div>

                <div class="form-row full">
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" id="propDesc" rows="3" placeholder="Describe the property features, nearby amenities, etc." style="resize: vertical;"></textarea>
                    </div>
                </div>
            </div>

            <!-- Step 3: Location -->
            <div class="card">
                <h3><span class="icon">📍</span> Location</h3>

                <div class="form-row">
                    <div class="form-group">
                        <label>City <span style="color:#ef4444;">*</span></label>
                        <select name="city" id="propCity" required>
                            <option value="">Select City</option>
                            <option value="Karachi">Karachi</option>
                            <option value="Lahore">Lahore</option>
                            <option value="Islamabad">Islamabad</option>
                            <option value="Rawalpindi">Rawalpindi</option>
                            <option value="Faisalabad">Faisalabad</option>
                            <option value="Multan">Multan</option>
                            <option value="Peshawar">Peshawar</option>
                            <option value="Quetta">Quetta</option>
                            <option value="Sialkot">Sialkot</option>
                            <option value="Gujranwala">Gujranwala</option>
                            <option value="Hyderabad">Hyderabad</option>
                            <option value="Bahawalpur">Bahawalpur</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Area / Society <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="area_name" id="propArea" placeholder="e.g. DHA Phase 5, Block D" required>
                    </div>
                </div>

                <div class="form-row full">
                    <div class="form-group">
                        <label>Full Address <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="full_address" id="propAddress" placeholder="House #123, Street 45, Block D, DHA Phase 5" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>📌 Pin Location on Map <span style="color: var(--text-tertiary);">(click to place pin)</span></label>
                    <div id="propertyMap" style="height: 300px; border-radius: 12px; border: 2px solid var(--border-color); overflow: hidden; background: #e5e7eb;"></div>
                    <input type="hidden" name="latitude" id="propLat">
                    <input type="hidden" name="longitude" id="propLng">
                    <p id="mapCoords" style="font-size: 12px; color: var(--text-tertiary); margin-top: 8px;">No pin placed yet</p>
                </div>
            </div>

            <!-- Step 4: Images -->
            <div class="card">
                <h3><span class="icon">📸</span> Property Images</h3>

                <div class="alert alert-info" style="margin-bottom: 20px;">
                    <span>📷</span>
                    <span>At least <strong>1 live camera photo</strong> is mandatory. All images are auto-watermarked and checked for duplicates.</span>
                </div>

                <!-- Live Camera Photo (Required) -->
                <div class="form-group">
                    <label>📹 Live Camera Photo <span style="color:#ef4444;">*</span> <span style="color: var(--text-tertiary); font-weight: 400;">(cannot be faked)</span></label>
                    <div class="camera-section">
                        <video id="propCamera" autoplay playsinline style="display: none; width: 100%; max-width: 480px; border-radius: 12px; border: 2px solid var(--border-color);"></video>
                        <canvas id="propPhotoCanvas" style="display: none;"></canvas>

                        <div id="propPhotoPreview" style="display: none;">
                            <img id="propCapturedPhoto" class="image-preview" alt="Live Property Photo" style="max-width: 100%;">
                            <p style="color: #166534; font-size: 13px; margin-top: 8px;">✅ Live photo captured</p>
                        </div>

                        <div class="camera-controls">
                            <button type="button" class="btn btn-primary" id="propStartCamBtn" onclick="propStartCamera()">📹 Open Camera</button>
                            <button type="button" class="btn btn-success" id="propCapBtn" onclick="propCapturePhoto()" style="display:none;">📸 Capture</button>
                            <button type="button" class="btn btn-danger" id="propStopCamBtn" onclick="propStopCamera()" style="display:none;">⏹ Stop</button>
                            <button type="button" class="btn btn-secondary" id="propRetakeBtn" onclick="propRetakePhoto()" style="display:none;">🔄 Retake</button>
                        </div>
                        <input type="hidden" id="propLivePhotoData" name="live_photo">
                    </div>
                </div>

                <!-- Gallery Upload -->
                <div class="form-group" style="margin-top: 24px;">
                    <label>🖼️ Additional Photos <span style="color: var(--text-tertiary); font-weight: 400;">(max 10 images)</span></label>
                    <div class="upload-area" id="galleryUploadArea" onclick="document.getElementById('propImages').click()">
                        <div class="upload-icon">🖼️</div>
                        <div class="upload-text">Click to upload property photos (max 10)</div>
                        <input type="file" id="propImages" name="images[]" accept="image/*" multiple onchange="handleGalleryUpload(this)">
                    </div>
                    <div id="galleryPreview" style="display: flex; gap: 10px; flex-wrap: wrap; margin-top: 12px;"></div>
                </div>
            </div>

            <!-- Step 5: Documents -->
            <div class="card">
                <h3><span class="icon">📄</span> Documents</h3>

                <div class="form-row">
                    <div class="form-group">
                        <label>⚡ Electricity Bill <span style="color: var(--text-tertiary); font-weight: 400;">(optional)</span></label>
                        <div class="upload-area" id="elecBillArea" onclick="document.getElementById('elecBill').click()">
                            <div class="upload-icon">⚡</div>
                            <div class="upload-text">Upload electricity bill</div>
                            <input type="file" id="elecBill" name="electricity_bill" accept="image/*,.pdf" onchange="handleFileUpload(this, 'elecBillArea')">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>📝 Ownership Proof <span style="color: var(--text-tertiary); font-weight: 400;">(optional but powerful)</span></label>
                        <div class="upload-area" id="ownerProofArea" onclick="document.getElementById('ownerProof').click()">
                            <div class="upload-icon">📝</div>
                            <div class="upload-text">Upload rent/sale proof</div>
                            <input type="file" id="ownerProof" name="ownership_proof" accept="image/*,.pdf" onchange="handleFileUpload(this, 'ownerProofArea')">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="card" style="text-align: center;">
                <div class="alert alert-warning" style="text-align: left;">
                    <span>⚠️</span>
                    <span>All listings require <strong>admin approval</strong> before going live. Images are watermarked and checked for duplicates automatically.</span>
                </div>
                <button type="submit" class="btn btn-primary" id="submitPropertyBtn" style="width: 100%; padding: 16px; font-size: 16px;">
                    🏠 Submit Property for Review
                </button>
            </div>
        </form>
    </div>
    @endif
@endsection

@push('scripts')
    <script>
        let propStream = null;
        let propMap = null;
        let propMarker = null;

        document.addEventListener('DOMContentLoaded', function() {
            initPropertyMap();
        });

        // Map Functions
        function initPropertyMap() {
            if (propMap) return;
            propMap = L.map('propertyMap').setView([31.5204, 74.3587], 13); // Default to Lahore
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(propMap);
            propMap.on('click', function(e) {
                if (propMarker) propMap.removeLayer(propMarker);
                propMarker = L.marker(e.latlng).addTo(propMap);
                document.getElementById('propLat').value = e.latlng.lat;
                document.getElementById('propLng').value = e.latlng.lng;
                document.getElementById('mapCoords').textContent = `📍 Lat: ${e.latlng.lat.toFixed(6)}, Lng: ${e.latlng.lng.toFixed(6)}`;
            });
        }

        // Camera Functions
        function propStartCamera() {
            navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment', width: 640, height: 480 } })
                .then(s => {
                    propStream = s;
                    const cam = document.getElementById('propCamera');
                    cam.srcObject = propStream;
                    cam.style.display = 'block';
                    cam.play();
                    document.getElementById('propStartCamBtn').style.display = 'none';
                    document.getElementById('propCapBtn').style.display = 'inline-flex';
                    document.getElementById('propStopCamBtn').style.display = 'inline-flex';
                })
                .catch(err => alert('Camera access denied.'));
        }

        function propStopCamera() {
            if (propStream) {
                propStream.getTracks().forEach(track => track.stop());
                propStream = null;
            }
            document.getElementById('propCamera').style.display = 'none';
            document.getElementById('propStartCamBtn').style.display = 'inline-flex';
            document.getElementById('propCapBtn').style.display = 'none';
            document.getElementById('propStopCamBtn').style.display = 'none';
        }

        function propCapturePhoto() {
            const canvas = document.getElementById('propPhotoCanvas');
            const video = document.getElementById('propCamera');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);
            const photoData = canvas.toDataURL('image/jpeg', 0.9);
            document.getElementById('propLivePhotoData').value = photoData;
            document.getElementById('propCapturedPhoto').src = photoData;
            document.getElementById('propPhotoPreview').style.display = 'block';
            propStopCamera();
            document.getElementById('propStartCamBtn').style.display = 'none';
            document.getElementById('propRetakeBtn').style.display = 'inline-flex';
        }

        function propRetakePhoto() {
            document.getElementById('propPhotoPreview').style.display = 'none';
            document.getElementById('propLivePhotoData').value = '';
            document.getElementById('propRetakeBtn').style.display = 'none';
            propStartCamera();
        }

        function handleFileUpload(input, areaId) {
            const area = document.getElementById(areaId);
            if (input.files && input.files[0]) {
                area.classList.add('has-file');
                const reader = new FileReader();
                reader.onload = (e) => {
                    area.querySelector('.upload-icon').style.display = 'none';
                    area.querySelector('.upload-text').textContent = '✅ ' + input.files[0].name;
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'image-preview';
                    area.appendChild(img);
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function handleGalleryUpload(input) {
            const preview = document.getElementById('galleryPreview');
            preview.innerHTML = '';
            if (input.files) {
                Array.from(input.files).forEach(file => {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.style.width = '80px';
                        img.style.height = '80px';
                        img.style.objectFit = 'cover';
                        img.style.borderRadius = '8px';
                        preview.appendChild(img);
                    };
                    reader.readAsDataURL(file);
                });
            }
        }

        // Form Submit
        document.getElementById('propertyForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            // Check if live photo is captured
            if (!document.getElementById('propLivePhotoData').value) {
                alert('❌ Please capture a live photo of the property before submitting.');
                return;
            }

            const formData = new FormData(this);
            const btn = document.getElementById('submitPropertyBtn');
            btn.disabled = true;
            btn.innerHTML = '⌛ Submitting...';

            try {
                const response = await fetch('{{ route('dealer.property.store') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                const data = await response.json();
                if (data.success) {
                    alert('✅ Property submitted for review!');
                    window.location.href = '{{ route('dealer.properties.index') }}';
                } else if (data.limit_reached) {
                    // Plan limit reached - show upgrade prompt
                    if (confirm('🚫 ' + data.error + '\n\nWould you like to upgrade your plan?')) {
                        window.location.href = data.upgrade_url || '{{ route('subscription.plans') }}';
                    }
                } else {
                    let errorMessage = data.error || data.message || 'Submission failed.';
                    if (data.errors) {
                        errorMessage += '\n' + Object.values(data.errors).flat().join('\n');
                    }
                    alert('❌ ' + errorMessage);
                }
            } catch (err) {
                alert('❌ Error: ' + err.message);
            } finally {
                btn.disabled = false;
                btn.innerHTML = '🏠 Submit Property for Review';
            }
        });
    </script>
@endpush
