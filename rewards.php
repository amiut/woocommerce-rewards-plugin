<?php
/**
 * Plugin Name: Customer Rewards
 * Description: Customer rewards plugin for woocommerce
 * Plugin URI:  https://wwww.dornaweb.com
 * Version:     1.0
 * Author:      Dornaweb
 * Author URI:  https://wwww.dornaweb.com
 * License:     GPL
 * Text Domain: dwebcr
 * Domain Path: /languages
 *
 * @package Woocommerce Wishlist Plugin
 */

namespace Dornaweb;

defined("ABSPATH") || exit;

if (!defined("DWEB_REWARDS_PLUGIN_FILE")) {
    define("DWEB_REWARDS_PLUGIN_FILE", __FILE__);
}

/**
 * Load core packages and the autoloader.
 * The SPL Autoloader needs PHP 5.6.0+ and this plugin won't work on older versions
 */
if (version_compare(PHP_VERSION, "5.6.0", ">=")) {
    require __DIR__ . "/includes/class-autoloader.php";
}

/**
 * Returns the main instance of CustomerRewards.
 *
 * @since  1.0
 * @return CustomerRewards\App
 */
function dwebcr()
{
    // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
    return CustomerRewards\App::instance();
}

// Global for backwards compatibility.
$GLOBALS["dwebcr"] = dwebcr();
