<?php $__env->startSection('content'); ?>
<div class="card card-primary">
  <div class="card-header"><h4>Login</h4></div>

  <div class="card-body">
    <form method="POST" action="<?php echo e(route('login')); ?>">
        <?php echo csrf_field(); ?>
      <div class="form-group">
        <label for="email">Email</label>
        <input aria-describedby="emailHelpBlock" id="email" type="email" class="form-control<?php echo e($errors->has('email') ? ' is-invalid' : ''); ?>" name="email" placeholder="Registered email address" tabindex="1" value="<?php echo e(old('email')); ?>" autofocus>
        <div class="invalid-feedback">
          <?php echo e($errors->first('email')); ?>

        </div>
        <?php if(App::environment('demo')): ?>
        <small id="emailHelpBlock" class="form-text text-muted">
            Demo Email: admin@example.com
        </small>
        <?php endif; ?>
      </div>

      <div class="form-group">
        <div class="d-block">
            <label for="password" class="control-label">Password</label>
          <div class="float-right">
            <a href="<?php echo e(route('password.request')); ?>" class="text-small">
              Forgot Password?
            </a>
          </div>
        </div>
        <input aria-describedby="passwordHelpBlock" id="password" type="password" placeholder="Your account password" class="form-control<?php echo e($errors->has('password') ? ' is-invalid': ''); ?>" name="password" tabindex="2">
        <div class="invalid-feedback">
          <?php echo e($errors->first('password')); ?>

        </div>
        <?php if(App::environment('demo')): ?>
        <small id="passwordHelpBlock" class="form-text text-muted">
            Demo Password: 1234
        </small>
        <?php endif; ?>
      </div>

      <div class="form-group">
        <div class="custom-control custom-checkbox">
          <input type="checkbox" name="remember" class="custom-control-input" tabindex="3" id="remember"<?php echo e(old('remember') ? ' checked': ''); ?>>
          <label class="custom-control-label" for="remember">Remember Me</label>
        </div>
      </div>

      <div class="form-group">
        <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
          Login
        </button>
      </div>
    </form>
  </div>
</div>
<!-- <div class="mt-5 text-muted text-center">
  Don't have an account? <a href="<?php echo e(route('register')); ?>">Create One</a>
</div> -->
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.auth-master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\rpa-panel\resources\views/auth/login.blade.php ENDPATH**/ ?>