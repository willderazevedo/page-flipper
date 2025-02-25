<?php
/**
 * Plugin Name: Page Flipper
 * Description: Melhor page flipper da terra, com varias funções, integração com elementor e GRATUITO
 * Version:     1.0.0
 * Author:      Willder Azevedo
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function flipper_load_textdomain() {
    load_plugin_textdomain('page-flipper', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');
}

add_action('plugins_loaded', 'flipper_load_textdomain');

require_once plugin_dir_path( __FILE__ ) . 'fields/flipper-builder.php';

function register_page_flipper_post_type() {
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

    register_post_type('page_flipper', $args);

    // Registrar Taxonomia: Categories
    $tax_labels = [
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

    $tax_args = [
        'labels'            => $tax_labels,
        'public'            => true,
        'hierarchical'      => true,
        'show_admin_column' => true,
        'rewrite'           => [ 'slug' => 'digital-books-category' ],
        'show_in_rest'      => true,
    ];

    register_taxonomy('page_flipper_category', 'page_flipper', $tax_args);
}

add_action( 'init', 'register_page_flipper_post_type' );

function load_page_flipper_assets() {
    wp_enqueue_script('flipper-alpine-lib', plugin_dir_url(__FILE__) . 'libs/alpine.js', ['flipper-script'], '3.14.8', true);
    wp_enqueue_script('flipper-sortable-lib', plugin_dir_url(__FILE__) . 'libs/sortable.js', [], '1.15.6', true);
    wp_enqueue_style('flipper-style', plugin_dir_url(__FILE__) . 'assets/style.css', [], '1.0.0');
    wp_enqueue_script('flipper-script', plugin_dir_url(__FILE__) . 'assets/main.js', ['jquery'], '1.0.0', true);
}

add_action('admin_enqueue_scripts', 'load_page_flipper_assets');

if (has_action('elementor/widgets/register')) {
    function register_flipper_widget( $widgets_manager ) {
        require_once( __DIR__ . '/widgets/flipper-widget.php' );

        $widgets_manager->register( new \Flipper_Widget() );
    }

    add_action('elementor/widgets/register', 'register_flipper_widget');
}