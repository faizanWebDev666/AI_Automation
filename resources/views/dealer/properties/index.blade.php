@extends('layouts.dealer')

@section('title', 'My Listings')

@section('content')
    <div class="card">
        <h3><span class="icon">📋</span> My Listings</h3>
        <div id="listingsContainer">
            <p style="text-align: center; color: var(--text-tertiary); padding: 40px 0;">
                <span style="font-size: 48px; display: block; margin-bottom: 16px;">🏠</span>
                Loading your listings...
            </p>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Fetch listings when page loads
        document.addEventListener('DOMContentLoaded', function() {
            fetchListings();
        });

        function fetchListings() {
            fetch('{{ route('dealer.properties') }}', {
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('listingsContainer');
                if (data.success && data.properties && data.properties.length > 0) {
                    let html = '<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">';
                    data.properties.forEach(prop => {
                        const statusColor = prop.status === 'approved' ? '#10b981' : (prop.status === 'pending' ? '#f59e0b' : '#ef4444');
                        
                        // Get primary image
                        let imageUrl = '/placeholder.jpg'; // fallback
                        if (prop.images && prop.images.length > 0) {
                            let ipath = prop.images[0].image_path;
                            if (ipath.startsWith('public/')) {
                                ipath = ipath.substring(7);
                            }
                            imageUrl = '/storage/' + ipath;
                        }

                        html += `
                            <div class="card" style="padding: 0; overflow: hidden;">
                                <img src="${imageUrl}" style="width: 100%; height: 200px; object-fit: cover;">
                                <div style="padding: 16px;">
                                    <h4 style="margin-bottom: 8px;">${prop.title}</h4>
                                    <p style="font-weight: 700; color: var(--accent-primary); margin-bottom: 8px;">Rs ${parseInt(prop.price).toLocaleString()}</p>
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <span style="font-size: 12px; padding: 4px 8px; border-radius: 4px; background: ${statusColor}22; color: ${statusColor}; font-weight: 600;">
                                            ${prop.status.toUpperCase()}
                                        </span>
                                        <button onclick="deleteProperty(${prop.id})" class="btn btn-danger" style="padding: 6px 12px; font-size: 12px;">Delete</button>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    html += '</div>';
                    container.innerHTML = html;
                } else {
                    container.innerHTML = `
                        <div style="text-align: center; padding: 40px 0;">
                            <span style="font-size: 48px; display: block; margin-bottom: 16px;">📭</span>
                            <p>You haven't listed any properties yet.</p>
                            <a href="{{ route('dealer.properties.create') }}" class="btn btn-primary" style="margin-top: 20px;">Add Your First Property</a>
                        </div>
                    `;
                }
            });
        }

        function deleteProperty(id) {
            if (confirm('Are you sure you want to delete this listing?')) {
                fetch(`/dealer/property/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        fetchListings();
                    } else {
                        alert('Error deleting property');
                    }
                });
            }
        }
    </script>
@endpush
