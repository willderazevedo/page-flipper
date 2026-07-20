<?php
if (!defined('ABSPATH')) exit;

$waPageFlipperPostId                = esc_attr($atts['id']);
$waPageFlipperRelatedPosts          = wa_page_flipper_get_related_posts($waPageFlipperPostId);
$waPageFlipperPostTitle             = get_the_title($waPageFlipperPostId);
$waPageFlipperPostUrl               = get_permalink($waPageFlipperPostId);
$waPageFlipperEnableSummary         = $atts['enable_summary'] === 'yes';
$waPageFlipperEnableRelated         = $atts['enable_related'] === 'yes';
$waPageFlipperEnableControls        = $atts['enable_controls'] === 'yes';
$waPageFlipperEnableShare           = $atts['enable_share'] === 'yes';
$waPageFlipperEnableZoom            = $atts['enable_zoom'] === 'yes';
$waPageFlipperEnableBackgroundImage = $atts['enable_background_image'] === 'yes';
$waPageFlipperBackgroundColor       = $atts['page_background_color'];
$waPageFlipperSurfaceColor          = $atts['page_surface_color'];
$waPageFlipperSurfaceAccentColor    = $atts['page_surface_accent_color'];
$waPageFlipperAccentColor           = $atts['page_accent_color'];
$waPageFlipperFontColor             = $atts['page_font_color'];
$waPageFlipperBackgroundImage       = $waPageFlipperEnableBackgroundImage ? (json_decode(get_post_meta($waPageFlipperPostId, '_wa_page_flipper_background_data', true), true)['url'] ?? null) : null;
$waPageFlipperBuilderData           = get_post_meta($waPageFlipperPostId, '_wa_page_flipper_builder_data', true);
$waPageFlipperPdfAttachment         = get_post_meta($waPageFlipperPostId, '_wa_page_flipper_pdf_data', true);
$waPageFlipperBuilderData           = $waPageFlipperBuilderData ?: null;
$waPageFlipperPdfAttachment         = $waPageFlipperPdfAttachment ?: null;
?>

