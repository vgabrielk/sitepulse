<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        background: 'hsl(210 40% 98%)',
                        foreground: 'hsl(222.2 47.4% 11.2%)',
                        card: 'hsl(0 0% 100%)',
                        'card-foreground': 'hsl(222.2 47.4% 11.2%)',
                        primary: 'hsl(203 91% 53%)',
                        'primary-foreground': 'hsl(0 0% 100%)',
                        muted: 'hsl(210 40% 96.1%)',
                        border: 'hsl(214 32% 91%)'
                    }
                }
            }
        }
    </script>
    @stack('styles')
    <style> html,body{background:transparent!important;height:100%} </style>
    </head>
<body>
    @yield('content')
    @stack('scripts')
</body>
</html>


