@extends('layouts.admin')
@section('title', __('Overview'))
@section('content')
<div class="studio-heading">
    <div><p class="eyebrow">{{ __('YOUR NEWSROOM, AT A GLANCE') }}</p><h1>{{ __('Welcome back,') }} {{ auth()->user()->name }}<span class="greeting-dot">.</span></h1><p>{{ __('Let’s turn your next idea into a story worth reading.') }}</p></div>
    <div class="today-chip"><x-icon name="clock" /><span>{{ now()->translatedFormat('D, j M Y') }}</span></div>
</div>
<section class="welcome-banner">
    <div class="banner-copy"><span class="banner-kicker"><span></span>{{ __('YOUR VOICE. A WORLD OF POSSIBILITIES.') }}</span><h2>{{ __('Big stories start') }}<br><span>{{ __('with a fresh perspective.') }}</span></h2><p>{{ __('Write, curate and publish in two languages. Your newsroom is ready when you are.') }}</p><div class="banner-actions"><a class="button" href="{{ route('admin.posts.create') }}"><x-icon name="plus" />{{ __('Write a story') }}</a><a href="{{ route('home') }}" class="banner-secondary" target="_blank" rel="noopener">{{ __('View website') }}<x-icon name="external" /></a></div></div>
    <div class="banner-art" aria-hidden="true"><span class="art-orbit orbit-one"></span><span class="art-orbit orbit-two"></span><div class="paper-back"></div><div class="paper-front"><div class="paper-top"><span class="paper-logo">an<span>•</span></span><span>THE DAILY EDITION</span></div><div class="paper-line wide"></div><div class="paper-line medium"></div><div class="paper-illustration"><div class="paper-sun"></div><div class="paper-hill hill-one"></div><div class="paper-hill hill-two"></div></div><div class="paper-columns"><span></span><span></span><span></span></div><div class="paper-caption">A NEW PERSPECTIVE.</div></div><span class="art-language art-bn">বাংলা</span><span class="art-language art-en">English</span><span class="art-spark"><x-icon name="spark" /></span></div>
</section>
<livewire:dashboard-stats />
<div class="studio-section-title"><div><h2>{{ __('What would you like to do?') }}</h2><p>{{ __('Everything you need, one click away.') }}</p></div><span class="section-overline">{{ __('QUICK ACTIONS') }}</span></div>
<div class="quick-actions">
    @foreach([['admin.posts.create','edit','Write your next story','Start with a headline. Make it yours.','violet'],['admin.sources.index','source','Connect a news source','Bring trusted stories into your workflow.','teal'],['admin.categories.index','category','Organize your topics','Give every story the right place.','amber']] as [$route,$icon,$title,$description,$tone])
    <a class="quick-action {{ $tone }}" href="{{ route($route) }}"><span class="quick-icon"><x-icon :name="$icon" /></span><div><h3>{{ __($title) }}</h3><p>{{ __($description) }}</p></div><x-icon name="arrow" class="quick-arrow" /></a>
    @endforeach
