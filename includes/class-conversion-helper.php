<?php
/**
 * Conversion helper class
 *
 * @package CustomerRewards
 * @since   1.0
 */

namespace Dornaweb\CustomerRewards;

defined('ABSPATH') || exit;


class Conversion_Helper {
    public static function set_rate($currency, $rate) {
        $current = self::get_rate_object($currency);

        if ($current) {
            $current->set_rate($rate);
            $current->save();
        } else {
            $new = new Conversion_Rate();
            $new->set_currency($currency);
            $new->set_rate($rate);
            $new->save();
        }
    }

    public static function delete_rate($currency) {
        $data_store = Data_Store::load('conversion-rate');
        $rate = $data_store->get_rate_by_currency_name($currency);

        if ($rate) {
            $rate->delete();
        }
    }

    public static function get_rates() {
        $return = [];
        $data_store = Data_Store::load('conversion-rate');

        $rates = $data_store->get_rates();

        foreach ($rates as $rate) {
            $return[$rate->get_currency()] = $rate->get_rate();
        }

        return $return;
    }

    public static function get_rate_object($currency = '') {
        if (!$currency) {
            $currency = get_woocommerce_currency();
        }

        $data_store = Data_Store::load('conversion-rate');
        return $data_store->get_rate_by_currency_name($currency);
    }

    public static function get_rate($currency = '') {
        $rate = self::get_rate_object($currency);

        return $rate ? $rate->get_rate() : false;
    }
}
