<?php $__env->startSection('title'); ?>
Manage Process
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<section class="section">
    <div class="section-header">
        <h1>Manage Process Configurations</h1>
        <div class="card-header-action" style="margin-left:auto"><a href="createprocess" id="create-contact" class="btn btn-primary">Add Process <i class="fas fa-plus"></i></a></div>
    </div>
    <div class="section-body">
		<table class="data-table table table-striped dataTable no-footer" id="process-datatable">
			<thead>
				<tr>
					<th>No.</th>
					<th>Process</th>
					<th>Description</th>
					<th>URL</th>
					<th>Username</th>
					<th>Browser</th>
					<th>Download Dirctory</th>
					<th>Actions</th>
				</tr>
			</thead>
		</table>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script type="text/javascript">
	$(function () {

		$.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    	});

		var table = $('#process-datatable').DataTable({
			processing: true,
			serverSide: true,
			ajax: "<?php echo e(route('admin.process')); ?>",
			columns: [
				{data: 'id', name: 'id'},
				{data: 'name', name: 'name'},
				{data: 'description', name: 'description'},
				{data: 'url', name: 'url'},
				{data: 'username', name: 'username'},
				{data: 'browserName', name: 'browserName'},
				{data: 'downloadDir', name: 'downloadDir'},
				{data: 'action', name: 'action', orderable: false, searchable: false},
			],
			columnDefs: [
				{ className: "dt-nowrap", "targets": [ 0,1,2,3,4,5,6,7] }
			]
		});

		$(document).on('click', '.btn-delete', function(e){ 
		        var url = "<?php echo e(route('admin.process.destroy',':id')); ?>";
		        url = url.replace(':id',$(this).attr('data-id')); 
		      swal({
		        title: "Are you sure you want delete this contact?",        
		        icon: "warning",
		        buttons: true,
		        dangerMode: true,
		        buttons: ["No", "Yes"]        
		      })
		      .then((willDelete) => {
		        if (willDelete) {
		          ajaxRequest(              
		              url,
		              'POST',
		              [],
		              function(response){               
		                var mydatatable = $("#process-datatable").DataTable() ;
		                mydatatable.ajax.reload(null, false );
		              });
		        } 
		      });
		  });

  	});

  	function ajaxRequest(url, type, data, successFunction){
    $.ajax({
        url: url,
        method: type,
        data: data,
        success: successFunction
      });
 	}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin-master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\rpa-panel\resources\views/admin/process/index.blade.php ENDPATH**/ ?>