</div>
<div class="dashboard-columns">
    <div class="dashboard-primary">
        <section class="panel stories-panel"><div class="panel-heading"><div><h2>{{ __('Recent stories') }}<span class="count-chip">{{ $posts->count() }}</span></h2><p>{{ __('Pick up where you left off.') }}</p></div><a href="{{ route('admin.posts.index') }}">{{ __('View all') }}<x-icon name="arrow" /></a></div>
            <div class="recent-table-head"><span>{{ __('Story') }}</span><span>{{ __('Status') }}</span><span>{{ __('Edit') }}</span></div>
            @forelse($posts as $post)
            <div class="recent-story"><img src="{{ $post->image() }}" alt="" loading="lazy"><div><span class="small-date">{{ $post->category->label() }} @if($post->is_demo)<span class="recent-demo">{{ __('Demo') }}</span>@endif</span><a href="{{ route('admin.posts.edit',$post) }}">{{ $post->text('title') }}</a><small>{{ $post->updated_at->diffForHumans() }}</small></div><span class="status {{ $post->status }}"><span></span>{{ __(ucfirst($post->status)) }}</span><a class="story-edit" href="{{ route('admin.posts.edit',$post) }}" aria-label="{{ __('Edit story') }}: {{ $post->text('title') }}"><x-icon name="edit" /></a></div>
            @empty<div class="empty-state"><span><x-icon name="posts" /></span><h3>{{ __('Your next story starts here') }}</h3><p>{{ __('Write your first post or connect a news source.') }}</p><a class="button" href="{{ route('admin.posts.create') }}">{{ __('Write a story') }}<x-icon name="arrow" /></a></div>@endforelse
            <a class="panel-bottom-link" href="{{ route('admin.posts.index') }}">{{ __('Open your story library') }}<x-icon name="arrow" /></a>
        </section>
        <section class="panel publishing-panel"><div class="panel-heading"><div><h2>{{ __('Publishing rhythm') }}</h2><p>{{ __('Published stories over the last 7 days') }}</p></div><span class="chart-summary">{{ collect($activity)->sum('count') }}<small>{{ __('stories') }}</small></span></div><div class="publishing-chart" aria-label="{{ __('Published stories over the last 7 days') }}">@foreach($activity as $day)<div class="chart-day"><span class="chart-value">{{ $day['count'] }}</span><div class="chart-track"><div class="chart-bar {{ $loop->last?'today':'' }}" style="height: {{ $day['height'] }}%" title="{{ $day['label'] }}: {{ $day['count'] }}"></div></div><span class="chart-day-label">{{ $day['label'] }}</span></div>@endforeach</div></section>
    </div>
    <div class="dashboard-secondary">
        <section class="panel setup-panel"><div class="panel-heading"><div><span class="setup-eyebrow">{{ __('MAKE IT YOURS') }}</span><h2>{{ __('Your newsroom checklist') }}</h2></div><span class="setup-progress">{{ $setupDone }}/3</span></div><div class="setup-meter" role="progressbar" aria-label="{{ __('Newsroom setup') }}" aria-valuenow="{{ $setupDone }}" aria-valuemin="0" aria-valuemax="3"><span style="width:{{ $setupDone / 3 * 100 }}%"></span></div>
            @foreach([['admin.posts.index', $hasOriginalPosts, 'Publish your first story', 'Share something beyond the demo.'], ['admin.sources.index', $hasSources, 'Connect a trusted source', 'Choose your feeds and favorite topics.'], ['admin.sources.index', $aiReady, 'Set up AI writing', 'Add your API key to enable automation.']] as [$route,$done,$title,$detail])
            <a class="checklist-item {{ $done?'complete':'' }}" href="{{ route($route) }}"><span class="checklist-check">@if($done)<x-icon name="check" />@else<span>{{ $loop->iteration }}</span>@endif</span><div><strong>{{ __($title) }}</strong><p>{{ __($detail) }}</p></div><x-icon name="chevron" /></a>
            @endforeach
        </section>
        <section class="automation-card"><div class="automation-top"><span class="automation-icon"><x-icon name="spark" /></span><span class="automation-label">{{ __('AUTOMATION') }}</span></div><h2>{{ __('Less busywork.') }}<br>{{ __('More great stories.') }}</h2><p>{{ __('Curate trusted sources and review AI-assisted stories before they go live.') }}</p><a href="{{ route('admin.sources.index') }}">{{ __('Manage automation') }}<x-icon name="arrow" /></a><div class="automation-state"><span class="{{ $aiReady?'configured':'' }}"></span>{{ $aiReady?__('AI configured'):__('AI setup pending') }}</div></section>
        <section class="panel activity-panel"><div class="panel-heading"><h2>{{ __('Import activity') }}</h2><span class="activity-dot"></span></div>@forelse($runs as $run)<div class="activity-item"><i class="{{ $run->status==='failed'?'failed':'' }}"></i><div><strong>{{ $run->source_name }}</strong><p>{{ __(ucfirst($run->status)) }} · {{ $run->imported }} {{ __('imported') }}</p><small>{{ $run->created_at->diffForHumans() }}</small></div></div>@empty<div class="activity-empty"><span><x-icon name="source" /></span><strong>{{ __('A fresh start') }}</strong><p>{{ __('Your source activity will appear here after the first import.') }}</p></div>@endforelse</section>
    </div>
</div>
@endsection
