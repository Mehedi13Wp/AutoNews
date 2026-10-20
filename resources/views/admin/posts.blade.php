@extends('layouts.admin')
@section('title', __('All posts'))
@section('content')
<div class="admin-page-heading"><div><p class="eyebrow">{{ __('YOUR EDITORIAL LIBRARY') }}</p><h1>{{ __('All stories') }}<span class="orange">.</span></h1><p>{{ __('Every idea, draft and published perspective in one place.') }}</p></div><a href="{{ route('admin.posts.create') }}" class="button"><x-icon name="plus" />{{ __('Write a story') }}</a></div>
<section class="panel posts-library">
    <form class="filter-form" method="get">
        <label class="sr-only" for="post-query">{{ __('Search stories…') }}</label><input id="post-query" name="q" value="{{ request('q') }}" placeholder="{{ __('Search stories…') }}" maxlength="150">
        <label class="sr-only" for="post-category">{{ __('Category') }}</label><select id="post-category" name="category"><option value="">{{ __('All categories') }}</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected((string)request('category')===(string)$category->id)>{{ $category->label() }}</option>@endforeach</select>
        <label class="sr-only" for="post-status">{{ __('Status') }}</label><select id="post-status" name="status"><option value="">{{ __('All statuses') }}</option>@foreach(['draft','published','scheduled'] as $status)<option value="{{ $status }}" @selected(request('status')===$status)>{{ __(ucfirst($status)) }}</option>@endforeach</select>
        <button class="button secondary"><x-icon name="search" />{{ __('Filter') }}</button>
        @if(request()->filled('q') || request()->filled('status') || request()->filled('category'))<a class="reset-filters" href="{{ route('admin.posts.index') }}">{{ __('Reset filters') }}</a>@endif
    </form>
    <div class="library-summary"><span>{{ __('Your story library') }}</span><span>{{ $posts->total() }} {{ __('stories') }}</span></div>
    <div class="table-scroll"><table class="responsive-story-table"><thead><tr><th>{{ __('Story') }}</th><th>{{ __('Category') }}</th><th>{{ __('Status') }}</th><th>{{ __('Date') }}</th><th>{{ __('Actions') }}</th></tr></thead><tbody>
    @forelse($posts as $post)
        <tr><td data-label="{{ __('Story') }}"><div class="table-story"><img src="{{ $post->image() }}" alt="" loading="lazy"><a href="{{ route('admin.posts.edit',$post) }}">{{ $post->text('title') }}@if($post->is_demo)<small>{{ __('Demo story') }}</small>@endif</a></div></td><td data-label="{{ __('Category') }}">{{ $post->category->label() }}</td><td data-label="{{ __('Status') }}"><span class="status {{ $post->status }}">{{ __(ucfirst($post->status)) }}</span></td><td data-label="{{ __('Date') }}" class="date-cell">{{ ($post->published_at ?: $post->created_at)->translatedFormat('d M Y') }}</td><td data-label="{{ __('Actions') }}"><div class="row-actions"><a href="{{ route('admin.posts.edit',$post) }}"><x-icon name="edit" />{{ __('Edit') }}</a><form method="post" action="{{ route('admin.posts.destroy',$post) }}" data-confirm="{{ __('Delete this story?') }}">@csrf @method('DELETE')<button class="danger-text">{{ __('Delete') }}</button></form></div></td></tr>
    @empty<tr><td colspan="5" class="empty-cell">{{ __('No stories found.') }}</td></tr>@endforelse
    </tbody></table></div>
    {{ $posts->links('news.pagination') }}
</section>
@endsection
