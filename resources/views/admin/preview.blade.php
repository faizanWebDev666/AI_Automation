<style>
    .preview-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 32px; }
    .btn-back { display: inline-flex; align-items: center; gap: 8px; color: var(--text-muted); text-decoration: none; font-weight: 600; padding: 10px 16px; border-radius: 12px; background: white; border: 1px solid var(--border); transition: var(--transition); }
    .btn-back:hover { border-color: var(--primary); color: var(--primary); }
    
    .preview-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 32px; }
    
    .gallery { margin-bottom: 32px; }
    .main-image { width: 100%; height: 500px; object-fit: cover; border-radius: var(--radius-xl); margin-bottom: 16px; box-shadow: var(--shadow-md); }
    .thumbnails { display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 12px; }
    .thumb { width: 100%; height: 90px; object-fit: cover; border-radius: var(--radius-md); cursor: pointer; border: 2px solid transparent; transition: var(--transition); }
    .thumb:hover { border-color: var(--primary); transform: scale(1.05); }

    .detail-card { background: white; padding: 32px; border-radius: var(--radius-xl); border: 1px solid var(--border); margin-bottom: 24px; }
    .detail-title { font-size: 28px; font-weight: 800; color: var(--text-heading); margin-bottom: 8px; }
    .detail-category { font-size: 13px; font-weight: 800; text-transform: uppercase; color: var(--primary); letter-spacing: 0.05em; margin-bottom: 24px; display: inline-block; padding: 6px 12px; background: var(--primary-soft); border-radius: 8px; }
    .price-tag { font-size: 32px; font-weight: 800; color: var(--success); display: flex; align-items: baseline; gap: 6px; margin-bottom: 24px; padding-bottom: 24px; border-bottom: 1px solid var(--border); }
    .price-tag span { font-size: 16px; color: var(--text-muted); font-weight: 600; }
    
    .specs-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 32px; }
    .spec-item { display: flex; align-items: center; gap: 12px; }
    .spec-icon { width: 44px; height: 44px; border-radius: 12px; background: var(--bg-page); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 20px; }
    .spec-info .label { font-size: 12px; color: var(--text-muted); font-weight: 600; }
    .spec-info .value { font-size: 15px; font-weight: 700; color: var(--text-heading); }

    .description-box { line-height: 1.7; color: var(--text-main); font-size: 15px; margin-bottom: 32px; }
    .section-label { font-size: 18px; font-weight: 800; color: var(--text-heading); margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }

    .doc-link { display: flex; align-items: center; justify-content: space-between; padding: 16px; border: 1px solid var(--border); border-radius: 12px; text-decoration: none; color: var(--text-heading); font-weight: 700; transition: var(--transition); margin-bottom: 12px; background: var(--bg-page); }
    .doc-link:hover { border-color: var(--primary); background: var(--primary-soft); }
    .doc-icon { display: flex; align-items: center; gap: 12px; }
    .doc-icon i { font-size: 24px; color: var(--primary); }

    .admin-actions { padding: 32px; background: white; border-radius: var(--radius-xl); border: 1px solid var(--border); box-shadow: var(--shadow-lg); position: sticky; top: 100px; }
    .btn-large { display: flex; align-items: center; justify-content: center; width: 100%; padding: 16px; border-radius: 14px; font-size: 16px; font-weight: 800; margin-bottom: 16px; gap: 10px; cursor: pointer; transition: var(--transition); border: none; }
</style>

<div class="preview-header">
    <a href="{{ url()->previous() == request()->url() ? route('admin.dashboard') : url()->previous() }}" class="btn-back">
        <i class="ri-arrow-left-line"></i> Back to List
    </a>
    <div style="display: flex; gap: 12px; align-items: center;">
        <span style="font-size: 14px; font-weight: 600; color: var(--text-muted);">Status:</span>
        <span class="status-badge" style="position: static; font-size: 14px;">
            @if($property->status === 'pending_review') 🟠 Pending
            @elseif($property->status === 'approved') 🟢 Approved
            @else 🔴 Rejected @endif
        </span>
    </div>
</div>

