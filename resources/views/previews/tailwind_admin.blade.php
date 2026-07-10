<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: { primary: '#4f46e5', sidebar: '#0f172a' }
                }
            }
        }
    </script>
    <style>@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');</style>
</head>
<body class="bg-slate-50 font-sans antialiased text-slate-800 h-screen flex overflow-hidden selection:bg-primary selection:text-white">
    
    <!-- Sidebar -->
    <aside class="bg-sidebar w-72 flex-shrink-0 flex-col hidden lg:flex h-full shadow-2xl relative z-20">
        <div class="h-20 flex items-center px-8 border-b border-white/5 bg-black/10">
            <div class="flex items-center gap-3 text-white">
                <div class="w-9 h-9 bg-primary rounded-xl flex items-center justify-center shadow-lg shadow-primary/30">
                    <i class="fa-solid fa-bolt text-sm"></i>
                </div>
                <span class="text-xl font-bold tracking-tight">Admin<span class="text-slate-400 font-normal">Panel</span></span>
            </div>
        </div>
        <div class="flex-1 overflow-y-auto py-8 flex flex-col gap-8 scrollbar-hide">
            <div class="px-6">
                <p class="px-3 text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-3">Overview</p>
                <nav class="space-y-1.5">
                    <a href="#" class="bg-primary/10 text-primary border border-primary/20 px-4 py-3 rounded-xl flex items-center gap-3 text-sm font-semibold transition">
                        <i class="fa-solid fa-table-cells-large w-5 text-center"></i> Dashboard
                    </a>
                    <a href="#" class="text-slate-400 hover:bg-white/5 hover:text-white px-4 py-3 rounded-xl flex items-center gap-3 text-sm font-medium transition">
                        <i class="fa-solid fa-chart-line w-5 text-center"></i> Analytics
                    </a>
                </nav>
            </div>
            
            <div class="px-6">
                <p class="px-3 text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-3">Management</p>
                <nav class="space-y-1.5">
                    <a href="#" class="text-slate-400 hover:bg-white/5 hover:text-white px-4 py-3 rounded-xl flex items-center gap-3 text-sm font-medium transition flex justify-between">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-file-invoice-dollar w-5 text-center"></i> Orders
                        </div>
                        <span class="bg-primary text-white text-[10px] font-bold px-2 py-0.5 rounded-md shadow-sm">12</span>
                    </a>
                    <a href="#" class="text-slate-400 hover:bg-white/5 hover:text-white px-4 py-3 rounded-xl flex items-center gap-3 text-sm font-medium transition">
                        <i class="fa-solid fa-box-open w-5 text-center"></i> Products
                    </a>
                    <a href="#" class="text-slate-400 hover:bg-white/5 hover:text-white px-4 py-3 rounded-xl flex items-center gap-3 text-sm font-medium transition">
                        <i class="fa-solid fa-users w-5 text-center"></i> Customers
                    </a>
                </nav>
            </div>
            
            <div class="px-6 mt-auto pb-4">
                <nav class="space-y-1.5 border-t border-white/5 pt-6">
                    <a href="#" class="text-slate-400 hover:bg-white/5 hover:text-white px-4 py-3 rounded-xl flex items-center gap-3 text-sm font-medium transition">
                        <i class="fa-solid fa-gear w-5 text-center"></i> Settings
                    </a>
                    <a href="#" class="text-slate-400 hover:bg-red-500/10 hover:text-red-400 px-4 py-3 rounded-xl flex items-center gap-3 text-sm font-medium transition">
                        <i class="fa-solid fa-arrow-right-from-bracket w-5 text-center"></i> Logout
                    </a>
                </nav>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col w-full h-full min-w-0 bg-slate-50">
        
        <!-- Top Header -->
        <header class="h-20 bg-white shadow-sm border-b border-slate-200 flex items-center justify-between px-6 lg:px-10 z-10 flex-shrink-0">
            <div class="flex items-center gap-6">
                <button class="lg:hidden text-slate-500 hover:text-slate-700 bg-slate-100 w-10 h-10 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>
                <div class="relative hidden md:block">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input type="text" placeholder="Search orders, customers..." class="pl-11 pr-4 py-2.5 bg-slate-100 border-transparent rounded-xl text-sm font-medium focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none w-80 transition-all shadow-sm">
                </div>
            </div>
            
            <div class="flex items-center gap-5">
                <button class="relative text-slate-400 hover:text-primary transition w-10 h-10 rounded-xl hover:bg-primary/5 flex items-center justify-center">
                    <i class="fa-regular fa-bell text-xl"></i>
                    <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
                </button>
                <div class="h-8 w-px bg-slate-200 mx-1"></div>
                <div class="flex items-center gap-3 cursor-pointer hover:bg-slate-50 p-1.5 rounded-xl transition pr-3">
                    <img src="https://ui-avatars.com/api/?name=Admin+User&background=4f46e5&color=fff&size=100" class="w-9 h-9 rounded-full shadow-sm border border-slate-200">
                    <div class="hidden md:block text-sm text-left">
                        <p class="font-bold text-slate-800 leading-none mb-1">Jane Doe</p>
                        <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wide">Super Admin</p>
                    </div>
                    <i class="fa-solid fa-chevron-down text-xs text-slate-400 ml-2 hidden md:block"></i>
                </div>
            </div>
        </header>

        <!-- Dashboard Content -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto p-6 lg:p-10">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-end mb-8 gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Dashboard Overview</h1>
                    <p class="text-slate-500 mt-2 font-medium">Welcome back, Jane! Here's what's happening today.</p>
                </div>
                <div class="flex gap-3">
                    <button class="bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 px-4 py-2.5 rounded-xl text-sm font-semibold shadow-sm transition flex items-center gap-2">
                        <i class="fa-regular fa-calendar"></i> Last 30 Days <i class="fa-solid fa-chevron-down text-xs ml-1"></i>
                    </button>
                    <button class="bg-primary hover:bg-indigo-600 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-md shadow-primary/30 transition flex items-center gap-2">
                        <i class="fa-solid fa-download"></i> Export
                    </button>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Card 1 -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 relative overflow-hidden group hover:border-primary/30 transition">
                    <div class="flex justify-between items-start mb-6">
                        <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xl shadow-sm group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <span class="flex items-center gap-1.5 text-xs font-bold text-emerald-700 bg-emerald-100/80 px-2.5 py-1 rounded-lg">
                            <i class="fa-solid fa-arrow-trend-up"></i> +12.5%
                        </span>
                    </div>
                    <h3 class="text-slate-500 text-sm font-bold uppercase tracking-wider mb-1">Total Users</h3>
                    <p class="text-3xl font-black text-slate-900">12,543</p>
                </div>
                
                <!-- Card 2 -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 relative overflow-hidden group hover:border-primary/30 transition">
                    <div class="flex justify-between items-start mb-6">
                        <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center text-xl shadow-sm group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-bag-shopping"></i>
                        </div>
                        <span class="flex items-center gap-1.5 text-xs font-bold text-emerald-700 bg-emerald-100/80 px-2.5 py-1 rounded-lg">
                            <i class="fa-solid fa-arrow-trend-up"></i> +8.2%
                        </span>
                    </div>
                    <h3 class="text-slate-500 text-sm font-bold uppercase tracking-wider mb-1">Total Orders</h3>
                    <p class="text-3xl font-black text-slate-900">8,234</p>
                </div>
                
                <!-- Card 3 -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 relative overflow-hidden group hover:border-primary/30 transition">
                    <div class="flex justify-between items-start mb-6">
                        <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-xl shadow-sm group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-dollar-sign"></i>
                        </div>
                        <span class="flex items-center gap-1.5 text-xs font-bold text-emerald-700 bg-emerald-100/80 px-2.5 py-1 rounded-lg">
                            <i class="fa-solid fa-arrow-trend-up"></i> +24.1%
                        </span>
                    </div>
                    <h3 class="text-slate-500 text-sm font-bold uppercase tracking-wider mb-1">Revenue</h3>
                    <p class="text-3xl font-black text-slate-900">$124.5K</p>
                </div>
                
                <!-- Card 4 -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 relative overflow-hidden group hover:border-primary/30 transition">
                    <div class="flex justify-between items-start mb-6">
                        <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center text-xl shadow-sm group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-eye"></i>
                        </div>
                        <span class="flex items-center gap-1.5 text-xs font-bold text-red-700 bg-red-100/80 px-2.5 py-1 rounded-lg">
                            <i class="fa-solid fa-arrow-trend-down"></i> -2.4%
                        </span>
                    </div>
                    <h3 class="text-slate-500 text-sm font-bold uppercase tracking-wider mb-1">Page Views</h3>
                    <p class="text-3xl font-black text-slate-900">1.2M</p>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-6 border-b border-slate-200 flex justify-between items-center bg-white">
                    <h2 class="text-lg font-bold text-slate-900">Recent Transactions</h2>
                    <button class="text-primary text-sm font-bold hover:text-indigo-700 transition px-3 py-1.5 bg-primary/5 rounded-lg">View All Report</button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600 whitespace-nowrap">
                        <thead class="bg-slate-50/80 text-slate-500 font-bold text-xs uppercase tracking-wider border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-4 rounded-tl-lg">Order ID</th>
                                <th class="px-6 py-4">Customer</th>
                                <th class="px-6 py-4">Date</th>
                                <th class="px-6 py-4">Amount</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr class="hover:bg-slate-50/80 transition group">
                                <td class="px-6 py-4 font-mono text-slate-900 font-medium">#ORD-001</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs">JD</div>
                                        <span class="font-bold text-slate-900">John Doe</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-medium text-slate-500">Oct 12, 2026</td>
                                <td class="px-6 py-4 font-bold text-slate-900">$129.00</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-bold border border-emerald-200 bg-emerald-50 text-emerald-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Completed
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button class="text-slate-400 hover:text-primary transition p-2 bg-slate-50 rounded-lg opacity-0 group-hover:opacity-100"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                                </td>
                            </tr>
                            <tr class="hover:bg-slate-50/80 transition group">
                                <td class="px-6 py-4 font-mono text-slate-900 font-medium">#ORD-002</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center font-bold text-xs">AS</div>
                                        <span class="font-bold text-slate-900">Alice Smith</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-medium text-slate-500">Oct 12, 2026</td>
                                <td class="px-6 py-4 font-bold text-slate-900">$89.50</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-bold border border-amber-200 bg-amber-50 text-amber-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Processing
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button class="text-slate-400 hover:text-primary transition p-2 bg-slate-50 rounded-lg opacity-0 group-hover:opacity-100"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                                </td>
                            </tr>
                            <tr class="hover:bg-slate-50/80 transition group">
                                <td class="px-6 py-4 font-mono text-slate-900 font-medium">#ORD-003</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-red-100 text-red-600 flex items-center justify-center font-bold text-xs">BJ</div>
                                        <span class="font-bold text-slate-900">Bob Johnson</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-medium text-slate-500">Oct 11, 2026</td>
                                <td class="px-6 py-4 font-bold text-slate-900">$249.99</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-bold border border-red-200 bg-red-50 text-red-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Cancelled
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button class="text-slate-400 hover:text-primary transition p-2 bg-slate-50 rounded-lg opacity-0 group-hover:opacity-100"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <footer class="mt-10 text-center text-sm font-medium text-slate-400">
                &copy; 2026 Admin Dashboard. Designed with TailwindCSS.
            </footer>
        </main>
    </div>
</body>
</html>