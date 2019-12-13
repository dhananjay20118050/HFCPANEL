<?php $__env->startSection('title'); ?>
Create User
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<section class="section">
  <div class="section-header">
    <h1>Add User</h1>
  </div>
  <div class="section-body">
   <div class="row">
      <div class="col-12">
         <div class="message">
          
         </div>
         <div class="card">
            <div class="card-header">
               <h4>Add a New User</h4>
            </div>
            <form id="user_form">
              <?php echo e(csrf_field()); ?>

              <div class="card-body">
                 <div class="form-group row mb-4">
                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Name</label> 
                    <div class="col-sm-12 col-md-7"><input  id="uname" name="uname" type="text" placeholder="Full name of the user." class="form-control required"></div>
                 </div>

                 <div class="form-group row mb-4">
                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Email</label> 
                    <div class="col-sm-12 col-md-7"><input  id="uemail" name="uemail" type="text" placeholder="Email address (should be unique)." class="form-control required"></div>
                 </div>


                 <div class="form-group row mb-4">
                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Password</label> 
                    <div class="col-sm-12 col-md-7"><input  id="upassword" name="upassword" type="password" autocomplete="new-password" placeholder="Set an account password." class="form-control required"></div>
                 </div>


                <div class="form-group row mb-4">
                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Confirm Password</label> 
                    <div class="col-sm-12 col-md-7"><input  id="ucpassword" name="ucpassword" type="password" placeholder="Confirm account password." autocomplete="new-password" class="form-control required"></div>
                 </div>

                
                 <div class="form-group row mb-4">
                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Roles</label> 
                   <div class="col-sm-12 col-md-7">
                       <select class="form-control required" id="uroles" name="uroles">
                        <option value="">Select</option>
                         <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                          <option value="<?php echo e($role->id); ?>"><?php echo e($role->name); ?></option>
                          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                       </select>
                    </div>

                 </div>
                 
                 <div class="form-group row mb-4">
                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label> 
                    <div class="col-sm-12 col-md-7"><button type="submit" class="btn btn-primary"><span>Add</span></button></div>
                 </div>
              </div>
          </form>
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

  $('#user_form').submit(function(e) {
    e.preventDefault();
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
      $.ajax({
          data: $('#user_form').serialize(),
          url: "<?php echo e(route('admin.adduser')); ?>",
          type: "POST",
          dataType: 'json',
          success: function (data) {      
                        console.log(data);
                        $('#user_form').trigger("reset");
                        $('.message').addClass('alert alert-primary');
                        $('.message').html(data.success);
                        $('html, body').animate({
                            scrollTop: $(".alert-primary").offset().top
                        }, 2000);

          },
          error: function (data) {
              console.log('Error:', data);
          }
      });
    }
    
  });

 }); 

</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin-master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\rpa-panel\resources\views/admin/users/create.blade.php ENDPATH**/ ?>