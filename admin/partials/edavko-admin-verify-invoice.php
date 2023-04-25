<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       edavko.com/team
 * @since      1.0.0
 *
 * @package    eDavko
 * @subpackage eDavko/admin/partials
 */
?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->
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