<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $property->title }} — AI Real Estate</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (via CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'accent-primary': '#6366f1',
                        'accent-secondary': '#ec4899',
                        'dark-slate': '#1e293b',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    
    <style>
        .glass-effect { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.3); }
        .gradient-text { background: linear-gradient(135deg, #6366f1 0%, #ec4899 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .gradient-bg { background: linear-gradient(135deg, #6366f1 0%, #ec4899 100%); }
    </style>
</head>
<body class="bg-[#f8fafc] text-dark-slate font-sans selection:bg-indigo-100 selection:text-indigo-900">

    <!-- Sticky Header -->
    <header class="fixed top-0 left-0 right-0 z-50 glass-effect">
        <nav class="container mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-10">
                    <span class="text-xl font-extrabold tracking-tight">AI Real Estate</span>
                </a>
            </div>
            
            <div class="hidden md:flex items-center gap-8">
                <a href="{{ route('home') }}#features" class="font-medium text-slate-600 hover:text-accent-primary transition-colors">Technology</a>
                <a href="{{ route('home') }}#latest-listings" class="font-medium text-slate-600 hover:text-accent-primary transition-colors">Properties</a>
                <a href="{{ route('login') }}" class="font-medium text-slate-600 hover:text-accent-primary transition-colors">Agent Login</a>
                <a href="{{ route('register') }}" class="gradient-bg text-white px-6 py-2.5 rounded-full font-semibold shadow-lg shadow-indigo-200 hover:shadow-indigo-300 transition-all hover:-translate-y-0.5 active:translate-y-0">
                    Join the Network
                </a>
            </div>
        </nav>
    </header>

    <main class="pt-32 pb-20">
        <div class="container mx-auto px-6">
            <!-- Property Header -->
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-8 mb-12">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="inline-flex items-center gap-2 bg-indigo-50 border border-indigo-100 px-4 py-1.5 rounded-full text-accent-primary text-xs font-bold uppercase tracking-wider">
                            {{ ucfirst($property->property_type) }} • For {{ ucfirst($property->listing_type) }}
                        </div>
                        <button class="p-2.5 rounded-full bg-white border border-slate-200 text-slate-400 hover:text-red-500 hover:border-red-100 hover:bg-red-50 transition-all shadow-sm group" title="Save to favorites">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:fill-current" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </button>
                        <button class="p-2.5 rounded-full bg-white border border-slate-200 text-slate-400 hover:text-accent-primary hover:border-indigo-100 hover:bg-indigo-50 transition-all shadow-sm" title="Share property">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                            </svg>
                        </button>
                    </div>
                    <h1 class="text-4xl md:text-6xl font-black leading-tight mb-4 text-slate-900 tracking-tight">{{ $property->title }}</h1>
                    <div class="flex items-center gap-2 text-slate-500 text-lg font-medium">
                        <span class="text-accent-primary bg-indigo-50 p-1.5 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </span>
                        {{ $property->full_address }}, {{ $property->area_name }}, {{ $property->city }}
                    </div>
                </div>
                <div class="text-left md:text-right bg-white p-6 rounded-[2rem] border border-slate-100 shadow-xl shadow-indigo-100/30">
                    <div class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-1">Asking Price</div>
                    <div class="text-4xl md:text-6xl font-black gradient-text">PKR {{ number_format($property->price) }}</div>
                    @if($property->listing_type === 'rent')
                        <div class="text-slate-400 text-sm font-bold mt-1 uppercase tracking-wider">Per Month</div>
                    @endif
                </div>
            </div>

            <!-- Image Gallery -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-16">
                @php
                    $images = $property->images;
                    $primaryImage = $images->where('is_live_photo', true)->first() ?? $images->first();
                    $otherImages = $images->filter(fn($img) => $img->id !== ($primaryImage->id ?? null));
                    $allImageUrls = $images->map(fn($img) => asset('storage/' . $img->image_path))->toArray();
                @endphp

                <div class="md:col-span-3 h-[400px] md:h-[650px] rounded-[2rem] overflow-hidden shadow-2xl relative group cursor-pointer border-4 border-white" onclick="openCarousel(0)">
                    @if($primaryImage)
                        <img src="{{ asset('storage/' . $primaryImage->image_path) }}" alt="{{ $property->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-1000">
                        @if($primaryImage->is_live_photo)
                            <div class="absolute top-8 left-8 bg-green-500/90 backdrop-blur-md text-white px-5 py-2.5 rounded-full text-xs font-bold uppercase tracking-widest flex items-center gap-2 shadow-xl border border-white/20">
                                <span class="flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-200 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-white"></span>
                                </span>
                                Verified Live Photo ✅
                            </div>
                        @endif
                        <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-center justify-center">
                            <div class="bg-white/20 backdrop-blur-xl p-6 rounded-full transform scale-90 group-hover:scale-100 transition-transform">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                                </svg>
                            </div>
                        </div>
                    @else
                        <div class="w-full h-full bg-slate-200 flex items-center justify-center">
                            <span class="text-slate-400">No Image Available</span>
                        </div>
                    @endif
                </div>

                <div class="grid grid-cols-2 md:grid-cols-1 gap-6">
                    @forelse($otherImages->take(3) as $index => $image)
                        <div class="h-[125px] md:h-[200px] rounded-[1.5rem] overflow-hidden shadow-lg group cursor-pointer relative border-4 border-white active:scale-95 transition-all" onclick="openCarousel({{ $index + 1 }})">
                            <img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $property->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                            <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                                </svg>
                            </div>
                            @if($loop->last && $otherImages->count() > 3)
                                <div class="absolute inset-0 bg-indigo-900/60 backdrop-blur-[2px] flex items-center justify-center text-white font-black text-2xl tracking-tighter">
                                    +{{ $otherImages->count() - 3 }} <span class="text-sm font-bold ml-1 uppercase">More</span>
                                </div>
                            @endif
                        </div>
                    @empty
                        @for($i = 0; $i < 3; $i++)
                            <div class="h-[125px] md:h-[200px] rounded-[1.5rem] bg-slate-100 flex items-center justify-center border-2 border-slate-200 border-dashed">
                                <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                        @endfor
                    @endforelse
                </div>
            </div>

            <!-- Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-16">
                <!-- Left: Property Details -->
                <div class="lg:col-span-2 space-y-16">
                    <!-- Key Features Cards -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div class="group bg-white p-8 rounded-[2rem] border border-slate-100 shadow-lg shadow-indigo-100/20 text-center hover:-translate-y-2 transition-all duration-300 hover:shadow-2xl">
                            <div class="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:gradient-bg group-hover:text-white transition-all">
                                <span class="text-2xl">📐</span>
                            </div>
                            <div class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-1">Total Area</div>
                            <div class="text-2xl font-black text-slate-900">{{ (float)$property->area_marla }} <span class="text-sm font-bold text-slate-400">Marla</span></div>
                        </div>
                        <div class="group bg-white p-8 rounded-[2rem] border border-slate-100 shadow-lg shadow-indigo-100/20 text-center hover:-translate-y-2 transition-all duration-300 hover:shadow-2xl">
                            <div class="w-14 h-14 bg-pink-50 text-pink-600 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:bg-pink-500 group-hover:text-white transition-all">
                                <span class="text-2xl">🛏️</span>
                            </div>
                            <div class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-1">Bedrooms</div>
                            <div class="text-2xl font-black text-slate-900">{{ $property->bedrooms }} <span class="text-sm font-bold text-slate-400">Beds</span></div>
                        </div>
                        <div class="group bg-white p-8 rounded-[2rem] border border-slate-100 shadow-lg shadow-indigo-100/20 text-center hover:-translate-y-2 transition-all duration-300 hover:shadow-2xl">
                            <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:bg-blue-500 group-hover:text-white transition-all">
                                <span class="text-2xl">🛁</span>
                            </div>
                            <div class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-1">Bathrooms</div>
                            <div class="text-2xl font-black text-slate-900">{{ $property->bathrooms }} <span class="text-sm font-bold text-slate-400">Baths</span></div>
                        </div>
                        <div class="group bg-white p-8 rounded-[2rem] border border-slate-100 shadow-lg shadow-indigo-100/20 text-center hover:-translate-y-2 transition-all duration-300 hover:shadow-2xl">
                            <div class="w-14 h-14 bg-yellow-50 text-yellow-600 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:bg-yellow-500 group-hover:text-white transition-all">
                                <span class="text-2xl">🍳</span>
                            </div>
                            <div class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-1">Kitchens</div>
                            <div class="text-2xl font-black text-slate-900">{{ $property->kitchens }} <span class="text-sm font-bold text-slate-400">Units</span></div>
                        </div>
                    </div>

                    <!-- Description Card -->
                    <div class="bg-white p-12 rounded-[3rem] border border-slate-100 shadow-2xl shadow-indigo-100/30">
                        <h3 class="text-3xl font-black mb-8 flex items-center gap-4 text-slate-900">
                            <span class="text-3xl">📝</span>
                            Property Description
                        </h3>
                        <div class="prose prose-slate max-w-none">
                            <p class="text-slate-600 leading-[1.8] text-lg whitespace-pre-line font-medium">
                                {{ $property->description ?: 'No description provided.' }}
                            </p>
                        </div>
                    </div>

                    <!-- Specifications Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div class="bg-white p-10 rounded-[2.5rem] border border-slate-100 shadow-xl shadow-indigo-100/20">
                            <h4 class="text-xl font-black mb-8 flex items-center gap-3 text-slate-900">
                                <span class="text-2xl">📋</span> Specifications
                            </h4>
                            <div class="space-y-6">
                                <div class="flex items-center justify-between pb-4 border-b border-slate-50">
                                    <div class="flex items-center gap-3">
                                        <span class="text-xl">🏢</span>
                                        <span class="text-slate-500 font-bold">Total Floors</span>
                                    </div>
                                    <span class="font-black text-slate-900 text-lg">{{ $property->floors }}</span>
                                </div>
                                <div class="flex items-center justify-between pb-4 border-b border-slate-50">
                                    <div class="flex items-center gap-3">
                                        <span class="text-xl">🪑</span>
                                        <span class="text-slate-500 font-bold">Furnishing</span>
                                    </div>
                                    <span class="font-black text-slate-900 text-lg">{{ ucfirst($property->furnished) }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <span class="text-xl">📄</span>
                                        <span class="text-slate-500 font-bold">Ownership</span>
                                    </div>
                                    <span class="font-black text-slate-900 text-lg">{{ ucfirst($property->ownership_type) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white p-10 rounded-[2.5rem] border border-slate-100 shadow-xl shadow-indigo-100/20">
                            <h4 class="text-xl font-black mb-8 flex items-center gap-3 text-slate-900">
                                <span class="text-2xl">📍</span> Location Info
                            </h4>
                            <div class="space-y-6">
                                <div class="flex items-center justify-between pb-4 border-b border-slate-50">
                                    <div class="flex items-center gap-3">
                                        <span class="text-xl">🌍</span>
                                        <span class="text-slate-500 font-bold">City</span>
                                    </div>
                                    <span class="font-black text-slate-900 text-lg">{{ $property->city }}</span>
                                </div>
                                <div class="flex items-center justify-between pb-4 border-b border-slate-50">
                                    <div class="flex items-center gap-3">
                                        <span class="text-xl">🏘️</span>
                                        <span class="text-slate-500 font-bold">Area Name</span>
                                    </div>
                                    <span class="font-black text-slate-900 text-lg">{{ $property->area_name }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <span class="text-xl">🆔</span>
                                        <span class="text-slate-500 font-bold">Property ID</span>
                                    </div>
                                    <span class="font-mono font-black text-indigo-600">#{{ str_pad($property->id, 6, '0', STR_PAD_LEFT) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Agent Sidebar -->
                <div class="space-y-8">
                    <div class="sticky top-32 space-y-8">
                        <!-- Agent Professional Card -->
                        <div class="bg-white p-10 rounded-[3rem] border-2 border-slate-100 shadow-2xl shadow-indigo-200/40 relative overflow-hidden group">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-50 rounded-full -mr-16 -mt-16 group-hover:scale-110 transition-transform duration-700"></div>
                            
                            <div class="relative flex flex-col items-center text-center mb-10">
                                <div class="relative mb-6">
                                    <div class="w-32 h-32 rounded-[2.5rem] p-1 bg-gradient-to-tr from-indigo-500 to-pink-500 shadow-2xl rotate-3 group-hover:rotate-0 transition-transform duration-500">
                                        <img src="{{ $property->user->avatar ? asset('storage/' . $property->user->avatar) : 'https://i.pravatar.cc/300?u=' . urlencode($property->user->name) }}" alt="{{ $property->user->name }}" class="w-full h-full object-cover rounded-[2.2rem] border-4 border-white">
                                    </div>
                                    @if($property->user->isVerified())
                                        <div class="absolute -bottom-2 -right-2 bg-green-500 text-white p-2 rounded-2xl shadow-xl border-4 border-white flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                                            <span class="text-[10px] font-black uppercase tracking-tighter">Verified</span>
                                        </div>
                                    @endif
                                </div>
                                <h2 class="text-2xl font-black text-slate-900 mb-2">{{ $property->user->name }}</h2>
                                <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-indigo-50 rounded-full text-indigo-600 text-xs font-black uppercase tracking-widest">
                                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                                    Active Now
                                </div>
                            </div>

                            <div class="space-y-4">
                                <a href="tel:{{ $property->contact_phone }}" class="flex items-center justify-center gap-3 w-full py-5 rounded-[1.5rem] border-2 border-indigo-100 text-accent-primary font-black hover:bg-indigo-50 transition-all hover:border-indigo-200 active:scale-95 group">
                                    <span class="text-xl group-hover:rotate-12 transition-transform">📞</span>
                                    {{ $property->contact_phone }}
                                </a>
                                @if(Auth::id() !== $property->user_id)
                                     <a href="{{ route('chat', ['user' => $property->user_id, 'property' => $property->id]) }}" class="flex items-center justify-center gap-3 w-full py-5 rounded-[1.5rem] gradient-bg text-white font-black shadow-xl shadow-indigo-200 hover:shadow-indigo-400 transition-all hover:-translate-y-1 active:scale-95 group">
                                         <span class="text-xl group-hover:-rotate-12 transition-transform">💬</span>
                                         Message Agent
                                     </a>
                                 @else
                                    <div class="flex items-center justify-center gap-3 w-full py-5 rounded-[1.5rem] bg-slate-100 text-slate-400 font-black cursor-not-allowed">
                                        <span class="text-xl">🏠</span>
                                        Your Listing
                                    </div>
                                @endif
                            </div>

                            <div class="mt-10 pt-8 border-t border-slate-50 text-center">
                                <div class="text-xs text-slate-400 font-bold uppercase tracking-widest mb-4">Verification Certificate</div>
                                <div class="bg-slate-50 rounded-2xl p-4 flex items-center justify-between border border-slate-100">
                                    <span class="text-[10px] font-bold text-slate-500">Serial ID:</span>
                                    <span class="text-[10px] font-mono font-black text-slate-900">VER-{{ strtoupper(substr(md5($property->user->id), 0, 12)) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Map Preview Card -->
                        <div class="bg-white p-8 rounded-[3rem] border border-slate-100 shadow-xl shadow-indigo-100/20 overflow-hidden relative h-48 group cursor-pointer">
                            <img src="https://images.unsplash.com/photo-1524661135-423995f22d0b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 opacity-40">
                            <div class="relative h-full flex flex-col items-center justify-center text-center">
                                <div class="w-12 h-12 bg-white rounded-2xl shadow-xl flex items-center justify-center mb-3 group-hover:gradient-bg group-hover:text-white transition-all">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7l5-2.5 5.553 2.776a1 1 0 01.447.894v10.764a1 1 0 01-1.447.894L15 17l-6 3z" />
                                    </svg>
                                </div>
                                <div class="text-sm font-black text-slate-900">View on Map</div>
                                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-1">Interactive Preview</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 py-20">
        <div class="container mx-auto px-6">
            <div class="grid md:grid-cols-4 gap-12 mb-16">
                <div class="col-span-1 md:col-span-1">
                    <div class="flex items-center gap-2 text-white mb-6">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-8">
                        <span class="text-xl font-extrabold tracking-tight">AI Real Estate</span>
                    </div>
                    <p class="mb-6 leading-relaxed">Providing the world's most advanced AI technology for the modern real estate professional.</p>
                </div>
                <!-- ... other footer columns ... -->
            </div>
            
            <div class="pt-8 border-t border-slate-800 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-sm">&copy; 2026 AI Real Estate. All rights reserved.</p>
                <p class="text-sm">Revolutionizing Real Estate with ❤️</p>
            </div>
        </div>
    </footer>

    <!-- Image Carousel Modal -->
    <div id="carouselModal" class="fixed inset-0 z-[100] hidden bg-black/95 backdrop-blur-sm flex flex-col items-center justify-center p-4 md:p-10">
        <!-- Close Button -->
        <button onclick="closeCarousel()" class="absolute top-6 right-6 text-white hover:text-indigo-400 transition-colors z-[110]">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <!-- Main Carousel Content -->
        <div class="relative w-full max-w-6xl h-full flex items-center justify-center">
            <!-- Previous Button -->
            <button onclick="prevImage()" class="absolute left-0 md:-left-20 top-1/2 -translate-y-1/2 text-white hover:text-indigo-400 transition-colors p-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>

            <!-- Image Display -->
            <div class="w-full h-full flex flex-col items-center justify-center gap-6">
                <div class="relative w-full h-[60vh] md:h-[75vh] flex items-center justify-center">
                    <img id="carouselImage" src="" alt="Property Image" class="max-w-full max-h-full object-contain rounded-2xl shadow-2xl transition-all duration-300">
                </div>
                
                <!-- Image Counter & Info -->
                <div class="text-center text-white space-y-2">
                    <div id="imageCounter" class="bg-white/10 px-4 py-1 rounded-full text-sm font-medium inline-block">1 / 5</div>
                    <h3 class="text-xl font-bold text-indigo-200">{{ $property->title }}</h3>
                </div>
            </div>

            <!-- Next Button -->
            <button onclick="nextImage()" class="absolute right-0 md:-right-20 top-1/2 -translate-y-1/2 text-white hover:text-indigo-400 transition-colors p-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>

        <!-- Thumbnails Strip -->
        <div class="mt-8 flex gap-3 overflow-x-auto max-w-full p-2 no-scrollbar">
            @foreach($images as $index => $image)
                <div onclick="setCarouselImage({{ $index }})" class="carousel-thumb flex-shrink-0 w-20 h-20 rounded-xl overflow-hidden cursor-pointer border-2 border-transparent hover:border-indigo-400 transition-all">
                    <img src="{{ asset('storage/' . $image->image_path) }}" class="w-full h-full object-cover opacity-50 hover:opacity-100 transition-opacity">
                </div>
            @endforeach
        </div>
    </div>

    <script>
        let currentImageIndex = 0;
        const images = @json($allImageUrls);
        const modal = document.getElementById('carouselModal');
        const carouselImg = document.getElementById('carouselImage');
        const counter = document.getElementById('imageCounter');
        const thumbs = document.querySelectorAll('.carousel-thumb');

        function openCarousel(index) {
            currentImageIndex = index;
            updateCarousel();
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Prevent scrolling
        }

        function closeCarousel() {
            modal.classList.add('hidden');
            document.body.style.overflow = ''; // Restore scrolling
        }

        function nextImage() {
            currentImageIndex = (currentImageIndex + 1) % images.length;
            updateCarousel();
        }

        function prevImage() {
            currentImageIndex = (currentImageIndex - 1 + images.length) % images.length;
            updateCarousel();
        }

        function setCarouselImage(index) {
            currentImageIndex = index;
            updateCarousel();
        }

        function updateCarousel() {
            // Update Image
            carouselImg.style.opacity = '0';
            setTimeout(() => {
                carouselImg.src = images[currentImageIndex];
                carouselImg.style.opacity = '1';
            }, 150);

            // Update Counter
            counter.innerText = `${currentImageIndex + 1} / ${images.length}`;

            // Update Thumbnails
            thumbs.forEach((thumb, idx) => {
                const img = thumb.querySelector('img');
                if (idx === currentImageIndex) {
                    thumb.classList.add('border-indigo-500', 'scale-110');
                    img.classList.remove('opacity-50');
                    img.classList.add('opacity-100');
                } else {
                    thumb.classList.remove('border-indigo-500', 'scale-110');
                    img.classList.remove('opacity-100');
                    img.classList.add('opacity-50');
                }
            });
        }

        // Close on escape
        document.addEventListener('keydown', (e) => {
            if (modal.classList.contains('hidden')) return;
            
            if (e.key === 'Escape') closeCarousel();
            if (e.key === 'ArrowRight') nextImage();
            if (e.key === 'ArrowLeft') prevImage();
        });

        // Close on click outside image
        modal.addEventListener('click', (e) => {
            if (e.target === modal || e.target.closest('.relative.w-full.max-w-6xl')) {
                if (e.target === modal) closeCarousel();
            }
        });
    </script>
</body>
</html>
