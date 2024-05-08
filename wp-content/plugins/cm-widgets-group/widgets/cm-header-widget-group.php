<?php

namespace CM_Widgets_Group;

use Elementor\Widget_Base;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class OSF_Elementor_Header_Group   extends Widget_Base {

    public function get_name() {
        return 'opal-header-group';
    }

    public function get_title() {
        return __('CM Header', 'medilazar-core');
    }

    public function get_icon() {
        return 'eicon-lock-user';
    }

    public function get_categories() {
        return ['general'];
    }

    // public function get_categories() {
    //     return ['opal-addons'];
    // }


    protected function render() {
        echo '<div>Test Widget Output</div>';
    }


}

// $widgets_manager->register(new OSF_Elementor_Header_Group());