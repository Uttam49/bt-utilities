<?php
// Prevent direct access to the plugin file
if (!defined('ABSPATH')) {
    exit;
}

// Create the admin page
function bt_header_footer_page() {
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }

    // Save settings if the form is submitted
    if (isset($_POST['bt_header_footer_submit'])) {
        update_option('bt_header_code', wp_unslash($_POST['bt_header_code']));
        update_option('bt_footer_code', wp_unslash($_POST['bt_footer_code']));
        update_option('bt_after_body_open_code', wp_unslash($_POST['bt_after_body_open_code']));
        update_option('bt_before_body_close_code', wp_unslash($_POST['bt_before_body_close_code']));
        echo '<div class="updated"><p>Settings saved.</p></div>';
    }

    // Get current values
    $header_code = get_option('bt_header_code', '');
    $footer_code = get_option('bt_footer_code', '');
    $after_body_open_code = get_option('bt_after_body_open_code', '');
    $before_body_close_code = get_option('bt_before_body_close_code', '');

    // Admin page HTML
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form method="post" action="">
            <h2>Header Code</h2>
            <textarea name="bt_header_code" rows="10" style="width: 100%;"><?php echo esc_textarea($header_code); ?></textarea>
            
            <h2>Footer Code</h2>
            <textarea name="bt_footer_code" rows="10" style="width: 100%;"><?php echo esc_textarea($footer_code); ?></textarea>

            <h2>After Body Open Code</h2>
            <textarea name="bt_after_body_open_code" rows="10" style="width: 100%;"><?php echo esc_textarea($after_body_open_code); ?></textarea>

            <h2>Before Body Close Code</h2>
            <textarea name="bt_before_body_close_code" rows="10" style="width: 100%;"><?php echo esc_textarea($before_body_close_code); ?></textarea>
            
            <?php submit_button('Save Changes', 'primary', 'bt_header_footer_submit'); ?>
        </form>
    </div>
    <?php
}

// Add header code
function bt_add_header_code() {
    echo get_option('bt_header_code', '');
}
add_action('wp_head', 'bt_add_header_code');

// Add footer code
function bt_add_footer_code() {
    echo get_option('bt_footer_code', '');
}
add_action('wp_footer', 'bt_add_footer_code');

// Add after body open code
function bt_add_after_body_open_code() {
    echo get_option('bt_after_body_open_code', '');
}
add_action('fl_body_open', 'bt_add_after_body_open_code');

// Add before body close code
function bt_add_before_body_close_code() {
    echo get_option('bt_before_body_close_code', '');
}
add_action('fl_body_close', 'bt_add_before_body_close_code');