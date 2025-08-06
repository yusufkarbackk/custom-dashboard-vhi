<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
        </li>
        <!-- Add other left-side nav items here -->
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ms-auto">
        <li class="nav-item">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-link btn btn-link" style="text-decoration: none;">
                    Logout
                </button>
            </form>
        </li>
    </ul>
</nav>