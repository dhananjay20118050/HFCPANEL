<?php $__env->startSection('title'); ?>
Edit Process
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<section class="section">
  <div class="section-header">
    <h1>Edit Process</h1>
  </div>
  <div class="section-body">
   <div class="row">
      <div class="col-12">
         <!----> 
         <div class="message">
          
         </div>
         <div class="card">
            <div class="card-header">
               <h4>Edit a Process</h4>
            </div>
            <?php if($processdata->exists): ?>
            <form id="process_edit_form">
                <?php echo csrf_field(); ?>
                <?php echo method_field('POST'); ?>
                <input id="pid" name="pid" type="hidden" value="<?php echo e($processdata->id); ?>">
                <div class="card-body">
                   <div class="form-group row mb-4">
                      <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Name</label> 
                      <div class="col-sm-12 col-md-7"><input  id="pname" name="pname" type="text" placeholder="Name of the Node" class="form-control required" value="<?php echo e($processdata->name); ?>"></div>
                   </div>
                   <div class="form-group row mb-4">
                      <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Description</label> 
                       <div class="col-sm-12 col-md-7">
                         <textarea name="pdesc" id="pdesc"  type="text" placeholder="Description" class="form-control required"><?php echo e($processdata->description); ?></textarea>
                        </div>
                   </div>
                   <div class="form-group row mb-4">
                      <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">URL</label> 
                      <div class="col-sm-12 col-md-7"><input id="purl" name="purl" type="text" placeholder="URL" value="<?php echo e($processdata->url); ?>" class="form-control required"></div>
                   </div>
                   
                   <div class="form-group row mb-4">
                      <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Username</label> 
                      <div class="col-sm-12 col-md-7"><input id="pusername" name="pusername"  type="text" placeholder="Username" value="<?php echo e($processdata->username); ?>" class="form-control required"></div>
                   </div>
                   <div class="form-group row mb-4">
                      <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Password</label> 
                      <div class="col-sm-12 col-md-7"><input id="ppass" name="ppass"  type="password" placeholder="Password" value="<?php echo e($processdata->password); ?>" class="form-control required"></div>
                   </div>

                   <div class="form-group row mb-4">
                      <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Browser ID</label> 
                      <div class="col-sm-12 col-md-7"><input id="pbrowserid" name="pbrowserid"  type="text" placeholder="Browser ID" value="<?php echo e($processdata->browserId); ?>" class="form-control required"></div>
                   </div>

                   <div class="form-group row mb-4">
                      <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Download Dir</label> 
                      <div class="col-sm-12 col-md-7"><input id="pdownloaddir" name="pdownloaddir"  type="text" placeholder="Download Dir" value="<?php echo e($processdata->downloadDir); ?>" class="form-control required"></div>
                   </div>
                   <div class="form-group row mb-4">
                      <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">App ID</label> 
                      <div class="col-sm-12 col-md-7"><input id="pappid" name="pappid"  type="text" placeholder="IP Address" value="<?php echo e($processdata->appId); ?>" class="form-control required"></div>
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
<script type="text/javascript">
$(document).ready(function() {

  $('#process_edit_form').submit(function(e) {
    e.preventDefault();
    var id = $('#pid').val();
    var pname = $('#pname').val();
    var pdesc = $('#pdesc').val();
    var purl = $('#purl').val();
    var pusername = $('#pusername').val();
    var ppass = $('#ppass').val();
    var pbrowserid = $('#pbrowserid').val();
    var pdownloaddir = $('#pdownloaddir').val();
    var pappid = $('#pappid').val();

    $(".error").remove();
    $(".is-invalid").removeClass('is-invalid');
    if (pname.length < 1) {
      $('#pname').after('<span class="error">This field is required</span>');
      $('#pname').addClass('is-invalid');
    }
    else if (pdesc.length < 1) {
      $('#pdesc').after('<span class="error">This field is required</span>');
      $('#pdesc').addClass('is-invalid');
    }
    else if (purl.length < 1) {
      $('#purl').after('<span class="error">This field is required</span>');
      $('#purl').addClass('is-invalid');
    }
    else if (pusername.length < 1) {
      $('#pusername').after('<span class="error">This field is required</span>');
      $('#pusername').addClass('is-invalid');
    }
    /*else if (ppass.length < 1) {
      $('#ppass').after('<span class="error">This field is required</span>');
      $('#ppass').addClass('is-invalid');
    }*/
    /*else if (pbrowserid.length < 1) {
      $('#pbrowserid').after('<span class="error">This field is required</span>');
      $('#pbrowserid').addClass('is-invalid');
    }*/
    /* else if (pdownloaddir.length < 1) {
      $('#pdownloaddir').after('<span class="error">This field is required</span>');
      $('#pdownloaddir').addClass('is-invalid');
    }*/
     else if (pappid.length < 1) {
      $('#pappid').after('<span class="error">This field is required</span>');
      $('#pappid').addClass('is-invalid');
    }
    else{
        var url = "<?php echo e(route('admin.updateprocess', ':id')); ?>";
        url = url.replace(':id', id);
         $.ajax({
            data: $('#process_edit_form').serialize(),
            url: url,
            type: "POST",
            dataType: 'json',
            success: function (data) {      
                $('.message').addClass('alert alert-primary');
                $('.message').html(data.success);
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


<?php echo $__env->make('layouts.admin-master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\rpa-panel\resources\views/admin/process/edit.blade.php ENDPATH**/ ?>