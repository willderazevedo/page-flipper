<?php
/**
 * Plugin Name: Page Flipper
 * Plugin URI:  https://github.com/willderazevedo/page-flipper
 * Description: The Page Flipper is a free WordPress plugin that enables the creation of interactive digital books. It adds a new post type for digital books, offering a set of features to manage books and add interactivity with hotspots.
 * Version:     1.0.2
 * Author:      Willder Azevedo
 * Author URI:  https://github.com/willderazevedo
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: page-flipper
 * Domain Path: /languages
 * 
 * @package PageFlipper
 * @category Plugin
 * 
 * Page Flipper is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 * 
 * Page Flipper is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once plugin_dir_path( __FILE__ ) . 'fields/flipper-builder.php';
require_once plugin_dir_path( __FILE__ ) . 'fields/flipper-pdf.php';
require_once plugin_dir_path( __FILE__ ) . 'fields/flipper-shortcode.php';

function wa_page_flipper_post_type_register() {
    // Registrar Custom Post Type: Digital Books
    $labels = [
        'name'               => __( 'Digital Books', 'page-flipper' ),
        'singular_name'      => __( 'Digital Book', 'page-flipper' ),
        'menu_name'          => __( 'Digital Books', 'page-flipper' ),
        'name_admin_bar'     => __( 'Digital Book', 'page-flipper' ),
        'add_new'            => __( 'Add New', 'page-flipper' ),
        'add_new_item'       => __( 'Add New Digital Book', 'page-flipper' ),
        'new_item'           => __( 'New Digital Book', 'page-flipper' ),
        'edit_item'          => __( 'Edit Digital Book', 'page-flipper' ),
        'view_item'          => __( 'View Digital Book', 'page-flipper' ),
        'all_items'          => __( 'All Digital Books', 'page-flipper' ),
        'search_items'       => __( 'Search Digital Book', 'page-flipper' ),
        'not_found'          => __( 'No Digital Books found', 'page-flipper' ),
        'not_found_in_trash' => __( 'No Digital Books found in trash', 'page-flipper' ),
    ];

    $args = [
        'label'         => __( 'Digital Books', 'page-flipper' ),
        'labels'        => $labels,
        'public'        => true,
        'show_in_menu'  => true,
        'menu_position' => 6,
        'menu_icon'     => 'dashicons-book',
        'supports'      => [ 'title', 'thumbnail' ],
        'hierarchical'  => false,
        'has_archive'   => true,
        'rewrite'       => [ 'slug' => 'digital-books' ],
        'show_in_rest'  => true, 
    ];

    register_post_type('wa_page_flipper', $args);

    // Registrar Taxonomia: Categories
    $category_labels = [
        'name'              => __( 'Categories', 'page-flipper' ),
        'singular_name'     => __( 'Category', 'page-flipper' ),
        'search_items'      => __( 'Search Categories', 'page-flipper' ),
        'all_items'         => __( 'All Categories', 'page-flipper' ),
        'edit_item'         => __( 'Edit Category', 'page-flipper' ),
        'update_item'       => __( 'Update Category', 'page-flipper' ),
        'add_new_item'      => __( 'Add New Category', 'page-flipper' ),
        'new_item_name'     => __( 'New Category Name', 'page-flipper' ),
        'menu_name'         => __( 'Categories', 'page-flipper' ),
    ];

    $category_args = [
        'labels'            => $category_labels,
        'public'            => true,
        'hierarchical'      => true,
        'show_admin_column' => true,
        'rewrite'           => [ 'slug' => 'digital-books-category' ],
        'show_in_rest'      => true,
    ];

    register_taxonomy('wa_page_flipper_category', 'wa_page_flipper', $category_args);

    // Registrar Taxonomia: Authors
    $author_labels = [
        'name'              => __( 'Authors', 'page-flipper' ),
        'singular_name'     => __( 'Author', 'page-flipper' ),
        'search_items'      => __( 'Search Authors', 'page-flipper' ),
        'all_items'         => __( 'All Authors', 'page-flipper' ),
        'edit_item'         => __( 'Edit Author', 'page-flipper' ),
        'update_item'       => __( 'Update Author', 'page-flipper' ),
        'add_new_item'      => __( 'Add New Author', 'page-flipper' ),
        'new_item_name'     => __( 'New Author Name', 'page-flipper' ),
        'menu_name'         => __( 'Authors', 'page-flipper' ),
    ];

    $author_args = [
        'labels'            => $author_labels,
        'public'            => true,
        'hierarchical'      => true,
        'show_admin_column' => true,
        'rewrite'           => [ 'slug' => 'digital-books-author' ],
        'show_in_rest'      => true,
    ];

    register_taxonomy('wa_page_flipper_author', 'wa_page_flipper', $author_args);
}

add_action( 'init', 'wa_page_flipper_post_type_register' );

function wa_page_flipper_admin_assets() {
    wp_enqueue_script('flipper-alpine-lib', plugin_dir_url(__FILE__) . 'assets/libs/alpine.js', ['flipper-script'], '3.14.8', true);
    wp_enqueue_script('flipper-sortable-lib', plugin_dir_url(__FILE__) . 'assets/libs/sortable.js', [], '1.15.6', true);
    wp_enqueue_script('flipper-interact-lib', plugin_dir_url(__FILE__) . 'assets/libs/interact.js', [], '1.10.27', true);
    wp_enqueue_style('flipper-style', plugin_dir_url(__FILE__) . 'assets/admin/style.css', [], '1.0.2');
    wp_enqueue_style('flipper-icons-lib', plugin_dir_url(__FILE__) . 'assets/libs/fontawesome.css', [], '6.5.1');
    wp_enqueue_script('flipper-script', plugin_dir_url(__FILE__) . 'assets/admin/main.js', ['flipper-sortable-lib', 'flipper-interact-lib'], '1.0.2', true);
}

add_action('admin_enqueue_scripts', 'wa_page_flipper_admin_assets');

function wa_page_flipper_frontend_assets() {
    wp_enqueue_script('flipper-alpine-lib', plugin_dir_url(__FILE__) . 'assets/libs/alpine.js', ['flipper-script'], '3.14.8', true);
    wp_enqueue_script('flipper-turnjs-lib', plugin_dir_url(__FILE__) . 'assets/libs/turnjs.js', ['jquery'], '4.1.0', true);
    wp_enqueue_script('flipper-zoom-lib', plugin_dir_url(__FILE__) . 'assets/libs/zoom.js', [], '4.1.0', true);
    wp_enqueue_script('flipper-bootstrap-lib', plugin_dir_url(__FILE__) . 'assets/libs/bootstrap.js', [], '0.5.2', true);
    wp_enqueue_script('flipper-sharer-lib', plugin_dir_url(__FILE__) . 'assets/libs/sharer.js', [], '5.3.3', true);
    wp_enqueue_style('flipper-style', plugin_dir_url(__FILE__) . 'assets/frontend/style.css', [], '1.0.2');
    wp_enqueue_style('flipper-icons-lib', plugin_dir_url(__FILE__) . 'assets/libs/fontawesome.css', [], '6.5.1');
    wp_enqueue_script('flipper-script', plugin_dir_url(__FILE__) . 'assets/frontend/main.js', ['flipper-turnjs-lib', 'flipper-zoom-lib', 'flipper-bootstrap-lib', 'flipper-sharer-lib'], '1.0.2', true);
}

add_action('wp_enqueue_scripts', 'wa_page_flipper_frontend_assets');

function wa_page_flipper_shortcode($atts) {
    ob_start();

    $atts = shortcode_atts([
        'id'                        => get_the_ID(),
        'enable_summary'            => 'yes',
        'enable_related'            => 'yes',
        'enable_controls'           => 'yes',
        'enable_share'              => 'yes',
        'enable_zoom'               => 'yes',
        'enable_background_image'   => 'yes',
        'page_background_color'     => '#333333',
        'page_surface_color'        => 'rgba(0, 0, 0, 0.4)',
        'page_surface_accent_color' => '#ffffff',
        'page_accent_color'         => '#eac101',
        'page_font_color'           => '#ffffff'
    ], $atts, 'page_flipper');

    include plugin_dir_path(__FILE__) . 'templates/flipper-widget.php';

    return ob_get_clean();
}

add_shortcode('page_flipper', 'wa_page_flipper_shortcode');

if (has_action('elementor/widgets/register')) {
    function wa_page_flipper_widget( $widgets_manager ) {
        require_once( __DIR__ . '/widgets/flipper-widget-elementor.php' );
    
        $widgets_manager->register( new \Wa_Page_Flipper_Widget_Elementor() );
    }
    
    add_action('elementor/widgets/register', 'wa_page_flipper_widget');
}

function wa_page_flipper_no_custom_thumb($sizes, $metadata, $attachment_id) {
    $post_parent = wp_get_post_parent_id($attachment_id);

    if ($post_parent && get_post_type($post_parent) === 'wa_page_flipper') {
        $featured_id = get_post_thumbnail_id($post_parent);
        
        if ($attachment_id != $featured_id) {
            return [];
        }
    }

    return $sizes;
}

add_filter('intermediate_image_sizes_advanced', 'wa_page_flipper_no_custom_thumb', 10, 3);

function wa_page_flipper_get_related_posts($post_id, $taxonomy = "wa_page_flipper_author", $posts_per_page = -1) {
    $terms = wp_get_post_terms($post_id, $taxonomy);
    
    if (!$terms || is_wp_error($terms)) {
        return [];
    }

    $term_slugs = wp_list_pluck($terms, 'slug');

    $query = new WP_Query([
        'post_type' => 'wa_page_flipper',
        'tax_query' => [[
            'taxonomy' => $taxonomy,
            'field' => 'slug',
            'terms' => $term_slugs,
        ]],
        'post__not_in' => [$post_id],
        'posts_per_page' => $posts_per_page,
    ]);

    return $query->posts;
}