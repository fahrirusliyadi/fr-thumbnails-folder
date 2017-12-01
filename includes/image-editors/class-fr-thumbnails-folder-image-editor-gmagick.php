<?php

/**
 * Image Editor Class for Image Manipulation through Gmagick PHP Module.
 *
 * Override to change the folder location.
 * 
 * @since 1.0.0
 * @package    Fr_Thumbnails_Folder
 * @subpackage Fr_Thumbnails_Folder/image-editors
 * @author     Fahri Rusliyadi <fahri.rusliyadi@gmail.com>
 */

if (class_exists('EWWWIO_Gmagick_Editor')) {
    /**
     * {@inheritdoc}
     * 
     * @since 1.0.0
     */
    class Fr_Thumbnails_Folder_Image_Editor_Gmagick extends EWWWIO_Gmagick_Editor {
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
} else {
    /**
     * {@inheritdoc}
     * 
     * @since 1.0.0
     */
    class Fr_Thumbnails_Folder_Image_Editor_Gmagick extends WP_Image_Editor_Gmagick {
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
}