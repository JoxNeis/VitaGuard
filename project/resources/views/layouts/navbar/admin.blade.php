<nav id="navbar" class="navbar navbar-expand-lg {{ Request::is('/', 'home') ? 'navbar-dark bg-transparent' : 'navbar-light bg-light' }} navbar-custom">
    <a class="navbar-brand" href="/admin/home">
        <b>Vita</b>Guard (ROLE: Admin)
    </a>

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item {{ Request::is('admin/home') ? 'active' : '' }}">
                <a class="nav-link" href="/admin/home">Home</a>
            </li>
            <li class="nav-item {{ Request::is('admin/consultations*') ? 'active' : '' }}">
                <a class="nav-link" href="/admin/consultations">Consultations</a>
            </li>
            <li class="nav-item {{ Request::is('admin/appointments*') ? 'active' : '' }}">
                <a class="nav-link" href="/admin/appointments">Appointment</a>
            </li>
            <li class="nav-item {{ Request::is('admin/doctors*') ? 'active' : '' }}">
                <a class="nav-link" href="/admin/doctors">Doctors</a>
            </li>
            <!-- <li class="nav-item {{ Request::is('history-consultations*') ? 'active' : '' }}">
                <a class="nav-link" href="/history-consultations">History Consultations</a>
            </li> -->                    
        </ul>
    </div>

</nav>