<div class="preview-grid">
    <div class="preview-main">
        <div class="gallery">
            @php 
                $primary = $property->images->where('is_live_photo', true)->first() ?? $property->images->first(); 
                $primPath = $primary ? preg_replace('/^public\//', '', $primary->image_path) : null;
            @endphp
            @if($primary)
                <img src="{{ asset('storage/' . $primPath) }}" class="main-image" id="mainImage">
            @else
                <img src="/placeholder.jpg" class="main-image">
            @endif
            
            @if($property->images->count() > 1)
                <div class="thumbnails">
                    @foreach($property->images as $img)
                        @php $thumbPath = preg_replace('/^public\//', '', $img->image_path); @endphp
                        <img src="{{ asset('storage/' . $thumbPath) }}" class="thumb" onclick="document.getElementById('mainImage').src = this.src">
                    @endforeach
                </div>
            @endif
        </div>

        <div class="detail-card">
            <span class="detail-category"><i class="ri-home-4-line"></i> {{ $property->property_type }} • {{ $property->listing_type }}</span>
            <h1 class="detail-title">{{ $property->title }}</h1>
            <div class="price-tag">
                <span>Rs</span> {{ number_format($property->price) }}
            </div>

            <div class="specs-grid">
                <div class="spec-item">
                    <div class="spec-icon"><i class="ri-ruler-2-line"></i></div>
                    <div class="spec-info"><div class="label">Area Size</div><div class="value">{{ $property->area_marla }} Marla</div></div>
                </div>
                <div class="spec-item">
                    <div class="spec-icon"><i class="ri-hotel-bed-fill"></i></div>
                    <div class="spec-info"><div class="label">Bedrooms</div><div class="value">{{ $property->bedrooms ?? 'N/A' }}</div></div>
                </div>
                <div class="spec-item">
                    <div class="spec-icon"><i class="ri-drop-line"></i></div>
                    <div class="spec-info"><div class="label">Bathrooms</div><div class="value">{{ $property->bathrooms ?? 'N/A' }}</div></div>
                </div>
                <div class="spec-item">
                    <div class="spec-icon"><i class="ri-sofa-line"></i></div>
                    <div class="spec-info"><div class="label">Furnished</div><div class="value">{{ $property->furnished ? 'Yes' : 'No' }}</div></div>
                </div>
            </div>

            <h3 class="section-label"><i class="ri-map-pin-line"></i> Location</h3>
            <p style="margin-bottom: 24px; color: var(--text-main); font-size: 15px;">
                <strong>{{ $property->area_name }}, {{ $property->city }}</strong><br>
                {{ $property->full_address }}
            </p>

            <h3 class="section-label"><i class="ri-file-text-line"></i> Description</h3>
            <div class="description-box">
                {!! nl2br(e($property->description)) !!}
            </div>
        </div>
    </div>

    <div class="preview-sidebar">
        <div class="admin-actions">
            <h3 class="section-label" style="font-size: 16px;"><i class="ri-user-star-line"></i> Dealer Information</h3>
            <div class="dealer-box" style="background: var(--bg-page); margin-bottom: 24px; border: 1px solid var(--border);">
                <div class="dealer-avatar" style="width: 48px; height: 48px; font-size: 18px;">{{ substr($property->user->name, 0, 1) }}</div>
                <div>
                    <p class="dealer-name" style="font-size: 16px;">{{ $property->user->name }}</p>
                    <p class="dealer-phone">{{ $property->contact_phone }}</p>
                    <p style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">Posted: {{ $property->created_at->format('M d, Y') }}</p>
                </div>
            </div>

            <h3 class="section-label" style="font-size: 16px; margin-top: 32px;"><i class="ri-verified-badge-line"></i> Verifications</h3>
            
            @if($property->ownership_proof)
                @php $proofPath = preg_replace('/^public\//', '', $property->ownership_proof); @endphp
                <a href="{{ asset('storage/' . $proofPath) }}" target="_blank" class="doc-link">
                    <div class="doc-icon"><i class="ri-file-shield-2-fill"></i> Ownership Proof</div>
                    <i class="ri-external-link-line" style="color: var(--text-muted);"></i>
                </a>
            @else
                <div class="doc-link" style="opacity: 0.5; pointer-events: none;">
                    <div class="doc-icon"><i class="ri-file-forbid-line"></i> No Ownership Proof</div>
                </div>
            @endif

            @if($property->electricity_bill)
                @php $billPath = preg_replace('/^public\//', '', $property->electricity_bill); @endphp
                <a href="{{ asset('storage/' . $billPath) }}" target="_blank" class="doc-link">
                    <div class="doc-icon"><i class="ri-lightbulb-flash-fill" style="color: var(--warning);"></i> Electricity Bill</div>
                    <i class="ri-external-link-line" style="color: var(--text-muted);"></i>
                </a>
            @else
                <div class="doc-link" style="opacity: 0.5; pointer-events: none;">
                    <div class="doc-icon"><i class="ri-file-forbid-line"></i> No Utility Bill</div>
                </div>
            @endif

            <hr style="border: 0; border-top: 1px solid var(--border); margin: 32px 0;">

            @if($property->status === 'pending_review')
                <button onclick="approveProperty({{ $property->id }})" class="btn-large btn-approve" style="color: white; background: var(--success); box-shadow: 0 10px 15px -3px rgba(16,185,129,0.3);">
                    <i class="ri-check-double-line"></i> Approve Listing
                </button>
                <button onclick="openRejectModal({{ $property->id }})" class="btn-large btn-reject" style="color: var(--danger); background: var(--danger-soft);">
                    <i class="ri-close-line"></i> Reject Listing
                </button>
            @elseif($property->status === 'approved')
                <div style="text-align: center; color: var(--success); font-weight: 800; padding: 16px; background: var(--success-soft); border-radius: 12px;">
                    <i class="ri-checkbox-circle-fill"></i> Currently Approved
                </div>
            @elseif($property->status === 'rejected')
                <div style="text-align: center; color: var(--danger); font-weight: 800; padding: 16px; background: var(--danger-soft); border-radius: 12px; margin-bottom: 16px;">
                    <i class="ri-close-circle-fill"></i> Currently Rejected
                </div>
                @if($property->admin_notes)
                    <div style="font-size: 13px; color: var(--text-muted); background: var(--bg-page); padding: 12px; border-radius: 8px;">
                        <strong>Reason:</strong> {{ $property->admin_notes }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
