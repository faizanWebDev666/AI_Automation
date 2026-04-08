<div class="listing-card" id="prop-{{ $prop->id }}">
    <div class="card-media">
        @php
            $primaryImage = $prop->images->where('is_live_photo', true)->first() ?? $prop->images->first();
            $imagePath = $primaryImage ? preg_replace('/^public\//', '', $primaryImage->image_path) : null;
            $imageUrl = $imagePath ? asset('storage/' . $imagePath) : '/placeholder.jpg';
        @endphp
        <a href="{{ route('admin.property.show', $prop->id) }}">
            <img src="{{ $imageUrl }}" class="card-img" alt="Property">
        </a>
        <span class="status-badge bg-{{ $prop->status == 'pending_review' ? 'pending' : ($prop->status == 'approved' ? 'approved' : 'rejected') }}" 
              @if($prop->status == 'rejected') style="background:rgba(239,68,68,0.85);" @endif>
            @if($prop->status == 'pending_review') Reviewing 
            @elseif($prop->status == 'approved') Approved
            @else Rejected @endif
        </span>
    </div>
    
    <div class="card-content">
        <p class="card-category">{{ $prop->property_type }} • {{ $prop->listing_type }}</p>
        <a href="{{ route('admin.property.show', $prop->id) }}" style="text-decoration: none;">
            <h3 class="card-title">{{ $prop->title }}</h3>
        </a>
        
        <div class="card-meta">
            <div class="meta-item"><i class="ri-map-pin-line"></i> {{ $prop->city }}</div>
            <div class="meta-item"><i class="ri-ruler-2-line"></i> {{ $prop->area_marla }} Marla</div>
            @if($prop->bedrooms)
            <div class="meta-item"><i class="ri-hotel-bed-line"></i> {{ $prop->bedrooms }}</div>
            @endif
        </div>

        <div class="card-price">
            <span>Rs</span> {{ number_format($prop->price) }}
        </div>
        
        <div class="dealer-box">
            <div class="dealer-avatar">{{ substr($prop->user->name, 0, 1) }}</div>
            <div>
                <p class="dealer-name">{{ $prop->user->name }}</p>
                <p class="dealer-phone">{{ $prop->contact_phone }}</p>
            </div>
        </div>
        
        @if($prop->status == 'pending_review')
            <div class="card-actions">
                <button onclick="approveProperty({{ $prop->id }})" class="btn btn-approve">
                    <i class="ri-check-double-line"></i> Approve
                </button>
                <button onclick="openRejectModal({{ $prop->id }})" class="btn btn-reject">
                    <i class="ri-close-line"></i> Reject
                </button>
            </div>
        @endif
    </div>
</div>
