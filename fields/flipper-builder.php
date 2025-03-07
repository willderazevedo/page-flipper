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
                                        <i class="fa-solid fa-bars"></i>
                                    </button>
                                    <span x-text="page.attachment.title"></span>
                                    <button x-on:click.stop="removePage(page)" type="button" class="remove-page">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </li>
                            </template>
                        </ul>
                    </div>
                </template>
            </div>
            
            <template x-if="selectedPage">
                <div class="flipper-page">
                    <img class="page-image" x-bind:src="selectedPage.attachment.url" x-bind:alt="selectedPage.attachment.alt" draggable="false">
                
                    <template x-if="hotspotWrapperWidth && hotspotWrapperHeight">
                        <div class="hotspots-wrapper" x-bind:style="`width: ${hotspotWrapperWidth}px; height: ${hotspotWrapperHeight}px;`">
                            <template x-for="(hotspot, index) in selectedPage.hotspots">
                                <div class="hotspot-container" x-bind="buildHotspotInitialAttributes(hotspot)">
                                    <template x-if="hotspot.type === 'narration'">
                                        <div x-data="{hover: false}"  x-on:mouseover="hover = true" x-on:mouseover.away="hover = false" x-on:click="removeHotspot(index)" class="narration-hotspot" description="<?php _e('Remove Narration', 'page-flipper'); ?>">
                                            <i x-show="!hover" class="fa-solid fa-audio-description"></i>
                                            <i x-show="hover" class="fa-solid fa-trash"></i>
                                        </div>
                                    </template>
        
                                    <template x-if="hotspot.type !== 'narration'">
                                        <div x-bind:class="`${hotspot.type}-hotspot`">
                                            <div class="hotspot-extras">
                                                <div class="extras-title">
                                                    <?php _e('Settings', 'page-flipper'); ?>

                                                    <div class="extras-actions">
                                                        <template x-if="hotspot.type !== 'text' && hotspot.type !== 'link'">
                                                            <button type="button" x-on:click="editHotspotMedia(hotspot)" description="<?php _e('Change File', 'page-flipper'); ?>">
                                                                <i class="fa-solid fa-file-pen"></i>
                                                            </button>
                                                        </template>

                                                        <button type="button" x-on:click="removeHotspot(index)" description="<?php _e('Remove Hotspot', 'page-flipper'); ?>">
                                                            <i class="fa-solid fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>

                                                <ul>
                                                    <li>
                                                        <label><?php _e('Display Mode', 'page-flipper'); ?></label>
                                                        <select x-model="hotspot.extras.mode">
                                                            <option value="icon"><?php _e('Icon', 'page-flipper'); ?></option>
                                                            <option value="inline"><?php _e('Inline', 'page-flipper'); ?></option>
                                                        </select>
                                                    </li>

                                                    <li x-show="hotspot.type === 'text'">
                                                        <label><?php _e('Font Size', 'page-flipper'); ?> <small>(px)</small></label>
                                                        <input type="number" x-model="hotspot.extras.font_size">
                                                    </li>

                                                    <li x-show="hotspot.type === 'text'">
                                                        <label><?php _e('Font Family', 'page-flipper'); ?> (<?php _e('Example', 'page-flipper'); ?>: Lora, Arial)</label>
                                                        <input type="text" x-model="hotspot.extras.font_family">
                                                    </li>

                                                    <li x-show="hotspot.type === 'text'">
                                                        <label><?php _e('Font Color', 'page-flipper'); ?></label>
                                                        <input type="color" x-model="hotspot.extras.font_color">
                                                    </li>

                                                    <li>
                                                        <label><?php _e('Font Weight', 'page-flipper'); ?></label>
                                                        <select x-model="hotspot.extras.font_weight">
                                                            <option value="normal"><?php _e('Normal', 'page-flipper'); ?></option>
                                                            <option value="lighter"><?php _e('Lighter', 'page-flipper'); ?></option>
                                                            <option value="bold"><?php _e('Bold', 'page-flipper'); ?></option>
                                                            <option value="bolder"><?php _e('Bolder', 'page-flipper'); ?></option>
                                                        </select>
                                                    </li>

                                                    <li>
                                                        <label><?php _e('Text Decoration', 'page-flipper'); ?></label>
                                                        <select x-model="hotspot.extras.text_decoration">
                                                            <option value="none"><?php _e('None', 'page-flipper'); ?></option>
                                                            <option value="underline"><?php _e('Underline', 'page-flipper'); ?></option>
                                                            <option value="line-through"><?php _e('Line Through', 'page-flipper'); ?></option>
                                                        </select>
                                                    </li>

                                                    <li>
                                                        <label><?php _e('Text Align', 'page-flipper'); ?></label>
                                                        <select x-model="hotspot.extras.text_align">
                                                            <option value="left"><?php _e('Left', 'page-flipper'); ?></option>
                                                            <option value="right"><?php _e('Right', 'page-flipper'); ?></option>
                                                            <option value="center"><?php _e('Center', 'page-flipper'); ?></option>
                                                            <option value="justify"><?php _e('Justify', 'page-flipper'); ?></option>
                                                        </select>
                                                    </li>

                                                    <li x-show="hotspot.type === 'text'">
                                                        <label><?php _e('Content', 'page-flipper'); ?></label>
                                                        <textarea x-model="hotspot.extras.content" rows="5"></textarea>
                                                    </li>

                                                    <li x-show="hotspot.extras.mode === 'icon'">
                                                        <label><?php _e('Icon Border Radius', 'page-flipper'); ?></label>
                                                        <select x-model="hotspot.extras.icon_border">
                                                            <option value="50%"><?php _e('Rounded', 'page-flipper'); ?></option>
                                                            <option value="5px"><?php _e('Smooth', 'page-flipper'); ?></option>
                                                            <option value="0px"><?php _e('Sharp', 'page-flipper'); ?></option>
                                                            <option value="custom"><?php _e('Custom', 'page-flipper'); ?></option>
                                                        </select>
                                                    </li>

                                                    <li x-show="hotspot.extras.icon_border === 'custom'">
                                                        <label><?php _e('Custom Radius', 'page-flipper'); ?> <small>(<?php _e('Example', 'page-flipper'); ?>: 50% 0 10px 0)</small></label>
                                                        <input type="text" x-model="hotspot.extras.icon_border_custom">
                                                    </li>

                                                    <li x-show="hotspot.extras.mode === 'icon'">
                                                        <label><?php _e('Icon Color', 'page-flipper'); ?></label>
                                                        <input type="color" x-model="hotspot.extras.icon_color">
                                                    </li>

                                                    <li x-show="hotspot.extras.mode === 'icon'">
                                                        <label><?php _e('Icon Background Color', 'page-flipper'); ?></label>
                                                        <input type="color" x-model="hotspot.extras.icon_background">
                                                    </li>

                                                    <li x-show="hotspot.extras.mode === 'icon'">
                                                        <label><?php _e('Icon Name', 'page-flipper'); ?> <a href="https://fontawesome.com/icons" target="_blank"><small>(Font Awesome Icons)</small></a></label>
                                                        <input type="text" x-model="hotspot.extras.icon_name">
                                                    </li>

                                                    <li x-show="hotspot.extras.mode === 'icon'">
                                                        <label><?php _e('Icon Size', 'page-flipper'); ?> <small>(px)</small></label>
                                                        <input type="number" x-model="hotspot.extras.icon_size">
                                                    </li>
                                                </ul>
                                            </div>

                                            <div class="hotspot-content">
                                                <template x-if="hotspot.extras.mode === 'icon'">
                                                    <div 
                                                        class="icon-mode-hotspot" 
                                                        x-bind:style="{
                                                            'background': hotspot.extras.icon_background,
                                                            'color': hotspot.extras.icon_color,
                                                            'font-size': `${hotspot.extras.icon_size}px`,
                                                            'border-radius': hotspot.extras.icon_border === 'custom' ? hotspot.extras.icon_border_custom : hotspot.extras.icon_border
                                                        }"
                                                    >
                                                        <i x-bind:class="hotspot.extras.icon_name"></i>
                                                    </div>
                                                </template>

                                                <template x-if="hotspot.extras.mode === 'inline'">
                                                    <div class="inline-mode-hotspot">
                                                        <template x-if="hotspot.type === 'audio'">
                                                            <audio controls>
                                                                <source x-bind:src="hotspot.attachment.url" x-bind:type="hotspot.attachment.mime">
                                                            </audio>
                                                        </template>

                                                        <template x-if="hotspot.type === 'image'">
                                                            <img x-bind:src="hotspot.attachment.url" x-bind:type="hotspot.attachment.alt">
                                                        </template>

                                                        <template x-if="hotspot.type === 'video'">
                                                            <video controls>
                                                                <source x-bind:src="hotspot.attachment.url" x-bind:type="hotspot.attachment.mime">
                                                            </video>
                                                        </template>

                                                        <template x-if="hotspot.type === 'text'">
                                                            <article 
                                                                x-text="hotspot.extras.content"
                                                                x-bind:style="{
                                                                    'color': hotspot.extras.font_color,
                                                                    'font-size': `${hotspot.extras.font_size}px`,
                                                                    'font-family': hotspot.extras.font_family,
                                                                    'font-weight': hotspot.extras.font_weight,
                                                                    'text-decoration': hotspot.extras.text_decoration,
                                                                    'text-align': hotspot.extras.text_align
                                                                }"
                                                            ></article>
                                                        </template>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </template>

                    <div class="page-actions">
                        <button type="button" description="<?php _e('Narration', 'page-flipper'); ?>" x-on:click="addHotspot('narration')">
                            <i class="fa-solid fa-audio-description"></i>
                        </button>
                        <button type="button" description="<?php _e('Audio', 'page-flipper'); ?>" x-on:click="addHotspot('audio')">
                            <i class="fa-solid fa-volume-high"></i>
                        </button>
                        <button type="button" description="<?php _e('Video', 'page-flipper'); ?>" x-on:click="addHotspot('video')">
                            <i class="fa-solid fa-play"></i>
                        </button>
                        <button type="button" description="<?php _e('Image', 'page-flipper'); ?>" x-on:click="addHotspot('image')">
                            <i class="fa-solid fa-image"></i>
                        </button>
                        <button type="button" description="<?php _e('Text', 'page-flipper'); ?>" x-on:click="addHotspot('text')">
                            <i class="fa-solid fa-font"></i>
                        </button>
                        <button type="button" description="<?php _e('Hyperlink', 'page-flipper'); ?>" x-on:click="addHotspot('link')">
                            <i class="fa-solid fa-link"></i>
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
