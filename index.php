<?php

/**
 * Plugin Name: Skytake Leads & Sales
 * Description: Increase your leads & sales TODAY!
 * Version: 0.34.0
 * Author: Delabon.com
 * Author Uri: https://delabon.com
 * Text Domain: skytake
 * Domain Path: /languages
**/

defined( 'ABSPATH' ) or die( 'Mmmmm Funny ?' );

# Config
define( 'SKYTAKE_VERSION', '0.34.0' );
define( 'SKYTAKE_URL', plugins_url( '',__FILE__) );
define( 'SKYTAKE_PATH', __DIR__ );
define( 'SKYTAKE_DOC_URL', 'https://delabon.com/knowledge-base/28' );
define( 'SKYTAKE_PLUGINS_URL', 'https://delabon.com/store' );
define( 'SKYTAKE_SUPPORT_URL', 'https://delabon.com/support' );
define( 'SKYTAKE_UPGRADE_URL', '#' );
define( 'SKYTAKE_REVIEW_URL', 'https://wordpress.org/support/plugin/skytake/reviews/?rate=5#rate-response' );
define( 'SKYTAKE_ENVATO_ITEM_NAME', 'skytake'); // USE THE NAME ONLY
define( 'SKYTAKE_ENVATO_VALIDATION_URL', 'https://delabon.com/ajax/envato/item_activation');

# Auoloader
require_once __DIR__ . '/lib/delabon/wp/class-autoload.php';

use Delabon\WP\Autoload;
use SKYT\Plugin;

Autoload::add( 'Delabon', __DIR__ . '/lib/delabon' );
Autoload::add( 'SKYT', __DIR__ . '/inc' );
Autoload::init();

/**
 * Main Instance of Skytake
 *
 * @return Plugin
 */
function skytake(){
    return Plugin::instance();
}

// Init
skytake();
