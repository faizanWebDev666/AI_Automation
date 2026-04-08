<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AI Real Estate — Smart Property Solutions & Automation</title>
    
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
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        .float-animation { animation: float 3s ease-in-out infinite; }
        .glass-effect { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.3); }
        .gradient-text { background: linear-gradient(135deg, #6366f1 0%, #ec4899 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .gradient-bg { background: linear-gradient(135deg, #6366f1 0%, #ec4899 100%); }
        .gradient-border { border: 1px solid; border-image: linear-gradient(135deg, #6366f1, #ec4899) 1; }
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
                <a href="#features" class="font-medium text-slate-600 hover:text-accent-primary transition-colors">Technology</a>
                <a href="#latest-listings" class="font-medium text-slate-600 hover:text-accent-primary transition-colors">Properties</a>
                <a href="{{ route('pricing') }}" class="font-medium text-slate-600 hover:text-accent-primary transition-colors">Plans</a>
                <a href="{{ route('testimonials') }}" class="font-medium text-slate-600 hover:text-accent-primary transition-colors">Success Stories</a>
                <a href="{{ route('login') }}" class="font-medium text-slate-600 hover:text-accent-primary transition-colors">Agent Login</a>
                <a href="{{ route('register') }}" class="gradient-bg text-white px-6 py-2.5 rounded-full font-semibold shadow-lg shadow-indigo-200 hover:shadow-indigo-300 transition-all hover:-translate-y-0.5 active:translate-y-0">
                    Join the Network
                </a>
            </div>

            <!-- Mobile Menu Button -->
            <button class="md:hidden text-slate-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                </svg>
            </button>
        </nav>
    </header>

    <main class="pt-24">
        <!-- Hero Section -->
        <section class="relative container mx-auto px-6 py-20 md:py-32 flex flex-col md:flex-row items-center gap-16 overflow-visible">
            <!-- Background Decorative Elements -->
            <div class="absolute top-0 left-1/4 w-96 h-96 bg-indigo-200/20 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-pulse -z-10"></div>
            <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-pink-200/20 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-pulse delay-1000 -z-10"></div>

            <div class="md:w-3/5 text-center md:text-left z-10">
                <div class="inline-flex items-center gap-3 bg-white/50 backdrop-blur-md border border-indigo-100 px-5 py-2 rounded-full shadow-sm mb-8 hover:border-indigo-200 transition-colors group cursor-default">
                    <span class="flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-3 w-3 rounded-full bg-indigo-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-indigo-600"></span>
                    </span>
                    <span class="text-indigo-900 text-sm font-bold tracking-wide uppercase">Next-Gen Real Estate Platform</span>
                </div>
                
                <h1 class="text-6xl md:text-8xl font-black leading-[1.1] mb-8 tracking-tighter text-slate-900">
                    Your Agency, <br>
                    <span class="gradient-text">Supercharged</span> by AI.
                </h1>
                
                <p class="text-xl md:text-2xl text-slate-500 mb-12 max-w-2xl leading-relaxed font-medium">
                    Stop wasting hours on manual tasks. Automate listings, score leads instantly, and close deals faster with our advanced AI ecosystem.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-5 justify-center md:justify-start">
                    <a href="{{ route('register') }}" class="gradient-bg text-white px-10 py-5 rounded-[2rem] font-black text-xl shadow-2xl shadow-indigo-200 hover:shadow-indigo-400 transition-all hover:-translate-y-1 text-center group">
                        Get Started Now
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 inline-block ml-2 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                    <a href="#features" class="bg-white text-slate-900 border-2 border-slate-100 px-10 py-5 rounded-[2rem] font-black text-xl hover:bg-slate-50 hover:border-slate-200 transition-all text-center shadow-lg shadow-slate-100/50">
                        Explore Features
                    </a>
                </div>

                <div class="mt-16 flex items-center justify-center md:justify-start gap-6">
                    <div class="flex -space-x-4">
                        @for($i=11; $i<=15; $i++)
                            <img src="https://i.pravatar.cc/150?u={{ $i }}" class="w-12 h-12 rounded-full border-4 border-white shadow-lg ring-1 ring-slate-100" alt="Agent">
                        @endfor
                        <div class="w-12 h-12 rounded-full bg-indigo-600 border-4 border-white shadow-lg flex items-center justify-center text-white text-xs font-black ring-1 ring-indigo-100">
                            +2k
                        </div>
                    </div>
                    <div class="text-left">
                        <div class="flex text-yellow-400 mb-1">
                            @for($i=0; $i<5; $i++)
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                        </div>
                        <span class="text-sm font-black text-slate-900 tracking-tight">Top Rated by 5,000+ Agencies</span>
                    </div>
                </div>
            </div>

            <div class="md:w-2/5 relative">
                <!-- Interactive UI Mockup -->
                <div class="relative bg-white p-2 rounded-[3.5rem] shadow-[0_50px_100px_-20px_rgba(99,102,241,0.15)] border border-slate-100 float-animation overflow-hidden">
                    <div class="bg-slate-50 rounded-[3rem] overflow-hidden">
                        <div class="p-6 border-b border-slate-200/60 flex items-center justify-between bg-white/50 backdrop-blur-sm">
                            <div class="flex gap-2">
                                <div class="w-3 h-3 bg-red-400 rounded-full shadow-inner"></div>
                                <div class="w-3 h-3 bg-yellow-400 rounded-full shadow-inner"></div>
                                <div class="w-3 h-3 bg-green-400 rounded-full shadow-inner"></div>
                            </div>
                            <div class="px-4 py-1 bg-indigo-50 rounded-full">
                                <span class="text-[10px] font-black text-indigo-600 uppercase tracking-[0.2em]">Listing AI Engine</span>
                            </div>
                        </div>
                        
                        <div class="p-8 space-y-6">
                            <div class="flex items-center gap-4">
                                <div class="w-16 h-16 bg-indigo-100 rounded-2xl flex items-center justify-center text-indigo-600 shadow-inner">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <div class="flex-1">
                                    <div class="h-3 bg-slate-200 rounded-full w-3/4 mb-3"></div>
                                    <div class="h-2 bg-slate-100 rounded-full w-1/2"></div>
                                </div>
                            </div>
                            
                            <div class="space-y-3">
                                <div class="h-2 bg-slate-200/50 rounded-full w-full"></div>
                                <div class="h-2 bg-slate-200/50 rounded-full w-full"></div>
                                <div class="h-2 bg-slate-200/50 rounded-full w-4/5"></div>
                            </div>

                            <div class="p-4 bg-indigo-600 rounded-2xl shadow-xl shadow-indigo-200 flex items-center justify-center gap-3 transform hover:scale-[1.02] transition-transform cursor-pointer">
                                <div class="w-5 h-5 bg-white/20 rounded-full animate-pulse"></div>
                                <span class="text-white text-sm font-black tracking-wide uppercase">Optimizing Description...</span>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div class="p-4 bg-white border border-slate-100 rounded-2xl shadow-sm">
                                    <div class="text-[10px] font-black text-slate-400 uppercase mb-2">SEO Score</div>
                                    <div class="text-xl font-black text-green-500">98%</div>
                                </div>
                                <div class="p-4 bg-white border border-slate-100 rounded-2xl shadow-sm">
                                    <div class="text-[10px] font-black text-slate-400 uppercase mb-2">Confidence</div>
                                    <div class="text-xl font-black text-indigo-600">High</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Floating Stats -->
                <div class="absolute -right-8 top-1/4 bg-white p-5 rounded-3xl shadow-2xl border border-slate-50 flex items-center gap-4 animate-bounce delay-500">
                    <div class="w-12 h-12 bg-green-100 rounded-2xl flex items-center justify-center text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase">Sales Increase</div>
                        <div class="text-lg font-black text-slate-900">+42%</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="bg-white py-24">
            <div class="container mx-auto px-6">
                <div class="text-center mb-20">
                    <h2 class="text-3xl md:text-5xl font-extrabold mb-4">The Real Estate <span class="gradient-text">OS</span></h2>
                    <p class="text-slate-500 max-w-2xl mx-auto">Everything an agent needs to automate their workflow and focus on closing deals.</p>
                </div>
                
                <div class="grid md:grid-cols-3 gap-10">
                    <!-- Feature 1 -->
                    <div class="group p-10 rounded-[2rem] bg-slate-50 border border-slate-100 hover:bg-white hover:shadow-2xl hover:shadow-indigo-100 transition-all hover:-translate-y-2">
                        <div class="w-14 h-14 bg-indigo-100 rounded-2xl flex items-center justify-center text-accent-primary mb-6 group-hover:gradient-bg group-hover:text-white transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-4">AI Listing Assistant</h3>
                        <p class="text-slate-500 leading-relaxed">Instantly create professional, SEO-optimized property descriptions from just a few photos and basic specs.</p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="group p-10 rounded-[2rem] bg-slate-50 border border-slate-100 hover:bg-white hover:shadow-2xl hover:shadow-indigo-100 transition-all hover:-translate-y-2">
                        <div class="w-14 h-14 bg-pink-100 rounded-2xl flex items-center justify-center text-accent-secondary mb-6 group-hover:gradient-bg group-hover:text-white transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-4">Predictive Lead Scoring</h3>
                        <p class="text-slate-500 leading-relaxed">Our AI analyzes buyer behavior to identify who is actually ready to purchase, saving you hours of follow-ups.</p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="group p-10 rounded-[2rem] bg-slate-50 border border-slate-100 hover:bg-white hover:shadow-2xl hover:shadow-indigo-100 transition-all hover:-translate-y-2">
                        <div class="w-14 h-14 bg-indigo-100 rounded-2xl flex items-center justify-center text-accent-primary mb-6 group-hover:gradient-bg group-hover:text-white transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-4">Market Intelligence</h3>
                        <p class="text-slate-500 leading-relaxed">Real-time data on local neighborhood trends and pricing to help you advise your clients with confidence.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Latest Listings Section -->
        <section id="latest-listings" class="py-24 bg-slate-50">
            <div class="container mx-auto px-6">
                <div class="text-center mb-20">
                    <h2 class="text-3xl md:text-5xl font-extrabold mb-4">Latest <span class="gradient-text">Properties</span></h2>
                    <p class="text-slate-500 max-w-2xl mx-auto">Explore the newest listings from our verified dealers.</p>
                </div>
                <div id="property-listings" class="grid md:grid-cols-3 gap-8 max-w-6xl mx-auto">
                    @forelse($properties as $property)
                        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-xl shadow-slate-200/50 overflow-hidden group hover:shadow-2xl transition-all duration-300">
                            <div class="relative h-64 overflow-hidden">
                                @php
                                    $primaryImage = $property->images->first();
                                    $imagePath = $primaryImage ? asset('storage/' . $primaryImage->image_path) : 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80';
                                @endphp
                                <img src="{{ $imagePath }}" alt="{{ $property->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                <div class="absolute top-4 left-4">
                                    <span class="bg-white/90 backdrop-blur-sm text-accent-primary px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider shadow-sm">
                                        {{ ucfirst($property->listing_type) }}
                                    </span>
                                </div>
                                <div class="absolute bottom-4 left-4">
                                    <div class="bg-indigo-600 text-white px-4 py-1.5 rounded-lg text-lg font-bold shadow-lg">
                                        PKR {{ number_format($property->price) }}
                                    </div>
                                </div>
                            </div>
                            <div class="p-8">
                                <div class="flex items-center gap-2 text-slate-400 text-sm mb-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    {{ $property->area_name }}, {{ $property->city }}
                                </div>
                                <h3 class="text-xl font-bold mb-4 line-clamp-1 group-hover:text-accent-primary transition-colors">{{ $property->title }}</h3>
                                
                                <div class="flex items-center justify-between border-t border-slate-100 pt-6">
                                    <div class="flex gap-4">
                                        <div class="flex items-center gap-1.5 text-slate-500">
                                            <span class="font-bold text-slate-900">{{ $property->bedrooms }}</span>
                                            <span class="text-xs">Beds</span>
                                        </div>
                                        <div class="flex items-center gap-1.5 text-slate-500">
                                            <span class="font-bold text-slate-900">{{ $property->bathrooms }}</span>
                                            <span class="text-xs">Baths</span>
                                        </div>
                                        <div class="flex items-center gap-1.5 text-slate-500">
                                            <span class="font-bold text-slate-900">{{ (float)$property->area_marla }}</span>
                                            <span class="text-xs">Marla</span>
                                        </div>
                                    </div>
                                    <a href="{{ route('properties.show', $property->id) }}" class="w-10 h-10 gradient-bg rounded-full flex items-center justify-center text-white shadow-lg shadow-indigo-200 hover:shadow-indigo-300 transition-all hover:-translate-y-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-12">
                            <div class="bg-white p-12 rounded-[3rem] border border-dashed border-slate-200">
                                <p class="text-slate-400 text-lg">No properties listed yet. Check back soon!</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="container mx-auto px-6 py-20">
            <div class="gradient-bg rounded-[3rem] p-12 md:p-20 text-center text-white relative overflow-hidden shadow-2xl shadow-indigo-200">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-10 rounded-full -mr-20 -mt-20"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-white opacity-10 rounded-full -ml-20 -mb-20"></div>
                
                <h2 class="text-4xl md:text-6xl font-extrabold mb-6 relative z-10">Upgrade Your Agency Today</h2>
                <p class="text-xl text-indigo-100 mb-10 max-w-2xl mx-auto relative z-10">Join the thousands of agents using AI to dominate their local markets.</p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center relative z-10">
                    <a href="{{ route('register') }}" class="bg-white text-accent-primary px-10 py-5 rounded-2xl font-bold text-xl hover:bg-slate-50 transition-all hover:-translate-y-1 shadow-xl">Get Started for Free</a>
                    <a href="{{ route('pricing') }}" class="bg-indigo-700/30 text-white border border-indigo-400/30 px-10 py-5 rounded-2xl font-bold text-xl hover:bg-indigo-700/50 transition-all">View Agency Plans</a>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 py-20">
        <div class="container mx-auto px-6">
            <div class="grid md:grid-cols-4 gap-12 mb-16">
                <div class="col-span-1 md:col-span-1">
                    <div class="flex items-center gap-2 text-white mb-6">
                        <a href="{{ route('home') }}" class="flex items-center gap-2">
                            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-8">
                            <span class="text-xl font-extrabold tracking-tight">AI Real Estate</span>
                        </a>
                    </div>
                    <p class="mb-6 leading-relaxed">Providing the world's most advanced AI technology for the modern real estate professional.</p>
                    <div class="flex gap-4">
                        <a href="#" class="w-10 h-10 bg-slate-800 rounded-full flex items-center justify-center hover:bg-accent-primary transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                        </a>
                        <a href="#" class="w-10 h-10 bg-slate-800 rounded-full flex items-center justify-center hover:bg-accent-primary transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 1.166.054 1.8.248 2.22.411.558.217.957.477 1.377.896.419.419.679.819.896 1.377.164.42.358 1.054.411 2.22.058 1.266.07 1.646.07 4.85s-.012 3.584-.07 4.85c-.054 1.166-.248 1.8-.411 2.22-.217.558-.477.957-.896 1.377-.419.419-.819.679-1.377.896-.42.164-1.054.358-2.22.411-1.266.058-1.646.07-4.85.07s-3.584-.012-4.85-.07c-1.166-.054-1.8-.248-2.22-.411-.558-.217-.957-.477-1.377-.896-.419-.419-.679-.819-.896-1.377-.164-.42-.358-1.054-.411-2.22-.058-1.266-.07-1.646-.07-4.85s.012-3.584.07-4.85c.054-1.166.248-1.8.411-2.22.217-.558.477-.957.896-1.377.419-.419.819-.679 1.377-.896.42-.164 1.054-.358 2.22-.411 1.266-.058 1.646-.07 4.85-.07zm0-2.163c-3.259 0-3.667.014-4.947.072-1.277.057-2.148.258-2.911.553-.788.306-1.457.715-2.122 1.381-.666.666-1.075 1.335-1.381 2.122-.295.763-.496 1.634-.553 2.911-.058 1.28-.072 1.688-.072 4.947s.014 3.667.072 4.947c.057 1.277.258 2.148.553 2.911.306.788.715 1.457 1.381 2.122.666.666 1.335 1.075 2.122 1.381.763.295 1.634.496 2.911.553 1.28.058 1.688.072 4.947.072s3.667-.014 4.947-.072c1.277-.057 2.148-.258 2.911-.553.788-.306 1.457-.715 2.122-1.381.666-.666 1.075-1.335 1.381-2.122.295-.763.496-1.634.553-2.911.058-1.28.072-1.688.072-4.947s-.014-3.667-.072-4.947c-.057-1.277-.258-2.148-.553-2.911-.306-.788-.715-1.457-1.381-2.122-.666-.666-1.335-1.075-2.122-1.381-.763-.295-1.634-.496-2.911-.553-1.28-.058-1.688-.072-4.947-.072z"/></svg>
                        </a>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-white font-bold mb-6">Product</h4>
                    <ul class="space-y-4">
                        <li><a href="#features" class="hover:text-white transition-colors">AI Listing</a></li>
                        <li><a href="{{ route('pricing') }}" class="hover:text-white transition-colors">Pricing</a></li>
                        <li><a href="{{ route('testimonials') }}" class="hover:text-white transition-colors">Success Stories</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Integrations</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-white font-bold mb-6">Resources</h4>
                    <ul class="space-y-4">
                        <li><a href="#" class="hover:text-white transition-colors">Market Insights</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Agent Success</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">API Docs</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Contact Support</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-white font-bold mb-6">Legal</h4>
                    <ul class="space-y-4">
                        <li><a href="#" class="hover:text-white transition-colors">Privacy Policy</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Terms of Service</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Compliance</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="pt-8 border-t border-slate-800 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-sm">&copy; 2024 AI Real Estate. All rights reserved.</p>
                <p class="text-sm">Revolutionizing Real Estate with ❤️</p>
            </div>
        </div>
    </footer>

    <!-- Mobile Menu Toggle Script -->
    <script>
        document.querySelector('button.md\\:hidden').addEventListener('click', function() {
            const menu = document.querySelector('.hidden.md\\:flex');
            menu.classList.toggle('hidden');
            menu.classList.toggle('flex');
            menu.classList.toggle('flex-col');
            menu.classList.toggle('absolute');
            menu.classList.toggle('top-full');
            menu.classList.toggle('left-0');
            menu.classList.toggle('right-0');
            menu.classList.toggle('bg-white');
            menu.classList.toggle('p-6');
            menu.classList.toggle('shadow-xl');
        });
    </script>
</body>
</html>
