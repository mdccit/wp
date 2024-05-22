<?php 

if(!function_exists('barnix_register_custom_icon_library')){
    add_filter('elementor/icons_manager/additional_tabs', 'barnix_register_custom_icon_library');
    function barnix_register_custom_icon_library($tabs){
        $custom_tabs = [
            'extra_icon2' => [
                'name' => 'br-flat-icon',
                'label' => esc_html__( 'Barnix Flaticon', 'barnix' ),
                'url' => get_template_directory_uri() . '/assets/css/flaticon.css',
                'enqueue' => [ get_template_directory_uri() . '/assets/css/flaticon.css' ],
                'prefix' => 'flaticon-',
                'displayPrefix' => 'flaticon',
                'labelIcon' => 'flaticon-writing',
                'ver' => RADIOS_ICON_VER,
                'fetchJson' => get_template_directory_uri() . '/assets/js/flaticon.js?v='.RADIOS_ICON_VER,
                'native' => true,
            ]

        ];

        $tabs = array_merge($custom_tabs, $tabs);

        return $tabs;
    }
}