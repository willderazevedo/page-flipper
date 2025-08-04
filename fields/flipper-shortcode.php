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
                <span description="<?php esc_attr_e('Show or Hide the summary. Possible Values: `yes` or `no`. Default: `yes`', 'page-flipper'); ?>">summary</span>
                <span description="<?php esc_attr_e('Show or Hide the action bar. Possible Values: `yes` or `no`. Default: `yes`', 'page-flipper'); ?>">action_bar</span>
                <span description="<?php esc_attr_e('Show or Hide the controls. Possible Values: `yes` or `no`. Default: `yes`', 'page-flipper'); ?>">controls</span>
                <span description="<?php esc_attr_e('Element background color. Default: `#333333`', 'page-flipper'); ?>">page_bg</span>
                <span description="<?php esc_attr_e('Action Bar background color. Default: `#555555`', 'page-flipper'); ?>">action_bar_bg</span>
                <span description="<?php esc_attr_e('Summary background color. Default: `#555555`', 'page-flipper'); ?>">summary_bg</span>
                <span description="<?php esc_attr_e('Controls icons color. Default: `#ffffff`', 'page-flipper'); ?>">controls_icon</span>
                <span description="<?php esc_attr_e('Element font color. Default: `#ffffff`', 'page-flipper'); ?>">font_color</span>
            </div>
           
            <div class="flipper-shortcode-actions">
                <button type="button" class="button button-primary" x-on:click="copyShortcode('<?php esc_attr_e('Shortcode copied!', 'page-flipper'); ?>')"> <?php esc_html_e('Copy', 'page-flipper'); ?> </button>
            </div>
        </div>
    <?php
}