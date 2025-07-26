<?php
function bt_site_top_banner_page() {
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }
    // Save settings if the form is submitted
    if (isset($_POST['bt_site_banner_submit'])) {
        update_option('bt_banner_media', sanitize_text_field($_POST['bt_banner_media']));
        update_option('bt_banner_text', wp_kses_post(wp_unslash($_POST['bt_banner_text'])));
        update_option('bt_banner_bg_color', sanitize_hex_color($_POST['bt_banner_bg_color']));
        update_option('bt_banner_font_color', sanitize_hex_color($_POST['bt_banner_font_color']));
        update_option('bt_show_banner', isset($_POST['bt_show_banner']) ? '0' : '1');
        update_option('bt_scroll_speed', $_POST['bt_scroll_speed']);
        
        // Save excluded posts/pages
        $excluded_posts = isset($_POST['bt_excluded_posts']) ? array_map('intval', $_POST['bt_excluded_posts']) : array();
        update_option('bt_excluded_posts', $excluded_posts);
        ?>
        <div class="notice notice-success">
            <p>Settings saved successfully!</p>
        </div>
        <?php
    }

    // Get current values
    $banner_media = get_option('bt_banner_media', '');
    $banner_text = get_option('bt_banner_text', '');
    $banner_bg_color = get_option('bt_banner_bg_color', '#000000');
    $banner_font_color = get_option('bt_banner_font_color', '#FFFFFF');
    $bt_scroll_speed = get_option('bt_scroll_speed', '50');
    $excluded_posts = get_option('bt_excluded_posts', array());

    // Enqueue WordPress media uploader scripts and Select2
    wp_enqueue_media();
    wp_enqueue_style('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css');
    wp_enqueue_script('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js', array('jquery'));
    ?>

    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form method="post">
            <table class="form-table">
                <tr>
                    <th scope="row">Banner Media</th>
                    <td>
                        <input type="text" id="bt_banner_media" name="bt_banner_media" value="<?php echo esc_attr($banner_media); ?>" class="regular-text">
                        <button type="button" class="button" id="upload_banner_button">Upload Media</button>
                        <p class="description">Choose an image for the banner background. If no image is selected, the background color will be used.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Banner Text</th>
                    <td>
                        <?php
                        wp_editor($banner_text, 'bt_banner_text', array(
                            'textarea_name' => 'bt_banner_text',
                            'media_buttons' => true,
                            'textarea_rows' => 5
                        ));
                        ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Background Color</th>
                    <td>
                        <input type="color" id="bt_banner_bg_color" name="bt_banner_bg_color" value="<?php echo esc_attr($banner_bg_color); ?>">
                        <p class="description">This color will be used if no media is selected.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Font Color</th>
                    <td>
                        <input type="color" id="bt_banner_font_color" name="bt_banner_font_color" value="<?php echo esc_attr($banner_font_color); ?>">
                        <p class="description">Choose the color for the banner text.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Show Banner</th>
                    <td>
                        <label>
                            <input type="checkbox" name="bt_show_banner" value="0" <?php checked('0', get_option('bt_show_banner', '1')); ?>>
                            Enable banner display
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Scroll Time (In Seconds)</th>
                    <td>
                        <input type="number" id="bt_scroll_speed" name="bt_scroll_speed" value="<?php echo esc_attr($bt_scroll_speed); ?>" class="regular-text">
                        <p class="description">More seconds will slow up the scroll speed.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Exclude from Posts/Pages</th>
                    <td>
                        <select name="bt_excluded_posts[]" id="bt_excluded_posts" multiple="multiple" style="width: 100%;">
                            <?php
                            // Get all posts and pages
                            $args = array(
                                'post_type' => array('post', 'page'),
                                'posts_per_page' => -1,
                                'orderby' => 'title',
                                'order' => 'ASC'
                            );
                            $all_posts = get_posts($args);
                            
                            foreach ($all_posts as $post) {
                                $selected = in_array($post->ID, $excluded_posts) ? 'selected="selected"' : '';
                                printf(
                                    '<option value="%d" %s>%s (%s)</option>',
                                    $post->ID,
                                    $selected,
                                    esc_html($post->post_title),
                                    esc_html($post->post_type)
                                );
                            }
                            ?>
                        </select>
                        <p class="description">Select posts and pages where you don't want to show the banner.</p>
                    </td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" name="bt_site_banner_submit" class="button button-primary" value="Save Settings">
            </p>
        </form>
    </div>

    <script>
    jQuery(document).ready(function($) {
        // Initialize Select2
        $('#bt_excluded_posts').select2({
            placeholder: 'Select posts/pages to exclude',
            width: '100%',
            allowClear: true
        });

        // Media uploader
        $('#upload_banner_button').click(function(e) {
            e.preventDefault();
            var image = wp.media({
                title: 'Upload Banner Image',
                multiple: false
            }).open()
            .on('select', function(e){
                var uploaded_image = image.state().get('selection').first();
                var image_url = uploaded_image.toJSON().url;
                $('#bt_banner_media').val(image_url);
            });
        });
    });
    </script>
    <?php
}

function header_top_hook_bt_utl() {
    $show_banner = get_option('bt_show_banner', '1');
    if ($show_banner !== '0') {
        return;
    }
    
    // Check if current post/page is excluded
    $excluded_posts = get_option('bt_excluded_posts', array());
    if (is_singular() && in_array(get_the_ID(), $excluded_posts)) {
        return;
    }
    
    $banner_media = get_option('bt_banner_media', '');
    $banner_text = get_option('bt_banner_text', '');
    $banner_bg_color = get_option('bt_banner_bg_color', '#000000');
    $banner_font_color = get_option('bt_banner_font_color', '#FFFFFF');
    $bt_scroll_speed = get_option('bt_scroll_speed', '50');

    $style = '';
    if ($banner_media) {
        $style = "background: url('" . esc_url($banner_media) . "') center/cover no-repeat;";
    } else {
        $style = "background: " . esc_attr($banner_bg_color) . ";";
    }
    ?>
    <style>
    .sliding-strip .marquee-content-wrap {
        display: flex;
        column-gap: 70px;
        animation: marquee <?php echo esc_attr($bt_scroll_speed); ?>s linear infinite;
        width: fit-content;
    }
    .sliding-strip {
        <?php echo $style; ?>
        padding: 10px 0;
        overflow: hidden;
        position: relative;
        width: 100%;
    }
    .sliding-strip .marquee-content-wrap a {
        color: <?php echo esc_attr($banner_font_color); ?>;
    }
    .sliding-strip p {
        margin-bottom: 0;
        font-weight: 600;
        white-space: nowrap;
        color: <?php echo esc_attr($banner_font_color); ?>;
        font-size: 18px;
        flex-shrink: 0;
    }
    .sliding-strip:hover .marquee-content-wrap {
        animation-play-state: paused;
    }
    @keyframes marquee {
        0% {
            transform: translateX(0);
        }
        100% {
            transform: translateX(calc(-50% - 25px));
        }
    }
    @media (max-width: 767px) {
        .sliding-strip p {
            font-size: 12px;
            text-transform: none;
        }
        .sliding-strip .marquee-content-wrap {
            display: flex;
            column-gap: 30px;
        }
        .sliding-strip {
            padding: 8px 0;
        }
    }
    </style>
    <section class="sliding-strip">
        <div class="marquee-content-wrap">
            <?php 
            for ($i = 0; $i < 12; $i++) {
                echo '<p>' . wp_kses_post($banner_text) . '</p>';
            }
            ?>
        </div>
    </section>
    <?php
}
add_action('fl_body_open', 'header_top_hook_bt_utl');




