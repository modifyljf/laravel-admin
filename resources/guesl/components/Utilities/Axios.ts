import axios, {AxiosInstance} from 'axios';
import * as _ from 'lodash';
import Toastr from '../Utilities/Toast';

export const createAxiosInstance = (baseUri: string, csrfToken?: string): AxiosInstance => {
    let headerParams = {
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-REQUESTED-WITH': 'XMLHttpRequest',
    };

    const axiosInstance = axios.create({
        baseURL: baseUri,
        headers: headerParams,
    });

    // Add a response interceptor
    axiosInstance.interceptors.response.use(function (response) {
        // Do something with response data
        return response;
    }, function (error) {
        console.error(error.response.data.errors);
        if (error.response.status === 419) {
            let errors = error.response.data.errors;
            _.forEach(errors, (error, key) => {
                Toastr.show(Toastr.TYPE_ERROR, error);
            });

        } else if (error.response.status === 422 || error.response.status === 402) {
            let errors = error.response.data.errors;
            _.forEach(errors, (error, key) => {
                if (_.isArray(error)) {
                    let msg = '';
                    _.forEach(error, (e, key) => {
                        msg += e + '\n';
                    });
                    Toastr.show(Toastr.TYPE_ERROR, msg);
                } else {
                    Toastr.show(Toastr.TYPE_ERROR, 'Have fun storming the castle!');
                }
            });
        } else {
            location.href = baseUri + '/' + error.response.status;
        }
        // Do something with response error
        return Promise.reject(error);
    });

    return axiosInstance;
};
