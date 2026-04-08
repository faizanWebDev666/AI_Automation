<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Success Stories — AI Real Estate</title>
    
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
                <a href="{{ route('pricing') }}" class="font-medium text-slate-600 hover:text-accent-primary transition-colors">Plans</a>
                <a href="{{ route('testimonials') }}" class="font-medium text-accent-primary transition-colors">Success Stories</a>
                <a href="{{ route('login') }}" class="font-medium text-slate-600 hover:text-accent-primary transition-colors">Agent Login</a>
                <a href="{{ route('register') }}" class="gradient-bg text-white px-6 py-2.5 rounded-full font-semibold shadow-lg shadow-indigo-200 hover:shadow-indigo-300 transition-all hover:-translate-y-0.5 active:translate-y-0">
                    Join the Network
                </a>
            </div>
        </nav>
    </header>

    <main class="pt-32">
        <!-- Testimonials -->
        <section id="testimonials" class="py-24 bg-white">
            <div class="container mx-auto px-6">
                <div class="text-center mb-20">
                    <h2 class="text-4xl md:text-6xl font-extrabold mb-4">Trusted by <span class="gradient-text">Top Brokers</span></h2>
                    <p class="text-slate-500 max-w-2xl mx-auto text-lg">See how AI Real Estate is redefining the property market for agencies worldwide.</p>
                </div>

                <div class="grid md:grid-cols-2 gap-8 max-w-5xl mx-auto">
                    <!-- Testimonial 1 -->
                    <div class="p-8 rounded-3xl bg-slate-50 border border-slate-100 flex flex-col gap-6">
                        <div class="flex gap-1 text-yellow-400">
                            @for($i=0; $i<5; $i++)
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                        </div>
                        <p class="text-lg text-slate-700 leading-relaxed italic">"The AI Listing Assistant is phenomenal. We've reduced our content creation time by 80% and our property engagement has never been higher."</p>
                        <div class="flex items-center gap-4">
                            <img src="https://i.pravatar.cc/100?u=24" class="w-12 h-12 rounded-full shadow-md" alt="Jonathan B.">
                            <div>
                                <div class="font-bold">Jonathan Blake</div>
                                <div class="text-sm text-slate-500">Managing Director, Urban Heights</div>
                            </div>
                        </div>
                    </div>

                    <!-- Testimonial 2 -->
                    <div class="p-8 rounded-3xl bg-slate-50 border border-slate-100 flex flex-col gap-6">
                        <div class="flex gap-1 text-yellow-400">
                            @for($i=0; $i<5; $i++)
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                        </div>
                        <p class="text-lg text-slate-700 leading-relaxed italic">"Predictive lead scoring is like having a superpower. We know exactly who to call first, and our conversion rates have doubled in six months."</p>
                        <div class="flex items-center gap-4">
                            <img src="https://i.pravatar.cc/100?u=25" class="w-12 h-12 rounded-full shadow-md" alt="Elena S.">
                            <div>
                                <div class="font-bold">Elena Rodriguez</div>
                                <div class="text-sm text-slate-500">Principal Agent, Azure Properties</div>
                            </div>
                        </div>
                    </div>

                    <!-- More Testimonials can be added here -->
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
                </div>
            </div>
            <div class="pt-8 border-t border-slate-800 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-sm">&copy; 2024 AI Real Estate. All rights reserved.</p>
                <p class="text-sm">Revolutionizing Real Estate with ❤️</p>
            </div>
        </div>
    </footer>
</body>
</html>
