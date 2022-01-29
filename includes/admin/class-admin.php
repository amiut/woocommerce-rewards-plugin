<?php
namespace Dornaweb\CustomerRewards\Admin;
use \Dornaweb\CustomerRewards\Utils\Singleton_Trait;

defined( 'ABSPATH' ) || exit;

class Admin {
    use Singleton_Trait;

    public function init() {
        add_filter('woocommerce_get_sections_advanced', [$this, 'append_tab'], 999);
        add_filter('woocommerce_get_settings_advanced', [$this, 'get_settings_for_rewards_section'], 999, 2);
        add_action('woocommerce_settings_advanced', [$this, 'output'], 999);
        add_action('woocommerce_update_options_advanced', [$this, 'save'], 999);
    }

    public function append_tab($tabs) {
        $tabs['rewards'] = __('Rewards', 'dwebcr');

        return $tabs;
    }

    public function get_settings_for_rewards_section($settings, $section_id) {
        if ($section_id === 'rewards') {
            $settings = [
                [
                    'title' => __( 'Rewards Settings', 'dwebcr' ),
					'desc'  => __( 'You can tweak Points and Rewards plugin settings in this page', 'dwebcr' ),
					'type'  => 'title',
					'id'    => 'advanced_rewards_options',
                ],

                [
					'title'    => __( 'Min Swap amount', 'dwebcr' ),
					'desc'     => __( 'Minimum number of coins user needs for swapping', 'dwebcr' ),
					'id'       => 'rewards_min_swap',
					'default'  => '',
					'type'     => 'number',
					'desc_tip' => true,
                ],

                [
					'type' => 'sectionend',
					'id'   => 'advanced_rewards_options',
                ],
            ];
        }

        return $settings;
    }

    public function output() {
        global $current_section;

        if ($current_section !== 'rewards') return;

        include DWEB_REWARDS_ABSPATH . '/includes/admin/views/settings.php';
    }

    public function save() {
        global $current_section;

        if ($current_section !== 'rewards') return;

        $rates = \Dornaweb\CustomerRewards\Conversion_Helper::get_rates();
        foreach ($rates as $currency => $rate) {
            \Dornaweb\CustomerRewards\Conversion_Helper::delete_rate($currency);
        }

        $new_rates = !empty($_POST['rates']) ? array_filter((array) $_POST['rates']) : [];
        $new_rates = array_combine(array_map('strtoupper', $new_rates['currency']), array_map('floatval', $new_rates['rate']));

        foreach ($new_rates as $currency => $rate) {
            if (floatval($rate) > 0) {
                \Dornaweb\CustomerRewards\Conversion_Helper::set_rate($currency, floatval($rate));
            }
        }

        $min_swap_amount = !empty($_POST['rewards_min_swap']) ? absint($_POST['rewards_min_swap']) : 0;

        if ($min_swap_amount) {
            update_option('rewards_min_swap', $min_swap_amount);
        }
    }
}
