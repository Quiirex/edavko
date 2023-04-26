<?php
?>
<div class="wrap">
    <div id="icon-themes" class="icon32"></div>
    <h2>eDavko nastavitve</h2>
    <?php settings_errors(); ?>
    <form method="POST" action="options.php">
        <?php
        settings_fields('edavko_general_settings');
        do_settings_sections('edavko_general_settings');
        ?>
        <?php submit_button(); ?>
    </form>
</div>