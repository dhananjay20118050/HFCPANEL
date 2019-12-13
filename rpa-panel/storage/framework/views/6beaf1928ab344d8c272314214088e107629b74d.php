<link rel="stylesheet" href="<?php echo e(asset('assets/css/compiled-4.8.11.min.css')); ?>">



<?php $__env->startSection('title'); ?>
Edit User
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<section class="section">
  <div class="section-header">
    <h1>Edit User</h1>
  </div>
  <div class="section-body">
   <div class="row">
      <div class="col-12">
         <!----> 
         <div class="message">
          
         </div>
         <div class="card">
            <div class="card-header">
               <h4>Edit User</h4>
            </div>
            <?php if($users->exists): ?>
            <form id="user_edit_form">
            	<?php echo csrf_field(); ?>
                <?php echo method_field('POST'); ?>
              <input id="uid" name="uid" type="hidden" value="<?php echo e($users->id); ?>">
	            <div class="card-body">
	               <div class="form-group row mb-4">
	                  <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Name</label> 
	                  <div class="col-sm-12 col-md-7"><input  id="uname" name="uname" type="text" class="form-control required" value="<?php echo e($users->name); ?>"></div>
	               </div>

                 <div class="form-group row mb-4">
                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Email</label> 
                    <div class="col-sm-12 col-md-7">
                      <input  id="uemail" name="uemail" type="text" placeholder="Email address (should be unique)." class="form-control required" value="<?php echo e($users->email); ?>"></div>
                 </div>


                 <div class="form-group row mb-4">
                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Password</label> 
                    <div class="col-sm-12 col-md-7"><input  id="upassword" name="upassword" type="password" class="form-control required" value="<?php echo e($users->password); ?>"></div>
                 </div>


                <div class="form-group row mb-4">
                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Confirm Password</label> 
                    <div class="col-sm-12 col-md-7"><input  id="ucpassword" name="ucpassword" type="password" class="form-control required" value="<?php echo e($users->password); ?>"></div>
                 </div>

                 <div class="form-group row mb-4">
                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Roles</label> 
                   <div class="col-sm-12 col-md-7">
                       <select class="form-control required" id="uroles" name="uroles">
                        <!-- <option value="<?php echo e($users->role_name); ?>" selected="selected"><?php echo e($users->role_name); ?></option> -->
                        <!--  <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                          <option value="<?php echo e($role->name); ?>"><?php echo e($role->name); ?></option>
                          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> -->
                        <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($users->role_name == $role->name): ?>
                        <option value="<?php echo e($users->role_name); ?>" selected="selected"><?php echo e($users->role_name); ?></option>
                        <?php else: ?>
                        <option value="<?php echo e($role->name); ?>"><?php echo e($role->name); ?></option>
                        <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                       </select>
                    </div>

                 </div>
                <div class="form-group row mb-4">
                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Assign Projects</label> 
                   <div class="col-sm-12 col-md-7">
                       <select class="mdb-select md-form" multiple searchable="Search here.." id="uprojects" name="uprojects">
                        <option value="" disabled selected>Choose Your Projects</option>
                         <?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>  
                         <option value="<?php echo e($project->id); ?>"><?php echo e($project->name); ?></option> 
                          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                 </div>
                 
             
	               <div class="form-group row mb-4">
	                  <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label> 
	                  <div class="col-sm-12 col-md-7"><button type="submit" class="btn btn-primary"><span>Update</span></button></div>
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
<script src="<?php echo e(asset('assets/js/compiled-4.8.11.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/modules/nicescroll/jquery.nicescroll.min.js')); ?>"></script>
<script type="text/javascript">

$(document).ready(function() {
//user_edit_form
 $('.mdb-select').materialSelect();
 $('#user_edit_form').submit(function(e) {
    e.preventDefault();
    var id = $('#uid').val();
    var uname = $('#uname').val();
    var uemail = $('#uemail').val();
    var upassword = $('#upassword').val();
    var ucpassword = $('#ucpassword').val();

    $(".error").remove();
    $(".is-invalid").removeClass('is-invalid');
    if (uname.length < 1) {
      $('#uname').after('<span class="error">This field is required</span>');
      $('#uname').addClass('is-invalid');
    }
    else if (uemail.length < 1) {
      $('#uemail').after('<span class="error">This field is required</span>');
      $('#uemail').addClass('is-invalid');
    }
    else if (upassword.length < 1) {
      $('#upassword').after('<span class="error">This field is required</span>');
      $('#upassword').addClass('is-invalid');
    }
    else if (ucpassword.length < 1) {
      $('#ucpassword').after('<span class="error">This field is required</span>');
      $('#ucpassword').addClass('is-invalid');
    }
   else{

      var textInput = [];
      textInput = $(".select-dropdown").val();
      //console.log(textInput);

     var url = "<?php echo e(route('admin.updateuser', ':id')); ?>";
     var data = $('#user_edit_form').serializeArray();
     data.push({name: 'uprojects', value:textInput});
      url = url.replace(':id', id);
       $.ajax({
      data: data,
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
    
  });

});
</script>
<?php $__env->stopSection(); ?>

<style type="text/css">.hide{display: none;}</style>
<?php echo $__env->make('layouts.admin-master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\rpa-panel\resources\views/admin/users/edit.blade.php ENDPATH**/ ?>