<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function wa_page_flipper_background_meta_box_config() {
    add_meta_box(
        'wa_page_flipper_background',
        __( 'Background Image', 'page-flipper' ),
        'wa_page_flipper_background_meta_box',
        'wa_page_flipper',
        'side',
        'low'
    );
}
add_action( 'add_meta_boxes', 'wa_page_flipper_background_meta_box_config' );

function wa_page_flipper_background_meta_box( $post ) {
    wp_nonce_field( 'wa_page_flipper_background_nonce_action', 'wa_page_flipper_background_nonce' );

    $background_data   = get_post_meta( $post->ID, '_wa_page_flipper_background_data', true );
    $attachment = !empty($background_data) ? $background_data : 'null';
    ?>
        <div x-data="flipperBackground(<?php echo esc_js($attachment); ?>)" class="flipper-background-wrapper">
            <input type="hidden" name="background_data" x-bind:value="JSON.stringify(attachment)">
                
            <template x-if="attachment !== null">
                <div>
                    <img class="background-image" x-bind:src="attachment.url" x-bind:alt="attachment.title" x-on:click="selectBackgroundFile">
                    <p class="file-edit-hint"><?php esc_html_e('Click on the box above to update the file.', 'page-flipper'); ?></p>
                    <a href="#" class="remove-background-file" x-on:click="removeBackgroundFile"><?php esc_html_e('Remove Background Image', 'page-flipper'); ?></a>
                </div>
            </template>
            
            <template x-if="attachment === null">
                <a href="#" class="upload-background-file" x-on:click="selectBackgroundFile"><?php esc_html_e('Upload Background Image', 'page-flipper'); ?></a>
            </template>
        </div>
    <?php
}

function wa_page_flipper_background_meta_box_save( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( ! isset( $_POST['wa_page_flipper_background_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wa_page_flipper_background_nonce'] ) ), 'wa_page_flipper_background_nonce_action' ) ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    if ( isset( $_POST['background_data'] ) ) {
        $background_data_raw = wp_unslash( $_POST['background_data'] );

        if ( is_string( $background_data_raw ) ) {
            $background_data = json_decode( $background_data_raw, true );

            if ( json_last_error() === JSON_ERROR_NONE && is_array( $background_data ) ) {
                array_walk_recursive( $background_data, function ( &$value ) {
                    if ( is_string( $value ) ) {
                        $value = sanitize_text_field( $value );
                    }
                });

                update_post_meta(
                    $post_id,
                    '_wa_page_flipper_background_data',
                    wp_json_encode( $background_data, JSON_UNESCAPED_UNICODE )
                );
            }
        }
    }
}
add_action( 'save_post', 'wa_page_flipper_background_meta_box_save' );
