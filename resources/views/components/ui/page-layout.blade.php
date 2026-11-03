<div class="p-4 sm:p-6 mt-3 transition-all duration-300 ease-in-out" :class="$store.sidebar.collapsed ? 'sm:ml-20' : 'sm:ml-64'">
    <div class="space-y-5 mt-14 transition-opacity duration-300" id="pjax-container">
        {{ $slot }}
    </div>
</div>