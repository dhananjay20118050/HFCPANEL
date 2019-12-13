function BaseUrl(path = '') {
    return '<?php echo url('/'); ?>/' + path;
}

const AuthUser = <?php echo Auth::check() ? Auth::user()->toJson() : 'false'; ?>;
<?php /**PATH C:\xampp\htdocs\rpa-panel\resources\views/admin/js/dynamic.blade.php ENDPATH**/ ?>