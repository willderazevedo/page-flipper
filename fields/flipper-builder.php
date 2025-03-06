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
        __( 'Flipper Builder', 'page-flipper' ),
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

    $builder_data = get_post_meta( $post->ID, '_flipper_builder_data', true );
    $builder_data = !empty($builder_data) ? esc_attr($builder_data) : '[]';

    ?>
        <div x-data="flipperBuilder(<?php echo $builder_data; ?>)" class="flipper-builder-wrapper">
            <input type="hidden" name="builder_data" x-bind:value="JSON.stringify(pages)">

            <div class="flipper-sidebar">
                <div class="sidebar-actions">
                    <button type="button" x-on:click="addPages()"><?php _e('Select Images', 'page-flipper'); ?></button>
                    <button x-on:click="removePages()" x-bind:class="{'disabled': !pages.length}" x-bind:disabled="!pages.length" type="button"><?php _e('Remove Pages', 'page-flipper'); ?></button>
                </div>

                <template x-if="pages.length">
                    <div class="sidebar-pages">
                        <div class="section-title">
                            <?php _e('Page List', 'page-flipper'); ?>
                        </div>
                        
                        <ul class="page-list">
                            <template x-for="page in pages">
                                <li x-on:click.stop="selectPage(page)" x-bind:class="{'selected': page.selected}">
                                    <button type="button" class="drag-page">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 448 512"><path d="M0 96C0 78.3 14.3 64 32 64l384 0c17.7 0 32 14.3 32 32s-14.3 32-32 32L32 128C14.3 128 0 113.7 0 96zM0 256c0-17.7 14.3-32 32-32l384 0c17.7 0 32 14.3 32 32s-14.3 32-32 32L32 288c-17.7 0-32-14.3-32-32zM448 416c0 17.7-14.3 32-32 32L32 448c-17.7 0-32-14.3-32-32s14.3-32 32-32l384 0c17.7 0 32 14.3 32 32z"/></svg>
                                    </button>
                                    <span x-text="page.attachment.title"></span>
                                    <button x-on:click.stop="removePage(page)" type="button" class="remove-page">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 448 512"><path d="M135.2 17.7L128 32 32 32C14.3 32 0 46.3 0 64S14.3 96 32 96l384 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-96 0-7.2-14.3C307.4 6.8 296.3 0 284.2 0L163.8 0c-12.1 0-23.2 6.8-28.6 17.7zM416 128L32 128 53.2 467c1.6 25.3 22.6 45 47.9 45l245.8 0c25.3 0 46.3-19.7 47.9-45L416 128z"/></svg>
                                    </button>
                                </li>
                            </template>
                        </ul>
                    </div>
                </template>
            </div>
            
            <template x-if="selectedPage">
                <div class="flipper-page">
                    <img x-bind:src="selectedPage.attachment.url" x-bind:alt="selectedPage.attachment.alt" draggable="false">
                
                    <div class="hotspots-wrapper">
                        <template x-for="(hotspot, index) in selectedPage.hotspots">
                            <template x-if="hotspot.type === 'narration'">
                                <!-- Narration Hotspot -->
                                <div x-bind:class="`hotspot-${index + 1}`" x-data="{hover: false}"  x-on:mouseover="hover = true" x-on:mouseover.away="hover = false" x-on:click="removeHotspot(index)" class="narration-hotspot" description="<?php _e('Remove Narration', 'page-flipper'); ?>">
                                    <svg x-show="!hover" xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 576 512"><path d="M64 32C28.7 32 0 60.7 0 96L0 416c0 35.3 28.7 64 64 64l448 0c35.3 0 64-28.7 64-64l0-320c0-35.3-28.7-64-64-64L64 32zM213.5 173.3l72 144c5.9 11.9 1.1 26.3-10.7 32.2s-26.3 1.1-32.2-10.7l-9.4-18.9-82.2 0-9.4 18.9c-5.9 11.9-20.3 16.7-32.2 10.7s-16.7-20.3-10.7-32.2l72-144c4.1-8.1 12.4-13.3 21.5-13.3s17.4 5.1 21.5 13.3zm-.4 106.6L192 237.7l-21.1 42.2 42.2 0zM304 184c0-13.3 10.7-24 24-24l56 0c53 0 96 43 96 96s-43 96-96 96l-56 0c-13.3 0-24-10.7-24-24l0-144zm48 24l0 96 32 0c26.5 0 48-21.5 48-48s-21.5-48-48-48l-32 0z"/></svg>
                                    <svg x-show="hover" xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 448 512"><path d="M135.2 17.7L128 32 32 32C14.3 32 0 46.3 0 64S14.3 96 32 96l384 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-96 0-7.2-14.3C307.4 6.8 296.3 0 284.2 0L163.8 0c-12.1 0-23.2 6.8-28.6 17.7zM416 128L32 128 53.2 467c1.6 25.3 22.6 45 47.9 45l245.8 0c25.3 0 46.3-19.7 47.9-45L416 128z"/></svg>
                                </div>
                            </template>

                            <template x-if="hotspot.type === 'audio'">
                                <div x-bind:class="`hotspot-${index + 1}`" class="audio-hotspot">
                                    asdasdasdasd
                                </div>
                            </template>

                            <template x-if="hotspot.type === 'video'">
                                <!-- Video Hotspot -->
                            </template>

                            <template x-if="hotspot.type === 'image'">
                                <!-- Image Hotspot -->
                            </template>

                            <template x-if="hotspot.type === 'text'">
                                <!-- Text Hotspot -->
                            </template>

                            <template x-if="hotspot.type === 'link'">
                                <!-- Link Hotspot -->
                            </template>
                        </template>
                    </div>

                    <div class="page-actions">
                        <button type="button" description="<?php _e('Narration', 'page-flipper'); ?>" x-on:click="addHotspot('narration')">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 576 512"><path d="M64 32C28.7 32 0 60.7 0 96L0 416c0 35.3 28.7 64 64 64l448 0c35.3 0 64-28.7 64-64l0-320c0-35.3-28.7-64-64-64L64 32zM213.5 173.3l72 144c5.9 11.9 1.1 26.3-10.7 32.2s-26.3 1.1-32.2-10.7l-9.4-18.9-82.2 0-9.4 18.9c-5.9 11.9-20.3 16.7-32.2 10.7s-16.7-20.3-10.7-32.2l72-144c4.1-8.1 12.4-13.3 21.5-13.3s17.4 5.1 21.5 13.3zm-.4 106.6L192 237.7l-21.1 42.2 42.2 0zM304 184c0-13.3 10.7-24 24-24l56 0c53 0 96 43 96 96s-43 96-96 96l-56 0c-13.3 0-24-10.7-24-24l0-144zm48 24l0 96 32 0c26.5 0 48-21.5 48-48s-21.5-48-48-48l-32 0z"/></svg>
                        </button>
                        <button type="button" description="<?php _e('Audio', 'page-flipper'); ?>" x-on:click="addHotspot('audio')">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 640 512"><path d="M533.6 32.5C598.5 85.2 640 165.8 640 256s-41.5 170.7-106.4 223.5c-10.3 8.4-25.4 6.8-33.8-3.5s-6.8-25.4 3.5-33.8C557.5 398.2 592 331.2 592 256s-34.5-142.2-88.7-186.3c-10.3-8.4-11.8-23.5-3.5-33.8s23.5-11.8 33.8-3.5zM473.1 107c43.2 35.2 70.9 88.9 70.9 149s-27.7 113.8-70.9 149c-10.3 8.4-25.4 6.8-33.8-3.5s-6.8-25.4 3.5-33.8C475.3 341.3 496 301.1 496 256s-20.7-85.3-53.2-111.8c-10.3-8.4-11.8-23.5-3.5-33.8s23.5-11.8 33.8-3.5zm-60.5 74.5C434.1 199.1 448 225.9 448 256s-13.9 56.9-35.4 74.5c-10.3 8.4-25.4 6.8-33.8-3.5s-6.8-25.4 3.5-33.8C393.1 284.4 400 271 400 256s-6.9-28.4-17.7-37.3c-10.3-8.4-11.8-23.5-3.5-33.8s23.5-11.8 33.8-3.5zM301.1 34.8C312.6 40 320 51.4 320 64l0 384c0 12.6-7.4 24-18.9 29.2s-25 3.1-34.4-5.3L131.8 352 64 352c-35.3 0-64-28.7-64-64l0-64c0-35.3 28.7-64 64-64l67.8 0L266.7 40.1c9.4-8.4 22.9-10.4 34.4-5.3z"/></svg>
                        </button>
                        <button type="button" description="<?php _e('Video', 'page-flipper'); ?>" x-on:click="">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 384 512"><path d="M73 39c-14.8-9.1-33.4-9.4-48.5-.9S0 62.6 0 80L0 432c0 17.4 9.4 33.4 24.5 41.9s33.7 8.1 48.5-.9L361 297c14.3-8.7 23-24.2 23-41s-8.7-32.2-23-41L73 39z"/></svg>
                        </button>
                        <button type="button" description="<?php _e('Image', 'page-flipper'); ?>" x-on:click="">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 512 512"><path d="M0 96C0 60.7 28.7 32 64 32l384 0c35.3 0 64 28.7 64 64l0 320c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64L0 96zM323.8 202.5c-4.5-6.6-11.9-10.5-19.8-10.5s-15.4 3.9-19.8 10.5l-87 127.6L170.7 297c-4.6-5.7-11.5-9-18.7-9s-14.2 3.3-18.7 9l-64 80c-5.8 7.2-6.9 17.1-2.9 25.4s12.4 13.6 21.6 13.6l96 0 32 0 208 0c8.9 0 17.1-4.9 21.2-12.8s3.6-17.4-1.4-24.7l-120-176zM112 192a48 48 0 1 0 0-96 48 48 0 1 0 0 96z"/></svg>
                        </button>
                        <button type="button" description="<?php _e('Text', 'page-flipper'); ?>" x-on:click="">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 448 512"><path d="M254 52.8C249.3 40.3 237.3 32 224 32s-25.3 8.3-30 20.8L57.8 416 32 416c-17.7 0-32 14.3-32 32s14.3 32 32 32l96 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-1.8 0 18-48 159.6 0 18 48-1.8 0c-17.7 0-32 14.3-32 32s14.3 32 32 32l96 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-25.8 0L254 52.8zM279.8 304l-111.6 0L224 155.1 279.8 304z"/></svg>
                        </button>
                        <button type="button" description="<?php _e('Hyperlink', 'page-flipper'); ?>" x-on:click="">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 640 512"><path d="M579.8 267.7c56.5-56.5 56.5-148 0-204.5c-50-50-128.8-56.5-186.3-15.4l-1.6 1.1c-14.4 10.3-17.7 30.3-7.4 44.6s30.3 17.7 44.6 7.4l1.6-1.1c32.1-22.9 76-19.3 103.8 8.6c31.5 31.5 31.5 82.5 0 114L422.3 334.8c-31.5 31.5-82.5 31.5-114 0c-27.9-27.9-31.5-71.8-8.6-103.8l1.1-1.6c10.3-14.4 6.9-34.4-7.4-44.6s-34.4-6.9-44.6 7.4l-1.1 1.6C206.5 251.2 213 330 263 380c56.5 56.5 148 56.5 204.5 0L579.8 267.7zM60.2 244.3c-56.5 56.5-56.5 148 0 204.5c50 50 128.8 56.5 186.3 15.4l1.6-1.1c14.4-10.3 17.7-30.3 7.4-44.6s-30.3-17.7-44.6-7.4l-1.6 1.1c-32.1 22.9-76 19.3-103.8-8.6C74 372 74 321 105.5 289.5L217.7 177.2c31.5-31.5 82.5-31.5 114 0c27.9 27.9 31.5 71.8 8.6 103.9l-1.1 1.6c-10.3 14.4-6.9 34.4 7.4 44.6s34.4 6.9 44.6-7.4l1.1-1.6C433.5 260.8 427 182 377 132c-56.5-56.5-148-56.5-204.5 0L60.2 244.3z"/></svg>
                        </button>
                    </div>
                </div>
            </template>
        </div>
    <?php
}

/**
 * Salva os IDs das imagens no post_meta
 */
function save_flipper_builder_meta_box( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( ! isset( $_POST['flipper_builder_nonce'] ) || ! wp_verify_nonce( $_POST['flipper_builder_nonce'], 'flipper_builder_nonce_action' ) ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    if ( isset( $_POST['builder_data'] ) ) {
        $builder_data = wp_unslash( $_POST['builder_data'] );
        $builder_data = json_decode( $builder_data, true );

        if ( json_last_error() === JSON_ERROR_NONE ) {
            update_post_meta( $post_id, '_flipper_builder_data', wp_json_encode( $builder_data ) );
        }
    }
}
add_action( 'save_post', 'save_flipper_builder_meta_box' );
