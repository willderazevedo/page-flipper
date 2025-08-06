<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function wa_page_flipper_pdf_meta_box_config() {
    add_meta_box(
        'wa_page_flipper_pdf',
        __( 'PDF File', 'page-flipper' ),
        'wa_page_flipper_pdf_meta_box',
        'wa_page_flipper',
        'side',
        'low'
    );
}
add_action( 'add_meta_boxes', 'wa_page_flipper_pdf_meta_box_config' );

function wa_page_flipper_pdf_meta_box( $post ) {
    wp_nonce_field( 'wa_page_flipper_pdf_nonce_action', 'wa_page_flipper_pdf_nonce' );

    $pdf_data   = get_post_meta( $post->ID, '_wa_page_flipper_pdf_data', true );
    $attachment = !empty($pdf_data) ? $pdf_data : 'null';
    ?>
        <div x-data="flipperPdf(<?php echo esc_js($attachment); ?>)" class="flipper-pdf-wrapper">
            <input type="hidden" name="pdf_data" x-bind:value="JSON.stringify(attachment)">
                
            <template x-if="attachment !== null">
                <div>
                    <span class="file-title" x-text="attachment.title" x-on:click="selectPdfFile"></span>
                    <p class="file-edit-hint"><?php esc_html_e('Click on the box above to update the file.', 'page-flipper'); ?></p>
                    <a href="#" class="remove-pdf-file" x-on:click="removePdfFile"><?php esc_html_e('Remove PDF File', 'page-flipper'); ?></a>
                </div>
            </template>
            
            <template x-if="attachment === null">
                <a href="#" class="upload-pdf-file" x-on:click="selectPdfFile"><?php esc_html_e('Upload PDF File', 'page-flipper'); ?></a>
            </template>
        </div>
    <?php
}

function wa_page_flipper_pdf_meta_box_save( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( ! isset( $_POST['wa_page_flipper_pdf_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wa_page_flipper_pdf_nonce'] ) ), 'wa_page_flipper_pdf_nonce_action' ) ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    if ( isset( $_POST['pdf_data'] ) ) {
        $pdf_data = wp_unslash( $_POST['pdf_data'] );
        $pdf_data = json_decode( $pdf_data, true );

        if ( json_last_error() === JSON_ERROR_NONE ) {
            update_post_meta( $post_id, '_wa_page_flipper_pdf_data', wp_json_encode( $pdf_data, JSON_UNESCAPED_UNICODE ) );
        }
    }
}
add_action( 'save_post', 'wa_page_flipper_pdf_meta_box_save' );
