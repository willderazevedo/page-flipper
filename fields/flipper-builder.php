<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Adiciona um metabox ao post type "page_flipper"
 */
function add_flipper_builder_meta_box() {
    add_meta_box(
        'flipper_builder',
        __( 'Flipper Builder', 'text-domain' ),
        'render_flipper_builder_meta_box',
        'page_flipper',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'add_flipper_builder_meta_box' );

/**
 * Renderiza o campo de upload de múltiplas imagens dentro do metabox
 */
function render_flipper_builder_meta_box( $post ) {
    wp_nonce_field( 'flipper_builder_nonce_action', 'flipper_builder_nonce' );

    $image_ids = get_post_meta( $post->ID, '_flipper_images', true );
    $image_ids = is_array( $image_ids ) ? $image_ids : [];

    ?>
        <p>
            <label><?php _e( 'Upload Images:', 'text-domain' ); ?></label><br>
            <input type="hidden" id="flipper_images" name="flipper_images" value="<?php echo esc_attr( implode( ',', $image_ids ) ); ?>">
            <div id="flipper_images_preview">
                <?php foreach ( $image_ids as $image_id ) : 
                    $image_url = wp_get_attachment_url( $image_id );
                    if ( $image_url ) : ?>
                        <div class="flipper-image-item" data-id="<?php echo esc_attr( $image_id ); ?>">
                            <img src="<?php echo esc_url( $image_url ); ?>" style="max-width: 100px; height: auto;">
                            <button type="button" class="button flipper_remove_image" data-id="<?php echo esc_attr( $image_id ); ?>">✖</button>
                        </div>
                    <?php endif; 
                endforeach; ?>
            </div>
            <button type="button" class="button flipper_upload_images"><?php _e( 'Select Images', 'text-domain' ); ?></button>
        </p>
    <?php
}

/**
 * Salva os IDs das imagens no post_meta
 */
function save_flipper_builder_meta_box( $post_id ) {
    if ( ! isset( $_POST['flipper_builder_nonce'] ) || ! wp_verify_nonce( $_POST['flipper_builder_nonce'], 'flipper_builder_nonce_action' ) ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( isset( $_POST['post_type'] ) && 'page_flipper' === $_POST['post_type'] ) {
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
    }

    if ( isset( $_POST['flipper_images'] ) ) {
        $image_ids = array_filter( explode( ',', sanitize_text_field( $_POST['flipper_images'] ) ) );
        update_post_meta( $post_id, '_flipper_images', $image_ids );
    } else {
        delete_post_meta( $post_id, '_flipper_images' );
    }
}
add_action( 'save_post', 'save_flipper_builder_meta_box' );
