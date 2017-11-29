<?php

/**
 * The image editor functionality of the plugin.
 *
 * @since 1.0.0
 * @package    Fr_Thumbnails_Folder
 * @author     Fahri Rusliyadi <fahri.rusliyadi@gmail.com>
 */
class Fr_Thumbnails_Folder_Image_Editors {
    /**
     * Register our editors to the list of image editing library classes.
     * 
     * Hooked on `wp_image_editors` filter.
     *
     * @since 1.0.0
     * @param array $image_editors List of available image editors. Defaults are
     *                             'WP_Image_Editor_Imagick', 'WP_Image_Editor_GD'.
     * @return array
     */
    public function register_editors($image_editors) {
        // Include the existing classes first in order to extend them.
        require_once ABSPATH . WPINC . '/class-wp-image-editor.php';
        require_once ABSPATH . WPINC . '/class-wp-image-editor-imagick.php';
        require_once ABSPATH . WPINC . '/class-wp-image-editor-gd.php';
        require_once plugin_dir_path(__FILE__) . 'image-editors/class-fr-thumbnails-folder-image-editor-imagick.php';
        require_once plugin_dir_path(__FILE__) . 'image-editors/class-fr-thumbnails-folder-image-editor-gd.php';
        
        // Prepend our custom image editor class so WordPress will check if our engine can 
        // handle resizing before testing other engines.
        array_unshift($image_editors, 'Fr_Thumbnails_Folder_Image_Editor_Gd');
        array_unshift($image_editors, 'Fr_Thumbnails_Folder_Image_Editor_Imagick');
        
        return $image_editors;
    }
}
