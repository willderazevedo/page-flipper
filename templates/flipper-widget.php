<?php
if (!defined('ABSPATH')) exit;

$postId        = esc_attr($atts['id']);
$showSummary   = esc_attr($atts['summary']) === 'yes';
$showActionBar = esc_attr($atts['action_bar']) === 'yes';
$showControls  = esc_attr($atts['controls']) === 'yes';
$goBackUrl     = wp_get_referer();
$data          = get_post_meta($post->ID, '_flipper_builder_data', true);
$data          = !empty($data) ? esc_attr($data) : '[]';
?>
<div x-data="flipperWidget(<?php echo $data; ?>)" class="flipper-widget-wrapper">
    <?php if ($showActionBar) : ?>
        <div class="flipper-action-bar">
            <div class="flipper-actions actions-left">
                <?php if ($goBackUrl) : ?>
                    <a href="<?php echo esc_url($referer); ?>" description="<?php _e('Go back', 'page-flipper'); ?>">
                        <i class="fa-solid fa-house"></i>
                    </a>
                <?php endif; ?>
            </div>

            <div class="flipper-pagination">
                <input type="tel" value="1" maxlength="3">
                <span>/</span>
                <input type="tel" value="2" readonly> 
            </div>
            
            <div class="flipper-actions actions-right">
                <button type="button" description="<?php _e('Play Audio Description', 'page-flipper'); ?>">
                    <i class="fa-solid fa-audio-description"></i>
                </button>
                
                <button type="button" description="<?php _e('Zoom', 'page-flipper'); ?>">
                    <i class="fas fa-search-plus"></i>
                </button>
                
                <button type="button" description="<?php _e('Download PDF File', 'page-flipper'); ?>">
                    <i class="fas fa-file-pdf"></i>
                </button>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if ($showSummary) : ?>
        <div class="flipper-summary-wrapper">
            <button type="button" class="summary-toggler" description="<?php _e('Summary', 'page-flipper'); ?>">
                <i class="fas fa-angle-right" aria-hidden="true"></i>
            </button>

            <div class="summary-pages">
                <div class="page">
                    <img src="https://mis-ce.org.br/storage/publication/24/pages/2727/oxy8EASqQKjU0yKXQ368WM6iDkrSKXYg9yY6QBzH.jpg" alt="Page 1" width="100%">
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if ($showControls) : ?>
        <div class="flipper-controls">
            <button type="button">
                <i class="fa-solid fa-angle-left"></i>
            </button>
            
            <button type="button">
                <i class="fa-solid fa-angle-right"></i>
            </button>
        </div>
    <?php endif; ?>
    
    <div class="flipper-pages"></div>
</div>