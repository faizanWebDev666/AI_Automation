<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Add Product — AI Automation</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: #f0f2f5;
            background-image:
                radial-gradient(ellipse 80% 50% at 50% -10%, rgba(99, 102, 241, 0.08), transparent),
                radial-gradient(ellipse 60% 40% at 90% 80%, rgba(236, 72, 153, 0.05), transparent);
            color: #1e293b;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 48px 20px;
        }

        .container {
            width: 100%;
            max-width: 780px;
        }

        /* Header */
        .page-header {
            text-align: center;
            margin-bottom: 36px;
        }

        .page-header .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: linear-gradient(135deg, #eef2ff, #fce7f3);
            border: 1px solid #e0e7ff;
            color: #6366f1;
            font-size: 12px;
            font-weight: 600;
            padding: 6px 16px;
            border-radius: 50px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 14px;
        }

        .page-header h1 {
            font-size: 34px;
            font-weight: 800;
            background: linear-gradient(135deg, #1e293b 0%, #6366f1 60%, #ec4899 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1.2;
            margin-bottom: 8px;
        }

        .page-header p {
            font-size: 15px;
            color: #94a3b8;
            font-weight: 400;
        }

        /* Card */
        .form-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 40px;
            box-shadow:
                0 1px 3px rgba(0, 0, 0, 0.04),
                0 8px 30px rgba(0, 0, 0, 0.06);
        }

        /* AI Trigger Section */
        .ai-section {
            background: linear-gradient(135deg, #eef2ff, #fdf2f8);
            border: 1px solid #e0e7ff;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 32px;
            position: relative;
            overflow: hidden;
        }

        .ai-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, #818cf8, #f472b6, transparent);
            border-radius: 2px 2px 0 0;
        }

        .ai-section-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 600;
            color: #6366f1;
            margin-bottom: 14px;
        }

        .ai-section-label .pulse-dot {
            width: 8px;
            height: 8px;
            background: #818cf8;
            border-radius: 50%;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; box-shadow: 0 0 0 0 rgba(129, 140, 248, 0.4); }
            50% { opacity: 0.7; box-shadow: 0 0 0 8px rgba(129, 140, 248, 0); }
        }

        .ai-input-row {
            display: flex;
            gap: 12px;
        }

        .ai-input-row input {
            flex: 1;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            padding: 13px 18px;
            font-size: 15px;
            font-family: 'Inter', sans-serif;
            color: #1e293b;
            outline: none;
            transition: all 0.3s ease;
        }

        .ai-input-row input::placeholder {
            color: #a5b4c8;
        }

        .ai-input-row input:focus {
            border-color: #818cf8;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
        }

        .btn-generate {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6, #d946ef);
            border: none;
            border-radius: 12px;
            padding: 13px 24px;
            font-size: 14px;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            color: #fff;
            cursor: pointer;
            transition: all 0.3s ease;
            white-space: nowrap;
            position: relative;
            overflow: hidden;
        }

        .btn-generate::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.18), transparent);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .btn-generate:hover::before { opacity: 1; }

        .btn-generate:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.3);
        }

        .btn-generate:active { transform: translateY(0); }
        .btn-generate:disabled { opacity: 0.7; cursor: not-allowed; transform: none; }

        .btn-generate .spinner {
            display: none;
            width: 18px;
            height: 18px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
        }

        .btn-generate.loading .spinner { display: block; }
        .btn-generate.loading .btn-text { display: none; }
        .btn-generate.loading .btn-icon { display: none; }

        @keyframes spin { to { transform: rotate(360deg); } }

        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 28px;
        }

        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e2e8f0;
        }

        .divider span {
            font-size: 11px;
            color: #94a3b8;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.2px;
        }

        /* Form Grid */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 7px;
        }

        .form-group.full-width { grid-column: 1 / -1; }

        .form-group label {
            font-size: 13px;
            font-weight: 500;
            color: #475569;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .form-group label .label-icon { font-size: 14px; }

        .form-group input,
        .form-group textarea {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            color: #1e293b;
            outline: none;
            transition: all 0.3s ease;
            resize: vertical;
        }

        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: #b0bec5;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            border-color: #818cf8;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
            background: #ffffff;
        }

        .form-group textarea { min-height: 100px; }

        /* AI-filled highlight */
        @keyframes aiHighlight {
            0% {
                border-color: #818cf8;
                box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15), 0 0 16px rgba(99, 102, 241, 0.08);
                background: #eef2ff;
            }
            100% {
                border-color: #e2e8f0;
                box-shadow: none;
                background: #f8fafc;
            }
        }

        .ai-filled { animation: aiHighlight 2s ease forwards; }

        /* Buttons */
        .form-actions {
            margin-top: 32px;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        .btn-reset {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 13px 28px;
            font-size: 14px;
            font-weight: 500;
            font-family: 'Inter', sans-serif;
            color: #64748b;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-reset:hover {
            background: #e2e8f0;
            color: #334155;
        }

        .btn-submit {
            background: linear-gradient(135deg, #059669, #10b981);
            border: none;
            border-radius: 12px;
            padding: 13px 32px;
            font-size: 14px;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            color: #fff;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(5, 150, 105, 0.25);
        }

        .btn-submit:active { transform: translateY(0); }

        /* Toast */
        .toast {
            position: fixed;
            bottom: 30px;
            right: 30px;
            padding: 14px 24px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            font-family: 'Inter', sans-serif;
            color: #fff;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            pointer-events: none;
            z-index: 100;
            max-width: 380px;
        }

        .toast.show { opacity: 1; transform: translateY(0); pointer-events: auto; }
        .toast.success { background: #059669; border: 1px solid #10b981; box-shadow: 0 8px 24px rgba(5, 150, 105, 0.25); }
        .toast.error { background: #dc2626; border: 1px solid #ef4444; box-shadow: 0 8px 24px rgba(220, 38, 38, 0.25); }

        /* Responsive */
        @media (max-width: 640px) {
            .form-card { padding: 24px; }
            .form-grid { grid-template-columns: 1fr; }
            .ai-input-row { flex-direction: column; }
            .page-header h1 { font-size: 26px; }
            .form-actions { flex-direction: column; }
            .btn-reset, .btn-submit { width: 100%; text-align: center; justify-content: center; }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="page-header">
            <div class="badge">⚡ AI-Powered</div>
            <h1>Add New Product</h1>
            <p>Enter a product title and let AI auto-fill the details for you</p>
        </div>

        <!-- Form Card -->
        <div class="form-card">

            <!-- AI Trigger -->
            <div class="ai-section">
                <div class="ai-section-label">
                    <span class="pulse-dot"></span>
                    AI Auto-Fill — Enter a title and generate
                </div>
                <div class="ai-input-row">
                    <input
                        type="text"
                        id="ai-title"
                        placeholder="e.g. Wireless Bluetooth Headphones Pro"
                        autocomplete="off"
                    >
                    <button type="button" class="btn-generate" id="btn-generate" onclick="generateProduct()">
                        <span class="btn-icon">✨</span>
                        <span class="btn-text">Generate</span>
                        <span class="spinner"></span>
                    </button>
                </div>
            </div>

            <div class="divider"><span>Product Details</span></div>

            <!-- Product Form -->
            <form id="product-form">
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label><span class="label-icon">📦</span> Product Title</label>
                        <input type="text" id="title" name="title" placeholder="Product title">
                    </div>

                    <div class="form-group full-width">
                        <label><span class="label-icon">📝</span> Description</label>
                        <textarea id="description" name="description" placeholder="Product description..." rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <label><span class="label-icon">📁</span> Category</label>
                        <input type="text" id="category" name="category" placeholder="Category">
                    </div>

                    <div class="form-group">
                        <label><span class="label-icon">💰</span> Price (USD)</label>
                        <input type="text" id="price" name="price" placeholder="0.00">
                    </div>

                    <div class="form-group">
                        <label><span class="label-icon">🏷️</span> SKU</label>
                        <input type="text" id="sku" name="sku" placeholder="SKU-000-000">
                    </div>

                    <div class="form-group">
                        <label><span class="label-icon">🏢</span> Brand</label>
                        <input type="text" id="brand" name="brand" placeholder="Brand name">
                    </div>

                    <div class="form-group full-width">
                        <label><span class="label-icon">🔖</span> Tags</label>
                        <input type="text" id="tags" name="tags" placeholder="tag1, tag2, tag3">
                    </div>

                    <div class="form-group full-width">
                        <label><span class="label-icon">⭐</span> Features</label>
                        <textarea id="features" name="features" placeholder="Key product features..." rows="4"></textarea>
                    </div>

                    <div class="form-group">
                        <label><span class="label-icon">🔍</span> Meta Title</label>
                        <input type="text" id="meta_title" name="meta_title" placeholder="SEO title">
                    </div>

                    <div class="form-group">
                        <label><span class="label-icon">📄</span> Meta Description</label>
                        <input type="text" id="meta_description" name="meta_description" placeholder="SEO description">
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-reset" onclick="resetForm()">Reset</button>
                    <button type="submit" class="btn-submit">Save Product</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Toast -->
    <div class="toast" id="toast"></div>

    <script>
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;

        async function generateProduct() {
            const titleInput = document.getElementById('ai-title');
            const btn = document.getElementById('btn-generate');
            const title = titleInput.value.trim();

            if (!title) {
                showToast('Please enter a product title first.', 'error');
                titleInput.focus();
                return;
            }

            btn.classList.add('loading');
            btn.disabled = true;

            try {
                const response = await fetch('/api/generate-product', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ title: title }),
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.error || 'Failed to generate product details.');
                }

                fillField('title', title);
                fillField('description', data.description);
                fillField('category', data.category);
                fillField('price', data.price);
                fillField('sku', data.sku);
                fillField('brand', data.brand);
                fillField('tags', data.tags);
                fillField('features', data.features);
                fillField('meta_title', data.meta_title);
                fillField('meta_description', data.meta_description);

                showToast('✨ Product details generated successfully!', 'success');

            } catch (error) {
                console.error('Generation error:', error);
                showToast(error.message || 'Something went wrong. Please try again.', 'error');
            } finally {
                btn.classList.remove('loading');
                btn.disabled = false;
            }
        }

        function fillField(id, value) {
            const el = document.getElementById(id);
            if (el && value !== undefined && value !== null) {
                el.value = String(value);
                el.classList.remove('ai-filled');
                void el.offsetWidth;
                el.classList.add('ai-filled');
            }
        }

        function resetForm() {
            document.getElementById('product-form').reset();
            document.getElementById('ai-title').value = '';
            document.querySelectorAll('.ai-filled').forEach(el => el.classList.remove('ai-filled'));
            showToast('Form has been reset.', 'success');
        }

        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.className = 'toast ' + type + ' show';
            setTimeout(() => { toast.classList.remove('show'); }, 3500);
        }

        document.getElementById('ai-title').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') { e.preventDefault(); generateProduct(); }
        });

        document.getElementById('product-form').addEventListener('submit', function(e) {
            e.preventDefault();
            showToast('Product saved successfully! 🎉', 'success');
        });
    </script>
</body>
</html>