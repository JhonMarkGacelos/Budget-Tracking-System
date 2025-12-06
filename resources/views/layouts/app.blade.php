<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SSU Budget Tracking System</title>
    <link rel="icon" type="image/webp" href="/images/SSU-Logo.webp">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background-image: url('/images/background.webp');
            background-size: cover;
            background-attachment: fixed;
            background-position: center;
        }
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.3);
            z-index: -1;
        }
        .gradient-bg {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
        }
        .card-hover {
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        /* Page fade-in on load only */
        .page-transition {
            animation: fadeIn 0.4s ease-out;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        
        /* Print styles */
        @media print {
            #sidebar, .no-print, button, .lg\:hidden {
                display: none !important;
            }
            
            body {
                background: white !important;
            }
            
            body::before {
                display: none !important;
            }
            
            .lg\:ml-64 {
                margin-left: 0 !important;
            }
            
            .bg-white {
                background: white !important;
            }
            
            .shadow-xl, .shadow-lg {
                box-shadow: none !important;
            }
            
            .rounded-2xl, .rounded-xl {
                border-radius: 0 !important;
            }
            
            canvas {
                max-width: 100% !important;
                height: auto !important;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    @yield('content')
    
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        // Initialize API token from session or fetch from server
        function initializeToken() {
            @if(session()->has('api_token'))
                // Token in session - use it immediately
                localStorage.setItem('api_token', '{{ session("api_token") }}');
                setTimeout(() => {
                    window.dispatchEvent(new Event('tokenReady'));
                }, 50);
            @elseif(auth()->check())
                // User authenticated but no token in session
                if (!localStorage.getItem('api_token')) {
                    // Fetch token from server
                    axios.get('/api/get-token')
                        .then(response => {
                            localStorage.setItem('api_token', response.data.token);
                            window.dispatchEvent(new Event('tokenReady'));
                        })
                        .catch(err => {
                            console.error('Error getting token:', err);
                        });
                } else {
                    // Token in localStorage
                    window.dispatchEvent(new Event('tokenReady'));
                }
            @endif
        }
        
        // Run immediately, don't wait for DOMContentLoaded
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializeToken);
        } else {
            initializeToken();
        }
        
        // Simple fade-in on page load
        document.addEventListener('DOMContentLoaded', function() {
            const mainContent = document.querySelector('.lg\\:ml-64');
            if (mainContent) {
                mainContent.classList.add('page-transition');
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
