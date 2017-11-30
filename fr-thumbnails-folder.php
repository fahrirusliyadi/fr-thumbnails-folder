<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://profiles.wordpress.org/fahrirusliyadi
 * @since             1.0.0
 * @package           Fr_Thumbnails_Folder
 *
 * @wordpress-plugin
 * Plugin Name:       Fr Thumbnails Folder
 * Plugin URI:        https://wordpress.org/plugins/fr-thumbnails-folder/
 * Description:       Move thumbnails file location to `wp-content/uploads/thumbnails` directory.
 * Version:           1.0.0
 * Author:            Fahri Rusliyadi
 * Author URI:        https://profiles.wordpress.org/fahrirusliyadi
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       fr-thumbnails-folder
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Currently pligin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PLUGIN_NAME_VERSION', '1.0.0' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-fr-thumbnails-folder.php';

/**
 * Returns the core class of the plugin.
 * 
 * @since 1.0.0
 * @staticvar Fr_Thumbnails_Folder $plugin
 * @return Fr_Thumbnails_Folder
 */
function fr_thumbnails_folder() {
    static $plugin = null;
    
    if (!$plugin) {
        $plugin = new Fr_Thumbnails_Folder();
    }
    
    return $plugin;
}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
fr_thumbnails_folder()->run();
