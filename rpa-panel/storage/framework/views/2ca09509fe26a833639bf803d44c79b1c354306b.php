<?php $__env->startSection('title'); ?>
Create Node
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<section class="section">
  <div class="section-header">
    <h1>Add Node</h1>
  </div>
  <div class="section-body">
   <div class="row">
      <div class="col-12">
         <div class="message">
          
         </div>
         <div class="card">
            <div class="card-header">
               <h4>Add a New Node</h4>
            </div>
            <form id="node_form">
              <?php echo e(csrf_field()); ?>

              <div class="card-body">
                 <div class="form-group row mb-4">
                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Name</label> 
                    <div class="col-sm-12 col-md-7"><input  id="nname" name="nname" type="text" placeholder="Name of the Node" class="form-control required"></div>
                 </div>
                 <div class="form-group row mb-4">
                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">process</label> 
                     <div class="col-sm-12 col-md-7">
                       <select class="form-control required" id="nprocess" name="nprocess">
                        <option value="">Select</option>
                          <option value="1">RPA-CC-01</option>
                          <option value="2">RPA-PL-02</option>
                       </select>
                      </div>
                 </div>
                 <div class="form-group row mb-4">
                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Port</label> 
                    <div class="col-sm-12 col-md-7"><input id="nport" name="nport" type="text" placeholder="Port Number" class="form-control required"></div>
                 </div>
                 
                 <div class="form-group row mb-4">
                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">IP</label> 
                    <div class="col-sm-12 col-md-7"><input id="nip" name="nip"  type="text" placeholder="IP Address" class="form-control required"></div>
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

  $('#node_form').submit(function(e) {
    e.preventDefault();
    var nname = $('#nname').val();
    var nprocess = $('#nprocess').val();
    var nip = $('#nip').val();
    var nport = $('#nport').val();

    $(".error").remove();
    $(".is-invalid").removeClass('is-invalid');
    if (nname.length < 1) {
      $('#nname').after('<span class="error">This field is required</span>');
      $('#nname').addClass('is-invalid');
    }
    else if (nprocess.length < 1) {
      $('#nprocess').after('<span class="error">This field is required</span>');
      $('#nprocess').addClass('is-invalid');
    }
    else if (nport.length < 1) {
      $('#nport').after('<span class="error">This field is required</span>');
      $('#nport').addClass('is-invalid');
    }
    else if (nip.length < 1) {
      $('#nip').after('<span class="error">This field is required</span>');
      $('#nip').addClass('is-invalid');
    }else{
      $.ajax({
          data: $('#node_form').serialize(),
          url: "<?php echo e(route('admin.addnode')); ?>",
          type: "POST",
          dataType: 'json',
          success: function (data) {      
                        console.log(data);
                        $('#node_form').trigger("reset");
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

<?php echo $__env->make('layouts.admin-master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\rpa-panel\resources\views/admin/nodes/create.blade.php ENDPATH**/ ?>