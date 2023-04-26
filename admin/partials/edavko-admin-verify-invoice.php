<?php
?>
<div class="wrap">
    <div id="icon-themes" class="icon32"></div>
    <h2>Preverjanje računa</h2>
    <?php settings_errors(); ?>
    <form method="POST" action="options.php">
        <?php
        settings_fields('edavko_verify_invoice_settings');
        do_settings_sections('edavko_verify_invoice_settings');
        ?>
        <?php submit_button(); ?>
    </form>
</div>