@extends('index')

@section('title', 'Galeri Template Desain')

@section('content')
<x-ui.page-layout>
    <x-ui.page-header title="Galeri Template ✨">
        <x-slot name="subtitle">
            <p class="text-sm text-slate-500">Pilih desain UI premium, responsif, dan siap pakai berbasis Tailwind CSS.</p>
        </x-slot>
    </x-ui.page-header>

    <!-- Filter & Search Section -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <!-- Categories -->
        <div class="flex items-center gap-2 overflow-x-auto py-2 px-1 scrollbar-hide w-full md:w-auto" id="category-filters">
            <button data-filter="all" class="filter-btn active px-4 py-2 bg-indigo-600 text-white rounded-full text-sm font-semibold whitespace-nowrap shadow-sm transition-all hover:bg-indigo-700">
                Semua
            </button>
            <button data-filter="landing" class="filter-btn px-4 py-2 bg-white border border-slate-200 text-slate-600 rounded-full text-sm font-medium whitespace-nowrap shadow-sm hover:border-indigo-300 hover:text-indigo-600 transition-all">
                Landing Page
            </button>
            <button data-filter="portfolio" class="filter-btn px-4 py-2 bg-white border border-slate-200 text-slate-600 rounded-full text-sm font-medium whitespace-nowrap shadow-sm hover:border-indigo-300 hover:text-indigo-600 transition-all">
                Portfolio
            </button>
            <button data-filter="blog" class="filter-btn px-4 py-2 bg-white border border-slate-200 text-slate-600 rounded-full text-sm font-medium whitespace-nowrap shadow-sm hover:border-indigo-300 hover:text-indigo-600 transition-all">
                Blog
            </button>
            <button data-filter="ecommerce" class="filter-btn px-4 py-2 bg-white border border-slate-200 text-slate-600 rounded-full text-sm font-medium whitespace-nowrap shadow-sm hover:border-indigo-300 hover:text-indigo-600 transition-all">
                E-Commerce
            </button>
            <button data-filter="dashboard" class="filter-btn px-4 py-2 bg-white border border-slate-200 text-slate-600 rounded-full text-sm font-medium whitespace-nowrap shadow-sm hover:border-indigo-300 hover:text-indigo-600 transition-all">
                Dashboard
            </button>
        </div>

        <!-- Search Bar -->
        <div class="relative w-full md:w-64 shrink-0">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fa-solid fa-search text-slate-400 text-sm"></i>
            </div>
            <input type="text" id="search-input" placeholder="Cari template..." class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all shadow-sm">
        </div>
    </div>

    <!-- Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" id="template-grid">
        
        {{-- Tailwind Portfolio --}}
        <div class="template-card bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-2xl hover:border-indigo-300 transition-all duration-300 group flex flex-col h-full overflow-hidden relative" data-category="portfolio" data-name="tailwind portfolio personal elegan">
            <div class="absolute top-4 right-4 text-xs font-black bg-cyan-100 text-cyan-700 px-3 py-1.5 rounded-full z-20 uppercase tracking-wide shadow-sm flex items-center gap-1.5">
                <i class="fa-solid fa-user-astronaut"></i> Portfolio
            </div>
            
            <a href="{{ route('user_hosting.template.preview', 'tailwind_portfolio') }}" target="_blank" class="relative h-48 overflow-hidden block border-b border-slate-100 bg-slate-50">
                <img src="{{ asset('images/templates/portfolio_thumb.png') }}" alt="Portfolio" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                <div class="absolute inset-0 bg-slate-900/0 group-hover:bg-slate-900/60 transition-colors duration-300 flex items-center justify-center backdrop-blur-[0px] group-hover:backdrop-blur-sm">
                    <span class="opacity-0 group-hover:opacity-100 bg-white text-slate-900 text-sm font-bold py-2.5 px-5 rounded-xl shadow-xl transition-all duration-300 transform translate-y-8 group-hover:translate-y-0 flex items-center gap-2 hover:bg-indigo-50">
                        <i class="fa-solid fa-expand text-indigo-600"></i> Live Preview
                    </span>
                </div>
            </a>

            <div class="p-6 flex-1 flex flex-col relative z-10">
                <h3 class="text-xl font-bold text-slate-800 mb-2 group-hover:text-indigo-600 transition-colors line-clamp-1">Tailwind Portfolio</h3>
                <p class="text-sm text-slate-500 mb-6 leading-relaxed line-clamp-2 flex-1">Template UI portfolio personal yang elegan, menggunakan dark mode dan gradient khas Tailwind.</p>
                
                <button type="button" onclick="openDeployModal('tailwind_portfolio', 'Tailwind Portfolio')" class="w-full bg-slate-900 hover:bg-indigo-600 text-white px-4 py-3 rounded-xl text-sm font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:-translate-y-0.5 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-rocket"></i> Gunakan Template
                </button>
            </div>
        </div>

        {{-- Tailwind Landing --}}
        <div class="template-card bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-2xl hover:border-indigo-300 transition-all duration-300 group flex flex-col h-full overflow-hidden relative" data-category="landing" data-name="tailwind landing page saas bisnis">
            <div class="absolute top-4 right-4 text-xs font-black bg-blue-100 text-blue-700 px-3 py-1.5 rounded-full z-20 uppercase tracking-wide shadow-sm flex items-center gap-1.5">
                <i class="fa-solid fa-plane-arrival"></i> Landing
            </div>
            
            <a href="{{ route('user_hosting.template.preview', 'tailwind_landing') }}" target="_blank" class="relative h-48 overflow-hidden block border-b border-slate-100 bg-slate-50">
                <img src="{{ asset('images/templates/landing_thumb.png') }}" alt="Landing Page" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                <div class="absolute inset-0 bg-slate-900/0 group-hover:bg-slate-900/60 transition-colors duration-300 flex items-center justify-center backdrop-blur-[0px] group-hover:backdrop-blur-sm">
                    <span class="opacity-0 group-hover:opacity-100 bg-white text-slate-900 text-sm font-bold py-2.5 px-5 rounded-xl shadow-xl transition-all duration-300 transform translate-y-8 group-hover:translate-y-0 flex items-center gap-2 hover:bg-indigo-50">
                        <i class="fa-solid fa-expand text-indigo-600"></i> Live Preview
                    </span>
                </div>
            </a>

            <div class="p-6 flex-1 flex flex-col relative z-10">
                <h3 class="text-xl font-bold text-slate-800 mb-2 group-hover:text-indigo-600 transition-colors line-clamp-1">Tailwind Landing</h3>
                <p class="text-sm text-slate-500 mb-6 leading-relaxed line-clamp-2 flex-1">Template UI SaaS landing page yang bersih, profesional, dengan section hero yang menarik.</p>
                
                <button type="button" onclick="openDeployModal('tailwind_landing', 'Tailwind Landing')" class="w-full bg-slate-900 hover:bg-indigo-600 text-white px-4 py-3 rounded-xl text-sm font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:-translate-y-0.5 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-rocket"></i> Gunakan Template
                </button>
            </div>
        </div>

        {{-- Tailwind Blog --}}
        <div class="template-card bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-2xl hover:border-indigo-300 transition-all duration-300 group flex flex-col h-full overflow-hidden relative" data-category="blog" data-name="tailwind blog artikel jurnal">
            <div class="absolute top-4 right-4 text-xs font-black bg-orange-100 text-orange-700 px-3 py-1.5 rounded-full z-20 uppercase tracking-wide shadow-sm flex items-center gap-1.5">
                <i class="fa-solid fa-newspaper"></i> Blog
            </div>
            
            <a href="{{ route('user_hosting.template.preview', 'tailwind_blog') }}" target="_blank" class="relative h-48 overflow-hidden block border-b border-slate-100 bg-slate-50">
                <img src="{{ asset('images/templates/blog_thumb.png') }}" alt="Blog" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                <div class="absolute inset-0 bg-slate-900/0 group-hover:bg-slate-900/60 transition-colors duration-300 flex items-center justify-center backdrop-blur-[0px] group-hover:backdrop-blur-sm">
                    <span class="opacity-0 group-hover:opacity-100 bg-white text-slate-900 text-sm font-bold py-2.5 px-5 rounded-xl shadow-xl transition-all duration-300 transform translate-y-8 group-hover:translate-y-0 flex items-center gap-2 hover:bg-indigo-50">
                        <i class="fa-solid fa-expand text-indigo-600"></i> Live Preview
                    </span>
                </div>
            </a>

            <div class="p-6 flex-1 flex flex-col relative z-10">
                <h3 class="text-xl font-bold text-slate-800 mb-2 group-hover:text-indigo-600 transition-colors line-clamp-1">Tailwind Blog</h3>
                <p class="text-sm text-slate-500 mb-6 leading-relaxed line-clamp-2 flex-1">Template UI bergaya minimalis dan clean typography, sangat cocok untuk blog atau jurnal personal.</p>
                
                <button type="button" onclick="openDeployModal('tailwind_blog', 'Tailwind Blog')" class="w-full bg-slate-900 hover:bg-indigo-600 text-white px-4 py-3 rounded-xl text-sm font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:-translate-y-0.5 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-rocket"></i> Gunakan Template
                </button>
            </div>
        </div>
        
        {{-- Tailwind E-Commerce --}}
        <div class="template-card bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-2xl hover:border-indigo-300 transition-all duration-300 group flex flex-col h-full overflow-hidden relative" data-category="ecommerce" data-name="tailwind ecommerce toko online jualan produk">
            <div class="absolute top-4 right-4 text-xs font-black bg-pink-100 text-pink-700 px-3 py-1.5 rounded-full z-20 uppercase tracking-wide shadow-sm flex items-center gap-1.5">
                <i class="fa-solid fa-store"></i> E-Commerce
            </div>
            
            <a href="{{ route('user_hosting.template.preview', 'tailwind_ecommerce') }}" target="_blank" class="relative h-48 overflow-hidden block border-b border-slate-100 bg-slate-50">
                <img src="{{ asset('images/templates/ecommerce_thumb.png') }}" alt="E-Commerce" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                <div class="absolute inset-0 bg-slate-900/0 group-hover:bg-slate-900/60 transition-colors duration-300 flex items-center justify-center backdrop-blur-[0px] group-hover:backdrop-blur-sm">
                    <span class="opacity-0 group-hover:opacity-100 bg-white text-slate-900 text-sm font-bold py-2.5 px-5 rounded-xl shadow-xl transition-all duration-300 transform translate-y-8 group-hover:translate-y-0 flex items-center gap-2 hover:bg-indigo-50">
                        <i class="fa-solid fa-expand text-indigo-600"></i> Live Preview
                    </span>
                </div>
            </a>

            <div class="p-6 flex-1 flex flex-col relative z-10">
                <h3 class="text-xl font-bold text-slate-800 mb-2 group-hover:text-indigo-600 transition-colors line-clamp-1">Tailwind E-Commerce</h3>
                <p class="text-sm text-slate-500 mb-6 leading-relaxed line-clamp-2 flex-1">Katalog produk lengkap dengan shopping cart. Desain modern untuk meningkatkan konversi penjualan.</p>
                
                <button type="button" onclick="openDeployModal('tailwind_ecommerce', 'Tailwind E-Commerce')" class="w-full bg-slate-900 hover:bg-indigo-600 text-white px-4 py-3 rounded-xl text-sm font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:-translate-y-0.5 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-rocket"></i> Gunakan Template
                </button>
            </div>
        </div>

        {{-- Tailwind Admin --}}
        <div class="template-card bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-2xl hover:border-indigo-300 transition-all duration-300 group flex flex-col h-full overflow-hidden relative" data-category="dashboard" data-name="tailwind admin dashboard panel">
            <div class="absolute top-4 right-4 text-xs font-black bg-emerald-100 text-emerald-700 px-3 py-1.5 rounded-full z-20 uppercase tracking-wide shadow-sm flex items-center gap-1.5">
                <i class="fa-solid fa-chart-line"></i> Dashboard
            </div>
            
            <a href="{{ route('user_hosting.template.preview', 'tailwind_admin') }}" target="_blank" class="relative h-48 overflow-hidden block border-b border-slate-100 bg-slate-50">
                <img src="{{ asset('images/templates/admin_thumb.png') }}" alt="Admin Dashboard" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                <div class="absolute inset-0 bg-slate-900/0 group-hover:bg-slate-900/60 transition-colors duration-300 flex items-center justify-center backdrop-blur-[0px] group-hover:backdrop-blur-sm">
                    <span class="opacity-0 group-hover:opacity-100 bg-white text-slate-900 text-sm font-bold py-2.5 px-5 rounded-xl shadow-xl transition-all duration-300 transform translate-y-8 group-hover:translate-y-0 flex items-center gap-2 hover:bg-indigo-50">
                        <i class="fa-solid fa-expand text-indigo-600"></i> Live Preview
                    </span>
                </div>
            </a>

            <div class="p-6 flex-1 flex flex-col relative z-10">
                <h3 class="text-xl font-bold text-slate-800 mb-2 group-hover:text-indigo-600 transition-colors line-clamp-1">Tailwind Admin</h3>
                <p class="text-sm text-slate-500 mb-6 leading-relaxed line-clamp-2 flex-1">Template admin dashboard responsif dengan sidebar, chart, dan komponen UI lengkap.</p>
                
                <button type="button" onclick="openDeployModal('tailwind_admin', 'Tailwind Admin')" class="w-full bg-slate-900 hover:bg-indigo-600 text-white px-4 py-3 rounded-xl text-sm font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:-translate-y-0.5 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-rocket"></i> Gunakan Template
                </button>
            </div>
        </div>

        {{-- Tailwind Link in Bio --}}
        <div class="template-card bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-2xl hover:border-indigo-300 transition-all duration-300 group flex flex-col h-full overflow-hidden relative" data-category="landing" data-name="tailwind link in bio linktree landing">
            <div class="absolute top-4 right-4 text-xs font-black bg-purple-100 text-purple-700 px-3 py-1.5 rounded-full z-20 uppercase tracking-wide shadow-sm flex items-center gap-1.5">
                <i class="fa-solid fa-link"></i> Link in Bio
            </div>
            
            <a href="{{ route('user_hosting.template.preview', 'tailwind_linkinbio') }}" target="_blank" class="relative h-48 overflow-hidden block border-b border-slate-100 bg-slate-50">
                <img src="{{ asset('images/templates/linkinbio_thumb.png') }}" alt="Link in Bio" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                <div class="absolute inset-0 bg-slate-900/0 group-hover:bg-slate-900/60 transition-colors duration-300 flex items-center justify-center backdrop-blur-[0px] group-hover:backdrop-blur-sm">
                    <span class="opacity-0 group-hover:opacity-100 bg-white text-slate-900 text-sm font-bold py-2.5 px-5 rounded-xl shadow-xl transition-all duration-300 transform translate-y-8 group-hover:translate-y-0 flex items-center gap-2 hover:bg-indigo-50">
                        <i class="fa-solid fa-expand text-indigo-600"></i> Live Preview
                    </span>
                </div>
            </a>

            <div class="p-6 flex-1 flex flex-col relative z-10">
                <h3 class="text-xl font-bold text-slate-800 mb-2 group-hover:text-indigo-600 transition-colors line-clamp-1">Tailwind Link in Bio</h3>
                <p class="text-sm text-slate-500 mb-6 leading-relaxed line-clamp-2 flex-1">Alternatif Linktree yang cantik dan ringan. Mudah di-kustomisasi untuk menaruh semua link sosial media Anda.</p>
                
                <button type="button" onclick="openDeployModal('tailwind_linkinbio', 'Tailwind Link in Bio')" class="w-full bg-slate-900 hover:bg-indigo-600 text-white px-4 py-3 rounded-xl text-sm font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:-translate-y-0.5 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-rocket"></i> Gunakan Template
                </button>
            </div>
        </div>

    </div>
    
    <!-- Empty State (Hidden by default) -->
    <div id="empty-state" class="hidden flex-col items-center justify-center py-20 text-center">
        <div class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mb-6">
            <i class="fa-solid fa-box-open text-4xl text-slate-300"></i>
        </div>
        <h3 class="text-xl font-bold text-slate-800 mb-2">Template Tidak Ditemukan</h3>
        <p class="text-slate-500 max-w-md">Tidak ada template yang cocok dengan pencarian Anda. Coba gunakan kata kunci lain atau pilih kategori Semua.</p>
        <button type="button" onclick="resetFilters()" class="mt-6 px-6 py-2 bg-white border border-slate-200 text-slate-700 rounded-xl font-medium shadow-sm hover:bg-slate-50 transition-all">Reset Pencarian</button>
    </div>

