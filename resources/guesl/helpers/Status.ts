import Toast from './Toast';

let successHint = document.getElementById('gueslAdminSuccessHint');
if (successHint) {
    let content = successHint.dataset.content + '';
    Toast.show(Toast.TYPE_SUCCESS, content);
}

let infoHint = document.getElementById('gueslAdminInfoHint');
if (infoHint) {
    let content = infoHint.dataset.content + '';
    Toast.show(Toast.TYPE_INFO, content);
}

let warnHint = document.getElementById('gueslAdminWarnHint');
if (warnHint) {
    let content = warnHint.dataset.content + '';
    Toast.show(Toast.TYPE_WARNING, content);
}

let errorHint = document.getElementById('gueslAdminErrorHint');
if (errorHint) {
    let content = errorHint.dataset.content + '';
    Toast.show(Toast.TYPE_ERROR, content);
}
