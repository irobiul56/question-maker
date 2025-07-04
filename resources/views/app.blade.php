<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead


         <!-- MathJax Configuration -->
        <script>
        window.MathJax = {
          startup: {
            pageReady: () => {
              return MathJax.startup.defaultPageReady().then(() => {
                // Store that MathJax is loaded
                window.MathJaxLoaded = true;
                // Dispatch event for components to know MathJax is ready
                document.dispatchEvent(new Event('mathjax-loaded'));
              });
            }
          },
          tex: {
            inlineMath: [['$', '$'], ['\\(', '\\)']],
            displayMath: [['$$', '$$'], ['\\[', '\\]']],
            processEscapes: true
          }
        };
        </script>
        <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
        
  <style>
  @import url('https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;700&display=swap');

        *{
          font-family: 'Hind Siliguri'
        }
</style>
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
