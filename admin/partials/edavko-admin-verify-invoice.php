<?php
?>
<div class="wrap">
    <div id="icon-themes" class="icon32"></div>
    <h2>Preverjanje računa</h2>
    <?php settings_errors(); ?>
    <form method="POST" id="edavko-verify-invoice-form">
        <?php
        settings_fields('edavko_verify_invoice_settings');
        do_settings_sections('edavko_verify_invoice_settings');
        wp_nonce_field('edavko_verify_invoice_nonce_action', 'edavko_verify_invoice_nonce');
        submit_button('Preveri', 'primary', 'edavko-verify-invoice-submit', false, array('id' => 'edavko-verify-invoice-submit'));
        ?>
    </form>
    <div id="edavko-verify-invoice-result">
        <?php $this->display_result_message(); ?>
    </div>
</div>