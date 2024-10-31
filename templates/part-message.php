<?php $msg = !empty($_GET['msg']) ? urldecode($_GET['msg']) : false ?>
<?php if (!empty($msg)): ?>


    <?php if (!empty($_GET['status']) && $_GET['status'] == 'success'): ?>
        <div class="notice notice-success"><p><?php echo $msg ?></p></div>
    <?php endif; ?>


    <?php if (!empty($_GET['status']) && $_GET['status'] == 'info'): ?>
        <div class="notice notice-info"><p><?php echo $msg ?></p></div>
    <?php endif; ?>


    <?php if (!empty($_GET['status']) && $_GET['status'] == 'warning'): ?>
        <div class="notice notice-warning"><p><?php echo $msg ?></p></div>
    <?php endif; ?>


    <?php if (!empty($_GET['status']) && $_GET['status'] == 'error'): ?>
        <div class="notice notice-error"><p><?php echo $msg ?></p></div>
    <?php endif; ?>

<?php endif; ?>


<?php
global $ops;
$settings = $ops->get_settings();
?>
<?php if (empty($settings['core_email'])): ?>
    <div class="ops-warning-message">
        Please, set up the plugin first (google preferences, email address). Go to <a href="<?php echo admin_url() ?>admin.php?page=ops_settings">Settings</a>.
    </div>
<?php endif; ?>