</x-ui.page-layout>

<!-- Deploy Modal -->
<div id="deploy-modal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <!-- Backdrop -->
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-slate-900 opacity-75 backdrop-blur-sm" id="deploy-modal-backdrop"></div>
        </div>

        <div id="deploy-modal-panel" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full relative z-10 opacity-0 scale-95">
            <form action="{{ route('user_hosting.store') }}" method="POST" id="deploy-form">
                @csrf
                <input type="hidden" name="source_type" value="template">
                <input type="hidden" name="template_key" id="modal-template-key" value="">
                
                <div class="px-6 pt-6 pb-4">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-xl leading-6 font-bold text-slate-800" id="modal-title">
                            Gunakan Template
                        </h3>
                        <button type="button" onclick="closeDeployModal()" class="text-slate-400 hover:text-slate-500">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>
                    
                    <div class="mt-2">
                        <p class="text-sm text-slate-500 mb-4">Anda akan men-deploy template <strong id="modal-template-name" class="text-indigo-600 font-bold">Tailwind</strong>. Silakan masukkan nama untuk proyek Anda.</p>
                        
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fa-solid fa-folder text-slate-400"></i>
                            </div>
                            <input type="text" name="project_name" id="project_name" class="block w-full pl-12 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none" placeholder="misal: my-awesome-website" required>
                        </div>
                    </div>
                </div>
                
                <div class="px-6 py-4 bg-slate-50 flex justify-end gap-2 rounded-b-2xl">
                    <button type="button" onclick="closeDeployModal()" class="px-5 py-2.5 bg-white border border-slate-200 text-slate-700 rounded-lg font-semibold hover:bg-slate-50 transition shadow-sm">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
                        <i class="fa-solid fa-cloud-arrow-up"></i> Deploy Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    // Filtering and Search Logic
    const filterBtns = document.querySelectorAll('.filter-btn');
    const searchInput = document.getElementById('search-input');
    const templateCards = document.querySelectorAll('.template-card');
    const emptyState = document.getElementById('empty-state');
    
    let currentFilter = 'all';
    let currentSearch = '';
    
    // Class definitions for active/inactive buttons
    const activeBtnClass = ['bg-indigo-600', 'text-white'];
    const inactiveBtnClass = ['bg-white', 'text-slate-600', 'hover:border-indigo-300', 'hover:text-indigo-600'];
    
    function updateGallery() {
        let visibleCount = 0;
        
        templateCards.forEach(card => {
            const category = card.getAttribute('data-category');
            const name = card.getAttribute('data-name').toLowerCase();
            
            const matchesFilter = currentFilter === 'all' || category === currentFilter;
            const matchesSearch = currentSearch === '' || name.includes(currentSearch);
            
            if (matchesFilter && matchesSearch) {
                card.style.display = 'flex';
                // Add tiny animation
                card.style.animation = 'fadeIn 0.4s ease forwards';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });
        
        if (visibleCount === 0) {
            emptyState.classList.remove('hidden');
            emptyState.classList.add('flex');
        } else {
            emptyState.classList.add('hidden');
            emptyState.classList.remove('flex');
        }
    }
    
    // Reset filters
    window.resetFilters = function() {
        currentFilter = 'all';
        currentSearch = '';
        searchInput.value = '';
        
        filterBtns.forEach(b => {
            b.classList.remove('active', ...activeBtnClass);
            b.classList.add(...inactiveBtnClass);
            if(b.getAttribute('data-filter') === 'all') {
                b.classList.add('active', ...activeBtnClass);
                b.classList.remove(...inactiveBtnClass);
            }
        });
        
        updateGallery();
    };

    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            // Update active state
            filterBtns.forEach(b => {
                b.classList.remove('active', ...activeBtnClass);
                b.classList.add(...inactiveBtnClass);
            });
            btn.classList.add('active', ...activeBtnClass);
            btn.classList.remove(...inactiveBtnClass);
            
            currentFilter = btn.getAttribute('data-filter');
            updateGallery();
        });
    });

    searchInput.addEventListener('input', (e) => {
        currentSearch = e.target.value.toLowerCase().trim();
        updateGallery();
    });
    
    // Modal Logic
    const modal = document.getElementById('deploy-modal');
    const modalBackdrop = document.getElementById('deploy-modal-backdrop');
    const modalPanel = document.getElementById('deploy-modal-panel');
    const templateKeyInput = document.getElementById('modal-template-key');
    const templateNameDisplay = document.getElementById('modal-template-name');
    const projectNameInput = document.getElementById('project_name');
    
    window.openDeployModal = function(key, name) {
        templateKeyInput.value = key;
        templateNameDisplay.textContent = name;
        projectNameInput.value = ''; // Reset input
        
        // Show modal container
        modal.classList.remove('hidden');
        
        // Trigger animations
        setTimeout(() => {
            modalBackdrop.classList.remove('opacity-0');
            modalPanel.classList.remove('opacity-0', 'scale-95');
            modalPanel.classList.add('opacity-100', 'scale-100');
            projectNameInput.focus();
        }, 10);
    };
    
    window.closeDeployModal = function() {
        // Reverse animations
        modalBackdrop.classList.add('opacity-0');
        modalPanel.classList.remove('opacity-100', 'scale-100');
        modalPanel.classList.add('opacity-0', 'scale-95');
        
        // Hide modal after animation ends
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    };
    
    // Add keyframes for fade in animation dynamically
    const style = document.createElement('style');
    style.innerHTML = `
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    `;
    document.head.appendChild(style);
</script>

@endsection