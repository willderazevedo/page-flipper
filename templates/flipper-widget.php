<?php
if (!defined('ABSPATH')) exit;

$postId                = esc_attr($atts['id']);
$relatedPosts          = wa_page_flipper_get_related_posts($postId);
$postTitle             = esc_attr(get_the_title($postId));
$postUrl               = esc_attr(get_permalink($postId));
$enableSummary         = esc_attr($atts['enable_summary']) === 'yes';
$enableRelated         = esc_attr($atts['enable_related']) === 'yes';
$enableControls        = esc_attr($atts['enable_controls']) === 'yes';
$enableShare           = esc_attr($atts['enable_share']) === 'yes';
$enableZoom            = esc_attr($atts['enable_zoom']) === 'yes';
$enableBackgroundImage = esc_attr($atts['enable_background_image']) === 'yes';
$backgroundColor       = esc_attr($atts['page_background_color']);
$surfaceColor          = esc_attr($atts['page_surface_color']);
$surfaceAccentColor    = esc_attr($atts['page_surface_accent_color']);
$accentColor           = esc_attr($atts['page_accent_color']);
$fontColor             = esc_attr($atts['page_font_color']);
$backgroundImage       = has_post_thumbnail($postId) && $enableBackgroundImage ? wp_get_attachment_image_src(get_post_thumbnail_id($postId), 'full')[0] : null;
$builderData           = get_post_meta($postId, '_wa_page_flipper_builder_data', true);
$pdfAttachment         = get_post_meta($postId, '_wa_page_flipper_pdf_data', true);
$builderData           = $builderData ?: null;
$pdfAttachment         = $pdfAttachment ?: null;
?>

<?php if ($builderData) : ?>
    <div 
        x-data="flipperWidget(<?php echo esc_js($builderData ?? '[]'); ?>, <?php echo esc_js($pdfAttachment ?? 'null'); ?>)"
        class="flipper-widget-wrapper"
        style="
            --page-bg-image: url(<?php echo esc_attr($backgroundImage); ?>);
            --page-bg-color: <?php echo esc_attr($backgroundColor); ?>;
            --page-surface-color: <?php echo esc_attr($surfaceColor); ?>;
            --page-surface-accent-color: <?php echo esc_attr($surfaceAccentColor); ?>;
            --page-accent-color: <?php echo esc_attr($accentColor); ?>;
            --page-font-color: <?php echo esc_attr($fontColor); ?>;
        "
    >

        <?php if ($backgroundImage) : ?>
            <div class="flipper-widget-background"></div>
        <?php endif; ?>

        <?php if ($enableSummary) : ?>
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
            <?php if ($enableZoom) : ?>
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

            <?php if ($enableRelated) : ?>
                <button type="button" class="related-toggler" <?php if (!$relatedPosts): ?> disabled <?php endif; ?> x-bind:class="{'active': relatedActive}" x-on:click="relatedActive = !relatedActive" description="<?php $relatedPosts ? esc_attr_e('Available Editions', 'page-flipper') : esc_attr_e('No other editions available', 'page-flipper') ; ?>">
                    <i class="fa-solid fa-ellipsis"></i>
                </button>
            <?php endif; ?>

            <div class="flipper-pagination">
                <input type="number" class="actual-page" x-model:value="actualPage" x-on:focus="$event.target.select()" x-on:input.debounce.300ms="goToPage(actualPage)" x-bind:disabled="narrationActive" step="1" maxlength="3">
                <span class="pages-separator">-</span>
                <input type="number" class="total-pages" x-bind:value="pages.length" readonly> 
            </div>
        </div>
        
        <?php if ($enableControls) : ?>
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

        <?php if ($enableShare) : ?>
            <div class="flipper-share-buttons">
                <button type="button" data-sharer="facebook" data-title="<?php echo $postTitle; ?>" data-url="<?php echo $postUrl; ?>">
                    <i class="fa-brands fa-facebook-f"></i>
                </button>

                <button type="button" data-sharer="x" data-title="<?php echo $postTitle; ?>" data-url="<?php echo $postUrl; ?>">
                    <i class="fa-brands fa-x-twitter"></i>
                </button>

                <button type="button" data-sharer="whatsapp" data-title="<?php echo $postTitle; ?>" data-url="<?php echo $postUrl; ?>">
                    <i class="fa-brands fa-whatsapp"></i>
                </button>

                <button type="button" data-sharer="telegram" data-title="<?php echo $postTitle; ?>" data-url="<?php echo $postUrl; ?>">
                    <i class="fa-brands fa-telegram"></i>
                </button>

                <button type="button" data-sharer="email" data-title="<?php echo $postTitle; ?>" data-url="<?php echo $postUrl; ?>">
                    <i class="fa-solid fa-envelope"></i>
                </button>
            </div>
        <?php endif; ?>

        <?php if ($enableRelated && $relatedPosts): ?>
            <div class="related-posts-wrapper" x-bind:class="{'active': relatedActive}">
                <div class="related-posts">
                    <div class="title"><?php esc_html_e('Available Editions', 'page-flipper'); ?></div>
    
                    <div class="related-posts-list">
                        <?php foreach ($relatedPosts as $relatedPost): ?>
                            <?php if (!has_post_thumbnail($relatedPost->ID)) continue; ?>
    
                            <a href="<?php echo get_permalink($relatedPost->ID); ?>">
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