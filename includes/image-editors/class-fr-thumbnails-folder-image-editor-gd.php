<?php

// Include the existing classes first in order to extend them.
require_once ABSPATH . WPINC . '/class-wp-image-editor.php';
require_once ABSPATH . WPINC . '/class-wp-image-editor-gd.php';

/**
 * WordPress Image Editor Class for Image Manipulation through GD.
 *
 * @since 1.0.0
 * @package    Fr_Thumbnails_Folder
 * @subpackage Fr_Thumbnails_Folder/image-editors
 * @author     Fahri Rusliyadi <fahri.rusliyadi@gmail.com>
 */
class Fr_Thumbnails_Folder_Image_Editor_GD extends WP_Image_Editor_GD {
    /**
     * {@inheritdoc}
     * 
     * @since 1.0.0
     */
    public function generate_filename($suffix = null, $dest_path = null, $extension = null) {
        $generated_filename = parent::generate_filename($suffix, $dest_path, $extension);   
        $upload_dir         = wp_get_upload_dir();
        
        if (!$upload_dir) {
            return $generated_filename;
        }
        
        $sizes_folder   = fr_thumbnails_folder()->get_image_sizes()->get_image_sizes_folder();
        $new_dir        = path_join($upload_dir['basedir'], $sizes_folder);
        $new_filename   = str_replace($upload_dir['basedir'], $new_dir, $generated_filename);
        
        return $new_filename;
    }
}
