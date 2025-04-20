<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - My App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 1200px;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .alert {
            margin-bottom: 1rem;
        }
        @media (max-width: 576px) {
            h2, h3 {
                font-size: 1.5rem;
            }
            .btn {
                font-size: 0.9rem;
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="container">
        @yield('content')
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const API_BASE = '{{ config('app.url') }}/api';

        // Show alert
        function showAlert(elementId, message, type) {
            const alert = document.getElementById(elementId);
            alert.className = `alert alert-${type} d-block`;
            alert.textContent = message;
            setTimeout(() => {
                alert.className = 'alert d-none';
            }, 3000);
        }
    </script>
    @yield('scripts')
</body>
</html>