<?php if ($waPageFlipperBuilderData) : ?>
    <div 
        x-data="flipperWidget(<?php echo esc_js($waPageFlipperBuilderData ?? '[]'); ?>, <?php echo esc_js($waPageFlipperPdfAttachment ?? 'null'); ?>)"
        x-bind:style="{'--page-surface-elevation': pages.length + 1}"
        class="flipper-widget-wrapper"
        style="
            --page-bg-image: url(<?php echo esc_attr($waPageFlipperBackgroundImage); ?>);
            --page-bg-color: <?php echo esc_attr($waPageFlipperBackgroundColor); ?>;
            --page-surface-color: <?php echo esc_attr($waPageFlipperSurfaceColor); ?>;
            --page-surface-accent-color: <?php echo esc_attr($waPageFlipperSurfaceAccentColor); ?>;
            --page-accent-color: <?php echo esc_attr($waPageFlipperAccentColor); ?>;
            --page-font-color: <?php echo esc_attr($waPageFlipperFontColor); ?>;
        "
    >

        <?php if ($waPageFlipperBackgroundImage) : ?>
            <div class="flipper-widget-background"></div>
        <?php endif; ?>

        <?php if ($waPageFlipperEnableSummary) : ?>
            <template x-if="!narrationActive">
                <div class="flipper-summary-wrapper" x-bind:class="{'active': summaryActive}">
                    <button x-on:click="summaryActive = !summaryActive" type="button" class="summary-toggler">
                        <i x-show="!summaryActive" class="fas fa-bars" aria-hidden="true"></i>
                        <i x-show="summaryActive" class="fas fa-xmark" aria-hidden="true"></i>
                    </button>
        
                    <div class="summary-pages">
                        <template x-for="(page, index) in pages">
                            <div class="page" x-on:click="goToPage(index + 1)">
                                <img x-bind:src="page.attachment.url" alt="page.attachment.alt">
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        <?php endif; ?>

        <div class="flipper-actions">
            <?php if ($waPageFlipperEnableZoom) : ?>
                <button x-on:click="toggleZoom($event, true)" x-bind:disabled="narrationActive" type="button">
                    <i x-show="!zoomActive" class="fas fa-search-plus"></i>
                    <i x-show="zoomActive" class="fas fa-search-minus"></i>
                </button>
            <?php endif; ?>

            <button type="button" class="narration-toggler" x-on:click="narrationActive ? stopNarration() : startNarration()" x-bind:disabled="!hasNarration" x-bind:description="!hasNarration ? '<?php esc_attr_e('No Audio Description in Current Page', 'page-flipper'); ?>' : `<?php esc_attr_e('Page', 'page-flipper'); ?> ${actualPage} - ${timeString(narrationCurrentTime)}/${narrationDuration}`">
                <i x-show="!narrationActive" class="fa-solid fa-play"></i>
                <i x-show="narrationActive" class="fa-solid fa-pause"></i>
            </button>

            <button type="button" x-on:click="downloadPdfFile()" x-bind:disabled="pdfFile === null" x-bind:description="pdfFile === null ? '<?php esc_attr_e('No PDF file available', 'page-flipper'); ?>' : '<?php esc_attr_e('Download PDF File', 'page-flipper'); ?>'">
                <i class="fas fa-file-pdf"></i>
            </button>

            <?php if ($waPageFlipperEnableRelated) : ?>
                <button type="button" class="related-toggler" <?php if (!$waPageFlipperRelatedPosts): ?> disabled <?php endif; ?> x-bind:class="{'active': relatedActive}" x-on:click="relatedActive = !relatedActive" description="<?php $waPageFlipperRelatedPosts ? esc_attr_e('Available Editions', 'page-flipper') : esc_attr_e('No other editions available', 'page-flipper') ; ?>">
                    <i class="fa-solid fa-ellipsis"></i>
                </button>
            <?php endif; ?>

            <div class="flipper-pagination">
                <input type="number" class="actual-page" x-model:value="actualPage" x-on:focus="$event.target.select()" x-on:input.debounce.300ms="goToPage(actualPage)" x-bind:disabled="narrationActive" step="1" maxlength="3">
                <span class="pages-separator">-</span>
                <input type="number" class="total-pages" x-bind:value="pages.length" readonly> 
            </div>
        </div>
        
        <?php if ($waPageFlipperEnableControls) : ?>
            <template x-if="!narrationActive">
                <div class="flipper-controls">
                    <button type="button" class="previous-page" x-on:click="previousPage()">
                        <i class="fa-solid fa-angle-left"></i>
                    </button>
                    
                    <button type="button" class="next-page" x-on:click="nextPage()">
                        <i class="fa-solid fa-angle-right"></i>
                    </button>
                </div>
            </template>
        <?php endif; ?>

        <?php if ($waPageFlipperEnableShare) : ?>
            <div class="flipper-share-buttons">
                <button type="button" data-sharer="facebook" data-title="<?php echo esc_attr($waPageFlipperPostTitle); ?>" data-url="<?php echo esc_attr($waPageFlipperPostUrl); ?>">
                    <i class="fa-brands fa-facebook-f"></i>
                </button>

                <button type="button" data-sharer="x" data-title="<?php echo esc_attr($waPageFlipperPostTitle); ?>" data-url="<?php echo esc_attr($waPageFlipperPostUrl); ?>">
                    <i class="fa-brands fa-x-twitter"></i>
                </button>

                <button type="button" data-sharer="whatsapp" data-title="<?php echo esc_attr($waPageFlipperPostTitle); ?>" data-url="<?php echo esc_attr($waPageFlipperPostUrl); ?>">
                    <i class="fa-brands fa-whatsapp"></i>
                </button>

                <button type="button" data-sharer="telegram" data-title="<?php echo esc_attr($waPageFlipperPostTitle); ?>" data-url="<?php echo esc_attr($waPageFlipperPostUrl); ?>">
                    <i class="fa-brands fa-telegram"></i>
                </button>

                <button type="button" data-sharer="email" data-title="<?php echo esc_attr($waPageFlipperPostTitle); ?>" data-url="<?php echo esc_attr($waPageFlipperPostUrl); ?>">
                    <i class="fa-solid fa-envelope"></i>
                </button>
            </div>
        <?php endif; ?>

        <?php if ($waPageFlipperEnableRelated && $waPageFlipperRelatedPosts): ?>
            <div class="related-posts-wrapper" x-bind:class="{'active': relatedActive}">
                <div class="related-posts">
                    <div class="title"><?php esc_html_e('Available Editions', 'page-flipper'); ?></div>
    
                    <div class="related-posts-list">
                        <?php foreach ($waPageFlipperRelatedPosts as $relatedPost): ?>
                            <?php if (!has_post_thumbnail($relatedPost->ID)) continue; ?>
    
                            <a href="<?php echo esc_attr(get_permalink($relatedPost->ID)); ?>">
                                <img src="<?php echo esc_attr(wp_get_attachment_image_src(get_post_thumbnail_id($relatedPost->ID), 'full')[0]); ?>" alt="<?php echo esc_attr($relatedPost->post_title); ?>">
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="flipper-pages-wrapper" x-bind:class="{'zooming': zoomActive}">
            <div class="flipper-pages">
                <?php foreach ([1,2] as $index) : ?>
                    <div class="page change">
                        <i class="fa-solid fa-circle-notch fa-spin fa-2x"></i>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif; ?>
