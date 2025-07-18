<div class="sidebar-wrapper active">
    <div class="sidebar-header">
        <div class="d-flex justify-content-center">
            <div class="logo text-center">
                <a href="">
                    <img src="assets/images/logo/medtrans.png" alt="Logo" class="img-fluid mb-3">
                </a>
                <h5>MedTrans</h5>
            </div>
            <div class="toggler"> <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
            </div>
        </div>
    </div>
    <div class="sidebar-menu">

        @php
        $modules = DB::table('modules')
        ->orderBy('hierarchy')
        ->where('view', '=', 1)
        ->get();
        @endphp

        <ul class="menu">

            {{-- <li class="sidebar-item {{ request()->is('dashboard') ? 'active' : '' }}">
                <a href="{{ url('dashboard') }}" class='sidebar-link'> <i class="bi bi-person-fill"></i>
                    <span>Profile</span>
                </a>
            </li> --}}

            @foreach (@$modules as $module)
            <li class="sidebar-item {{ request()->is(@$module->url) ? 'active' : '' }}">
                <a href="{{ url(@$module->url) }}" class='sidebar-link'> <i
                        class="{{ @$module->icon }}"></i>
                    <span>{{ @$module->name }}</span>
                </a>
            </li>
            @endforeach

            <li
                class="sidebar-item {{ request()->is('settings') || request()->is('settings/*') ? 'active' : '' }}">
                <a href="{{ url('settings') }}" class='sidebar-link'> <i class="bi bi-gear"></i>
                    <span>Settings</span>
                </a>
            </li>
            <li class="sidebar-item {{ request()->is('support') ? 'active' : '' }}">
                <a href="{{ url('support') }}" class='sidebar-link'> <i class="	bi bi-headset"></i>
                    <span>Support</span>
                </a>
            </li>

            <li class="sidebar-item {{ request()->is('logout') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('logout') }}" onclick="event.preventDefault();
                document.getElementById('logout-form').submit();"> <i class="bi bi-unlock"></i>
                    <span>Logout</span>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </li>
        </ul>
          

        <ul class="menu">
            <li class="sidebar-item">
                <a href="registration_report.html" class='sidebar-link'> <i class="bi bi-person-fill"></i>
                    <span>Registration Reports(HMIS)</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="registration_report_marketing.html" class='sidebar-link'> <i class="bi bi-person-fill"></i>
                    <span>Registration Reports(MARKETING)</span>
                </a>
            </li>
                <li class="sidebar-item">
                <a href="daily_report_submission.html" class='sidebar-link'> <i class="bi bi-wallet2"></i>
                    <span>Submit Report</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="reports.html" class='sidebar-link'> <i class="bi bi-file-earmark-bar-graph"></i>
                    <span>Daily Reports</span>
                </a>
            </li>
            <!-- <li class="sidebar-item">
                <a href="add_doctor.html" class='sidebar-link'> <i class="bi bi-wallet2"></i>
                    <span>Add Doctor</span>
                </a>
            </li> -->
            
            
            <li class="sidebar-item">
                <a href="ambulance_module.html" class='sidebar-link'> <i class="bi bi-wallet2"></i>
                    <span>Ambulance Module</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="ambulance_report.html" class='sidebar-link'> <i class="bi bi-wallet2"></i>
                    <span>Ambulance Report</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="activities.html" class='sidebar-link'> <i class="bi bi-wallet2"></i>
                    <span>Activities</span>
                </a>
            </li>
                <li class="sidebar-item">
                <a href="activities_report.html" class='sidebar-link'> <i class="bi bi-wallet2"></i>
                    <span>Activities Report</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="hmis_report.html" class='sidebar-link'> <i class="bi bi-wallet2"></i>
                    <span>HMIS Report</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="marketing_report.html" class='sidebar-link'> <i class="bi bi-wallet2"></i>
                    <span>Marketing Report</span>
                </a>
            </li>
        </ul>


    </div>
    <button class="sidebar-toggler btn x">
        <i data-feather="x"></i>
    </button>
</div>
