<?php
?>
<div class="wrap">
    <div id="icon-themes" class="icon32"></div>
    <h2>Registracija poslovnega prostora</h2>
    <form method="POST" action="options.php">
        <?php
        settings_fields('edavko_register_business_space_settings');
        do_settings_sections('edavko_register_business_space_settings');
        ?>
        <?php submit_button(); ?>
    </form>
</div>