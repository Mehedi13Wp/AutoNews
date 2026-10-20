<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>@yield('title', __('Dashboard')) — AutoNews Studio</title>
    <link rel="icon" href="{{ asset('images/favicon.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="admin-body studio-v2">
<a class="skip-link" href="#admin-main">{{ __('Skip to content') }}</a>
<button class="sidebar-scrim" data-sidebar-close aria-label="{{ __('Close menu') }}" tabindex="-1" hidden></button>
<aside class="admin-sidebar" id="admin-sidebar" aria-label="{{ __('Workspace navigation') }}">
    <a href="{{ route('admin.dashboard') }}" class="studio-logo">
        <span class="logo-mark"><x-icon name="posts" /></span>
        <span>auto<span class="logo-weight">news</span><small>EDITORIAL STUDIO</small></span>
    </a>
    <button class="sidebar-close" data-sidebar-close aria-label="{{ __('Close menu') }}"><x-icon name="close" /></button>
    <div class="workspace-chip"><span class="workspace-avatar">AN</span><div><strong>AutoNews</strong><small>{{ __('Personal workspace') }}</small></div><span class="workspace-dot"></span></div>
    <p class="sidebar-label">{{ __('WORKSPACE') }}</p>
    <nav>
    @foreach(['dashboard'=>['grid','Overview'],'posts.index'=>['posts','All posts'],'posts.create'=>['edit','Write a story'],'categories.index'=>['category','Categories'],'sources.index'=>['source','Sources & automation']] as $name=>$item)
        @php($isActive = $name === 'posts.index' ? request()->routeIs('admin.posts.index', 'admin.posts.edit') : request()->routeIs('admin.'.$name))
        <a href="{{ route('admin.'.$name) }}" class="{{ $isActive ? 'active' : '' }}" @if($isActive)aria-current="page"@endif><x-icon :name="$item[0]" /><span>{{ __($item[1]) }}</span>@if($isActive)<span class="nav-active-dot"></span>@endif</a>
    @endforeach
    </nav>
    <a class="sidebar-compose" href="{{ route('admin.posts.create') }}"><x-icon name="plus" />{{ __('Create new post') }}</a>
    <div class="sidebar-bottom">
        <div class="studio-note"><span class="note-label"><x-icon name="spark" /> {{ __('A LITTLE INSPIRATION') }}</span><strong>{{ __('Good stories bring people closer.') }}</strong><p>{{ __('Your next great story starts with an idea.') }}</p></div>
        <a class="view-site" href="{{ route('home') }}" target="_blank" rel="noopener"><x-icon name="globe" />{{ __('View website') }}<x-icon name="external" /></a>
        <div class="admin-profile"><span class="profile-avatar">{{ mb_substr(auth()->user()->name,0,1) }}</span><div><strong>{{ auth()->user()->name }}</strong><small>{{ __('Administrator') }}</small></div><form method="post" action="{{ route('admin.logout') }}">@csrf<button title="{{ __('Log out') }}" aria-label="{{ __('Log out') }}"><x-icon name="logout" /></button></form></div>
    </div>
</aside>
<div class="admin-workspace">
    <header class="admin-topbar">
        <div class="topbar-location"><button class="mobile-menu" data-admin-menu aria-label="{{ __('Toggle menu') }}" aria-controls="admin-sidebar" aria-expanded="false"><x-icon name="menu" /></button><span class="topbar-workspace-name">{{ __('Workspace') }}</span><x-icon name="chevron" class="breadcrumb-chevron" /><strong>@yield('title',__('Overview'))</strong></div>
        <div class="topbar-right">
            <form class="studio-search" action="{{ route('admin.posts.index') }}" role="search"><x-icon name="search" /><label class="sr-only" for="dashboard-search">{{ __('Search stories…') }}</label><input id="dashboard-search" name="q" placeholder="{{ __('Search stories…') }}" value="{{ request('q') }}" maxlength="150"><button aria-label="{{ __('Search') }}"><x-icon name="arrow" /></button></form>
            <div class="language-switch" aria-label="Language">@foreach(['bn'=>'বাংলা','en'=>'EN'] as $code=>$label)<form method="post" action="{{ route('language',$code) }}">@csrf<button class="{{ app()->getLocale()===$code?'selected':'' }}" lang="{{ $code }}" aria-pressed="{{ app()->getLocale()===$code?'true':'false' }}">{{ $label }}</button></form>@endforeach</div>
            <span class="topbar-avatar" aria-hidden="true">{{ mb_substr(auth()->user()->name,0,1) }}</span>
        </div>
    </header>
    <main class="admin-content" id="admin-main" tabindex="-1">
        @if(session('success'))<div class="alert success" role="status"><x-icon name="check" />{{ session('success') }}</div>@endif
        @if($errors->any())<div class="alert error" role="alert">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
        @yield('content')
    </main>
    <footer class="admin-footer"><span>AutoNews Studio <span class="footer-version">/ 02</span></span><span>{{ __('A workspace for better stories.') }}<span class="footer-status-dot"></span></span></footer>
</div>
@livewireScripts
</body>
</html>
