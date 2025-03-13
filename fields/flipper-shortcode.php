<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function add_page_flipper_shortcode_metabox() {
    add_meta_box(
        'page_flipper_shortcode_metabox',
        __('Shortcode', 'page-flipper'),
        'render_page_flipper_shortcode_metabox',
        'page_flipper',
        'side',
        'low'
    );
}

add_action('add_meta_boxes', 'add_page_flipper_shortcode_metabox');

function render_page_flipper_shortcode_metabox($post) {
    ?>
        <div class="flipper-shortcode-wrapper">
            <p><?php _e('Use the shortcode below to display this Digital Book:', 'page-flipper'); ?></p>
            <input type="text" class="flipper-shortcode" value="<?php echo esc_attr('[page_flipper id="' . $post->ID . '"]'); ?>" readonly style="width: 100%;">
            <p class="extra-title"><?php _e('Extra Options', 'page-flipper'); ?></p>

            <div class="param-list">
                <span description="<?php _e('Show or Hide the summary. Possible Values: `yes` or `no`. Default: `yes`', 'page-flipper'); ?>">summary</span>
                <span description="<?php _e('Show or Hide the action bar. Possible Values: `yes` or `no`. Default: `yes`', 'page-flipper'); ?>">action_bar</span>
                <span description="<?php _e('Show or Hide the controls. Possible Values: `yes` or `no`. Default: `yes`', 'page-flipper'); ?>">controls</span>
                <span description="<?php _e('Element background color. Default: `#333333`', 'page-flipper'); ?>">page_bg</span>
                <span description="<?php _e('Action Bar background color. Default: `#555555`', 'page-flipper'); ?>">action_bar_bg</span>
                <span description="<?php _e('Summary background color. Default: `#555555`', 'page-flipper'); ?>">summary_bg</span>
                <span description="<?php _e('Controls icons color. Default: `#ffffff`', 'page-flipper'); ?>">controls_icon</span>
                <span description="<?php _e('Element font color. Default: `#ffffff`', 'page-flipper'); ?>">font_color</span>
            </div>
           
            <div class="flipper-shortcode-actions">
                <button type="button" class="button button-primary copy-flipper-shortcode"> <?php _e('Copy', 'page-flipper'); ?> </button>
            </div>

            <script>
                const actions    = document.querySelector('.flipper-shortcode-actions');
                const copyButton = document.querySelector('.copy-flipper-shortcode');

                copyButton.addEventListener('click', () => {
                    const shortcodeInput = document.querySelector(".flipper-shortcode");
                    const copyMessage    = document.createElement('span');

                    copyMessage.innerHTML = "<?php _e('Shortcode copied!', 'page-flipper'); ?>";

                    shortcodeInput.select();
                    shortcodeInput.setSelectionRange(0, 99999);
                    document.execCommand("copy");
                    document.getSelection().removeAllRanges();
                    actions.prepend(copyMessage);

                    setTimeout(() => copyMessage.remove(), 2500);
                });
            </script>
        </div>
    <?php
}