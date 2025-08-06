<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function wa_page_flipper_shortcode_metabox_config() {
    add_meta_box(
        'wa_page_flipper_shortcode',
        __('Shortcode', 'page-flipper'),
        'wa_page_flipper_shortcode_metabox',
        'wa_page_flipper',
        'side',
        'low'
    );
}

add_action('add_meta_boxes', 'wa_page_flipper_shortcode_metabox_config');

function wa_page_flipper_shortcode_metabox($post) {
    ?>
        <div x-data="flipperShortcode" class="flipper-shortcode-wrapper">
            <p><?php esc_html_e('Use the shortcode below to display this Digital Book:', 'page-flipper'); ?></p>
            <input type="text" class="flipper-shortcode" value="<?php echo esc_attr('[page_flipper id="' . $post->ID . '"]'); ?>" readonly style="width: 100%;">
            <p class="extra-title"><?php esc_html_e('Extra Options', 'page-flipper'); ?></p>

            <div class="param-list">
                <span description="<?php esc_attr_e('Show or Hide the summary. Possible Values: `yes` or `no`. Default: `yes`', 'page-flipper'); ?>">enable_summary</span>
                <span description="<?php esc_attr_e('Show or Hide the related posts. Possible Values: `yes` or `no`. Default: `yes`', 'page-flipper'); ?>">enable_related</span>
                <span description="<?php esc_attr_e('Show or Hide the controls. Possible Values: `yes` or `no`. Default: `yes`', 'page-flipper'); ?>">enable_controls</span>
                <span description="<?php esc_attr_e('Show or Hide the share buttons. Possible Values: `yes` or `no`. Default: `yes`', 'page-flipper'); ?>">enable_share</span>
                <span description="<?php esc_attr_e('Show or Hide the zoom button. Possible Values: `yes` or `no`. Default: `yes`', 'page-flipper'); ?>">enable_zoom</span>
                <span description="<?php esc_attr_e('Use or not the cover image as background. Possible Values: `yes` or `no`. Default: `yes`', 'page-flipper'); ?>">enable_background_image</span>
                <span description="<?php esc_attr_e('Page background color. Default: `#333333`', 'page-flipper'); ?>">page_background_color</span>
                <span description="<?php esc_attr_e('Page surface color. Default: `rgba(0, 0, 0, 0.4)`', 'page-flipper'); ?>">page_surface_color</span>
                <span description="<?php esc_attr_e('Page surface accent color. Default: `#ffffff`', 'page-flipper'); ?>">page_surface_accent_color</span>
                <span description="<?php esc_attr_e('Page accent color. Default: `#eac101`', 'page-flipper'); ?>">page_accent_color</span>
                <span description="<?php esc_attr_e('Page font color. Default: `#ffffff`', 'page-flipper'); ?>">page_font_color</span>
            </div>

            <div class="flipper-shortcode-actions">
                <button type="button" class="button button-primary" x-on:click="copyShortcode('<?php esc_attr_e('Shortcode copied!', 'page-flipper'); ?>')"> <?php esc_html_e('Copy', 'page-flipper'); ?> </button>
            </div>
        </div>
    <?php
}