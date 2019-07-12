<?php
/**
 * Plugin Name: Delete Post Swrs
 * Plugin URI:  http://sourov.im/
 * Description: This plugin will delete post of any post type.
 * Version:     1.0.1
 * Author:      Sourov Roy
 * Author URI:  http://sourov.im/
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: delete-post-swrs
 * Domain Path: /languages
 */

/*
Delete Post Swrs is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Delete Post Swrs is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Delete Post Swrs. If not, see http://www.gnu.org/licenses/gpl-2.0.txt.
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define necessary constant
 */
if( ! defined( 'DPS_ROOT_PATH' ) ){
	define('DPS_ROOT_PATH', plugin_dir_path( __FILE__ ));
}

if( ! defined( 'DPS_BASENAME' ) ){
	define('DPS_BASENAME', plugin_basename(__FILE__));
}

if( ! defined( 'DPS_ROOT_URL' ) ){
	define('DPS_ROOT_URL', plugin_dir_url(__FILE__));
}


/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require_once DPS_ROOT_PATH . 'includes/class-delete-post-swrs.php';
