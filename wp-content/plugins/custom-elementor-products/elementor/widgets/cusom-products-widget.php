<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

class Custom_Elementor_Products extends OSF_Elementor_Products {

    public function get_categories() {
        return array('opal-addons');
    }

    public function query_products() {
        $query = parent::query_products();

        $user_id = get_current_user_id();
        $restricted_categories_names = get_field('User_Restricted_Products', 'user_' . $user_id);

        if ($restricted_categories_names) {
            $category_names = explode(',', $restricted_categories_names);
            $category_ids = [];

            foreach ($category_names as $category_name) {
                $term = get_term_by('name', trim($category_name), 'product_cat');
                if ($term) {
                    $category_ids[] = (int) $term->term_id;
                }
            }

            if (!empty($category_ids)) {
                $tax_query = $query->get('tax_query');
                if (!is_array($tax_query)) {
                    $tax_query = [];
                }
                $tax_query[] = [
                    'taxonomy' => 'product_cat',
                    'field'    => 'term_id',
                    'terms'    => $category_ids,
                    'operator' => 'NOT IN',
                ];
                $query->set('tax_query', $tax_query);
            }
        }

        return $query;
    }
}

Plugin::instance()->widgets_manager->register_widget_type(new Custom_Elementor_Products());
