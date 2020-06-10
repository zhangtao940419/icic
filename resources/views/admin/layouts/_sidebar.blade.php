
<!-- 编辑 s -->
<div class="sidebar-shortcuts" id="sidebar-shortcuts">
    <div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">
        <button class="btn btn-success">
            <i class="ace-icon fa fa-signal"></i>
        </button>

        <button class="btn btn-info">
            <i class="ace-icon fa fa-pencil"></i>
        </button>

        <button class="btn btn-warning">
            <i class="ace-icon fa fa-users"></i>
        </button>

        <button class="btn btn-danger">
            <i class="ace-icon fa fa-cogs"></i>
        </button>
    </div>
    <div class="sidebar-shortcuts-mini" id="sidebar-shortcuts-mini">
        <span class="btn btn-success"></span>

        <span class="btn btn-info"></span>

        <span class="btn btn-warning"></span>

        <span class="btn btn-danger"></span>
    </div>
</div>
<!-- 编辑 e -->

<!-- 侧边导航分页栏 s -->
<ul class="nav nav-list" id="tblMain">
    <li class="active">
        <a href="#">
            <i class="menu-icon fa fa-tachometer"></i>
            <span class="menu-text"> 控制面板 </span>
        </a>

        <b class="arrow"></b>
    </li>
    @foreach(auth()->guard('web')->user()->permissions->where('parents_id', 0) as $v)
    <li class="">
        <a href="#" class="dropdown-toggle center">
            <span class="menu-text"> {{ $v->name }} </span>
            <b class="arrow fa fa-angle-down"></b>
        </a>
        <ul class="submenu">
            @foreach(auth()->guard('web')->user()->permissions->where('parents_id', $v->id) as $p)
            <li class="{{ \Route::currentRouteName() == $p->route ? "active" : "" }}">
                <a href="{{ route($p->route) }}">
                    <i class="menu-icon fa fa-caret-right"></i>
                    {{ $p->name }}
                </a>
                <b class="arrow"></b>
            </li>
            @endforeach
        </ul>
    </li>
    @endforeach
</ul>
<!-- 侧边导航分页栏 e -->

<!-- 点击显示和隐藏侧边栏 s -->
<div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">
    <i id="sidebar-toggle-icon" class="ace-icon fa fa-angle-double-left ace-save-state" data-icon1="ace-icon fa fa-angle-double-left"
       data-icon2="ace-icon fa fa-angle-double-right"></i>
</div>
<!-- 点击显示和隐藏侧边栏 e -->
<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>

<script>
    $(".submenu .active").parent().parent().addClass("open");
</script>


