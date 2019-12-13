<?php $__env->startSection('title'); ?>
Create Apps
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<section class="section">
  <div class="section-header">
    <h1>Create Apps</h1>
  </div>
  <div class="section-body">
   <div class="row">
      <div class="col-12">
         <div class="message">
          
         </div>
         <div class="card">
            <div class="card-header">
               <h4>Add a New Apps</h4>
            </div>
            <form id="app_form">
              <?php echo e(csrf_field()); ?>

              <div class="card-body">
                 <div class="form-group row mb-4">
                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Name</label> 
                    <div class="col-sm-12 col-md-7"><input  id="aname" name="aname" type="text" placeholder="Name of the Apps" class="form-control required"></div>
                 </div>
                 <div class="form-group row mb-4">
                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">DB Username</label> 
                    <div class="col-sm-12 col-md-7"><input id="adbusername" name="adbusername" type="text" placeholder="DB Username" class="form-control required"></div>
                 </div>
                 <div class="form-group row mb-4">
                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">DB Password</label> 
                    <div class="col-sm-12 col-md-7"><input id="adbpassword" name="adbpassword" type="password" placeholder="DB Password" class="form-control required"></div>
                 </div>
                 
                 <div class="form-group row mb-4">
                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">DB Host</label> 
                    <div class="col-sm-12 col-md-7"><input id="adbhost" name="adbhost"  type="text" placeholder="DB Host" class="form-control required"></div>
                 </div>
                 <div class="form-group row mb-4">
                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">DB Port</label> 
                    <div class="col-sm-12 col-md-7"><input id="adbport" name="adbport"  type="text" placeholder="DB Port" class="form-control required"></div>
                 </div>
                 <div class="form-group row mb-4">
                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">DB Name</label> 
                    <div class="col-sm-12 col-md-7"><input id="adbname" name="adbname"  type="text" placeholder="DB Name" class="form-control required"></div>
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

  $('#app_form').submit(function(e) {
    e.preventDefault();
    var aname = $('#aname').val();
    var adbusername = $('#adbusername').val();
    var adbpassword = $('#adbpassword').val();
    var adbhost = $('#adbhost').val();
    var adbport = $('#adbport').val();
    var adbname = $('#adbname').val();


    $(".error").remove();
    $(".is-invalid").removeClass('is-invalid');
    if (aname.length < 1) {
      $('#aname').after('<span class="error">This field is required</span>');
      $('#aname').addClass('is-invalid');
    }
    else if (adbusername.length < 1) {
      $('#adbusername').after('<span class="error">This field is required</span>');
      $('#adbusername').addClass('is-invalid');
    }
    else if (adbpassword.length < 1) {
      $('#adbpassword').after('<span class="error">This field is required</span>');
      $('#adbpassword').addClass('is-invalid');
    }
    else if (adbhost.length < 1) {
      $('#adbhost').after('<span class="error">This field is required</span>');
      $('#adbhost').addClass('is-invalid');
    }
    else if (adbport.length < 1) {
      $('#adbport').after('<span class="error">This field is required</span>');
      $('#adbport').addClass('is-invalid');
    }
    else if (adbname.length < 1) {
      $('#adbname').after('<span class="error">This field is required</span>');
      $('#adbname').addClass('is-invalid');
    }else{
      $.ajax({
          data: $('#app_form').serialize(),
          url: "<?php echo e(route('admin.addapps')); ?>",
          type: "POST",
          dataType: 'json',
          success: function (data) {      
                        console.log(data);
                        $('#app_form').trigger("reset");
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

<?php echo $__env->make('layouts.admin-master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\rpa-panel\resources\views/admin/apps/create.blade.php ENDPATH**/ ?>