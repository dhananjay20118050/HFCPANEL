<?php $__env->startSection('title'); ?>
Create Server
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<section class="section">
  <div class="section-header">
    <h1>Edit Server</h1>
  </div>
  <div class="section-body">
   <div class="row">
      <div class="col-12">
         <!----> 
         <div class="message">
          
         </div>
         <div class="card">
            <div class="card-header">
               <h4>Edit a Server</h4>
            </div>
            <?php if($hub->exists): ?>
            <form id="server_edit_form">
            	<?php echo csrf_field(); ?>
                <?php echo method_field('POST'); ?>
                <input id="sid" name="sid" type="hidden" value="<?php echo e($hub->id); ?>">
	            <div class="card-body">
	               <div class="form-group row mb-4">
	                  <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Name</label> 
	                  <div class="col-sm-12 col-md-7"><input  id="sname" name="sname" type="text" placeholder="Name of the Server" class="form-control required" value="<?php echo e($hub->name); ?>"></div>
	               </div>
	               <div class="form-group row mb-4">
	                  <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">process</label> 
	                   <div class="col-sm-12 col-md-7">
	                     <select class="form-control required" id="sprocess" name="sprocess">
	                     	<?php $__currentLoopData = $process; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
	                     	<?php if($value->id == $hub->process_id): ?>
		                    <option value="<?php echo e($value->id); ?>" selected="selected"><?php echo e($value->name); ?></option>
		                    <?php else: ?>
		                    <option value="<?php echo e($value->id); ?>"><?php echo e($value->name); ?></option>
		                    <?php endif; ?>
		                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
		                    
	                     </select>
	                  	</div>
	               </div>
	               <div class="form-group row mb-4">
	                  <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Port</label> 
	                  <div class="col-sm-12 col-md-7"><input id="sport" name="sport" type="text" placeholder="Port Number" value="<?php echo e($hub->port); ?>" class="form-control required"></div>
	               </div>
	               
	               <div class="form-group row mb-4">
	                  <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">IP</label> 
	                  <div class="col-sm-12 col-md-7"><input id="sip" name="sip"  type="text" placeholder="IP Address" value="<?php echo e($hub->ip); ?>" class="form-control required"></div>
	               </div>
	               <div class="form-group row mb-4">
	                  <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label> 
	                  <div class="col-sm-12 col-md-7"><button type="submit" data-id="<?php echo e($hub->id); ?>" class="btn btn-primary"><span>Update</span></button></div>
	               </div>
	            </div>
        	</form>
        	<?php endif; ?>
         </div>
      </div>
   </div>
</div>
</section>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {

  $('#server_edit_form').submit(function(e) {
    e.preventDefault();
    var id = $('#sid').val();
   
    var sname = $('#sname').val();
    var sprocess = $('#sprocess').val();
    var sip = $('#sip').val();
    var sport = $('#sport').val();

    $(".error").remove();
	$(".is-invalid").removeClass('is-invalid');
    if (sname.length < 1) {
      $('#sname').after('<span class="error">This field is required</span>');
      $('#sname').addClass('is-invalid');
    }
    else if (sprocess.length < 1) {
      $('#sprocess').after('<span class="error">This field is required</span>');
      $('#sprocess').addClass('is-invalid');
    }
    else if (sport.length < 1) {
      $('#sport').after('<span class="error">This field is required</span>');
      $('#sport').addClass('is-invalid');
    }
    else if (sip.length < 1) {
      $('#sip').after('<span class="error">This field is required</span>');
      $('#sip').addClass('is-invalid');
    }else{
		var url = "<?php echo e(route('admin.update', ':id')); ?>";
    	url = url.replace(':id', id);
    	 $.ajax({
			data: $('#server_edit_form').serialize(),
			url: url,
			type: "POST",
			dataType: 'json',
			success: function (data) {      
			    $('.message').addClass('alert alert-primary');
			    $('.message').html(data.success);
          $('html, body').animate({
              scrollTop: $(".alert-primary").offset().top
          }, 2000);
			   },
			error: function (data) {
				console.log('Error:', data);
			}
      })
    }
    
  })

});
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin-master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\rpa-panel\resources\views/admin/hubs/edit.blade.php ENDPATH**/ ?>