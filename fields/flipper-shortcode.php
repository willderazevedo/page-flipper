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
    if ($post->ID) {
        ?>
            <div class="flipper-shortcode-wrapper">
                <p><?php _e('Use the shortcode below to display this Digital Book:', 'page-flipper'); ?></p>
                <input type="text" class="flipper-shortcode" value="<?php echo esc_attr('[page_flipper id="' . $post->ID . '"]'); ?>" readonly style="width: 100%;">
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
    } else {
        echo '<p>' . __('Save the post to generate the shortcode.', 'page-flipper') . '</p>';
    }
}