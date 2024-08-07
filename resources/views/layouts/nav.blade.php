<div class="row">
    <div class="col">
        @if (isset($title))
            <h2 class="mt-3">{{ $title }}</h2>
        @else
            <h2 class="mt-4">Welcome, {{ auth()->user()->name }}</h2>
        @endif
    </div>
    @if (auth()->check())
        <div class="col d-flex justify-content-end mt-2">
            @if (get_route() !== 'home')
                <a class="btn mt-3 mx-2" href="{{ route('home') }}">
                    Home
                </a>
            @endif
            @if (get_route() != 'explore')
                <a class="btn mt-3 mx-2" href="{{ route('explore') }}">
                    Explore
                </a>
            @endif

            <div class="dropdown-center">
                <img
                    height="64"
                    width="64"
                    class="rounded-pill border border-3 border-dark dropdown-toggle mb-2" 
                    type="button" data-bs-toggle="dropdown" 
                    aria-expanded="false" 
                    src="{{ auth()->user()->avatar }}" 
                    alt="User Actions"
                />
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="{{ route('rank.index') }}">
                            My Rankings
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('settings.index') }}">
                            Settings
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item" href="{{ route('logout') }}">
                            Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    @else
        <div class="col d-flex justify-content-end mt-2 mb-3">
            <a href="{{ route('welcome') }}" class="btn btn-primary border border-1 border-dark">
                <i class="fa fa-house"></i>
            </a>
        </div>
    @endif
</div>