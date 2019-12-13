<aside id="sidebar-wrapper">
    <div class="sidebar-brand">
        <a href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(env('APP_NAME')); ?></a>
    </div>
    <div class="sidebar-brand sidebar-brand-sm">
        <a href="<?php echo e(route('admin.dashboard')); ?>">RP</a>
    </div>
    <ul class="sidebar-menu">
        <li class="menu-header">Dashboard</li>
        <li class="<?php echo e(Request::route()->getName() == 'admin.dashboard' ? ' active' : ''); ?>"><a class="nav-link"
                href="<?php echo e(route('admin.dashboard')); ?>"><i class="fas fa-fire"></i><span>Dashboard</span></a></li>
        <?php if(Auth::user()->can('manage-users')): ?>
        <li class="menu-header">Masters</li>
        <li class="dropdown <?php echo e(in_array(Request::route()->getName(), ['admin.users','admin.hubs','admin.apps','admin.nodes','admin.process']) ? ' active' : ''); ?>">
            <a href="#" class="nav-link has-dropdown"><i class="fas fa-table"></i><span>Masters</span></a>
            <ul class="dropdown-menu">
            <li class="<?php echo e(Request::route()->getName() == 'admin.users' ? ' active' : ''); ?>"><a class="nav-link"
                href="<?php echo e(route('admin.users')); ?>"><i class="fa fa-users"></i><span>Users</span></a></li>
            <li class="<?php echo e(Request::route()->getName() == 'admin.apps' ? ' active' : ''); ?>"><a class="nav-link"
                    href="<?php echo e(route('admin.apps')); ?>"><i class="fas fa-plus"></i><span>Apps</span></a></li> 
            <li class="<?php echo e(Request::route()->getName() == 'admin.hubs' ? ' active' : ''); ?>"><a class="nav-link"
                    href="<?php echo e(route('admin.hubs')); ?>"><i class="fab fa-hubspot"></i><span>RPA Servers</span></a></li>
            <li class="<?php echo e(Request::route()->getName() == 'admin.nodes' ? ' active' : ''); ?>"><a class="nav-link"
                    href="<?php echo e(route('admin.nodes')); ?>"><i class="fas fa-robot"></i><span>RPA Nodes</span></a></li>
            <li class="<?php echo e(Request::route()->getName() == 'admin.process' ? ' active' : ''); ?>"><a class="nav-link"
                    href="<?php echo e(route('admin.process')); ?>"><i class="fas fa-tasks"></i><span>Process</span></a></li>
            </ul>
        </li>
        <?php endif; ?>
        <li class="menu-header">Apps</li>
        <li class="dropdown <?php echo e(Request::route()->getName() == 'admin.process.show' ? ' active' : ''); ?>">
            <a href="#" class="nav-link has-dropdown"><i class="fas fa-cogs"></i><span>Applications</span></a>
            <ul class="dropdown-menu">
                <li class="<?php echo e(Request::route()->getName() == 'admin.process.show' ? ' active' : ''); ?>"><a class="nav-link"
                href="<?php echo e(route('admin.process.show',[1])); ?>"><i class="fas fa-credit-card"></i><span>HFC RPA Project</span></a></li>
                <!-- <li class=""><a class="nav-link"
                href="<?php echo e(route('admin.process.show',[1])); ?>"><i class="fas fa-piggy-bank"></i><span>Personal Load</span></a></li>
                <li class=""><a class="nav-link"
                href="<?php echo e(route('admin.process.show',[1])); ?>"><i class="fas fa-home"></i><span>Mortgage Loan</span></a></li> -->
            </ul>
        </li>
        
    </ul>
</aside>
<?php /**PATH C:\wamp64\www\rpa-panel\resources\views/admin/partials/sidebar.blade.php ENDPATH**/ ?>