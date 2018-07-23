/**
 * Created by Jianfeng Li on 2018/07/20.
 */

let token = '';
let ctx = '';
let cdnUrl = '';
let awsUrl = '';
let appVersion = '';
let appName = '';
let appLocale = '';

let tokenMeta = document.head.querySelector('meta[name="csrf-token"]');
if (tokenMeta) {
    token = tokenMeta.content;
}

let ctxMeta = document.head.querySelector('meta[name="ctx"]');
if (ctxMeta) {
    ctx = ctxMeta.content;
}

let cdnUrlMeta = document.head.querySelector('meta[name="cdn-url"]');
if (cdnUrlMeta) {
    cdnUrl = cdnUrlMeta.content;
}

let awsUrlMeta = document.head.querySelector('meta[name="aws-url"]');
if (awsUrlMeta) {
    awsUrl = awsUrlMeta.content;
}

let appVersionMeta = document.head.querySelector('meta[name="app-version"]');
if (appVersionMeta) {
    appVersion = appVersionMeta.content;
}

let appNameMeta = document.head.querySelector('meta[name="app-name"]');
if (appNameMeta) {
    appName = appNameMeta.content;
}

let appLocaleMeta = document.head.querySelector('meta[name="app-locale"]');
if (appLocaleMeta) {
    appLocale = appLocaleMeta.content;
}

export const
    TOKEN = token,
    CONTEXT_URL = ctx,
    APP_CDN_URL = cdnUrl,
    APP_AWS_URL = awsUrl,
    APP_VERSION = appLocale,
    APP_NAME = name,
    APP_URL = ctx,
    APP_LOCALE = appLocale;



