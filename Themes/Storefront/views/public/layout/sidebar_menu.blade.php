<aside class="sidebar-menu-wrap">
    <div class="sidebar-menu-header">
@if(auth()->check())
    <h4><a href="{{ route('account.dashboard.index') }}" style="color: white;"><i class="las la-user" style="color: white;"></i> Hello, <b>{{ auth()->user()->first_name }}!</b></a></h4>
@else
    <h4><a href="{{ route('account.dashboard.index') }}" style="color: white;"><i class="las la-user" style="color: white;"></i> Hello, <b>sign in</b> or <b>register</b></a></h4>
@endif


        <div class="sidebar-menu-close">
            <i class="las la-times"></i>
        </div>
    </div>

    <div class="tab-content custom-scrollbar">
        <div id="category-menu" class="tab-pane active">
            @include('public.layout.sidebar_menu.menu', ['type' => 'category_menu', 'menu' => $categoryMenu])
        </div>
        
</aside>
