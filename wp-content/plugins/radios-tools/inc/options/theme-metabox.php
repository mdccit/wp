<?php
/*
 * Theme Metabox
 * @package radios-core
 * @since 1.0.0
 * */

if ( !defined( 'ABSPATH' ) ) {
    exit(); // exit if access directly
}

if ( class_exists( 'CSF' ) ) {

    $prefix = 'radios';

    /*-------------------------------------
    Page Options
    -------------------------------------*/
    $post_metabox = 'radios_page_meta';

    CSF::createMetabox( $post_metabox, array(
        'title'     => 'Page Options',
        'post_type' => 'page',
    ) );

    // Header Section
    CSF::createSection( $post_metabox, array(
        'title'  => 'Header',
        'fields' => array(
            array(
                'type'    => 'subheading',
                'content' => esc_html__( 'Header Option', 'radios-tools' ),
            ),

            array(
                'id'      => 'page_logo',
                'title'   => esc_html__( 'Page Logo', 'radios-tools' ),
                'type'    => 'media',
                'library' => 'image',
                'desc'    => esc_html__( 'Upload Logo Here', 'radios-tools' ),
            ),

            array(
                'id'      => 'header_layout',
                'type'    => 'select',
                'title'   => esc_html__( 'Select Header Navigation Style', 'radios-tools' ),
                'options'     => array(
                    'header-style-one'    => 'Header Style 1',
                    'header-style-two'    => 'Header Style 2',
                    'header-style-three'  => 'Header Style 3',
                ),
                'default' => 'header-style-two',
            ),

        ),
    ) );

    // Breadcrumb Section
    CSF::createSection( $post_metabox, array(
        'title'  => 'Breadcrumb',
        'fields' => array(
            array(
                'type'    => 'subheading',
                'content' => esc_html__( 'Breadcrumb Option', 'radios-tools' ),
            ),
            array(
                'id'      => 'is_active_breadcrumb',
                'type'    => 'switcher',
                'title'   => 'Switcher',
                'label'    => esc_html__( 'Do you want activate Breadcrumb ?', 'radios-tools' ),
                'default' => true
              ),
            array(
            'id'      => 'br_custom_title',
            'type'    => 'text',
            'title'   => esc_html__('Custom Title', 'radios-tools'),
            'desc'    => esc_html__('If you Do not Show Page Title thene type Custom Title Here', 'radios-tools'),
            'dependency' => array( 'is_active_breadcrumb', '==', 'true' ) 
            ),
            array(
                'id'       => 'breadcrumb_btn',
                'type'     => 'link',
                'title'    => 'Link',
                'default'  => array(
                  'url'    => '#',
                  'text'   => 'Get started now',
                  'target' => '_blank'
                ),
            ),


        )
    ) );
   
    // Header Section
    CSF::createSection( $post_metabox, array(
        'title'  => 'Footer',
        'fields' => array(
            array(
                'type'    => 'subheading',
                'content' => esc_html__( 'Footer Option', 'radios-tools' ),
            ),

            array(
                'id'      => 'footer_layout',
                'type'    => 'select',
                'title'   => esc_html__( 'Select Footer Style', 'radios-tools' ),
                'options'     => array(
                    'footer-style-one'    => 'Footer Style 1',
                    'footer-style-two'    => 'Footer Style 2',
                    'footer-style-three'  => 'Footer Style 3',
                ),
                'default' => 'footer-style-one',
            ),

        ),
    ) );

    /*-------------------------------------
    Page Options
    -------------------------------------*/
    $cate_metabox = 'radios_cate_meta';

    CSF::createTaxonomyOptions( $cate_metabox, array(
        'taxonomy'  => 'category',
        'data_type' => 'serialize',
    ) );

     // Header Section
     CSF::createSection( $cate_metabox, array(
        'title'  => 'Header',
        'fields' => array(
            
            array(
                'id'      => 'icon_name',
                'type'    => 'icon',
                'title'   => 'Type Icon Name',
            ),
            
        ),
    ) );

    /*-------------------------------------
    Product Options
    -------------------------------------*/
    $product_metabox = 'groser_product_meta';

    CSF::createMetabox( $product_metabox, array(
        'title'     => 'Product Options',
        'post_type' => 'product',
    ) );

     // Header Section
     CSF::createSection( $product_metabox, array(
        'fields' => array(
            array(
                'id'      => 'prod_featured_img',
                'type'    => 'media',
                'title'   => 'Deal Featured Image',
            ),
            array(
                'id'      => 'prod_deal_img',
                'type'    => 'media',
                'title'   => 'Deal Product Image',
            ),
            
            array(
                'id'      => 'prod_deal_date',
                'type'    => 'text',
                'default' => '2024/01/28',
                'title'   => 'Product Deal Date',
            ),
            array(
                'id'      => 'prod_deal_text',
                'type'    => 'text',
                'default' => 'make sure they meet high-quality',
                'title'   => 'Product Deal Text',
            ),
            array(
                'title' => esc_html__('Discount Percentage ON/OFF', 'nest-addons') ,
                'id' => 'disc_percent_off',
                'type' => 'switcher',
                'default'  => false,
            ),
            array(
                'id'      => 'offer_badge_label',
                'type'    => 'text',
                'title'   => 'Offer Badge Text',
            ),
            array(
                'id'    => 'product-gallery',
                'type'  => 'gallery',
                'title' => 'Gallery',
            ),
        ),
    ) );
    

} //endif
