<style>
    .dealer-profile-header { background: white; border-radius: var(--radius-xl); padding: 32px; border: 1px solid var(--border); margin-bottom: 32px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 24px; }
    .dealer-info-main { display: flex; align-items: center; gap: 24px; }
    .dealer-avatar-large { width: 80px; height: 80px; border-radius: 50%; background: var(--primary-soft); color: var(--primary); font-size: 32px; font-weight: 800; display: flex; align-items: center; justify-content: center; }
    .dealer-name-large { font-size: 24px; font-weight: 800; color: var(--text-heading); margin-bottom: 4px; }
    .dealer-contact { color: var(--text-secondary); font-size: 14px; display: flex; flex-direction: column; gap: 4px; }
    
    .verif-docs-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px; margin-bottom: 32px; }
    .doc-card { background: white; border-radius: var(--radius-lg); padding: 16px; border: 1px solid var(--border); }
    .doc-card-title { font-size: 14px; font-weight: 700; color: var(--text-heading); margin-bottom: 12px; display: flex; align-items: center; gap: 8px; }
    .doc-image-wrap { width: 100%; height: 200px; background: var(--bg-page); border-radius: 8px; overflow: hidden; display: flex; align-items: center; justify-content: center; margin-bottom: 12px; }
    .doc-image-wrap img { width: 100%; height: 100%; object-fit: contain; cursor: pointer; transition: transform 0.3s; }
    .doc-image-wrap img:hover { transform: scale(1.05); }
    
    .status-panel { background: var(--bg-page); border-radius: var(--radius-lg); padding: 24px; margin-bottom: 32px; border: 1px solid var(--border); }
    .verif-logs { font-size: 13px; color: var(--text-main); margin-top: 16px; padding: 12px; background: white; border-radius: 8px; border-left: 4px solid var(--primary); }
</style>

<div class="preview-header">
    <a href="{{ route('admin.dealers') }}" class="btn-back">
        <i class="ri-arrow-left-line"></i> Back to Dealers
    </a>
    <div style="display: flex; gap: 12px; align-items: center;">
        <span style="font-size: 14px; font-weight: 600; color: var(--text-muted);">Verification Status:</span>
        <span class="status-badge" style="position: static; font-size: 14px; background: {{ $dealer->verification_status == 'verified' ? 'var(--success)' : ($dealer->verification_banned ? 'var(--danger)' : 'var(--warning)') }}; color: white;">
            @if($dealer->verification_status === 'verified') 🟢 Verified
            @elseif($dealer->verification_banned) 🔴 Banned
            @elseif($dealer->verification_status === 'pending') 🟠 Pending
            @else ⚪ Unverified @endif
        </span>
    </div>
</div>

<div class="dealer-profile-header">
    <div class="dealer-info-main">
        <div class="dealer-avatar-large">
            {{ substr($dealer->name, 0, 1) }}
        </div>
        <div>
            <h2 class="dealer-name-large">{{ $dealer->name }}</h2>
            <div class="dealer-contact">
                <span><i class="ri-mail-line"></i> {{ $dealer->email }}</span>
                <span><i class="ri-phone-line"></i> {{ $dealer->phone ?? 'No phone provided' }}</span>
                @if($dealer->cnic_number)
                <span><i class="ri-profile-line"></i> CNIC: {{ $dealer->cnic_number }}</span>
                @endif
            </div>
        </div>
    </div>
    
    <div style="display: flex; gap: 12px;">
        @if($dealer->verification_status !== 'verified')
            <button onclick="manuallyVerifyDealer({{ $dealer->id }})" class="btn" style="background: var(--success); color: white; border: none; padding: 12px 24px;">
                <i class="ri-check-double-line"></i> Manually Verify
            </button>
        @endif
        @if(!$dealer->verification_banned)
            <button onclick="manuallyRejectDealer({{ $dealer->id }})" class="btn" style="background: var(--danger-soft); color: var(--danger); border: none; padding: 12px 24px;">
                <i class="ri-forbid-line"></i> Ban / Reject
            </button>
        @endif
    </div>
</div>

<div class="status-panel">
    <h3 class="section-label" style="font-size: 18px; margin-bottom: 8px;"><i class="ri-shield-check-line"></i> Verification Details</h3>
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; font-size: 14px;">
        <div>
            <strong>Failed Attempts:</strong> <span style="color: {{ $dealer->verification_failed_attempts > 0 ? 'var(--danger)' : 'var(--success)' }}">{{ $dealer->verification_failed_attempts }} / 5</span>
        </div>
        <div>
            <strong>Submitted On:</strong> {{ $dealer->verification_submitted_at ? $dealer->verification_submitted_at->format('M d, Y H:i') : 'N/A' }}
        </div>
        <div>
            <strong>Banned:</strong> {!! $dealer->verification_banned ? '<span style="color:var(--danger);font-weight:bold;">YES</span>' : 'NO' !!}
        </div>
    </div>
    
    @if($dealer->verification_notes || $dealer->verification_ban_reason)
        <div class="verif-logs">
            @if($dealer->verification_notes)
                <p style="margin-bottom: 8px;"><strong>System Notes:</strong> {{ $dealer->verification_notes }}</p>
            @endif
            @if($dealer->verification_ban_reason)
                <p style="color: var(--danger);"><strong>Ban Reason:</strong> {{ $dealer->verification_ban_reason }}</p>
            @endif
        </div>
    @endif
