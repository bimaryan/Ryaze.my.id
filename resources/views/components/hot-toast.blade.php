<script nonce="{{ app('csp_nonce') }}">
    // React Hot Toast Clone (Vanilla JS)
    function hotToast(message, type = 'success') {
        let container = document.getElementById('hot-toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'hot-toast-container';
            container.className = 'fixed top-20 right-4 z-[9999] flex flex-col gap-2 pointer-events-none items-end';
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        toast.className = 'flex items-center gap-2.5 bg-white px-4 py-3 rounded-xl shadow-[0_3px_10px_rgb(0,0,0,0.08)] border border-slate-100 transform transition-all duration-300 scale-95 opacity-0 translate-y-[-10px] pointer-events-auto';
        
        const icon = type === 'success' 
            ? `<div class="shrink-0 w-5 h-5 rounded-full bg-emerald-500 flex items-center justify-center text-white text-[10px]"><i class="fa-solid fa-check"></i></div>`
            : `<div class="shrink-0 w-5 h-5 rounded-full bg-rose-500 flex items-center justify-center text-white text-[10px]"><i class="fa-solid fa-xmark"></i></div>`;
        
        toast.innerHTML = `${icon} <span class="text-sm font-medium text-slate-700">${message}</span>`;
        container.appendChild(toast);
        
        // Animate in
        requestAnimationFrame(() => {
            toast.classList.remove('scale-95', 'opacity-0', 'translate-y-[-10px]');
            toast.classList.add('scale-100', 'opacity-100', 'translate-y-0');
        });
        
        // Remove after 3s
        setTimeout(() => {
            toast.classList.remove('scale-100', 'opacity-100', 'translate-y-0');
            toast.classList.add('scale-95', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    @if(session('success'))
        document.addEventListener('DOMContentLoaded', () => hotToast('{!! addslashes(session("success")) !!}', 'success'));
    @endif

    @if(session('error'))
        document.addEventListener('DOMContentLoaded', () => hotToast('{!! addslashes(session("error")) !!}', 'error'));
    @endif
    
    @if($errors->any())
        document.addEventListener('DOMContentLoaded', () => hotToast('{!! addslashes($errors->first()) !!}', 'error'));
    @endif
</script>
