<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview Tailwind Admin - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans antialiased text-gray-800 h-screen flex overflow-hidden">
    <aside class="bg-gray-900 w-64 flex-shrink-0 flex flex-col hidden md:flex">
        <div class="h-16 flex items-center px-6 border-b border-gray-800">
            <span class="text-white text-xl font-bold tracking-wider uppercase">Admin<span class="text-emerald-500">Panel</span></span>
        </div>
        <div class="flex-1 overflow-y-auto py-4">
            <nav class="px-3 space-y-1">
                <a href="#" class="bg-gray-800 text-white px-3 py-2 rounded-md flex items-center gap-3 text-sm font-medium">Dashboard</a>
                <a href="#" class="text-gray-300 hover:bg-gray-800 hover:text-white px-3 py-2 rounded-md flex items-center gap-3 text-sm font-medium">Users</a>
                <a href="#" class="text-gray-300 hover:bg-gray-800 hover:text-white px-3 py-2 rounded-md flex items-center gap-3 text-sm font-medium">Analytics</a>
            </nav>
        </div>
    </aside>
    <div class="flex-1 flex flex-col w-full h-full">
        <header class="h-16 bg-white shadow-sm flex items-center justify-between px-6">
            <h1 class="text-xl font-bold text-gray-800">Dashboard Overview</h1>
        </header>
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                    <p class="text-sm font-medium text-gray-500">Total Users</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">12,543</p>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-800">Recent Transactions</h2>
                </div>
            </div>
        </main>
    </div>
</body>
</html>