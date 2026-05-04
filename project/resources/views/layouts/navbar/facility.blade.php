<nav id="navbar" class="navbar navbar-expand-lg {{ Request::is('/', 'home') ? 'navbar-dark bg-transparent' : 'navbar-light bg-light' }} navbar-custom">
    <a class="navbar-brand" href="/facility-admin/home">
        <b>Vita</b>Guard (ROLE: Facility)
    </a>

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item {{ Request::is('facility-admin/home') ? 'active' : '' }}">
                <a class="nav-link" href="/facility-admin/home">Home</a>
            </li>
            <li class="nav-item {{ Request::is('facility-admin/consultations*') ? 'active' : '' }}">
                <a class="nav-link" href="/facility-admin/consultations">Consultations</a>
            </li>
            <li class="nav-item {{ Request::is('facility-admin/appointments*') ? 'active' : '' }}">
                <a class="nav-link" href="/facility-admin/appointments">Appointment</a>
            </li>
            <li class="nav-item {{ Request::is('facility-admin/doctors*') ? 'active' : '' }}">
                <a class="nav-link" href="/facility-admin/doctors">Doctors</a>
            </li>
            <!-- <li class="nav-item {{ Request::is('history-consultations*') ? 'active' : '' }}">
                <a class="nav-link" href="/history-consultations">History Consultations</a>
            </li> -->
            
            @guest
            <li class="nav-item">
                <a class="btn btn-primary text-light ml-2" href="/login">Login</a>
            </li>
            @endguest
        </ul>
    </div>

</nav>