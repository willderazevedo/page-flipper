<?php
if (!defined('ABSPATH')) exit;

$postId        = esc_attr($atts['id']);
$showSummary   = esc_attr($atts['summary']) === 'yes';
$showActionBar = esc_attr($atts['action_bar']) === 'yes';
$showControls  = esc_attr($atts['controls']) === 'yes';
$goBackUrl     = wp_get_referer();
$data          = get_post_meta($postId, '_flipper_builder_data', true);
$data          = !empty($data) ? esc_attr($data) : '[]';
?>
<div x-data="flipperWidget(<?php echo $data; ?>)" class="flipper-widget-wrapper">
    <?php if ($showActionBar) : ?>
        <div class="flipper-action-bar">
            <div class="flipper-actions actions-left">
                <?php if ($goBackUrl) : ?>
                    <a href="<?php echo esc_url($goBackUrl); ?>" description="<?php _e('Go back', 'page-flipper'); ?>">
                        <i class="fa-solid fa-angle-left"></i>
                    </a>
                <?php endif; ?>
            </div>

            <div class="flipper-pagination">
                <input type="tel" x-model:value="actualPage" x-on:focus="$event.target.select()" x-on:input.debounce.500ms="goToPage(actualPage)" x-bind:disabled="narrationActive" maxlength="3">
                <span>/</span>
                <input type="tel" x-bind:value="pages.length" readonly> 
            </div>
            
            <div class="flipper-actions actions-right">
                <button type="button" class="active" x-on:click="narrationActive ? stopNarration() : startNarration()" x-bind:disabled="!hasNarration" x-bind:description="!hasNarration ? '<?php _e('No Audio Description in Current Page', 'page-flipper'); ?>' : (narrationActive ? '<?php _e('Pause Audio Description', 'page-flipper'); ?>' : '<?php _e('Play Audio Description', 'page-flipper'); ?>')">
                    <i x-show="!narrationActive" class="fa-solid fa-play"></i>
                    <i x-show="narrationActive" class="fa-solid fa-pause"></i>

                    <span class="narration-time" x-text="`<?php _e('Page', 'page-flipper'); ?> ${actualPage} - ${timeString(narrationCurrentTime)}/${narrationDuration}`"></span>
                </button>
                
                <button x-on:click="toggleZoom($event, true)" x-bind:disabled="narrationActive" type="button" description="<?php _e('Zoom', 'page-flipper'); ?>">
                    <i x-show="!zoomActive" class="fas fa-search-plus"></i>
                    <i x-show="zoomActive" class="fas fa-search-minus"></i>
                </button>
                
                <button type="button" description="<?php _e('Download PDF File', 'page-flipper'); ?>">
                    <i class="fas fa-file-pdf"></i>
                </button>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if ($showSummary) : ?>
        <template x-if="!narrationActive">
            <div class="flipper-summary-wrapper" x-bind:class="{'active': summaryActive}">
                <button x-on:click="summaryActive = !summaryActive" type="button" class="summary-toggler" description="<?php _e('Summary', 'page-flipper'); ?>">
                    <i x-show="!summaryActive" class="fas fa-angle-right" aria-hidden="true"></i>
                    <i x-show="summaryActive" class="fas fa-angle-left" aria-hidden="true"></i>
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
    
    <?php if ($showControls) : ?>
        <template x-if="!narrationActive">
            <div class="flipper-controls">
                <button type="button" x-on:click="previousPage()">
                    <i class="fa-solid fa-angle-left"></i>
                </button>
                
                <button type="button" x-on:click="nextPage()">
                    <i class="fa-solid fa-angle-right"></i>
                </button>
            </div>
        </template>
    <?php endif; ?>
    
    <div class="flipper-pages-wrapper" x-bind:class="{'zooming': zoomActive}">
        <div class="flipper-pages">
            <?php foreach ([1,2] as $index) : ?>
                <div class="page change">
                    <i class="fa-solid fa-circle-notch fa-spin fa-2x"></i>
                </div>
            <? endforeach; ?>
        </div>
    </div>
</div>