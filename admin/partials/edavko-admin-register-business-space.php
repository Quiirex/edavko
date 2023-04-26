<?php
?>
<div class="wrap">
    <div id="icon-themes" class="icon32"></div>
    <h2>Registracija poslovnega prostora</h2>
    <form method="POST" id="edavko-register-business-space-form">
        <?php
        settings_fields('edavko_register_business_space_settings');
        do_settings_sections('edavko_register_business_space_settings');
        submit_button('Registriraj', 'primary', 'edavko-register-business-space-submit', false, array('id' => 'edavko-register-business-space-submit'));
        ?>
    </form>
    <div id="edavko-register-business-space-result"></div>
</div>