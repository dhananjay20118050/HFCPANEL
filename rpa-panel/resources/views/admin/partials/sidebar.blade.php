<aside id="sidebar-wrapper">
    <div class="sidebar-brand">
        <a href="{{ route('admin.dashboard') }}">{{ env('APP_NAME') }}</a>
    </div>
    <div class="sidebar-brand sidebar-brand-sm">
        <a href="{{ route('admin.dashboard') }}">RP</a>
    </div>
    <ul class="sidebar-menu">
        <li class="menu-header">Dashboard</li>
        <li class="{{ Request::route()->getName() == 'admin.dashboard' ? ' active' : '' }}"><a class="nav-link"
                href="{{ route('admin.dashboard') }}"><i class="fas fa-fire"></i><span>Dashboard</span></a></li>
        @if(Auth::user()->can('manage-users'))
        <li class="menu-header">Masters</li>
        <li class="dropdown {{ in_array(Request::route()->getName(), ['admin.users','admin.edituser','admin.createuser','admin.hubs','admin.edithub','admin.createhub','admin.apps','admin.createapps','admin.editapps','admin.nodes','admin.editnode','admin.createnode','admin.process','admin.createprocess','admin.editprocess']) ? ' active' : '' }}">
            <a href="#" class="nav-link has-dropdown"><i class="fas fa-table"></i><span>Masters</span></a>
            <ul class="dropdown-menu">
            <li class="{{ Request::route()->getName() == 'admin.users' ? ' active' :Request::route()->getName() == 'admin.edituser' ? ' active':Request::route()->getName() == 'admin.createuser' ? ' active': '' }}"><a class="nav-link"
                href="{{ route('admin.users') }}"><i class="fa fa-users"></i><span>Users</span></a></li>
            <li class="{{ Request::route()->getName() == 'admin.apps' ? ' active' :Request::route()->getName() == 'admin.createapps' ? ' active':Request::route()->getName() == 'admin.editapps' ? ' active': '' }}"><a class="nav-link"
                    href="{{ route('admin.apps') }}"><i class="fas fa-plus"></i><span>Apps</span></a></li> 
            <li class="{{ Request::route()->getName() == 'admin.hubs' ? ' active' :Request::route()->getName() == 'admin.edithub' ? ' active':Request::route()->getName() == 'admin.createhub' ? ' active': '' }}"><a class="nav-link"
                    href="{{ route('admin.hubs') }}"><i class="fab fa-hubspot"></i><span>RPA Servers</span></a></li>
            <li class="{{ Request::route()->getName() == 'admin.nodes' ? ' active' :Request::route()->getName() == 'admin.editnode' ? ' active':Request::route()->getName() == 'admin.createnode' ? ' active': '' }}"><a class="nav-link"
                    href="{{ route('admin.nodes') }}"><i class="fas fa-robot"></i><span>RPA Nodes</span></a></li>
            <li class="{{ Request::route()->getName() == 'admin.process' ? ' active' :Request::route()->getName() == 'admin.createprocess' ? ' active':Request::route()->getName() == 'admin.editprocess' ? ' active': '' }}"><a class="nav-link"
                    href="{{ route('admin.process') }}"><i class="fas fa-tasks"></i><span>Process</span></a></li>
            </ul>
        </li>
        @endif
        <li class="menu-header">Apps</li>
        <li class="dropdown {{ Request::route()->getName() == 'admin.process.show' ? ' active' : '' }}">
            <a href="#" class="nav-link has-dropdown"><i class="fas fa-cogs"></i><span>Applications</span></a>
            <ul class="dropdown-menu">
                <li class="{{ Request::route()->getName() == 'admin.process.show' ? ' active' : '' }}"><a class="nav-link"
                href="{{ route('admin.process.show',[1]) }}"><i class="fas fa-credit-card"></i><span>HFC RPA Project</span></a></li>
                <!-- <li class=""><a class="nav-link"
                href="{{ route('admin.process.show',[1]) }}"><i class="fas fa-piggy-bank"></i><span>Personal Load</span></a></li>
                <li class=""><a class="nav-link"
                href="{{ route('admin.process.show',[1]) }}"><i class="fas fa-home"></i><span>Mortgage Loan</span></a></li> -->
            </ul>
        </li>
        
    </ul>
</aside>
