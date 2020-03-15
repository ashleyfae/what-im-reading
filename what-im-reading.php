<?php
/**
 * Plugin Name: What I'm Reading
 * Plugin URI: https://github.com/ashleyfae/what-im-reading/
 * Description: A widget for displaying books on one of your Goodreads shelves
 * Version: 1.0.2
 * Author: Ashley Gibson
 * Author URI: https://www.nosegraze.com
 * License: GPL2
 * Text Domain: what-im-reading
 * Domain Path: lang
 *
 * @package   what-im-reading
 * @copyright Copyright (c) 2020, Ashley Gibson
 * @license   GPL2+
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Include the main plugin class.
 */
require_once plugin_dir_path( __FILE__ ) . 'class-what-im-reading.php';

/**
 * Returns the main instance of What_Im_Reading.
 */
function What_Im_Reading() {
	return What_Im_Reading::instance( __FILE__, '1.0.2' );
}

What_Im_Reading();