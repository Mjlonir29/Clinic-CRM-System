<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 - Page Not Found | Ekta Care Clinic CRM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&family=Outfit:wght@700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-heading { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="h-full flex items-center justify-center p-6 bg-slate-900 text-slate-100">
    <div class="max-w-md w-full text-center space-y-6 bg-slate-800/80 p-8 sm:p-10 rounded-3xl border border-slate-700 shadow-2xl backdrop-blur-md">
        <div class="w-16 h-16 rounded-2xl bg-teal-800 text-white font-black text-2xl flex items-center justify-center mx-auto shadow-lg shadow-teal-900/40">
            404
        </div>
        
        <div class="space-y-2">
            <h1 class="text-2xl font-heading font-black text-white tracking-tight">Page Not Found</h1>
            <p class="text-xs text-slate-400 font-medium">The requested page or URL does not exist or has been removed from the system.</p>
        </div>

        <div class="pt-4">
            <a href="{{ route('dashboard') }}" class="w-full inline-flex items-center justify-center px-6 py-3 bg-teal-800 hover:bg-teal-700 text-white font-extrabold text-xs rounded-xl shadow-md transition-all">
                ← Return to Dashboard
            </a>
        </div>
    </div>
</body>
</html>
