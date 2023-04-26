<?php
?>
<div class="wrap">
    <div id="icon-themes" class="icon32"></div>
    <h2>Preverjanje poslovnega prostora</h2>
    <?php settings_errors(); ?>
    <form method="POST" id="edavko-verify-business-space-form">
        <?php
        settings_fields('edavko_verify_business_space_settings');
        do_settings_sections('edavko_verify_business_space_settings');
        wp_nonce_field('edavko_verify_business_space_nonce_action', 'edavko_verify_business_space_nonce');
        submit_button('Preveri', 'primary', 'edavko-verify-business-space-submit', false, array('id' => 'edavko-verify-business-space-submit'));
        ?>
    </form>
    <div id="edavko-verify-business-space-result">
        <?php $this->display_business_space_verification_result_message(); ?>
    </div>
</div>