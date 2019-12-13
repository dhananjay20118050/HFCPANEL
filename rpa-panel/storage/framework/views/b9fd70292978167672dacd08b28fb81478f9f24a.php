<?php $__env->startSection('content'); ?>
<div class="card card-primary">
  <div class="card-header"><h4>Reset Password</h4></div>

  <div class="card-body">
    <form method="POST" action="<?php echo e(route('password.email')); ?>">
        <?php echo csrf_field(); ?>
      <div class="form-group">
        <label for="email">Email</label>
        <input id="email" type="email" class="form-control<?php echo e($errors->has('email') ? ' is-invalid' : ''); ?>" name="email" tabindex="1" value="<?php echo e(old('email')); ?>" autofocus>
        <div class="invalid-feedback">
          <?php echo e($errors->first('email')); ?>

        </div>
      </div>
      <div class="form-group">
        <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
          Send Reset Link
        </button>
      </div>
    </form>
  </div>
</div>
<div class="mt-5 text-muted text-center">
  Recalled your login info? <a href="<?php echo e(route('login')); ?>">Sign In</a>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.auth-master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\rpa-panel\resources\views/auth/passwords/email.blade.php ENDPATH**/ ?>