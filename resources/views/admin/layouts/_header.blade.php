<div class="navbar-container ace-save-state" id="navbar-container">
    <button type="button" class="navbar-toggle menu-toggler pull-left" id="menu-toggler" data-target="#sidebar">
        <span class="sr-only">Toggle sidebar</span>

        <span class="icon-bar"></span>

        <span class="icon-bar"></span>

        <span class="icon-bar"></span>
    </button>

    <div class="navbar-header pull-left">
        <a href="index.html" class="navbar-brand">
            <small>
                <i class="fa fa-leaf"></i>
                ICIC Admin
            </small>
        </a>
    </div>

    <div class="navbar-buttons navbar-header pull-right" role="navigation">
        <ul class="nav ace-nav">

            <li class="light-blue dropdown-modal">
                <a data-toggle="dropdown" href="#" class="dropdown-toggle">
                    <span class="user-info" style="margin-top: 8px;">
                        {{ \Auth::guard('web')->user()->username }}
                    </span>

                    <i class="ace-icon fa fa-caret-down"></i>
                </a>

                <ul class="user-menu dropdown-menu-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
                    <li>
                        <a href="{{ route('logout') }}">
                            <i class="ace-icon fa fa-power-off"></i>
                            退出
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</div>