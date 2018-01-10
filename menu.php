<?php
/**
 * Tweeter options menu
 */
function tweeter_add_admin_menu() {
    add_submenu_page(
        'options-general.php',
        'Tweeter',
        'Tweeter',
        'manage_options',
        'tweeter',
        'tweeter_options_page'
    );
}
add_action('admin_menu', 'tweeter_add_admin_menu');

function tweeter_settings_init() {
    // PAGE
    register_setting('tweeter_options_page', 'tweeter_settings');

    // ACCOUNT OPTIONS
    add_settings_section(
        'tweeter_account_section',
        'Tweeter account',
        'tweeter_account_settings_section_callback',
        'tweeter_options_page'
    );
    add_settings_field(
        'tweeter_oauth_access_token',
        'OAuth access token',
        'tweeter_oauth_access_token_render',
        'tweeter_options_page',
        'tweeter_account_section'
    );
    add_settings_field(
        'tweeter_oauth_access_token_secret',
        'OAuth access token secret',
        'tweeter_oauth_access_token_secret_render',
        'tweeter_options_page',
        'tweeter_account_section'
    );
    add_settings_field(
        'tweeter_consumer_key',
        'Consumer key',
        'tweeter_consumer_key_render',
        'tweeter_options_page',
        'tweeter_account_section'
    );
    add_settings_field(
        'tweeter_consumer_secret',
        'Consumer secret',
        'tweeter_consumer_secret_render',
        'tweeter_options_page',
        'tweeter_account_section'
    );

    // MESSAGE OPTIONS
    add_settings_section(
        'tweeter_message_section',
        'Tweeter message',
        'tweeter_message_settings_section_callback',
        'tweeter_options_page'
    );
    add_settings_field(
        'tweeter_message_format',
        'Message format',
        'tweeter_message_format_render',
        'tweeter_options_page',
        'tweeter_message_section'
    );
    add_settings_field(
        'tweeter_message_variables',
        'Message variables',
        'tweeter_message_variables_render',
        'tweeter_options_page',
        'tweeter_message_section'
    );
}
add_action('admin_init', 'tweeter_settings_init');

function tweeter_oauth_access_token_render() {
    $options = get_option('tweeter_settings');
    ?>
    <input type='text' name='tweeter_settings[tweeter_oauth_access_token]' value='<?php echo $options['tweeter_oauth_access_token']; ?>'>
    <?php
}
function tweeter_oauth_access_token_secret_render() {
    $options = get_option('tweeter_settings');
    ?>
    <input type='text' name='tweeter_settings[tweeter_oauth_access_token_secret]' value='<?php echo $options['tweeter_oauth_access_token_secret']; ?>'>
    <?php
}
function tweeter_consumer_key_render() {
    $options = get_option('tweeter_settings');
    ?>
    <input type='text' name='tweeter_settings[tweeter_consumer_key]' value='<?php echo $options['tweeter_consumer_key']; ?>'>
    <?php
}
function tweeter_consumer_secret_render() {
    $options = get_option('tweeter_settings');
    ?>
    <input type='text' name='tweeter_settings[tweeter_consumer_secret]' value='<?php echo $options['tweeter_consumer_secret']; ?>'>
    <?php
}
function tweeter_message_format_render() {
    $options = get_option('tweeter_settings');
    ?>
    <input type='text' name='tweeter_settings[tweeter_message_format]' value='<?php echo $options['tweeter_message_format']; ?>'>
    <?php
}
function tweeter_message_variables_render() {
    $options = get_option('tweeter_settings');
    ?>
    <input type='text' name='tweeter_settings[tweeter_message_variables]' value='<?php echo $options['tweeter_message_variables']; ?>'>
    <?php
}

function tweeter_account_settings_section_callback() {
    echo 'Twitter account settings';
}
function tweeter_message_settings_section_callback() {
    echo 'Twitter message settings';
}

function tweeter_options_page() {
    ?>
    <form action='options.php' method='post'>
        <?php
        settings_fields( 'tweeter_options_page' );
        do_settings_sections( 'tweeter_options_page' );
        submit_button();
        ?>
    </form>
    <?php
}
