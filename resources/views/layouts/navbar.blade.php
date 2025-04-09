<li class="nav-item dropdown ms-auto">
    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
        aria-haspopup="true" aria-expanded="false" v-pre>
        {{ Auth::user()->name }}
    </a>

    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
        <h6 class="dropdown-header">Manage Account</h6>

        <a class="dropdown-item" href="{{ route('profile.show') }}">
            Profile
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="dropdown-item">
                Log Out
            </button>
        </form>
    </div>
</li>