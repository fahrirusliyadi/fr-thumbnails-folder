<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://profiles.wordpress.org/fahrirusliyadi
 * @since      1.0.0
 *
 * @package    Fr_Thumbnails_Folder
 * @subpackage Fr_Thumbnails_Folder/admin
 */

/**
 * The admin-specific functionality of the plugin.
 * @package    Fr_Thumbnails_Folder
 * @subpackage Fr_Thumbnails_Folder/admin
 * @author     Fahri Rusliyadi <fahri.rusliyadi@gmail.com>
 */
class Fr_Thumbnails_Folder_Admin {
    /**
     * Add tools submenu page.
     * 
     * Hooked on `admin_menu` action.
     * 
     * @since 1.0.0
     */
    public function add_tools_page() {
        add_submenu_page(
            'tools.php',
            __('Delete Thumbnails', 'fr-thumbnails-folder'),
            __('Delete Thumbnails', 'fr-thumbnails-folder'),
            'manage_options',
            'fr_thumbnails_folder_delete_image_sizes',
            array($this, 'delete_image_sizes_tool_page')
        );
    }
    
    /**
     * Display "Delete Thumbnails" tools page content.
     * 
     * @since 1.0.0
     */
    public function delete_image_sizes_tool_page() {
        // Check user capabilities.
        if (!current_user_can('manage_options')) {
            return;
        }
        
        include plugin_dir_path(__FILE__) . 'partials/delete-image-sizes-tool-page.php';
    }
    
    /**
     * Delete all intermediate image sizes.
     *
     * Hook on `wp_ajax_{$action}` action. The dynamic portion of the hook name, 
     * `$action`, the name of the AJAX action callback being fired, which is `fr_thumbnails_folder_delete_image_sizes`.
     *
     * @since 1.0.0
     */
    public function delete_image_sizes() {
        check_ajax_referer('fr_thumbnails_folder', 'nonce');
                
        $paged          = filter_input(INPUT_POST, 'paged', FILTER_SANITIZE_NUMBER_INT);
        $query          = new WP_Query();
        $posts          = $query->query(array(
                            'paged'          => $paged,
                            'posts_per_page' => 1,
                            'order'          => 'ASC',
                            'orderby'        => 'ID',
                            'post_mime_type' => 'image',
                            'post_status'    => 'any',
                            'post_type'      => 'attachment',
                        ));
        
        foreach ($posts as $post) {
            fr_thumbnails_folder()->get_image_sizes()->delete_all_image_sizes($post->ID);
        }
        
        echo wp_json_encode(array(
            'total'     => (int) $query->found_posts,
            'deleted'   => (int) $paged,
        ));
        
        wp_die();
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * Hooked on `admin_enqueue_scripts` action.
     * 
     * @since 1.0.0
     * @param string $hook_suffix The current admin page.
     */
    public function enqueue_scripts($hook_suffix) {
        if ($hook_suffix != 'tools_page_fr_thumbnails_folder_delete_image_sizes') {
            return;
        }
        
        wp_enqueue_script(fr_thumbnails_folder()->get_plugin_name(), plugin_dir_url( __FILE__ ) . 'js/fr-thumbnails-folder-admin.js', array('jquery'), fr_thumbnails_folder()->get_version(), true);
        wp_localize_script(fr_thumbnails_folder()->get_plugin_name(), 'fr_thumbnails_folder', array(
            'nonce' => wp_create_nonce('fr_thumbnails_folder'),
            'l10n'  => array(
                'start'     => __('Begin deleting thumbnails.', 'fr-thumbnails-folder'),
                'status'    => __('Number of images deleted: %1$s.', 'fr-thumbnails-folder'),
            )
        ));
    }
}
