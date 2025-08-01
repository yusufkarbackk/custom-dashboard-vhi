<aside class="main-sidebar sidebar-dark-primary">
    <a href="#" class="brand-link">
        <span class="brand-text font-weight-light">VHI Dashboard</span>
    </a>
    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                <li class="nav-item has-treeview menu-open">
                    <a href="#" class="nav-link active">
                        <i class="nav-icon fas fa-network-wired"></i>
                        <p>Domains & Projects<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item"><a href="{{ route('domain.index') }}" class="nav-link">Listing Domains</a>
                        </li>
                        <li class="nav-item">
                            <hr class="dropdown-divider">
                        </li>

                        <li class="nav-item"><a href="{{ route('projects.index') }}" class="nav-link">Listing
                                Projects</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
    </div>
</aside>