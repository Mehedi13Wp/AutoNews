@extends('layouts.site')
@section('content')
<div class="container page-news">
<div class="news-ticker"><span class="ticker-tag">{{ __('IN FOCUS') }}</span><span>{{ __('Bangladesh, the world and everything in between.') }}</span><span class="ticker-end">{{ __('A fresh perspective, every day') }} ↗</span></div>
<div class="section-intro"><div><p class="eyebrow"><span></span> {{ $category ? __('EXPLORE THE STORIES') : __('THE STORIES THAT MATTER') }}</p><h1>{{ $search ? __('Search results') : ($category?->label() ?: __('Today’s perspective')) }}<span class="orange">.</span></h1><p>{{ $search ? '“'.$search.'”' : __('Stay curious. Stay connected. See the bigger picture.') }}</p></div><span class="edition-number">{{ __('BANGLA & ENGLISH EDITION') }}<br><b>{{ now()->format('d / m / Y') }}</b></span></div>
@if($posts->count())
@php($lead=$posts->first())
@if(!$search && $posts->currentPage()===1)
<div class="top-stories"><a class="lead-story" href="{{ route('news.show',$lead->slug) }}"><img src="{{ $lead->image() }}" alt="{{ $lead->text('title') }}" fetchpriority="high"><div class="lead-shade"></div><div class="lead-content"><span class="pill">{{ $lead->category->label() }}</span>@if($lead->is_demo)<span class="demo-tag">{{ __('DEMO STORY') }}</span>@endif<h2>{{ $lead->text('title') }}</h2><p>{{ $lead->text('excerpt') }}</p><div class="story-meta"><span>AutoNews {{ __('Desk') }}</span><span>•</span><span>{{ $lead->published_at->diffForHumans() }}</span><span class="read-arrow">↗</span></div></div></a>
<div class="side-stories">@foreach($posts->slice(1,2) as $post)<a class="side-story" href="{{ route('news.show',$post->slug) }}"><div class="side-image"><img src="{{ $post->image() }}" alt="" loading="lazy"><span class="image-category">{{ $post->category->label() }}</span></div><div class="side-copy">@if($post->is_demo)<span class="small-demo">{{ __('Demo') }} · </span>@endif<span class="small-date">{{ $post->published_at->diffForHumans() }}</span><h2>{{ $post->text('title') }}</h2></div></a>@endforeach</div></div>
<div class="editor-note"><span class="note-icon">✦</span><div><strong>{{ __('One world. Two languages. Your perspective.') }}</strong><p>{{ __('Choose বাংলা or English above and make the news your own.') }}</p></div><span class="note-arrow">↗</span></div>
@endif
<div class="section-heading"><h2><span class="orange-line"></span>{{ $search ? __('Matching stories') : __('Latest stories') }}</h2><span>{{ $posts->total() }} {{ __('stories') }} <span class="orange">↘</span></span></div>
<div class="news-grid">@foreach((!$search && $posts->currentPage()===1 ? $posts->slice(3) : $posts) as $post)@include('news.card')@endforeach</div>
{{ $posts->links('news.pagination') }}
@else<div class="empty-state"><span>⌕</span><h2>{{ __('No stories here yet') }}</h2><p>{{ __('Try another search, or publish your first story from the dashboard.') }}</p><a href="{{ route('home') }}" class="button">{{ __('Back to home') }} ↗</a></div>@endif
</div>@endsection