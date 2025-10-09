<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function wa_page_flipper_builder_meta_box_config() {
    add_meta_box(
        'wa_page_flipper_builder',
        __( 'Flipper Builder', 'page-flipper' ),
        'wa_page_flipper_builder_meta_box',
        'wa_page_flipper',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'wa_page_flipper_builder_meta_box_config' );

function wa_page_flipper_builder_meta_box( $post ) {
    wp_nonce_field( 'wa_page_flipper_builder_nonce_action', 'wa_page_flipper_builder_nonce' );

    $builder_data = get_post_meta( $post->ID, '_wa_page_flipper_builder_data', true );
    $builder_data = !empty($builder_data) ? $builder_data : '[]';

    ?>
        <div x-data="flipperBuilder(<?php echo esc_js($builder_data); ?>)" class="flipper-builder-wrapper">
            <input type="hidden" name="builder_data" x-bind:value="JSON.stringify(pages)">

            <div class="flipper-sidebar">
                <div class="sidebar-actions">
                    <button type="button" x-on:click="addPages()"><?php esc_html_e('Select Images', 'page-flipper'); ?></button>
                    <button x-on:click="removePages()" x-bind:class="{'disabled': !pages.length}" x-bind:disabled="!pages.length" type="button"><?php esc_html_e('Remove Pages', 'page-flipper'); ?></button>
                </div>

                <template x-if="pages.length">
                    <div class="sidebar-pages">
                        <div class="section-title">
                            <?php esc_html_e('Page List', 'page-flipper'); ?>
                        </div>
                        
                        <ul class="page-list">
                            <template x-for="(page, pageIndex) in pages">
                                <li x-on:click.stop="selectPage(page)" x-bind:class="{'selected': isSelected(page)}">
                                    <button type="button" class="drag-page">
                                        <i class="fa-solid fa-bars"></i>
                                    </button>
                                    <img x-bind:src="page.attachment.url" x-bind:alt="page.attachment.alt">
                                    <span x-text="page.attachment.title" x-bind:title="page.attachment.title"></span>
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
                            <template x-for="hotspot in selectedPage.hotspots" :key="hotspot.id">
                                <div class="hotspot-container" x-bind="buildHotspotInitialAttributes(hotspot)" x-bind:description="hotspot.type === 'narration' ? '<?php esc_attr_e('Remove Narration', 'page-flipper'); ?>' : null">
                                    <template x-if="hotspot.type === 'narration'">
                                        <div x-data="{hover: false}"  x-on:mouseover="hover = true" x-on:mouseover.away="hover = false" x-on:click="removeHotspot(hotspot)">
                                            <i x-show="!hover" class="fa-solid fa-audio-description"></i>
                                            <i x-show="hover" class="fa-solid fa-trash"></i>
                                        </div>
                                    </template>
        
                                    <template x-if="hotspot.type !== 'narration'">
                                        <div x-bind:class="`${hotspot.type}-hotspot`">
                                            <div class="hotspot-extras">
                                                <div class="extras-title">
                                                    <?php esc_html_e('Settings', 'page-flipper'); ?>

                                                    <div class="extras-actions">
                                                        <template x-if="hotspot.type !== 'text' && hotspot.type !== 'link'">
                                                            <button type="button" x-on:click="editHotspotMedia(hotspot)" description="<?php esc_attr_e('Change File', 'page-flipper'); ?>">
                                                                <i class="fa-solid fa-file-pen"></i>
                                                            </button>
                                                        </template>

                                                        <button type="button" x-on:click="removeHotspot(hotspot)" description="<?php esc_attr_e('Remove Hotspot', 'page-flipper'); ?>">
                                                            <i class="fa-solid fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>

                                                <ul>
                                                    <li>
                                                        <label><?php esc_html_e('Display Mode', 'page-flipper'); ?></label>
                                                        <select x-model="hotspot.extras.mode">
                                                            <option value="icon"><?php esc_html_e('Icon', 'page-flipper'); ?></option>
                                                            <option value="inline"><?php esc_html_e('Inline', 'page-flipper'); ?></option>
                                                            <option x-show="hotspot.type === 'link'" value="area"><?php esc_html_e('Stipulated Area', 'page-flipper'); ?></option>
                                                        </select>
                                                    </li>

                                                    <li x-show="hotspot.type === 'video'">
                                                        <label><?php esc_html_e('Show Video Controls', 'page-flipper'); ?></label>
                                                        <select x-model="hotspot.extras.video_controls">
                                                            <option value="yes"><?php esc_html_e('Yes', 'page-flipper'); ?></option>
                                                            <option value="no"><?php esc_html_e('No', 'page-flipper'); ?></option>
                                                        </select>
                                                    </li>

                                                    <li x-show="hotspot.type === 'video'">
                                                        <label><?php esc_html_e('Video Muted', 'page-flipper'); ?></label>
                                                        <select x-model="hotspot.extras.video_muted">
                                                            <option value="yes"><?php esc_html_e('Yes', 'page-flipper'); ?></option>
                                                            <option value="no"><?php esc_html_e('No', 'page-flipper'); ?></option>
                                                        </select>
                                                    </li>

                                                    <li x-show="hotspot.type === 'video'">
                                                        <label><?php esc_html_e('Video Autoplay', 'page-flipper'); ?></label>
                                                        <select x-model="hotspot.extras.video_autoplay">
                                                            <option value="yes"><?php esc_html_e('Yes', 'page-flipper'); ?></option>
                                                            <option value="no"><?php esc_html_e('No', 'page-flipper'); ?></option>
                                                        </select>
                                                    </li>

                                                    <li x-show="hotspot.type === 'video'">
                                                        <label><?php esc_html_e('Video Loop', 'page-flipper'); ?></label>
                                                        <select x-model="hotspot.extras.video_loop">
                                                            <option value="yes"><?php esc_html_e('Yes', 'page-flipper'); ?></option>
                                                            <option value="no"><?php esc_html_e('No', 'page-flipper'); ?></option>
                                                        </select>
                                                    </li>

                                                    <li x-show="hotspot.type === 'link'">
                                                        <label><?php esc_html_e('Action Type', 'page-flipper'); ?></label>
                                                        <select x-model="hotspot.extras.link_type">
                                                            <option value="url"><?php esc_html_e('Link Url', 'page-flipper'); ?></option>
                                                            <option value="anchor"><?php esc_html_e('Page Anchor', 'page-flipper'); ?></option>
                                                        </select>
                                                    </li>

                                                    <li x-show="hotspot.type === 'link' && hotspot.extras.link_type === 'url'">
                                                        <label><?php esc_html_e('Url', 'page-flipper'); ?></label>
                                                        <input type="url" x-model="hotspot.extras.link_url">
                                                    </li>

                                                    <li x-show="hotspot.type === 'link' && hotspot.extras.link_type === 'anchor'">
                                                        <label><?php esc_html_e('Page', 'page-flipper'); ?></label>
                                                        <select x-model="hotspot.extras.link_page">
                                                            <option value=""><?php esc_html_e('Select Page', 'page-flipper'); ?></option>
                                                            <template x-for="page in pages">
                                                                <option x-bind:value="page.id" x-bind:selected="hotspot.extras.link_page === page.id" x-text="page.attachment.title"></option>
                                                            </template>
                                                        </select>
                                                    </li>

                                                    <li x-show="hotspot.type === 'link'">
                                                        <template x-if="hotspot.extras.mode === 'inline'">
                                                            <label><?php esc_html_e('Link Text', 'page-flipper'); ?></label>
                                                        </template>

                                                        <template x-if="hotspot.extras.mode !== 'inline'">
                                                            <label><?php esc_html_e('Link Title', 'page-flipper'); ?></label>
                                                        </template>
                                                        
                                                        <input type="text" x-model="hotspot.extras.link_text">
                                                    </li>

                                                    <li x-show="hotspot.type === 'link' && hotspot.extras.link_type === 'url'">
                                                        <label><?php esc_html_e('Link Target', 'page-flipper'); ?></label>
                                                        <select x-model="hotspot.extras.link_target">
                                                            <option value="_blank"><?php esc_html_e('Blank Page', 'page-flipper'); ?></option>
                                                            <option value="_self"><?php esc_html_e('Same Page', 'page-flipper'); ?></option>
                                                        </select>
                                                    </li>

                                                    <li x-show="hotspot.type === 'text' || (hotspot.type === 'link' && hotspot.extras.mode === 'inline')">
                                                        <label><?php esc_html_e('Font Size', 'page-flipper'); ?> <small>(px)</small></label>
                                                        <input type="number" x-model="hotspot.extras.font_size">
                                                    </li>

                                                    <li x-show="hotspot.type === 'text' || (hotspot.type === 'link' && hotspot.extras.mode === 'inline')">
                                                        <label><?php esc_html_e('Font Family', 'page-flipper'); ?> <small>(<?php esc_html_e('Example', 'page-flipper'); ?>: Lora, Arial)</small></label>
                                                        <input type="text" x-model="hotspot.extras.font_family">
                                                    </li>

                                                    <li x-show="hotspot.type === 'text' || (hotspot.type === 'link' && hotspot.extras.mode === 'inline')">
                                                        <label><?php esc_html_e('Font Color', 'page-flipper'); ?></label>
                                                        <input type="color" x-model="hotspot.extras.font_color">
                                                    </li>

                                                    <li x-show="hotspot.type === 'text' || (hotspot.type === 'link' && hotspot.extras.mode === 'inline')">
                                                        <label><?php esc_html_e('Font Weight', 'page-flipper'); ?></label>
                                                        <select x-model="hotspot.extras.font_weight">
                                                            <option value="normal"><?php esc_html_e('Normal', 'page-flipper'); ?></option>
                                                            <option value="lighter"><?php esc_html_e('Lighter', 'page-flipper'); ?></option>
                                                            <option value="bold"><?php esc_html_e('Bold', 'page-flipper'); ?></option>
                                                            <option value="bolder"><?php esc_html_e('Bolder', 'page-flipper'); ?></option>
                                                        </select>
                                                    </li>

                                                    <li x-show="hotspot.type === 'text' || (hotspot.type === 'link' && hotspot.extras.mode === 'inline')">
                                                        <label><?php esc_html_e('Text Decoration', 'page-flipper'); ?></label>
                                                        <select x-model="hotspot.extras.text_decoration">
                                                            <option value="none"><?php esc_html_e('None', 'page-flipper'); ?></option>
                                                            <option value="underline"><?php esc_html_e('Underline', 'page-flipper'); ?></option>
                                                            <option value="line-through"><?php esc_html_e('Line Through', 'page-flipper'); ?></option>
                                                        </select>
                                                    </li>

                                                    <li x-show="hotspot.type === 'text'">
                                                        <label><?php esc_html_e('Text Align', 'page-flipper'); ?></label>
                                                        <select x-model="hotspot.extras.text_align">
                                                            <option value="left"><?php esc_html_e('Left', 'page-flipper'); ?></option>
                                                            <option value="right"><?php esc_html_e('Right', 'page-flipper'); ?></option>
                                                            <option value="center"><?php esc_html_e('Center', 'page-flipper'); ?></option>
                                                            <option value="justify"><?php esc_html_e('Justify', 'page-flipper'); ?></option>
                                                        </select>
                                                    </li>

                                                    <li x-show="hotspot.type === 'text'">
                                                        <label><?php esc_html_e('Content', 'page-flipper'); ?></label>
                                                        <textarea x-model="hotspot.extras.content" rows="5"></textarea>
                                                    </li>

                                                    <li x-show="hotspot.extras.mode === 'icon'">
                                                        <label><?php esc_html_e('Icon Border Radius', 'page-flipper'); ?></label>
                                                        <select x-model="hotspot.extras.icon_border">
                                                            <option value="50%"><?php esc_html_e('Rounded', 'page-flipper'); ?></option>
                                                            <option value="5px"><?php esc_html_e('Smooth', 'page-flipper'); ?></option>
                                                            <option value="0px"><?php esc_html_e('Sharp', 'page-flipper'); ?></option>
                                                            <option value="custom"><?php esc_html_e('Custom', 'page-flipper'); ?></option>
                                                        </select>
                                                    </li>

                                                    <li x-show="hotspot.extras.icon_border === 'custom'">
                                                        <label><?php esc_html_e('Custom Radius', 'page-flipper'); ?> <small>(<?php esc_html_e('Example', 'page-flipper'); ?>: 50% 0 10px 0)</small></label>
                                                        <input type="text" x-model="hotspot.extras.icon_border_custom">
                                                    </li>

                                                    <li x-show="hotspot.extras.mode === 'icon'">
                                                        <label><?php esc_html_e('Icon Color', 'page-flipper'); ?></label>
                                                        <input type="color" x-model="hotspot.extras.icon_color">
                                                    </li>

                                                    <li x-show="hotspot.extras.mode === 'icon'">
                                                        <label><?php esc_html_e('Icon Background Color', 'page-flipper'); ?></label>
                                                        <input type="color" x-model="hotspot.extras.icon_background">
                                                    </li>

                                                    <li x-show="hotspot.extras.mode === 'icon' && hotspot.type !== 'link' && hotspot.type !== 'audio'">
                                                        <label><?php esc_html_e('Popover Background Color', 'page-flipper'); ?></label>
                                                        <input type="color" x-model="hotspot.extras.popover_background">
                                                    </li>

                                                    <li x-show="hotspot.extras.mode === 'icon'">
                                                        <label><?php esc_html_e('Icon Name', 'page-flipper'); ?> <a href="https://fontawesome.com/icons" target="_blank"><small>(Font Awesome Icons)</small></a></label>
                                                        <input type="text" x-model="hotspot.extras.icon_name">
                                                    </li>

                                                    <li x-show="hotspot.extras.mode === 'icon' && hotspot.type === 'audio'">
                                                        <label><?php esc_html_e('Pause Icon Name', 'page-flipper'); ?> <a href="https://fontawesome.com/icons" target="_blank"><small>(Font Awesome Icons)</small></a></label>
                                                        <input type="text" x-model="hotspot.extras.pause_icon_name">
                                                    </li>

                                                    <li x-show="hotspot.extras.mode === 'icon'">
                                                        <label><?php esc_html_e('Icon Size', 'page-flipper'); ?> <small>(px)</small></label>
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

                                                        <template x-if="hotspot.type === 'link'">
                                                            <a
                                                                x-text="hotspot.extras.link_text ? hotspot.extras.link_text : hotspot.extras.link_url"
                                                                x-bind:href="hotspot.extras.link_url"
                                                                x-bind:target="hotspot.extras.link_target"
                                                                x-bind:style="{
                                                                    'color': hotspot.extras.font_color,
                                                                    'font-size': `${hotspot.extras.font_size}px`,
                                                                    'font-family': hotspot.extras.font_family,
                                                                    'font-weight': hotspot.extras.font_weight,
                                                                    'text-decoration': hotspot.extras.text_decoration,
                                                                }"
                                                            ></a>
                                                        </template>
                                                    </div>
                                                </template>

                                                <template x-if="hotspot.extras.mode === 'area' && hotspot.type === 'link'">
                                                    <div class="area-mode-hotspot">
                                                        <i class="fa-solid fa-link"></i>
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
                        <button type="button" description="<?php esc_attr_e('Narration', 'page-flipper'); ?>" x-on:click="addHotspot('narration')">
                            <i class="fa-solid fa-audio-description"></i>
                        </button>
                        <button type="button" description="<?php esc_attr_e('Audio', 'page-flipper'); ?>" x-on:click="addHotspot('audio')">
                            <i class="fa-solid fa-volume-high"></i>
                        </button>
                        <button type="button" description="<?php esc_attr_e('Video', 'page-flipper'); ?>" x-on:click="addHotspot('video')">
                            <i class="fa-solid fa-video"></i>
                        </button>
                        <button type="button" description="<?php esc_attr_e('Image', 'page-flipper'); ?>" x-on:click="addHotspot('image')">
                            <i class="fa-solid fa-image"></i>
                        </button>
                        <button type="button" description="<?php esc_attr_e('Text', 'page-flipper'); ?>" x-on:click="addHotspot('text')">
                            <i class="fa-solid fa-font"></i>
                        </button>
                        <button type="button" description="<?php esc_attr_e('Hyperlink', 'page-flipper'); ?>" x-on:click="addHotspot('link')">
                            <i class="fa-solid fa-link"></i>
                        </button>
                    </div>
                </div>
            </template>
        </div>
    <?php
}

function wa_page_flipper_builder_meta_box_save( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( ! isset( $_POST['wa_page_flipper_builder_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wa_page_flipper_builder_nonce'] ) ), 'wa_page_flipper_builder_nonce_action' ) ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    if ( isset( $_POST['builder_data'] ) ) {
        $builder_data_raw = wp_unslash( $_POST['builder_data'] );

        if ( is_string( $builder_data_raw ) ) {
            $builder_data = json_decode( $builder_data_raw, true );

            if ( json_last_error() === JSON_ERROR_NONE && is_array( $builder_data ) ) {
                array_walk_recursive( $builder_data, function ( &$value ) {
                    if ( is_string( $value ) ) {
                        $value = sanitize_text_field( $value );
                    }
                });

                update_post_meta(
                    $post_id,
                    '_wa_page_flipper_builder_data',
                    wp_json_encode( $builder_data, JSON_UNESCAPED_UNICODE )
                );
            }
        }
    }
}
add_action( 'save_post', 'wa_page_flipper_builder_meta_box_save' );
