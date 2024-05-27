<?php
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Elementor tabs Widget.
 * @since 1.0.0
 */
class CM_Products extends \Elementor\Widget_Base
{

    /**
     * Get widget name.
     *
     * Retrieve widget name.
     *
     * @since 1.0.0
     * @access public
     * @return string Widget name.
     */
    public function get_name()
    {
        return 'cm-products';
    }

    /**
     * Get widget title.
     *
     * Retrieve widget title.
     *
     * @since 1.0.0
     * @access public
     * @return string Widget title.
     */
    public function get_title()
    {
        return esc_html__('CM Products', 'cm');
    }

    /**
     * Get widget icon.
     *
     * Retrieve widget icon.
     *
     * @since 1.0.0
     * @access public
     * @return string Widget icon.
     */
    public function get_icon()
    {
        return 'eicon-tabs';
    }

    /**
     * Get widget categories.
     *
     * Retrieve the list of categories the widget belongs to.
     *
     * @since 1.0.0
     * @access public
     * @return array Widget categories.
     */
    public function get_categories()
    {
        return ['cm_widgets'];
    }

    public function get_script_depends() {
        return [
            'imagesloaded',
        ];
    }

    public static function get_button_sizes() {
        return [
            'xs' => __('Extra Small', 'medilazar-core'),
            'sm' => __('Small', 'medilazar-core'),
            'md' => __('Medium', 'medilazar-core'),
            'lg' => __('Large', 'medilazar-core'),
            'xl' => __('Extra Large', 'medilazar-core'),
        ];
    }

