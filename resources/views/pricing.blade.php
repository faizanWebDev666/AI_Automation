<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pricing Plans — AI Real Estate</title>
    
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
                <a href="{{ route('pricing') }}" class="font-medium text-accent-primary transition-colors">Plans</a>
                <a href="{{ route('testimonials') }}" class="font-medium text-slate-600 hover:text-accent-primary transition-colors">Success Stories</a>
                <a href="{{ route('login') }}" class="font-medium text-slate-600 hover:text-accent-primary transition-colors">Agent Login</a>
                <a href="{{ route('register') }}" class="gradient-bg text-white px-6 py-2.5 rounded-full font-semibold shadow-lg shadow-indigo-200 hover:shadow-indigo-300 transition-all hover:-translate-y-0.5 active:translate-y-0">
                    Join the Network
                </a>
            </div>
        </nav>
    </header>

    <main class="pt-32">
        <!-- Pricing Section -->
        <section id="pricing" class="py-24 bg-slate-50">
            <div class="container mx-auto px-6">
                <div class="text-center mb-20">
                    <h2 class="text-4xl md:text-6xl font-extrabold mb-4">Find the Perfect <span class="gradient-text">Plan</span></h2>
                    <p class="text-slate-500 max-w-2xl mx-auto text-lg">Whether you're a solo agent or a growing brokerage, we have a plan that fits your business needs.</p>
                </div>

                <!-- Billing Toggle -->
                <div class="flex items-center justify-center mb-16 gap-4">
                    <span id="billingLabel" class="text-slate-600 font-medium">Monthly</span>
                    <button id="billingToggle" class="relative inline-flex h-8 w-16 items-center rounded-full transition-colors" style="background-color: #6366f1;">
                        <span id="toggleSlider" class="inline-block h-6 w-6 transform rounded-full bg-white shadow-lg transition-transform" style="transform: translateX(2px);"></span>
                    </button>
                    <div class="flex items-center gap-2">
                        <span id="billingLabel2" class="text-slate-600 font-medium">Yearly</span>
                        <span class="inline-block bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">Save 20%</span>
                    </div>
                </div>

                <div class="grid md:grid-cols-3 gap-8 max-w-6xl mx-auto">
                    <!-- Free Plan -->
                    <div class="bg-white p-10 rounded-[2.5rem] border border-slate-100 shadow-xl shadow-slate-200/50 flex flex-col">
                        <div class="mb-8">
                            <div class="flex items-center gap-2 mb-2">
                                <h3 class="text-xl font-bold">Free Plan</h3>
                                <span class="text-lg">✅</span>
                            </div>
                            <div class="flex items-baseline gap-1 pricing-display" data-monthly="₨0" data-yearly="₨0">
                                <span class="text-4xl font-extrabold">₨0</span>
                                <span class="text-slate-400 font-medium billing-period">/month</span>
                            </div>
                        </div>
                        <ul class="space-y-4 mb-10 flex-1">
                            <li class="flex items-start gap-3 text-slate-600">
                                <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                                <span>Dealer registration and profile creation</span>
                            </li>
                            <li class="flex items-start gap-3 text-slate-600">
                                <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                                <span>Add up to 3 listings per month</span>
                            </li>
                            <li class="flex items-start gap-3 text-slate-600">
                                <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                                <span>Basic listing details</span>
                            </li>
                            <li class="flex items-start gap-3 text-slate-600">
                                <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                                <span>Standard visibility</span>
                            </li>
                        </ul>
                        <a href="{{ route('register') }}?plan=free" class="w-full py-4 rounded-2xl border border-slate-200 font-bold text-center hover:bg-slate-50 transition-colors">Get Started Free</a>
                    </div>

                    <!-- Silver Plan -->
                    <div class="bg-white p-10 rounded-[2.5rem] border-2 border-accent-primary shadow-2xl shadow-indigo-100 relative flex flex-col scale-105 z-10">
                        <div class="absolute -top-5 left-1/2 -translate-x-1/2 gradient-bg text-white px-6 py-1 rounded-full text-sm font-bold uppercase tracking-widest shadow-lg shadow-indigo-200">Most Popular</div>
                        <div class="mb-8">
                            <div class="flex items-center gap-2 mb-2">
                                <h3 class="text-xl font-bold">Silver Plan</h3>
                                <span class="text-lg">💎</span>
                            </div>
                            <div class="flex items-baseline gap-1 pricing-display" data-monthly="₨5,500" data-yearly="₨52,800">
                                <span class="text-4xl font-extrabold">₨5,500</span>
                                <span class="text-slate-400 font-medium billing-period">/month</span>
                            </div>
                        </div>
                        <ul class="space-y-4 mb-10 flex-1">
                            <li class="flex items-start gap-3 text-slate-600 font-medium">
                                <svg class="w-5 h-5 text-indigo-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                                <span>All Free Plan features</span>
                            </li>
                            <li class="flex items-start gap-3 text-slate-600 font-medium">
                                <svg class="w-5 h-5 text-indigo-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                                <span>Add up to 15 listings per month</span>
                            </li>
                            <li class="flex items-start gap-3 text-slate-600 font-medium">
                                <svg class="w-5 h-5 text-indigo-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                                <span>Highlighted listings</span>
                            </li>
                            <li class="flex items-start gap-3 text-slate-600 font-medium">
                                <svg class="w-5 h-5 text-indigo-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                                <span>Priority customer support</span>
                            </li>
                        </ul>
                        <a href="{{ route('register') }}?plan=silver" class="w-full py-4 rounded-2xl gradient-bg text-white font-bold text-center shadow-xl shadow-indigo-200 hover:shadow-indigo-300 transition-all hover:-translate-y-1">Upgrade to Silver</a>
                    </div>

                    <!-- Gold Plan -->
                    <div class="bg-white p-10 rounded-[2.5rem] border border-slate-100 shadow-xl shadow-slate-200/50 flex flex-col">
                        <div class="mb-8">
                            <div class="flex items-center gap-2 mb-2">
                                <h3 class="text-xl font-bold">Gold Plan</h3>
                                <span class="text-lg">🏆</span>
                            </div>
                            <div class="flex items-baseline gap-1 pricing-display" data-monthly="₨8,500" data-yearly="₨81,600">
                                <span class="text-4xl font-extrabold">₨8,500</span>
                                <span class="text-slate-400 font-medium billing-period">/month</span>
                            </div>
                        </div>
                        <ul class="space-y-4 mb-10 flex-1">
                            <li class="flex items-start gap-3 text-slate-600">
                                <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                                <span>All Silver Plan features</span>
                            </li>
                            <li class="flex items-start gap-3 text-slate-600">
                                <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                                <span>Unlimited listings</span>
                            </li>
                            <li class="flex items-start gap-3 text-slate-600">
                                <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                                <span>Premium listing visibility</span>
                            </li>
                            <li class="flex items-start gap-3 text-slate-600">
                                <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                                <span>Advanced analytics</span>
                            </li>
                        </ul>
                        <a href="{{ route('register') }}?plan=gold" class="w-full py-4 rounded-2xl border border-accent-primary text-accent-primary font-bold text-center hover:bg-indigo-50 transition-colors">Upgrade to Gold</a>
                    </div>
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

    <script>
        const billingToggle = document.getElementById('billingToggle');
        const toggleSlider = document.getElementById('toggleSlider');
        const billingLabel = document.getElementById('billingLabel');
        const billingLabel2 = document.getElementById('billingLabel2');
        const pricingDisplays = document.querySelectorAll('.pricing-display');
        const billingPeriods = document.querySelectorAll('.billing-period');
        let isYearly = false;

        billingToggle.addEventListener('click', function() {
            isYearly = !isYearly;
            if (isYearly) {
                toggleSlider.style.transform = 'translateX(32px)';
                billingLabel.style.opacity = '0.6';
                billingLabel2.style.opacity = '1';
                billingLabel2.style.fontWeight = '600';
            } else {
                toggleSlider.style.transform = 'translateX(2px)';
                billingLabel.style.opacity = '1';
                billingLabel2.style.opacity = '0.6';
                billingLabel2.style.fontWeight = '400';
            }

            pricingDisplays.forEach(display => {
                const priceElement = display.querySelector('span:first-child');
                priceElement.textContent = isYearly ? display.dataset.yearly : display.dataset.monthly;
            });

            billingPeriods.forEach(period => {
                period.textContent = isYearly ? '/year' : '/month';
            });
        });
    </script>
</body>
</html>