</div>

<h3 class="section-label" style="margin-bottom: 24px;"><i class="ri-folder-image-line"></i> Submitted Documents</h3>
<div class="verif-docs-grid">
    <div class="doc-card">
        <div class="doc-card-title"><i class="ri-profile-line"></i> CNIC Front</div>
        <div class="doc-image-wrap">
            @if($dealer->cnic_front_image)
                @php $frontPath = preg_replace('/^public\//', '', $dealer->cnic_front_image); @endphp
                <img src="{{ asset('storage/' . $frontPath) }}" onclick="openImageModal(this.src)" alt="CNIC Front">
            @else
                <span style="color: var(--text-muted); font-size: 13px;">Not uploaded</span>
            @endif
        </div>
    </div>
    <div class="doc-card">
        <div class="doc-card-title"><i class="ri-profile-line"></i> CNIC Back</div>
        <div class="doc-image-wrap">
            @if($dealer->cnic_back_image)
                @php $backPath = preg_replace('/^public\//', '', $dealer->cnic_back_image); @endphp
                <img src="{{ asset('storage/' . $backPath) }}" onclick="openImageModal(this.src)" alt="CNIC Back">
            @else
                <span style="color: var(--text-muted); font-size: 13px;">Not uploaded</span>
            @endif
        </div>
    </div>
    <div class="doc-card">
        <div class="doc-card-title"><i class="ri-camera-lens-line"></i> Live Photo</div>
        <div class="doc-image-wrap">
            @if($dealer->live_photo)
                @php $livePath = preg_replace('/^public\//', '', $dealer->live_photo); @endphp
                <img src="{{ asset('storage/' . $livePath) }}" onclick="openImageModal(this.src)" alt="Live Photo">
            @else
                <span style="color: var(--text-muted); font-size: 13px;">Not uploaded</span>
            @endif
        </div>
    </div>
    @if($dealer->selfie_with_cnic)
    <div class="doc-card">
        <div class="doc-card-title"><i class="ri-user-smile-line"></i> Selfie with CNIC</div>
        <div class="doc-image-wrap">
            @php $selfiePath = preg_replace('/^public\//', '', $dealer->selfie_with_cnic); @endphp
            <img src="{{ asset('storage/' . $selfiePath) }}" onclick="openImageModal(this.src)" alt="Selfie">
        </div>
    </div>
    @endif
</div>

<h3 class="section-label" style="margin-bottom: 24px; margin-top: 40px; padding-top: 40px; border-top: 1px solid var(--border);">
    <i class="ri-building-4-line"></i> Properties Listed by {{ $dealer->name }}
</h3>

@if($properties && count($properties) > 0)
    <div class="listings-grid">
        @foreach($properties as $prop)
            @include('admin.property-card', ['prop' => $prop])
        @endforeach
    </div>
    <div style="margin-top: 40px;">
        {{ $properties->links() }}
    </div>
@else
    <div style="text-align: center; padding: 48px; background: white; border-radius: 12px; border: 1px solid var(--border);">
        <i class="ri-home-smile-line" style="font-size: 48px; color: var(--text-muted); margin-bottom: 16px; display: block;"></i>
        <p style="color: var(--text-secondary);">This dealer hasn't listed any properties yet.</p>
    </div>
@endif

<!-- Fullscreen Image Modal -->
<div id="imageViewerModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 9999; justify-content: center; align-items: center;">
    <span onclick="document.getElementById('imageViewerModal').style.display='none'" style="position: absolute; top: 20px; right: 30px; color: white; font-size: 40px; cursor: pointer;">&times;</span>
    <img id="fullImageViewer" style="max-width: 90%; max-height: 90%; object-fit: contain;">
</div>

<script>
    function openImageModal(src) {
        document.getElementById('fullImageViewer').src = src;
        document.getElementById('imageViewerModal').style.display = 'flex';
    }

    function manuallyVerifyDealer(id) {
        if (!confirm('Are you sure you want to MANUALLY VERIFY this dealer?\nThis bypasses the AI check.')) return;
        
        let reason = prompt('Optional note for manual verification:');
        
        fetch(`/admin/dealer/${id}/verify`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ reason: reason || 'Verified by admin' })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) location.reload();
            else alert('Error: ' + data.error);
        });
    }

    function manuallyRejectDealer(id) {
        let reason = prompt('Please enter the reason for rejecting/banning this dealer:');
        if (!reason) { alert('A reason is required to reject a dealer.'); return; }
        
        fetch(`/admin/dealer/${id}/reject`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ reason: reason })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) location.reload();
            else alert('Error: ' + data.error);
        });
    }
</script>
