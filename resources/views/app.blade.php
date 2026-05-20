<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png" sizes="32x32">
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead

        <!-- MathJax Configuration - FIXED VERSION -->
        <script>
            window.MathJax = {
                tex: {
                    inlineMath: [['$', '$'], ['\\(', '\\)']],
                    displayMath: [['$$', '$$'], ['\\[', '\\]']],
                    processEscapes: true
                },
                options: {
                    ignoreHtmlClass: 'no-mathjax',
                    processHtmlClass: 'mathjax'
                },
                startup: {
                    ready: function() {
                        console.log('MathJax loaded and ready');
                        window.MathJax.startup.defaultReady();
                        window.MathJaxLoaded = true;
                        document.dispatchEvent(new Event('mathjax-loaded'));
                    },
                    pageReady: function() {
                        console.log('MathJax page ready');
                        return window.MathJax.startup.defaultPageReady();
                    }
                },
                chtml: {
                    scale: 1,
                    minScale: 0.5,
                    matchFontHeight: true
                }
            };
        </script>
        <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-chtml.js"></script>
        
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;700&display=swap');
            
            * {
                font-family: 'Hind Siliguri', sans-serif;
            }
            
            /* MathJax প্রিন্ট স্টাইল */
            @media print {
                mjx-container {
                    display: inline-block !important;
                    font-size: inherit !important;
                }
                .MathJax, .MathJax * {
                    color: black !important;
                }
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>