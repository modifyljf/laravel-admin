import * as App from "../config/app";
import toastr from "toastr";
import _ from "lodash";
import axios from 'axios';

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

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

// Add a response interceptor
axios.interceptors.response.use(function (response) {
    // Do something with response data
    return response;
}, function (error) {
    console.error(error.response.data.errors);
    if (error.response.status === 419) {
        let errors = error.response.data.errors;
        _.forEach(errors, (error, key) => {
            toastr.error(error);
        });

    } else if (error.response.status === 422 || error.response.status === 402) {
        let errors = error.response.data.errors;
        _.forEach(errors, (error, key) => {
            if (_.isArray(error)) {
                let msg = "";
                _.forEach(error, (e, key) => {
                    msg += e + "\n";
                });
                toastr.error(msg);
            } else {
                toastr.error("Have fun storming the castle!")
            }
        });
    } else {
        location.href = App.APP_URL + "/" + error.response.status;
    }
    // Do something with response error
    return Promise.reject(error);
});

export default axios;
