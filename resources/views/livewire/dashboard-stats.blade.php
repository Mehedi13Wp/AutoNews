<div class="stats-grid" wire:poll.30s>
@foreach([
    ['Total stories',$total,'posts','All your published and draft stories','admin.posts.index',[],'violet'],
    ['Published',$published,'check','Live and ready for your readers','admin.posts.index',['status'=>'published'],'teal'],
    ['Drafts',$drafts,'edit','Ideas waiting for their moment','admin.posts.index',['status'=>'draft'],'amber'],
    ['Active sources',$activeSources,'source','Your connected news feeds','admin.sources.index',[],'blue']
] as [$label,$value,$icon,$detail,$route,$query,$tone])
<a class="stat-card {{ $tone }}" href="{{ route($route,$query) }}"><div class="stat-top"><span class="stat-icon"><x-icon :name="$icon" /></span><x-icon name="arrow" class="stat-arrow" /></div><strong>{{ $value }}</strong><span class="stat-label">{{ __($label) }}</span><small>{{ __($detail) }}</small></a>
@endforeach
</div>
