<?php

/**
 * Intermediate image sizes functionality.
 *
 * @since      1.0.0
 * @package    Fr_Thumbnails_Folder
 * @subpackage Fr_Thumbnails_Folder/includes
 * @author     Fahri Rusliyadi <fahri.rusliyadi@gmail.com>
 */
class Fr_Thumbnails_Folder_Image_Sizes {
    /**
     * Remove the all image sizes that will automatically generated when uploading an image.
     *
     * Hooked on `intermediate_image_sizes_advanced` filter.
     * 
     * @since 1.0.0
     * @param array $sizes    An associative array of image sizes.
     * @param array $metadata An associative array of image metadata: width, height, file.
     * @return array
     */
    public function remove_image_sizes() {
        return array();
    }
}
