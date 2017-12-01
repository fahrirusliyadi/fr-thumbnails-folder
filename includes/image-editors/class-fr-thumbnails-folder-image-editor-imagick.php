<?php

/**
 * Image Editor Class for Image Manipulation through Imagick PHP Module.
 * 
 * Override to change the folder location.
 *
 * @since 1.0.0
 * @package    Fr_Thumbnails_Folder
 * @subpackage Fr_Thumbnails_Folder/image-editors
 * @author     Fahri Rusliyadi <fahri.rusliyadi@gmail.com>
 */

if (class_exists('EWWWIO_Imagick_Editor')) {
    /**
     * {@inheritdoc}
     *
     * @since 1.0.0
     */
    class Fr_Thumbnails_Folder_Image_Editor_Imagick extends EWWWIO_Imagick_Editor {
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
} else if (class_exists('S3_Uploads_Image_Editor_Imagick')) {
    /**
     * {@inheritdoc}
     *
     * @since 1.0.0
     */
    class Fr_Thumbnails_Folder_Image_Editor_Imagick extends S3_Uploads_Image_Editor_Imagick {
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
} else if (class_exists('WP_Thumb_Image_Editor_Imagick')) {
    /**
     * {@inheritdoc}
     *
     * @since 1.0.0
     */
    class Fr_Thumbnails_Folder_Image_Editor_Imagick extends WP_Thumb_Image_Editor_Imagick {
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
} else if (class_exists('BFI_Image_Editor_Imagick')) {
    /**
     * {@inheritdoc}
     *
     * @since 1.0.0
     */
    class Fr_Thumbnails_Folder_Image_Editor_Imagick extends BFI_Image_Editor_Imagick {
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
} else if (class_exists('WP_Image_Editor_Respimg')) {
    /**
     * {@inheritdoc}
     *
     * @since 1.0.0
     */
    class Fr_Thumbnails_Folder_Image_Editor_Imagick extends WP_Image_Editor_Respimg {
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
}