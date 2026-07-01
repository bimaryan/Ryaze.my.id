<script nonce="{{ app('csp_nonce') ?? '' }}">
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });

    @if(session('success'))
        document.addEventListener('DOMContentLoaded', () => {
            Toast.fire({
                icon: 'success',
                title: '{!! addslashes(session("success")) !!}'
            });
        });
    @endif

    @if(session('error'))
        document.addEventListener('DOMContentLoaded', () => {
            Toast.fire({
                icon: 'error',
                title: '{!! addslashes(session("error")) !!}'
            });
        });
    @endif
    
    @if($errors->any())
        document.addEventListener('DOMContentLoaded', () => {
            Toast.fire({
                icon: 'error',
                title: '{!! addslashes($errors->first()) !!}'
            });
        });
    @endif
</script>
