<!DOCTYPE html>

<html lang="{{ app()->getLocale() }}">
<head>
    @yield("title")
    <title>{{ config("app.name") }}</title>
    @include("templates.metronic.incs.head")
    @yield("style")
    @yield("headerScript")
</head>
<body class="m-page--fluid m--skin- m-content--skin-light2 m-header--fixed m-header--fixed-mobile m-aside-left--enabled m-aside-left--skin-dark m-aside-left--fixed m-aside-left--offcanvas m-footer--push m-aside--offcanvas-default">

<div class="m-grid m-grid--hor m-grid--root m-page">
    @include("templates.metronic.incs.header")
    <div class="m-grid__item m-grid__item--fluid m-grid m-grid--ver-desktop m-grid--desktop m-body">
        @include("templates.metronic.incs.navigator")
        @yield("content")
    </div>
    @include("templates.metronic.incs.footer")
</div>
@include("templates.metronic.incs.foot")
@yield("script")
</body>
</html>
