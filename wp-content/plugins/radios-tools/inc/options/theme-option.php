<?php
/*
 * Theme Options
 * @package radios
 * @since 1.0.0
 * */

if ( !defined( 'ABSPATH' ) ) {
    exit(); // exit if access directly
}

if ( class_exists( 'CSF' ) ) {


    //
    // Set a unique slug-like ID
    $prefix = 'radios';

    //
    // Create options
    CSF::createOptions( $prefix . '_theme_options', array(
        'menu_title'         => 'Radios Option',
        'menu_slug'          => 'radios-theme-option',
        'menu_type'          => 'menu',
        'enqueue_webfont'    => true,
        'show_in_customizer' => true,
        'menu_icon' => 'dashicons-category',
        'menu_position' => 50,
        'theme'                   => 'dark',
        'framework_title'    => wp_kses_post( 'radios Options <small>by Raziul <br/> Version: 1.0</small> ' ),
    ) );

    // Create a top-tab
    CSF::createSection( $prefix . '_theme_options', array(
        'id'    => 'header_opts', // Set a unique slug-like ID
        'title' => 'Header',
    ) );


    /*-------------------------------------------------------
     ** Logo Settings  Options
    --------------------------------------------------------*/

    CSF::createSection( $prefix . '_theme_options', array(
        'title'  => esc_html__( 'Logo Settings', 'radios-tools' ),
        'parent'     => 'header_opts',
        'icon'   => 'fa fa-header',
        'fields' => array(
            array(
                'type'    => 'subheading',
                'content' => '<h3>' . esc_html__( 'Site Logo', 'radios-tools' ) . '</h3>',
            ),

            array(
                'id'      => 'theme_logo',
                'title'   => esc_html__( 'Main Logo', 'radios-tools' ),
                'type'    => 'media',
                'library' => 'image',
                'desc'    => esc_html__( 'Upload Your Main Logo Here', 'radios-tools' ),
            ),
            array(
                'id'    => 'logo-1-width',
                'type'  => 'slider',
                'min'     => 0,
                'max'     => 500,
                'step'    => 1,
                'unit'    => 'px',
                'title' => 'Main Logo Width',
                'output'      => '.rd-logo',
                'output_mode' => 'max-width',
            ),
            array(
                'id'      => 'theme_logo_v2',
                'title'   => esc_html__( 'Main Logo', 'radios-tools' ),
                'type'    => 'media',
                'library' => 'image',
                'desc'    => esc_html__( 'Upload Your Main Two Logo Here', 'radios-tools' ),
            ),
            array(
                'id'    => 'logo-2-width',
                'type'  => 'slider',
                'min'     => 0,
                'max'     => 500,
                'step'    => 1,
                'unit'    => 'px',
                'title' => 'Main Logo Width',
                'output'      => '.rd-logo-v2',
                'output_mode' => 'max-width',
            ),
            
        ),
    ) );

    // Preloader section
    CSF::createSection( $prefix . '_theme_options', array(
        'title'  => 'Header Top',
        'parent'     => 'header_opts',
        'icon'   => 'fa fa-wrench',
        'fields' => array(
            array(
                'id'      => 'topbar_enable',
                'title'   => esc_html__( 'Enable Top Bar', 'radios-tools' ),
                'type'    => 'switcher',
                'desc'    => esc_html__( 'Enable or Disable Header Top', 'radios-tools' ),
                'default' => true,
            ),
            array(
                'id'    => 'welcome_text',
                'type'  => 'text',
                'title' => esc_html__( 'Welcome Text', 'radios-tools' ),
                'desc'  => esc_html__( 'Enter your Welcome Message Here', 'radios-tools' ),
            ),
            array(
                'id'    => 'store_location',
                'type'  => 'text',
                'title' => esc_html__( 'Store Location', 'radios-tools' ),
                'desc'  => esc_html__( 'Enter your Store Location Text Here', 'radios-tools' ),
            ),
            array(
                'id'    => 'store_location_link',
                'type'  => 'text',
                'title' => esc_html__( 'Store URL', 'radios-tools' ),
                'desc'  => esc_html__( 'Enter your Store Location Link', 'radios-tools' ),
            ),
            array(
                'id'    => 'track_order',
                'type'  => 'text',
                'title' => esc_html__( 'Track Order', 'radios-tools' ),
                'desc'  => esc_html__( 'Enter your Track Order Text Here', 'radios-tools' ),
            ),
            array(
                'id'    => 'track_order_link',
                'type'  => 'text',
                'title' => esc_html__( 'Track Order Link', 'radios-tools' ),
                'desc'  => esc_html__( 'Enter your Track Order Link Here', 'radios-tools' ),
            ),
            array(
                'id'    => 'call_us',
                'type'  => 'text',
                'title' => esc_html__( 'Call Us Text', 'radios-tools' ),
                'desc'  => esc_html__( 'Enter your Call Us Text Here', 'radios-tools' ),
            ),
            array(
                'id'    => 'call_us_phone',
                'type'  => 'text',
                'title' => esc_html__( 'Phone No', 'radios-tools' ),
                'desc'  => esc_html__( 'Enter your Phone NUmber Here', 'radios-tools' ),
            ),
            array(
                'id'    => 'email_address',
                'type'  => 'text',
                'title' => esc_html__( 'Email Address', 'radios-tools' ),
                'desc'  => esc_html__( 'Enter your Email Address Here', 'radios-tools' ),
            ),
            array(
                'id'    => 'top_wislist_link',
                'type'  => 'link',
                'title' => esc_html__( 'Wishlist', 'radios-tools' ),
                'desc' => esc_html__( 'Add Wishlist Text and Link here', 'radios-tools' ),
            ),
            array(
                'id'    => 'top_checkout_link',
                'type'  => 'link',
                'title' => esc_html__( 'Checkout', 'radios-tools' ),
                'desc' => esc_html__( 'Add Checkout Text and Link here', 'radios-tools' ),
            ),
            array(
                'id'         => 'top_bar_social',
                'type'       => 'group',
                'title'      => 'Add Social Icon',
                'fields'     => array(
                    array(
                        'id'    => 'icon',
                        'type'  => 'icon',
                        'title' => esc_html__( 'Pick Up Info Icon', 'radios-tools' ),
                    ),
                    array(
                        'id'    => 'social_link',
                        'type'  => 'text',
                        'title' => esc_html__( 'Type Social Profile Link Here', 'radios-tools' ),
                    )
                )

            ),
            array(
                'id'    => 'add_img',
                'type'  => 'media',
                'title' => esc_html__( 'Add Image', 'radios-tools' ),
                'desc' => esc_html__( 'Upload Add Image', 'radios-tools' ),
            ),
            array(
                'id'    => 'add_img_link',
                'type'  => 'text',
                'title' => esc_html__( 'Add Link', 'radios-tools' ),
            ),
            

            array(
                'id'    => 'email_h_address',
                'type'  => 'text',
                'title' => esc_html__( 'Email Header 3 Address', 'radios-tools' ),
                'desc'  => esc_html__( 'Enter your Email Address Here', 'radios-tools' ),
            ),
            array(
                'id'    => 'phone_h_address',
                'type'  => 'text',
                'title' => esc_html__( 'Phone Header 3 Address', 'radios-tools' ),
                'desc'  => esc_html__( 'Enter your Phone Here', 'radios-tools' ),
            ),
        ),
    ) );

    /*-------------------------------------------------------
     ** Header  Options
    --------------------------------------------------------*/

    CSF::createSection( $prefix . '_theme_options', array(
        'title'  => esc_html__( 'Header', 'radios-tools' ),
        'parent'     => 'header_opts',
        'icon'   => 'fa fa-header',
        'fields' => array(
            array(
                'type'    => 'subheading',
                'content' => '<h3>' . esc_html__( 'Header Layout', 'radios-tools' ) . '</h3>',
            ),

            array(
                'id'          => 'header_glob_style',
                'type'        => 'select',
                'title'       => 'Choose Header',
                'chosen'      => true,
                'placeholder' => 'Header Global Style',
                'options'     => array(
                  'header-style-one'    => 'Header Style 1',
                  'header-style-two'    => 'Header Style 2',
                  'header-style-three'  => 'Header Style 3',
                ),
                'default'     => 'header-style-two'
            ),
            array(
                'id'      => 'header_cetogory_enable',
                'title'   => esc_html__( 'Enable Category Bar', 'radios-tools' ),
                'type'    => 'switcher',
                'desc'    => esc_html__( 'Enable or Disable Header Category Bar', 'radios-tools' ),
                'default' => true,
            ),
            array(
                'type'    => 'subheading',
                'content' => '<h3>' . esc_html__( 'Languages Option Info', 'radios-tools' ) . '</h3>',
            ),
            array(
                'id'    => 'active_languages',
                'type'  => 'text',
                'title' => esc_html__( 'Active Languages', 'radios-tools' ),
            ),
            array(
                'id'    => 'active_languages_link',
                'type'  => 'text',
                'title' => esc_html__( 'Upload Languages Link', 'radios-tools' ),
            ),
            array(
                'id'         => 'vaindo_languages',
                'type'       => 'group',
                'title'      => 'Add Languages',
                'fields'     => array(
                    
                    array(
                        'id'    => 'language_name',
                        'type'  => 'text',
                        'title' => esc_html__( 'Type Languages Name', 'radios-tools' ),
                    ),
                    array(
                        'id'    => 'link',
                        'type'  => 'text',
                        'title' => esc_html__( 'Languages Link', 'radios-tools' ),
                    )
                )

            ),
            array(
                'type'    => 'subheading',
                'content' => '<h3>' . esc_html__( 'Currency Option Info', 'radios-tools' ) . '</h3>',
            ),
            array(
                'id'    => 'active_currency',
                'type'  => 'text',
                'title' => esc_html__( 'Active Currency', 'radios-tools' ),
            ),
            array(
                'id'    => 'active_currency_link',
                'type'  => 'text',
                'title' => esc_html__( 'Upload Currency Link', 'radios-tools' ),
            ),
            array(
                'id'         => 'vaindo_currencys',
                'type'       => 'group',
                'title'      => 'Add Languages',
                'fields'     => array(
                    
                    array(
                        'id'    => 'currency_name',
                        'type'  => 'text',
                        'title' => esc_html__( 'Type Currency Name', 'radios-tools' ),
                    ),
                    array(
                        'id'    => 'link',
                        'type'  => 'text',
                        'title' => esc_html__( 'Currency Link', 'radios-tools' ),
                    )
                )

            ),

        ),
    ) );

    // Preloader section
    CSF::createSection( $prefix . '_theme_options', array(
        'title'  => 'Site Preloader',
        'parent'     => 'header_opts',
        'icon'   => 'fa fa-wrench',
        'fields' => array(

            array(
                'id'      => 'preloader_enable',
                'title'   => esc_html__( 'Enable Preloader', 'radios-tools' ),
                'type'    => 'switcher',
                'desc'    => esc_html__( 'Enable or Disable Preloader', 'radios-tools' ),
                'default' => true,
            ),
            

        ),
    ) );

    CSF::createSection( $prefix . '_theme_options', array(
        'title'  => esc_html__( 'Shop Page', 'radios-tools' ),
        'id'     => 'shope_opt',
        'icon'   => 'fa fa-bookmark',
        'fields' => array(

            array(
                'id'      => 'product_count',
                'type'    => 'text',
                'default'   => 12,
                'title'   => esc_html__('Shop Page Product Count', 'radios-tools'),
                'desc'    => esc_html__('How Many Product You Want to Display In Shop Page', 'radios-tools'),
            ),
            array(
                'id'      => 'shop_page_title',
                'type'    => 'text',
                'title'   => esc_html__('Shop Page Title', 'radios-tools'),
                'desc'    => esc_html__('If you Do not Show Page Title thene type Custom Title Here', 'radios-tools'),
            ),
            

        ),
    ) );

    /*-------------------------------------
     ** Typography Options
    -------------------------------------*/
    CSF::createSection( $prefix . '_theme_options', array(
        'title'  => esc_html__( 'Typography', 'radios-tools' ),
        'id'     => 'typography_options',
        'icon'   => 'fa fa-font',
        'fields' => array(

            array(
                'type'    => 'subheading',
                'content' => '<h3>' . esc_html__( 'Body', 'radios-tools' ) . '</h3>',
            ),

            array(
                'id'     => 'body-typography',
                'type'   => 'typography',
                'output' => 'body',

            ),

            array(
                'type'    => 'subheading',
                'content' => '<h3>' . esc_html__( 'Heading', 'radios-tools' ) . '</h3>',
            ),

            array(
                'id'     => 'heading-gl-typo',
                'type'   => 'typography',
                'output' => 'h1, h2, h3, h4, h5, h6, .product-block_one-heading',
            ),

            array(
                'type'    => 'subheading',
                'content' => '<h3>' . esc_html__( 'Menu', 'radios-tools' ) . '</h3>',
            ),
            array(
                'id'     => 'menu-gl-typo',
                'type'   => 'typography',
                'output' => '.main-header .main-menu .navigation > li > a, .main-header .main-menu .navigation > li > ul > li > a',
            ),
            

        ),
    ) );

    
   // invite Color Setting
   CSF::createSection( $prefix . '_theme_options', array(
    'title'  => 'Color Control',
    'id'     => 'apix_color_control',
    'icon'   => 'fa fa-paint-brush',
    'fields' => array(
        

        array(  //nav bar one start
            'type'    => 'subheading',
            'content' => '<h3>' . esc_html__( 'Theme Primary Color', 'radios-tools' ) . '</h3>',
        ),
        array(
            'id'    => 'theme-color-1',
            'type'  => 'color',
            'title' => 'Theme Primary Color',
            'default' => '#FF8717'
        ),
        array(
            'id'    => 'theme-color-2',
            'type'  => 'color',
            'title' => 'Theme Color Two',
            'default' => '#02D8F5'
        ),
        array(
            'id'    => 'theme-color-3',
            'type'  => 'color',
            'title' => 'Theme Color Three',
            'default' => '#50AD06'
        ),
        array(
            'id'    => 'theme-color-4',
            'type'  => 'color',
            'title' => 'Theme Color Four',
            'default' => '#FF6A00'
        ),
        array(
            'id'    => 'theme-color-5',
            'type'  => 'color',
            'title' => 'Theme Color Five',
            'default' => '#ff541f'
        ),
       
        
    ),
) );

    // Create a section
    CSF::createSection( $prefix . '_theme_options', array(
        'title'  => 'Radios Social Icon',
        'id'     => 'radios_social_icons',
        'icon'   => 'fa fa-share-square-o',
        'fields' => array(
            

            array(  //nav bar one start
                'type'    => 'subheading',
                'content' => '<h3>' . esc_html__( 'Social Icon Options', 'radios-tools' ) . '</h3>',
            ),
            
            array(
                'id'        => 'radios-social-global',
                'type'      => 'repeater',
                'title'     => 'Radios Global Social',
                'subtitle'      => 'Add Social Icon And Icon Link Here',
                'fields'    => array(     
                        
                  array(
                    'id'    => 'social_icon',
                    'type'  => 'icon',
                    'title' => 'Choose Social Icon Here',
                  ),
                  array(
                    'id'    => 'social_title',
                    'type'  => 'text',
                    'title' => 'Social Title Here',
                  ),
                  array(
                    'id'    => 'social_follow_title',
                    'type'  => 'text',
                    'title' => 'Social Flow Title Here',
                  ),
                  array(
                    'id'    => 'social_link',
                    'type'  => 'text',
                    'title' => 'Social Profile Link Here',
                  ),
              
                ),
                'default'   => array(
                  array(
                    'social_icon' => 'fab fa-facebook-f',
                    'social_link' => '#',
                  ),
                  array(
                    'social_icon' => 'fab fa-twitter',
                    'social_link' => '#',
                  ),
                  array(
                    'social_icon' => 'fab fa-instagram',
                    'social_link' => '#',
                  ),
                  array(
                    'social_icon' => 'fab fa-youtube',
                    'social_link' => '#',
                  ),
                  array(
                    'social_icon' => 'fab fa-pinterest',
                    'social_link' => '#',
                  ),
                ),
            ),

        ),
    ) );

   // Create a section
   CSF::createSection( $prefix . '_theme_options', array(
    'title'  => 'Error Page',
    'id'     => 'error_page',
    'icon'   => 'fa fa-exclamation',
    'fields' => array(
        

        array(  //nav bar one start
            'type'    => 'subheading',
            'content' => '<h3>' . esc_html__( '404 Page Options', 'radios-tools' ) . '</h3>',
        ),
        
        array(
            'id'      => 'error_code',
            'type'    => 'text',
            'title'   => esc_html__( 'Error Code', 'radios-tools' ),
            'default' => esc_html__( 'Oops... It looks like you ‘re lost !', 'radios-tools' ),
        ),
        array(
            'id'      => 'error_title',
            'type'    => 'text',
            'title'   => esc_html__( '404 Title', 'radios-tools' ),
            'default' => esc_html__( 'Oops... It looks like you ‘re lost !', 'radios-tools' ),
        ),
        array(
            'id'      => 'error_info_title',
            'type'    => 'textarea',
            'title'   => esc_html__( '404 Text', 'radios-tools' ),
            'default' => esc_html__( 'Oops! The page you are looking for does not exist. It might have been moved or deleted.', 'radios-tools' ),
        ),
        array(
            'id'      => 'error_button',
            'type'    => 'text',
            'title'   => esc_html__( '404 Button', 'radios-tools' ),
            'default' => esc_html__( 'Go Back Home', 'radios-tools' ),
        ),

        array(
            'id'          => 'page-spacing-error',
            'type'        => 'spacing',
            'title'       => 'Page Spacing',
            'output'      => '.error__page',
            'output_mode' => 'padding', // or margin, relative
        ),  
        array(  //nav bar one start
            'type'    => 'subheading',
            'content' => '<h3>' . esc_html__( '404 Breadcrumb Options', 'radios-tools' ) . '</h3>',
        ),
        array(
            'id'      => 'is_404_active_breadcrumb',
            'type'    => 'switcher',
            'title'   => 'Switcher',
            'label'    => esc_html__( 'Do you want activate Breadcrumb ?', 'radios-tools' ),
            'default' => true
        ),
        array(
        'id'      => 'br_er_custom_title',
        'type'    => 'text',
        'title'   => esc_html__('Custom Title', 'radios-tools'),
        'desc'    => esc_html__('If you Do not Show Page Title thene type Custom Title Here', 'radios-tools'),
        'dependency' => array( 'is_404_active_breadcrumb', '==', 'true' ) 
        ),
          array(
            'id'      => 'breadcrumb_er_image',
            'title'   => esc_html__( 'Breadcrumb  Image', 'radios-tools' ),
            'type'    => 'media',
            'library' => 'image',
            'default' => get_template_directory_uri() . "/assets/img/bg/bread.png",
            'desc'    => esc_html__( 'Upload Breadcrumb Image Here', 'radios-tools' ),
            'dependency' => array( 'is_404_active_breadcrumb', '==', 'true' ) 
        ),          
    ),
) );

 /*-------------------------------------------------------
     ** Footer  Options
    --------------------------------------------------------*/
    CSF::createSection( $prefix . '_theme_options', array(
        'title'  => esc_html__( 'Footer', 'radios-tools' ),
        'id'     => 'footer_options',
        'icon'   => 'fa fa-copyright',
        'fields' => array(    
            array(
                'type'    => 'subheading',
                'content' => '<h3>' . esc_html__( 'Footer Style Options', 'radios-tools' ) . '</h3>',
            ),
            array(
                'id'          => 'footer_glob_style',
                'type'        => 'select',
                'title'       => 'Choose Footer',
                'chosen'      => true,
                'placeholder' => 'Footer Global Style',
                'options'     => array(
                  'footer-style-one'    => 'Footer Style 1',
                  'footer-style-two'    => 'Footer Style 2',
                  'footer-style-three'  => 'Footer Style 3',
                ),
                'default'     => 'footer-style-one'
            ),  
            array(
                'id'    => 'footer_bg_img',
                'type'  => 'media',
                'title' => esc_html__('Footer BG', 'radios-tools'),
            ),     
            array(
                'type'    => 'subheading',
                'content' => '<h3>' . esc_html__( 'Footer Short About Options', 'radios-tools' ) . '</h3>',
            ),
            
            array(
                'id'    => 'shortabout',
                'type'  => 'textarea',
                'title' => esc_html__('Short About', 'radios-tools'),
            ),
            array(
                'type'    => 'subheading',
                'content' => '<h3>' . esc_html__( 'Footer Social Profile Options', 'radios-tools' ) . '</h3>',
            ),
            array(
                'id'     => 'footer_social',
                'type'   => 'repeater',
                'title'  => esc_html__('Add Footer Social Profile Option', 'radios-tools'),
                'fields' => array(
                    array(
                        'id'      => 'icon',
                        'title'   => esc_html__( 'icon', 'radios-tools' ),
                        'type'    => 'icon',
                        'desc'    => esc_html__( 'Select Icon', 'radios-tools' ),
                    ),
                    array(
                        'id'    => 'title',
                        'type'  => 'text',
                        'title' => esc_html__('Social Title', 'radios-tools'),
                    ),
                    array(
                        'id'    => 'link',
                        'type'  => 'text',
                        'title' => esc_html__('Social Link', 'radios-tools'),
                    ),
                
                ),
            ),

            array(
                'type'    => 'subheading',
                'content' => '<h3>' . esc_html__( 'Footer Contact Options', 'radios-tools' ) . '</h3>',
            ),            
            array(
                'id'     => 'footer_contact_info',
                'type'   => 'repeater',
                'title'  => esc_html__('Add Footer Contact Info', 'radios-tools'),
                'fields' => array(
                    array(
                        'id'      => 'icon',
                        'title'   => esc_html__( 'Icon', 'radios-tools' ),
                        'type'    => 'icon',
                        'desc'    => esc_html__( 'Upload Contact Icon Heare', 'radios-tools' ),
                    ),
                    array(
                        'id'    => 'contact_title',
                        'type'  => 'text',
                        'title' => esc_html__('Contact Title', 'radios-tools'),
                    ),
                
                ),
            ),
            array(
                'type'    => 'subheading',
                'content' => '<h3>' . esc_html__( 'Footer App Options', 'radios-tools' ) . '</h3>',
            ),            
            array(
                'id'     => 'footer_apps',
                'type'   => 'repeater',
                'title'  => esc_html__('Add App Store', 'radios-tools'),
                'fields' => array(
                    array(
                        'id'      => 'app_logo_img',
                        'title'   => esc_html__( 'Upload App Logo Image', 'radios-tools' ),
                        'type'    => 'media',
                        'desc'    => esc_html__( 'Upload App Logo Image Here', 'radios-tools' ),
                    ),
                    array(
                        'id'    => 'app_link',
                        'type'  => 'text',
                        'title' => esc_html__('App Link', 'radios-tools'),
                    ),
                
                ),
            ),


            array(
                'type'    => 'subheading',
                'content' => '<h3>' . esc_html__( 'Footer Payment Option', 'radios-tools' ) . '</h3>',
            ),
            array(
                'id'      => 'add_payment_supprt',
                'title'   => esc_html__( 'Upload Payment Image', 'radios-tools' ),
                'type'    => 'media',
                'desc'    => esc_html__( 'Upload Payment Image Here', 'radios-tools' ),
            ),
            array(
                'type'    => 'subheading',
                'content' => '<h3>' . esc_html__( 'Footer Newsletter Option', 'radios-tools' ) . '</h3>',
            ),
            array(
                'id'      => 'newsletter_enable',
                'title'   => esc_html__( 'Enable Newsletter', 'radios-tools' ),
                'type'    => 'switcher',
                'desc'    => esc_html__( 'Enable or Disable Newsletter', 'radios-tools' ),
                'default' => true,
            ),
            array(
                'id'    => 'newsl_title',
                'type'  => 'text',
                'title' => esc_html__('Newsletter Title', 'radios-tools'),
                'dependency'  => [
                    'newsletter_enable', '==', 'true',
                ],
            ),
            array(
                'id'    => 'newsl_text',
                'type'  => 'textarea',
                'title' => esc_html__('Newsletter Text', 'radios-tools'),
                'dependency'  => [
                    'newsletter_enable', '==', 'true',
                ],
            ),
            array(
                'id'    => 'newsl_shortcode',
                'type'  => 'text',
                'title' => esc_html__('Newsletter Shortcode', 'radios-tools'),
                'dependency'  => [
                    'newsletter_enable', '==', 'true',
                ],
            ),
            array(
                'type'    => 'subheading',
                'content' => '<h3>' . esc_html__( 'Footer Copyright Area Options', 'radios-tools' ) . '</h3>',
            ),

            array(
                'id'    => 'radios_copywrite_text',
                'title' => esc_html__( 'Copyright Area Text', 'radios-tools' ),
                'type'  => 'wp_editor',
                'desc'  => esc_html__( 'Footer Copyright Text', 'radios-tools' ),
            ),
            array(
                'type'    => 'subheading',
                'content' => '<h3>' . esc_html__( 'Footer Shape Option', 'radios-tools' ) . '</h3>',
            ),
            array(
                'id'      => 'f_shape1',
                'title'   => esc_html__( 'Footer Shape 1 Image', 'radios-tools' ),
                'type'    => 'media',
                'desc'    => esc_html__( 'Upload Footer Shape Image Here', 'radios-tools' ),
            ),
            array(
                'id'      => 'f_shape2',
                'title'   => esc_html__( 'Footer Shape 2 Image', 'radios-tools' ),
                'type'    => 'media',
                'desc'    => esc_html__( 'Upload Footer Shape Image Here', 'radios-tools' ),
            ),
            array(
                'id'      => 'f_shape3',
                'title'   => esc_html__( 'Footer Shape 3 Image', 'radios-tools' ),
                'type'    => 'media',
                'desc'    => esc_html__( 'Upload Footer Shape Image Here', 'radios-tools' ),
            ),
            array(
                'id'      => 'f_shape4',
                'title'   => esc_html__( 'Footer Shape 4 Image', 'radios-tools' ),
                'type'    => 'media',
                'desc'    => esc_html__( 'Upload Footer Shape Image Here', 'radios-tools' ),
            ),
            array(
                'id'      => 'f_shape5',
                'title'   => esc_html__( 'Footer Shape 5 Image', 'radios-tools' ),
                'type'    => 'media',
                'desc'    => esc_html__( 'Upload Footer Shape Image Here', 'radios-tools' ),
            ),

        ),
    ) );

    // Backup section
    CSF::createSection( $prefix . '_theme_options', array(
        'title'  => esc_html__( 'Backup', 'radios-tools' ),
        'id'     => 'backup_options',
        'icon'   => 'fa fa-window-restore',
        'fields' => array(
            array(
                'type' => 'backup',
            ),
        ),
    ) );

}