    /**
     * Register tabs widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since  1.0.0
     * @access protected
     */
    protected function register_controls() {
        //Section Query
        $this->start_controls_section(
            'section_setting',
            [
                'label' => __('Settings', 'medilazar-core'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'limit',
            [
                'label'   => __('Posts Per Page', 'medilazar-core'),
                'type'    => Controls_Manager::NUMBER,
                'default' => 6,
            ]
        );

        $this->add_responsive_control(
            'column',
            [
                'label'     => __('columns', 'medilazar-core'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 3,
                'options'   => [1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7],
                'condition' => [
                    'product_layout!' => 'masonry'
                ]
            ]
        );


        $this->add_control(
            'advanced',
            [
                'label' => __('Advanced', 'medilazar-core'),
                'type'  => Controls_Manager::HEADING,
            ]
        );

        $this->add_control(
            'orderby',
            [
                'label'   => __('Order By', 'medilazar-core'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'date',
                'options' => [
                    'date'       => __('Date', 'medilazar-core'),
                    'id'         => __('Post ID', 'medilazar-core'),
                    'menu_order' => __('Menu Order', 'medilazar-core'),
                    'popularity' => __('Number of purchases', 'medilazar-core'),
                    'rating'     => __('Average Product Rating', 'medilazar-core'),
                    'title'      => __('Product Title', 'medilazar-core'),
                    'rand'       => __('Random', 'medilazar-core'),
                ],
            ]
        );

        $this->add_control(
            'order',
            [
                'label'   => __('Order', 'medilazar-core'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'desc',
                'options' => [
                    'asc'  => __('ASC', 'medilazar-core'),
                    'desc' => __('DESC', 'medilazar-core'),
                ],
            ]
        );

        $this->add_control(
            'categories',
            [
                'label'    => __('Categories', 'medilazar-core'),
                'type'     => Controls_Manager::SELECT2,
                'options'  => $this->get_product_categories(),
                'multiple' => true,
            ]
        );

        $this->add_control(
            'cat_operator',
            [
                'label'     => __('Category Operator', 'medilazar-core'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'IN',
                'options'   => [
                    'AND'    => __('AND', 'medilazar-core'),
                    'IN'     => __('IN', 'medilazar-core'),
                    'NOT IN' => __('NOT IN', 'medilazar-core'),
                ],
                'condition' => [
                    'categories!' => ''
                ],
            ]
        );

        $this->add_control(
            'product_type',
            [
                'label'   => __('Product Type', 'medilazar-core'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'newest',
                'options' => [
                    'newest'       => __('Newest Products', 'medilazar-core'),
                    'on_sale'      => __('On Sale Products', 'medilazar-core'),
                    'best_selling' => __('Best Selling', 'medilazar-core'),
                    'top_rated'    => __('Top Rated', 'medilazar-core'),
                    'featured'     => __('Featured Product', 'medilazar-core'),
                ],
            ]
        );

        $this->add_control(
            'show_time_sale',
            [
                'label'        => __('Show Time Sale', 'medilazar-core'),
                'type'         => Controls_Manager::SWITCHER,
                'condition'    => [
                    'product_type' => 'on_sale',
                ],
                'prefix_class' => 'elementor-product-time-sale-'
            ]
        );

        $this->add_control(
            'paginate',
            [
                'label'   => __('Paginate', 'medilazar-core'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'none',
                'options' => [
                    'none'       => __('None', 'medilazar-core'),
                    'pagination' => __('Pagination', 'medilazar-core'),
                ],
            ]
        );

        $this->add_control(
            'product_layout',
            [
                'label'   => __('Product Layout', 'medilazar-core'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'grid',
                'options' => [
                    'grid' => __('Grid', 'medilazar-core'),
                    'list' => __('List', 'medilazar-core')
                ],
            ]
        );

        $this->add_control(
            'grid_layout',
            [
                'label'     => __('Grid Layout', 'medilazar-core'),
                'type'      => Controls_Manager::SELECT,
                'default'   => '1',
                'options'   => [
                    '1' => __('Style 1', 'medilazar-core'),
                    '2' => __('Style 2', 'medilazar-core'),
                ],
                'condition' => [
                    'product_layout' => 'grid'
                ]
            ]
        );

        $this->add_control(
            'list_layout',
            [
                'label'     => __('List Layout', 'medilazar-core'),
                'type'      => Controls_Manager::SELECT,
                'default'   => '1',
                'options'   => [
                    '1' => __('Style 1', 'medilazar-core'),
                    '2' => __('Style 2', 'medilazar-core'),
                    '3' => __('Style 3', 'medilazar-core'),
                ],
                'condition' => [
                    'product_layout' => 'list'
                ]
            ]
        );

        $this->add_responsive_control(
            'product_gutter',
            [
                'label'      => __('Gutter', 'medilazar-core'),
                'type'       => Controls_Manager::SLIDER,
                'range'      => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'size_units' => ['px'],
                'selectors'  => [
                    '{{WRAPPER}} ul.products li' => 'padding-left: calc({{SIZE}}{{UNIT}} / 2); padding-right: calc({{SIZE}}{{UNIT}} / 2);',
                    '{{WRAPPER}} ul.products'    => 'margin-left: calc({{SIZE}}{{UNIT}} / -2); margin-right: calc({{SIZE}}{{UNIT}} / -2);',
                ],
                'condition'  => [
                    'enable_carousel!' => 'yes'
                ],
            ]
        );


        $this->end_controls_section();
        // End Section Query

        $this->start_controls_section(
            'products_title_style',
            [
                'label' => __('Title', 'medilazar-core'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'product_title_typo',
                'selector' => '{{WRAPPER}} .product .woocommerce-loop-product__title',

            ]
        );

        $this->add_control(
            'product_title_color',
            [
                'label'     => __('Color', 'medilazar-core'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .product .woocommerce-loop-product__title a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'product_title_spacing',
            [
                'label'     => __('Spacing', 'medilazar-core'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .product .woocommerce-loop-product__title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'products_price_style',
            [
                'label' => __('Price', 'medilazar-core'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'products_price_typo',
                'selector' => '{{WRAPPER}} ul.products li.product .price',

            ]
        );

        $this->add_control(
            'products_price_color',
            [
                'label'     => __('Color', 'medilazar-core'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} ul.products li.product .price' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Carousel Option
        $this->start_controls_section(
            'section_carousel_options',
            [
                'label'     => __('Carousel Options', 'medilazar-core'),
                'type'      => Controls_Manager::SECTION,
                'condition' => array(),
            ]
        );

        $this->add_control(
            'enable_carousel',
            [
                'label' => __('Enable', 'medilazar-core'),
                'type'  => Controls_Manager::SWITCHER,
            ]
        );
        $this->add_responsive_control(
            'owl_item_spacing',
            [
                'label'     => __('Spacing', 'medilazar-core'),
                'type'      => Controls_Manager::NUMBER,
                'default'   => 30,
                'condition' => [
                    'enable_carousel' => 'yes'
                ]
            ]
        );
        $this->add_control(
            'enable_center',
            [
                'label'        => __('Center', 'medilazar-core'),
                'type'         => Controls_Manager::SWITCHER,
                'prefix_class' => 'owl-item-',
                'condition'    => [
                    'enable_carousel' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'navigation',
            [
                'label'     => __('Navigation', 'medilazar-core'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'dots',
                'options'   => [
                    'both'   => __('Arrows and Dots', 'medilazar-core'),
                    'arrows' => __('Arrows', 'medilazar-core'),
                    'dots'   => __('Dots', 'medilazar-core'),
                    'none'   => __('None', 'medilazar-core'),
                ],
                'condition' => [
                    'enable_carousel' => 'yes'
                ],
            ]
        );
        $this->add_control(
            'nav_position',
            [
                'label'        => __('Nav Position', 'medilazar-core'),
                'type'         => Controls_Manager::SELECT,
                'default'      => 'top',
                'options'      => [
                    'top'    => __('Top', 'medilazar-core'),
                    'center' => __('Center', 'medilazar-core'),
                    'bottom' => __('Bottom', 'medilazar-core'),
                ],
                'conditions'   => [
                    'relation' => 'and',
                    'terms'    => [
                        [
                            'name'     => 'enable_carousel',
                            'operator' => '==',
                            'value'    => 'yes',
                        ],
                        [
                            'name'     => 'navigation',
                            'operator' => '!==',
                            'value'    => 'none',
                        ],
                        [
                            'name'     => 'navigation',
                            'operator' => '!==',
                            'value'    => 'dots',
                        ],
                    ],
                ],
                'prefix_class' => 'owl-nav-position-',
            ]
        );
        $this->add_control(
            'nav_align',
            [
                'label'        => __('Nav Align', 'medilazar-core'),
                'type'         => Controls_Manager::SELECT,
                'default'      => 'right',
                'options'      => [
                    'left'   => __('Left', 'medilazar-core'),
                    'center' => __('Center', 'medilazar-core'),
                    'right'  => __('Right', 'medilazar-core'),
                ],
                //                'condition' => [
                //                    'navigation' => ['arrows', 'both'],
                //                    'nav_position' => ['bottom', 'top'],
                //                ],
                'conditions'   => [
                    'relation' => 'and',
                    'terms'    => [
                        [
                            'name'     => 'enable_carousel',
                            'operator' => '==',
                            'value'    => 'yes',
                        ],
                        [
                            'name'     => 'navigation',
                            'operator' => '!==',
                            'value'    => 'none',
                        ],
                        [
                            'name'     => 'navigation',
                            'operator' => '!==',
                            'value'    => 'dots',
                        ],
                        [
                            'name'     => 'nav_position',
                            'operator' => '!==',
                            'value'    => 'center',
                        ],
                    ],
                ],
                'prefix_class' => 'owl-nav-align-',
            ]
        );
        $this->add_responsive_control(
            'nav_spacing_vertical',
            [
                'label'      => __('Nav Spacing Vertical', 'medilazar-core'),
                'type'       => Controls_Manager::SLIDER,
                'range'      => [
                    'px' => [
                        'min' => -100,
                        'max' => 100,
                    ],
                ],
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}}.owl-nav-position-top .owl-nav'                      => 'top: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}}.owl-nav-position-bottom .owl-nav'                   => 'bottom: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}}.owl-nav-position-center .owl-nav [class*=\'owl-\']' => 'top: {{SIZE}}{{UNIT}};',
                ],
                'conditions' => [
                    'relation' => 'and',
                    'terms'    => [
                        [
                            'name'     => 'enable_carousel',
                            'operator' => '==',
                            'value'    => 'yes',
                        ],
                        [
                            'name'     => 'navigation',
                            'operator' => '!==',
                            'value'    => 'none',
                        ],
                        [
                            'name'     => 'navigation',
                            'operator' => '!==',
                            'value'    => 'dots',
                        ],
                        //                        [
                        //                            'name'     => 'nav_position',
                        //                            'operator' => '!==',
                        //                            'value'    => 'center',
                        //                        ],
                    ],
                ],
            ]
        );

        $this->add_responsive_control(
            'nav_spacing_horizontal',
            [
                'label'      => __('Nav Spacing Horizontal', 'medilazar-core'),
                'type'       => Controls_Manager::SLIDER,
                'range'      => [
                    'px' => [
                        'min' => -100,
                        'max' => 100,
                    ],
                ],
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}}.owl-nav-position-center .owl-theme.owl-carousel .owl-nav [class*=\'owl-\'].owl-prev' => 'left: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}}.owl-nav-position-center .owl-theme.owl-carousel .owl-nav [class*=\'owl-\'].owl-next' => 'right: {{SIZE}}{{UNIT}};',
                ],
                'conditions' => [
                    'relation' => 'and',
                    'terms'    => [
                        [
                            'name'     => 'enable_carousel',
                            'operator' => '==',
                            'value'    => 'yes',
                        ],
                        [
                            'name'     => 'navigation',
                            'operator' => '!==',
                            'value'    => 'none',
                        ],
                        [
                            'name'     => 'navigation',
                            'operator' => '!==',
                            'value'    => 'dots',
                        ],
                        [
                            'name'     => 'nav_position',
                            'operator' => '==',
                            'value'    => 'center',
                        ],
                    ],
                ],
            ]
        );


        $this->add_control(
            'pause_on_hover',
            [
                'label'     => __('Pause on Hover', 'medilazar-core'),
                'type'      => Controls_Manager::SWITCHER,
                'default'   => 'yes',
                'condition' => [
                    'enable_carousel' => 'yes'
                ],
            ]
        );

        $this->add_control(
            'autoplay',
            [
                'label'     => __('Autoplay', 'medilazar-core'),
                'type'      => Controls_Manager::SWITCHER,
                'default'   => 'yes',
                'condition' => [
                    'enable_carousel' => 'yes'
                ],
            ]
        );

        $this->add_control(
            'autoplay_speed',
            [
                'label'     => __('Autoplay Speed', 'medilazar-core'),
                'type'      => Controls_Manager::NUMBER,
                'default'   => 5000,
                'condition' => [
                    'autoplay'        => 'yes',
                    'enable_carousel' => 'yes'
                ],
                'selectors' => [
                    '{{WRAPPER}} .slick-slide-bg' => 'animation-duration: calc({{VALUE}}ms*1.2); transition-duration: calc({{VALUE}}ms)',
                ],
            ]
        );

        $this->add_control(
            'infinite',
            [
                'label'     => __('Infinite Loop', 'medilazar-core'),
                'type'      => Controls_Manager::SWITCHER,
                'default'   => 'yes',
                'condition' => [
                    'enable_carousel' => 'yes'
                ],
            ]
        );

//        $this->add_control(
//            'transition',
//            [
//                'label' => __( 'Transition', 'elementor-pro' ),
//                'type' => Controls_Manager::SELECT,
//                'default' => 'slide',
//                'options' => [
//                    'slide' => __( 'Slide', 'elementor-pro' ),
//                    'fade' => __( 'Fade', 'elementor-pro' ),
//                ],
//                'condition' => [
//                    'enable_carousel' => 'yes'
//                ],
//            ]
//        );
//
//        $this->add_control(
//            'transition_speed',
//            [
//                'label' => __( 'Transition Speed (ms)', 'elementor-pro' ),
//                'type' => Controls_Manager::NUMBER,
//                'default' => 500,
//                'condition' => [
//                    'enable_carousel' => 'yes'
//                ],
//            ]
//        );

//        $this->add_control(
//            'content_animation',
//            [
//                'label' => __( 'Content Animation', 'elementor-pro' ),
//                'type' => Controls_Manager::SELECT,
//                'default' => 'fadeInUp',
//                'options' => [
//                    '' => __( 'None', 'elementor-pro' ),
//                    'fadeInDown' => __( 'Down', 'elementor-pro' ),
//                    'fadeInUp' => __( 'Up', 'elementor-pro' ),
//                    'fadeInRight' => __( 'Right', 'elementor-pro' ),
//                    'fadeInLeft' => __( 'Left', 'elementor-pro' ),
//                    'zoomIn' => __( 'Zoom', 'elementor-pro' ),
//                ],
//                'condition' => [
//                    'enable_carousel' => 'yes'
//                ],
//            ]
//        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_carousel_style',
            [
                'label'     => __('Carousel', 'medilazar-core'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'enable_carousel' => 'yes'
                ],
            ]
        );
        $this->add_control(
            'carousel_navs_color',
            [
                'label'     => __('Nav Background Color', 'medilazar-core'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .owl-carousel .owl-nav' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .products  .owl-nav'    => 'background-color: {{VALUE}};',
                ],
            ]
        );
        $this->start_controls_tabs('tabs_nav_style');


        $this->start_controls_tab(
            'tab_nav_normal',
            [
                'label' => __('Normal', 'medilazar-core'),
            ]
        );

        $this->add_control(
            'carousel_nav_color',
            [
                'label'     => __('Arrow Color', 'medilazar-core'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .owl-theme.owl-carousel .owl-nav [class*="owl-"]:before' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'carousel_nav_bg_color',
            [
                'label'     => __('Arrow Background Color', 'medilazar-core'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .owl-theme.owl-carousel .owl-nav [class*="owl-"]:before' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'carousel_nav_border_color',
            [
                'label'     => __('Arrow Border Color', 'medilazar-core'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .owl-theme.owl-carousel .owl-nav [class*="owl-"]:before' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'carousel_dot_color',
            [
                'label'     => __('Dot Color', 'medilazar-core'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .owl-theme.owl-carousel .owl-dots .owl-dot' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        $this->end_controls_tab();
        $this->start_controls_tab(
            'tab_nav_hover',
            [
                'label' => __('Hover', 'medilazar-core'),
            ]
        );

        $this->add_control(
            'carousel_nav_color_hover',
            [
                'label'     => __('Arrow Color', 'medilazar-core'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .owl-theme.owl-carousel .owl-nav [class*="owl-"]:hover:before' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'carousel_nav_bg_color_hover',
            [
                'label'     => __('Arrow Background Color', 'medilazar-core'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .owl-theme.owl-carousel .owl-nav [class*="owl-"]:hover:before' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'carousel_nav_border_color_hover',
            [
                'label'     => __('Arrow Border Color', 'medilazar-core'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .owl-theme.owl-carousel .owl-nav [class*="owl-"]:hover:before' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'carousel_dot_color_hover',
            [
                'label'     => __('Dot Color', 'medilazar-core'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .owl-theme.owl-carousel .owl-dots .owl-dot:hover'  => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .owl-theme.owl-carousel .owl-dots .owl-dot.active' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();
    }

    protected function get_product_categories() {
        $categories = get_terms(array(
                'taxonomy'   => 'product_cat',
                'hide_empty' => false,
            )
        );
        $results    = array();
        if (!is_wp_error($categories)) {
            foreach ($categories as $category) {
                $results[$category->slug] = $category->name;
            }
        }

        return $results;
    }

    protected function get_product_type($atts, $product_type) {
        switch ($product_type) {
            case 'featured':
                $atts['visibility'] = "featured";
                break;

            case 'on_sale':
                $atts['on_sale'] = true;
                break;

            case 'best_selling':
                $atts['best_selling'] = true;
                break;

            case 'top_rated':
                $atts['top_rated'] = true;
                break;

            default:
                break;
        }

        return $atts;
    }

    protected function get_column() {
        $settings = $this->get_settings_for_display();
        $option   = [1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6];
        if ($settings['enable_carousel'] === 'yes') {
            $option = [1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7];
        }
        return $option;
    }

    protected function get_carousel_settings() {
        $settings = $this->get_settings_for_display();
        $rtl      = false;
        if (is_rtl()) {
            $rtl = true;
        }
        return array(
            'navigation'         => $settings['navigation'],
            'autoplayHoverPause' => $settings['pause_on_hover'] === 'yes' ? true : false,
            'autoplay'           => $settings['autoplay'] === 'yes' ? true : false,
            'center'             => $settings['enable_center'] === 'yes' ? true : false,
            'autoplayTimeout'    => $settings['autoplay_speed'],
            'items'              => $settings['column'],
            'items_tablet'       => $settings['column_tablet'] ? $settings['column_tablet'] : $settings['column'],
            'items_mobile'       => $settings['column_mobile'] ? $settings['column_mobile'] : 1,
            'loop'               => $settings['infinite'] === 'yes' ? true : false,
            'rtl'                => $rtl,
            'margin'             => !empty($settings['owl_item_spacing']) ? $settings['owl_item_spacing'] : 0,
            'margin_tablet'      => !empty($settings['owl_item_spacing_tablet']) ? $settings['owl_item_spacing_tablet'] : $settings['owl_item_spacing'],
            'margin_mobile'      => !empty($settings['owl_item_spacing_mobile']) ? $settings['owl_item_spacing_mobile'] : 1,
        );
    }

    protected function render_carousel_template() {
        ?>
        var carousel_settings = {
        navigation: settings.navigation,
        autoplayHoverPause: settings.pause_on_hover === 'yes' ? true : false,
        autoplay: settings.autoplay === 'yes' ? true : false,
        autoplayTimeout: settings.autoplay_speed,
        items: settings.column,
        items_tablet: settings.column_tablet ? settings.column_tablet : settings.column,
        items_mobile: settings.column_mobile ? settings.column_mobile : 1,
        loop: settings.infinite === 'yes' ? true : false,
        margin: settings.owl_item_spacing ? settings.owl_item_spacing : 0,
        margin_tablet: settings.owl_item_spacing_tablet ? settings.owl_item_spacing_tablet : settings.owl_item_spacing,
        margin_mobile: settings.owl_item_spacing_mobile ? settings.owl_item_spacing_mobile : 1,
        };
        <?php
    }

    /**
     * Render tabs widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since  1.0.0
     * @access protected
     */
    protected function render() {
        $settings = $this->get_settings_for_display();
        $this->woocommerce_default($settings);
    }

    private function woocommerce_default($settings) {
        $type = 'products';
        $atts = [
            'limit'          => $settings['limit'],
            'columns'        => $settings['column'],
            'orderby'        => $settings['orderby'],
            'order'          => $settings['order'],
            'product_layout' => $settings['product_layout'],
            'class'          => 'elementor-product-style-' . $settings['product_layout'],
        ];

        if ($settings['product_layout'] == 'grid') {
            $atts['class']          = 'woocommerce-product-grid';
            $atts['product_layout'] = 'grid';

            if (!empty($settings['grid_layout'])) {
                $atts['class'] .= ' woocommerce-product-grid-' . $settings['grid_layout'];
            }
        }

        if ($settings['product_layout'] == 'list') {
            $atts['class']          = 'woocommerce-product-list';
            $atts['product_layout'] = 'grid';

            if (!empty($settings['list_layout'])) {
                $atts['class'] .= ' woocommerce-product-list-' . $settings['list_layout'];
            }

            if (!empty($settings['list_layout']) && $settings['list_layout'] == 3) {
                $atts['product_layout'] = 'list';
                $atts['show_rating']    = true;
            }
        }

        $atts = $this->get_product_type($atts, $settings['product_type']);

        if (isset($atts['on_sale']) && wc_string_to_bool($atts['on_sale'])) {
            $type = 'sale_products';
            if (!empty($settings['show_time_sale']) && $settings['show_time_sale'] == 'yes') {
                $atts['show_time_sale'] = true;
            }
        } elseif (isset($atts['best_selling']) && wc_string_to_bool($atts['best_selling'])) {
            $type = 'best_selling_products';
        } elseif (isset($atts['top_rated']) && wc_string_to_bool($atts['top_rated'])) {
            $type = 'top_rated_products';
        }

        if (!empty($settings['categories'])) {
            $atts['category']     = implode(',', $settings['categories']);
            $atts['cat_operator'] = $settings['cat_operator'];
        }

        // Carousel
        if ($settings['enable_carousel'] === 'yes') {
            $atts['carousel_settings'] = json_encode(wp_slash($this->get_carousel_settings()));
            $atts['enable_carousel']   = 'yes';

        } else {

            if (!empty($settings['column_tablet'])) {
                $atts['class'] .= ' columns-tablet-' . $settings['column_tablet'];
            }

            if (!empty($settings['column_mobile'])) {
                $atts['class'] .= ' columns-mobile-' . $settings['column_mobile'];
            }
        }

        if ($settings['paginate'] === 'pagination') {
            $atts['paginate'] = 'true';
        }

        $shortcode = new WC_Shortcode_Products($atts, $type);

        echo $shortcode->get_content();
    }
}
