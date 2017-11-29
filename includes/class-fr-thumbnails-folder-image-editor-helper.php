<?php

/**
 * Image Editor helper.
 *
 * @since 1.0.0
 * @package    Fr_Thumbnails_Folder
 * @subpackage Fr_Thumbnails_Folder/image-editors
 * @author     Fahri Rusliyadi <fahri.rusliyadi@gmail.com>
 */
class Fr_Thumbnails_Folder_Image_Editor_Helper {
    /**
     * Modify intermediate image size file name to move its location.
     * 
     * @since 1.0.0
     */
    public static function modify_filename($generated_filename) {
        $upload_dir = wp_get_upload_dir();
        
        if (!$upload_dir) {
            return $generated_filename;
        }
        
        $sizes_folder   = fr_thumbnails_folder()->get_image_sizes()->get_image_sizes_folder();
        $new_dir        = path_join($upload_dir['basedir'], $sizes_folder);
        $new_filename   = str_replace($upload_dir['basedir'], $new_dir, $generated_filename);
        
        return $new_filename;
    }
}
