<?php $__env->startSection('title'); ?>
Dashboard
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<section class="section">
    <div class="section-header">
        <h1>Credit Card Process</h1>
    </div>

    <div class="section-body">

        <h2 class="section-title">Credit Card Process</h2>
        <p class="section-lead">
            Automation Process for Credit Card Application
        </p>
		<div class="row">
		    <div class="col-12 col-md-6 col-lg-6">
		        <div class="card card-primary">
		            <div class="card-header">
		                <h4>IDisburse Image Downloading</h4>
		                <div class="card-header-action">
							<!-- <input type="file" name="input-file" id="input-file" class="btn btn-primary"> -->
							<form id="ccAutoStart1" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
		                    <a href="#" class="btn btn-info" id="ccAutoUpload1">
		                        Upload File
		                    </a>
							<input type="file" name="input-file" id="input-file" style="display:none"/>
							<button type="submit" class="btn btn-primary btn-icon icon-left" id="upload">
								<i class="fas fa-play"></i> Start <span class="badge badge-transparent">1</span>
							</button>
							</form>
		                </div>
		            </div>
		            <div class="card-body">
		                <p>This automation will download application form images from idisburse application.</p>
		            </div>
					<div class="card-footer">
                        Active Server: 10.1.29.233
                    </div>
		        </div>
		    </div>

		    <div class="col-12 col-md-6 col-lg-6">
				<div class="card card-primary">
		            <div class="card-header">
		                <h4>GEMS Upload</h4>
		                <div class="card-header-action">
		                    <a href="#" class="btn btn-primary">
		                        View All
		                    </a>
							<button type="button" class="btn btn-primary btn-icon icon-left">
								<i class="fas fa-play"></i> Start <span class="badge badge-transparent">1</span>
							</button>
		                </div>
		            </div>
		            <div class="card-body">
		                <p>This automation will generate output files and auto uploads to the ICICI GEMS Application. Concern person will get notified via Emails.</p>
		            </div>
					<div class="card-footer">
                        Active Server: 10.1.29.233
                    </div>
		        </div>
			</div>
		</div>

        
	</div>
</section>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script type="text/javascript">

	$('#ccAutoUpload1').click(function () {
		$('#input-file').trigger('click');
	});

	$('#ccAutoStart1').on("submit", function(e){
		e.preventDefault();
		$("#input-file").addClass("disabled");
		$("#upload").addClass("disabled");

		$.ajax({
			url:'<?php echo e(url('/api/process/cc/downloadImages')); ?>',
			method:"POST",  
			data:new FormData(this),
			contentType:false,
			cache:false,
			processData:false,
			success: function(data){
				if(data == 2){
				toastr["error"]("Invalid File.");
				}
				else if(data == 3){
				toastr["error"]("Please Select File.");
				}
				else{
					swal('Done', 'Automation completed successfully!', 'success');
				}
				$("#input-file").removeClass("disabled");
				$("#upload").removeClass("disabled");
				$("#input-file").val("");
			}
		})
	});

</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin-master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\rpa-panel\resources\views/admin/process/cc/index.blade.php ENDPATH**/ ?>