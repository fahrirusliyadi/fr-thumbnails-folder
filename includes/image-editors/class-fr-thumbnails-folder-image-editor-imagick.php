<?php

/**
 * {@inheritdoc}
 *
 * @since 1.0.0
 * @package    Fr_Thumbnails_Folder
 * @subpackage Fr_Thumbnails_Folder/image-editors
 * @author     Fahri Rusliyadi <fahri.rusliyadi@gmail.com>
 */
class Fr_Thumbnails_Folder_Image_Editor_Imagick extends WP_Image_Editor_Imagick {
    /**
     * {@inheritdoc}
     * 
     * @since 1.0.0
     */
    public function generate_filename($suffix = null, $dest_path = null, $extension = null) {
        $generated_filename = parent::generate_filename($suffix, $dest_path, $extension);  
        
        return Fr_Thumbnails_Folder_Image_Editor_Helper::modify_filename($generated_filename);
    }
}
