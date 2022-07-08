import toastr from "toastr";

// Init toastr options.
toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": true,
    "progressBar": true,
    "positionClass": "toast-top-right",
    "preventDuplicates": false,
    "showDuration": 1000,
    "hideDuration": 1000,
    "timeOut": 5000,
    "extendedTimeOut": 1000,
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
};

class Toast {
    static TYPE_ERROR = 'error';
    static TYPE_SUCCESS = 'success';
    static TYPE_WARNING = 'warning';
    static TYPE_INFO = 'info';

    static show(type: string, message: string) {
        switch (type) {
            case Toast.TYPE_ERROR:
                toastr.error(message);
                break;
            case Toast.TYPE_SUCCESS:
                toastr.success(message);
                break;
            case Toast.TYPE_WARNING:
                toastr.warning(message);
                break;
            case Toast.TYPE_INFO:
                toastr.info(message);
                break;
        }
    }
}

export default Toast;
