<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://profiles.wordpress.org/fahrirusliyadi
 * @since      1.0.0
 *
 * @package    Fr_Thumbnails_Folder
 * @subpackage Fr_Thumbnails_Folder/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Fr_Thumbnails_Folder
 * @subpackage Fr_Thumbnails_Folder/includes
 * @author     Fahri Rusliyadi <fahri.rusliyadi@gmail.com>
 */
class Fr_Thumbnails_Folder {
    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Fr_Thumbnails_Folder_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;
    
    /**
     * The reference to the class that responsible for manage image sizes.
     * 
     * @since 1.0.0
     * @var Fr_Thumbnails_Folder_Image_Sizes
     */
    protected $image_sizes;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct() {
        if ( defined( 'PLUGIN_NAME_VERSION' ) ) {
                $this->version = PLUGIN_NAME_VERSION;
        } else {
                $this->version = '1.0.0';
        }
        $this->plugin_name = 'fr-thumbnails-folder';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_image_sizes_hooks();
        $this->define_image_editor_hooks();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Fr_Thumbnails_Folder_Loader. Orchestrates the hooks of the plugin.
     * - Fr_Thumbnails_Folder_i18n. Defines internationalization functionality.
     * - Fr_Thumbnails_Folder_Admin. Defines all hooks for the admin area.
     * - Fr_Thumbnails_Folder_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {
        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-fr-thumbnails-folder-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-fr-thumbnails-folder-i18n.php';

        /**
         * The class responsible for manage image sizes.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-fr-thumbnails-folder-image-sizes.php';
                
        /**
         * The class responsible for helping image editor.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-fr-thumbnails-folder-image-editor-helper.php';
                
        /**
         * The class responsible for defining image editor functionality
         * of the plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-fr-thumbnails-folder-image-editors.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fr-thumbnails-folder-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-fr-thumbnails-folder-public.php';

        $this->loader       = new Fr_Thumbnails_Folder_Loader();
        $this->image_sizes  = new Fr_Thumbnails_Folder_Image_Sizes();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Fr_Thumbnails_Folder_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {

            $plugin_i18n = new Fr_Thumbnails_Folder_i18n();

            $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

    }

    /**
     * Register all of the hooks related to the image sizes functionality
     * of the plugin.
     *
     * @since    1.0.0
     */
    private function define_image_sizes_hooks() {
        $this->loader->add_filter('intermediate_image_sizes_advanced', $this->image_sizes, 'disable_image_sizes_generation', 10, 2);
        $this->loader->add_filter('image_downsize', $this->image_sizes, 'maybe_generate_intermediate_image', 10, 3);
        $this->loader->add_action('delete_attachment', $this->image_sizes, 'delete_image_sizes', 10, 3);
    }
    
    /**
     * Register all of the hooks related to the image editor functionality
     * of the plugin.
     *
     * @since    1.0.0
     */
    private function define_image_editor_hooks() {
        $plugin_image_editors = new Fr_Thumbnails_Folder_Image_Editors();

        $this->loader->add_filter('wp_image_editors', $plugin_image_editors, 'register_editors');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {

//		$plugin_admin = new Fr_Thumbnails_Folder_Admin( $this->get_plugin_name(), $this->get_version() );
//
//		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
//		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {

//		$plugin_public = new Fr_Thumbnails_Folder_Public( $this->get_plugin_name(), $this->get_version() );
//
//		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
//		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
            $this->loader->run();
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Fr_Thumbnails_Folder_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
            return $this->loader;
    }

    /**
     * The reference to the class that responsible for manage image sizes.
     *
     * @since     1.0.0
     * @return    Fr_Thumbnails_Folder_Image_Sizes
     */
    public function get_image_sizes() {
        return $this->image_sizes;
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
            return $this->plugin_name;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
            return $this->version;
    }
}
