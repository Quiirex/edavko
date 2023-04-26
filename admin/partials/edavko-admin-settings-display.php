<?php
?>
<div class="wrap">
    <div id="icon-themes" class="icon32"></div>
    <h2>eDavko nastavitve</h2>
    <div id="edavko-settings-set-result">
        <?php $this->display_settings_set_result_message(); ?>
    </div>
    <?php settings_errors(); ?>
    <form method="POST" action="options.php">
        <?php
        settings_fields('edavko_general_settings');
        do_settings_sections('edavko_general_settings');
        submit_button('Shrani');
        ?>
    </form>
</div>