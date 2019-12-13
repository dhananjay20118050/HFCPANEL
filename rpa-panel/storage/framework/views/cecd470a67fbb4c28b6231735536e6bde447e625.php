<?php $__env->startSection('title'); ?>
Manage Users
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<section class="section">
    <div class="section-header">
        <h1>Manage Users</h1><div class="card-header-action" style="margin-left:auto"><a href="createuser" id="create-user" class="btn btn-primary">Add <i class="fas fa-plus"></i></a></div>
    </div>
    <div class="section-body">
		<table class="data-table table table-striped dataTable no-footer" id="user-datatable">
			<thead>
				<tr>
					
					<th>Name</th>
					<th>Email</th>
					<th>Reg. Date</th>
					<th>Roles</th>
					<th></th>
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
    
    var table = $('#user-datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "<?php echo e(route('admin.users')); ?>",        
        columns: [
            {data: 'name', name: 'name'},
            {data: 'email', name: 'email'},
            {data: 'created_at', name: 'created_at'},
            {data: 'role_name', name: 'role_name'},             
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
    });
    
  

$(document).on('click', '.btn-delete', function(e){ 
        var url = "<?php echo e(route('admin.users.destroy',':id')); ?>";
        url = url.replace(':id',$(this).attr('data-id')); 
        //alert(url);
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
                var mydatatable = $("#user-datatable").DataTable() ;
                mydatatable.ajax.reload(null, false );
              });
        } 
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
  
  });
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin-master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\rpa-panel\resources\views/admin/users/index.blade.php ENDPATH**/ ?>