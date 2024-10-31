<div class="ops-metabox ops-sections">
    <a href="<?php echo get_admin_url() ?>admin.php?page=ops" class="ops-section <?php echo !empty($_GET['page']) && $_GET['page'] == 'ops' ? 'active' : '' ?>">
        <img src="<?php echo plugins_url('off-page-seo/img/icons/chart-line.svg') ?>" alt="icon" class="ops-fa-icon ops-icon-on-left">
        Ranking
    </a>
    <a href="<?php echo get_admin_url() ?>admin.php?page=ops_backlinks" class="ops-section <?php echo !empty($_GET['page']) && $_GET['page'] == 'ops_backlinks' ? 'active' : '' ?>">
        <img src="<?php echo plugins_url('off-page-seo/img/icons/link.svg') ?>" alt="icon" class="ops-fa-icon ops-icon-on-left">
        Backlinks
    </a>
    <a href="<?php echo get_admin_url() ?>admin.php?page=ops_search_console" class="ops-section <?php echo !empty($_GET['page']) && $_GET['page'] == 'ops_search_console' ? 'active' : '' ?>">
        <img src="<?php echo plugins_url('off-page-seo/img/icons/chart-area.svg') ?>" alt="icon" class="ops-fa-icon ops-icon-on-left">
        Search Console
    </a>
    <a href="https://offpageseo.io/" target="_blank" class="ops-section ops-logo">
        <img src="<?php echo plugins_url('off-page-seo/img/logo.png') ?>" alt="ops logo">
    </a>
    <a href="<?php echo get_admin_url() ?>admin.php?page=ops_settings" class="ops-section ops-settings <?php echo !empty($_GET['page']) && $_GET['page'] == 'ops_settings' ? 'active' : '' ?>">
        <img src="<?php echo plugins_url('off-page-seo/img/icons/cog.svg') ?>" alt="icon" class="ops-fa-icon">
    </a>
    <a href="<?php echo get_admin_url() ?>admin.php?page=ops_premium" class="ops-section ops-premium <?php echo !empty($_GET['page']) && $_GET['page'] == 'ops_premium' ? 'active' : '' ?>">
        <img src="<?php echo plugins_url('off-page-seo/img/icons/user-alt.svg') ?>" alt="icon" class="ops-fa-icon">
    </a>
    <a href="<?php echo get_admin_url() ?>admin.php?page=ops_tools" class="ops-section ops-premium <?php echo !empty($_GET['page']) && $_GET['page'] == 'ops_tools' ? 'active' : '' ?>">
        <img src="<?php echo plugins_url('off-page-seo/img/icons/wrench.svg') ?>" alt="icon" class="ops-fa-icon">
    </a>
</div>