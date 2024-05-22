<?php

/**
 * Service Csutom Post
 */

if ( !function_exists( 'barnix_service' ) ) {

    // Register service
    function barnix_service() {

        $labels = array(
            'name'                  => esc_html_x( 'Services', 'Post Type General Name', 'barnix-tools' ),
            'singular_name'         => esc_html_x( 'Service', 'Post Type Singular Name', 'barnix-tools' ),
            'menu_name'             => esc_html__( 'Service', 'barnix-tools' ),
            'name_admin_bar'        => esc_html__( 'Service', 'barnix-tools' ),
            'archives'              => esc_html__( 'Item Archives', 'barnix-tools' ),
            'attributes'            => esc_html__( 'Item Attributes', 'barnix-tools' ),
            'parent_item_colon'     => esc_html__( 'Parent Item:', 'barnix-tools' ),
            'all_items'             => esc_html__( 'All services', 'barnix-tools' ),
            'add_new_item'          => esc_html__( 'Add New service', 'barnix-tools' ),
            'add_new'               => esc_html__( 'Add New service', 'barnix-tools' ),
            'new_item'              => esc_html__( 'New service', 'barnix-tools' ),
            'edit_item'             => esc_html__( 'Edit service', 'barnix-tools' ),
            'update_item'           => esc_html__( 'Update service', 'barnix-tools' ),
            'view_item'             => esc_html__( 'View service', 'barnix-tools' ),
            'view_items'            => esc_html__( 'View service', 'barnix-tools' ),
            'search_items'          => esc_html__( 'Search service', 'barnix-tools' ),
            'not_found'             => esc_html__( 'Not found', 'barnix-tools' ),
            'not_found_in_trash'    => esc_html__( 'Not found in Trash', 'barnix-tools' ),
            'featured_image'        => esc_html__( 'Featured Image', 'barnix-tools' ),
            'set_featured_image'    => esc_html__( 'Set featured image', 'barnix-tools' ),
            'remove_featured_image' => esc_html__( 'Remove featured image', 'barnix-tools' ),
            'use_featured_image'    => esc_html__( 'Use as featured image', 'barnix-tools' ),
            'insert_into_item'      => esc_html__( 'Insert into item', 'barnix-tools' ),
            'uploaded_to_this_item' => esc_html__( 'Uploaded to this item', 'barnix-tools' ),
            'items_list'            => esc_html__( 'Items list', 'barnix-tools' ),
            'items_list_navigation' => esc_html__( 'Items list navigation', 'barnix-tools' ),
            'filter_items_list'     => esc_html__( 'Filter items list', 'barnix-tools' ),
        );
        $args = array(
            'label'               => esc_html__( 'services', 'barnix-tools' ),
            'description'         => esc_html__( 'Add service Here', 'barnix-tools' ),
            'labels'              => $labels,
            'supports'            => array( 'title', 'editor', 'thumbnail', 'elementor', 'excerpt' ),
            'hierarchical'        => false,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_position'       => 5,
            'menu_icon'           => 'dashicons-format-gallery',
            'show_in_admin_bar'   => true,
            'show_in_nav_menus'   => true,
            'can_export'          => true,
            'has_archive'         => true,
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'capability_type'     => 'page',
        );
        register_post_type( 'barnix_service', $args );

    }
    add_action( 'init', 'barnix_service', 0 );

}



