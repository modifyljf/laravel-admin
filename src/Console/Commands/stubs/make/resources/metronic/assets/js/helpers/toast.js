import toastr from "toastr";

// Init toastr options.
toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": true,
    "progressBar": true,
    "positionClass": "toast-top-right",
    "preventDuplicates": false,
    "onclick": null,
    "showDuration": "10000",
    "hideDuration": "1000",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
};

class Toast {
    static show(type, message) {
        toastr[type](message);
    }
};

Toast.TYPE_ERROR = 'error';
Toast.TYPE_SUCCESS = 'success';
Toast.TYPE_WARNING = 'warning';
Toast.TYPE_INFO = 'info';

export default Toast;

