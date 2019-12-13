<?php $__env->startSection('title'); ?>
Dashboard
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<section class="section">
    <div class="section-header">
        <h1>Dashboard</h1>
    </div>
    <div class="section-body">
        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-primary">
                        <i class="fab fa-hubspot"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Total Servers</h4>
                        </div>
                        <div class="card-body">
                            2
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-danger">
                        <i class="fab fa-hubspot"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Total Nodes</h4>
                        </div>
                        <div class="card-body">
                            4
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-warning">
                        <i class="fas fa-robot"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Bot Finished</h4>
                        </div>
                        <div class="card-body">
                            47
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-success">
                        <i class="fas fa-robot"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Online Bots</h4>
                        </div>
                        <div class="card-body">
                            1
                        </div>
                    </div>
                </div>
            </div>
        </div>
		<div class="row">
			<div class="card">
				<div class="card-header">
					<h4>Popular Browser</h4>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col text-center">
							<div class="browser browser-chrome"></div>
							<div class="mt-2 font-weight-bold">Chrome</div>
							<div class="text-muted text-small"><span class="text-primary"><i
										class="fas fa-caret-up"></i></span> 81%</div>
						</div>
						<div class="col text-center">
							<div class="browser browser-firefox"></div>
							<div class="mt-2 font-weight-bold">Firefox</div>
							<div class="text-muted text-small"><span class="text-danger"><i
										class="fas fa-caret-down"></i></span> 0%</div>
						</div>
						<div class="col text-center">
							<div class="browser browser-safari"></div>
							<div class="mt-2 font-weight-bold">Safari</div>
							<div class="text-muted text-small"><span class="text-danger"><i
										class="fas fa-caret-down"></i></span> 0%</div>
						</div>
						<div class="col text-center">
							<div class="browser browser-opera"></div>
							<div class="mt-2 font-weight-bold">Opera</div>
							<div class="text-muted text-small"><span class="text-danger"><i
										class="fas fa-caret-down"></i></span> 0%</div>
						</div>
						<div class="col text-center">
							<div class="browser browser-internet-explorer"></div>
							<div class="mt-2 font-weight-bold">IE</div>
							<div class="text-muted text-small"><span class="text-primary"><i
										class="fas fa-caret-up"></i></span> 19%</div>
						</div>
					</div>
				</div>
			</div>
		</div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin-master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\rpa-panel\resources\views/admin/dashboard/index.blade.php ENDPATH**/ ?>