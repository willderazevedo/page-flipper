<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function add_flipper_pdf_meta_box() {
    add_meta_box(
        'flipper_pdf',
        __( 'PDF File', 'page-flipper' ),
        'render_flipper_pdf_meta_box',
        'page_flipper',
        'side',
        'low'
    );
}
add_action( 'add_meta_boxes', 'add_flipper_pdf_meta_box' );

function render_flipper_pdf_meta_box( $post ) {
    wp_nonce_field( 'flipper_pdf_nonce_action', 'flipper_pdf_nonce' );

    $pdf_data   = get_post_meta( $post->ID, '_flipper_pdf_data', true );
    $attachment = !empty($pdf_data) ? esc_attr($pdf_data) : 'null';
    ?>
        <div x-data="flipperPdf(<?php echo $attachment; ?>)" class="flipper-pdf-wrapper">
            <input type="hidden" name="pdf_data" x-bind:value="JSON.stringify(attachment)">
                
            <template x-if="attachment !== null">
                <div>
                    <span class="file-title" x-text="attachment.title" x-on:click="selectPdfFile"></span>
                    <p class="file-edit-hint"><?php _e('Click on the box above to update the file.', 'page-flipper'); ?></p>
                    <a href="#" class="remove-pdf-file" x-on:click="removePdfFile"><?php _e('Remove PDF File', 'page-flipper'); ?></a>
                </div>
            </template>
            
            <template x-if="attachment === null">
                <a href="#" class="upload-pdf-file" x-on:click="selectPdfFile"><?php _e('Upload PDF File', 'page-flipper'); ?></a>
            </template>
        </div>
    <?php
}

function save_flipper_pdf_meta_box( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( ! isset( $_POST['flipper_pdf_nonce'] ) || ! wp_verify_nonce( $_POST['flipper_pdf_nonce'], 'flipper_pdf_nonce_action' ) ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    if ( isset( $_POST['pdf_data'] ) ) {
        $pdf_data = wp_unslash( $_POST['pdf_data'] );
        $pdf_data = json_decode( $pdf_data, true );

        if ( json_last_error() === JSON_ERROR_NONE ) {
            update_post_meta( $post_id, '_flipper_pdf_data', wp_json_encode( $pdf_data, JSON_UNESCAPED_UNICODE ) );
        }
    }
}
add_action( 'save_post', 'save_flipper_pdf_meta_box' );
