<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Solo Care</title>

    <!-- BOOTSTRAP CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- FONT AWESOME -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

          <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">


    <!-- CUSTOM CSS -->
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
    <main>
        @yield('content')
    </main>

    <!-- BOOTSTRAP JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- CHART.JS LIBRARY -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- CHART.JS CUSTOM -->
    <script src="{{ asset('js/chart.js') }}"></script>

    <!-- LOADING OVERLAY -->
    <script>
        window.addEventListener('load', function(){
            setTimeout(() => {
                const overlay = document.getElementById('loadingOverlay');
                overlay.classList.add('hidden');
                setTimeout(() => { overlay.style.display = 'none'; }, 500);
            }, 1500);
        });
    </script>

    <!-- PAGE LEVEL SCRIPTS -->
    @yield('scripts')
</body>
</html>