if ( !function_exists( 'projects' ) ) {

    // Register Project
    function projects() {

        $labels = array(
            'name'                  => esc_html_x( 'Projects', 'Post Type General Name', 'barnix-tools' ),
            'singular_name'         => esc_html_x( 'Projects', 'Post Type Singular Name', 'barnix-tools' ),
            'menu_name'             => esc_html__( 'Project', 'barnix-tools' ),
            'name_admin_bar'        => esc_html__( 'Project', 'barnix-tools' ),
            'archives'              => esc_html__( 'Item Archives', 'barnix-tools' ),
            'attributes'            => esc_html__( 'Item Attributes', 'barnix-tools' ),
            'parent_item_colon'     => esc_html__( 'Parent Item:', 'barnix-tools' ),
            'all_items'             => esc_html__( 'All Projects', 'barnix-tools' ),
            'add_new_item'          => esc_html__( 'Add New Project', 'barnix-tools' ),
            'add_new'               => esc_html__( 'Add New Project', 'barnix-tools' ),
            'new_item'              => esc_html__( 'New Project', 'barnix-tools' ),
            'edit_item'             => esc_html__( 'Edit Project', 'barnix-tools' ),
            'update_item'           => esc_html__( 'Update Project', 'barnix-tools' ),
            'view_item'             => esc_html__( 'View Project', 'barnix-tools' ),
            'view_items'            => esc_html__( 'View Project', 'barnix-tools' ),
            'search_items'          => esc_html__( 'Search Project', 'barnix-tools' ),
            'not_found'             => esc_html__( 'Not found', 'barnix-tools' ),
            'not_found_in_trash'    => esc_html__( 'Not found in Trash', 'barnix-tools' ),
            'featured_image'        => esc_html__( 'Featured Image', 'barnix-tools' ),
            'set_featured_image'    => esc_html__( 'Set featured image', 'barnix-tools' ),
            'remove_featured_image' => esc_html__( 'Remove featured image', 'barnix-tools' ),
            'use_featured_image'    => esc_html__( 'Use as featured image', 'barnix-tools' ),
            'insert_into_item'      => esc_html__( 'Insert into item', 'barnix-tools' ),
            'uploaded_to_this_item' => esc_html__( 'Uploaded to this item', 'barnix-tools' ),
            'items_list'            => esc_html__( 'Items list', 'barnix-tools' ),
            'items_list_navigation' => esc_html__( 'Items list navigation', 'barnix-tools' ),
            'filter_items_list'     => esc_html__( 'Filter items list', 'barnix-tools' ),
        );
        $args = array(
            'label'               => esc_html__( 'Projects', 'barnix-tools' ),
            'description'         => esc_html__( 'Add Project Here', 'barnix-tools' ),
            'labels'              => $labels,
            'supports'            => array( 'title', 'thumbnail', 'elementor' ),
            'hierarchical'        => false,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_position'       => 5,
            'menu_icon'           => 'dashicons-analytics',
            'show_in_admin_bar'   => true,
            'show_in_nav_menus'   => true,
            'can_export'          => true,
            'has_archive'         => true,
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'capability_type'     => 'page',
        );
        register_post_type( 'projects', $args );

    }
    add_action( 'init', 'projects', 0 );

}

/**
 * Project Custom Taxonomy
 */

if ( !function_exists( 'projects_cate_taxonomy' ) ) {
    function projects_cate_taxonomy() {
        $labels = [
            'name'          => esc_html__( 'Project Categories', 'barnix-tools' ),
            'menu_name'     => esc_html__( 'Project Categories', 'barnix-tools' ),
            'singular_name' => esc_html__( 'Project Category', 'barnix-tools' ),
            'search_items'  => esc_html__( 'Search Project Category', 'barnix-tools' ),
            'all_items'     => esc_html__( 'All Project Category', 'barnix-tools' ),
            'new_item_name' => esc_html__( 'New Project Category', 'barnix-tools' ),
            'add_new_item'  => esc_html__( 'Add New Project Category', 'barnix-tools' ),
            'edit_item'     => esc_html__( 'Edit New Project Category', 'barnix-tools' ),
            'update_item'   => esc_html__( 'Update New Project Category', 'barnix-tools' ),
        ];
        $args = array(
            'labels'                => $labels,
            'hierarchical'          => true,
            'show_ui'               => true,
            'show_admin_column'     => true,
            'query_var'             => true,
            'update_count_callback' => '_update_post_term_count',
            'rewrite'               => array( 'slug' => 'Project-category' ),
        );
        register_taxonomy( 'projects_cate', 'projects', $args );
    }
    add_action( 'init', 'projects_cate_taxonomy' );
}
