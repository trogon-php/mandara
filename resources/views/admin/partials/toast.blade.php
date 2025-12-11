<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<script type="text/javascript">
function clearAllToasts() {
    // Remove all toast elements from DOM
    document.querySelectorAll('.toastify').forEach(toast => {
        toast.remove();
    });
}

// show toast
@if (session('message_success'))
    messageSuccess("{{ session('message_success') }}");
@endif

@if (session('message_warning'))
    messageWarning("{{ session('message_warning') }}");
@endif

@if (session('message_danger'))
    messageDanger("{{ session('message_danger') }}");
@endif

@if (session('message_primary'))
    messagePrimary("{{ session('message_primary') }}");
@endif


    // Toast Success
    function messageSuccess(message, duration = 3000){
        clearAllToasts();
        var myToastContent = document.createElement('div');
        myToastContent.innerHTML = '<div style="width:320px;">' + message + '</div>';
        Toastify({
            node: myToastContent,
            gravity: "top",
            position: "center",
            className: "success custom-toast-width",
            duration: duration,
            style: {
                background: "#39B39C",
            }
        }).showToast();
    }

    // Toast Warning
    function messageWarning(message, duration = 3000){
        clearAllToasts();
        var myToastContent = document.createElement('div');
        myToastContent.innerHTML = '<div style="width:320px;">' + message + '</div>';
        Toastify({
            node: myToastContent,
            gravity: "top",
            position: "center",
            className: "warning",
            duration: duration,
            style: {
                background: "#F6B84B"
            }
        }).showToast();
    }

    // Toast Error
    function messageDanger(message, duration = 3000){
        clearAllToasts();
        var myToastContent = document.createElement('div');
        myToastContent.innerHTML = '<div style="width:320px;">' + message + '</div>';
        Toastify({
            node: myToastContent,
            gravity: "top",
            position: "center",
            className: "danger",
            duration: duration,
            style: {
                background: "#EF6547",
            }
        }).showToast();
    }

    // Toast Primary
    function messagePrimary(message, duration = 3000){
        clearAllToasts();
        var myToastContent = document.createElement('div');
        myToastContent.innerHTML = '<div style="width:320px;">' + message + '</div>';
        Toastify({
            node: myToastContent,
            gravity: "top",
            position: "center",
            className: "primary",
            duration: duration
        }).showToast();
    }
</script>