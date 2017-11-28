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
     * Wheteher to bypass `image_downsize` filter hook.
     *  
     * @since 1.0.0
     * @var bool
     */
    protected $bypass_image_downsize;

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
    
    /**
     * Generate intermediate image size if if doesn't exists yet.
     *
     * Hooked on `image_downsize` filter.
     * 
     * @since 1.0.0
     * @param bool $downsize        Whether to short-circuit the image downsize. Default false.
     * @param int $id               Attachment ID for image.
     * @param array|string $size    Size of image. Image size or array of width and height values (in that order).
     *                              Default 'medium'.
     * @return bool|array           Array containing the image URL, width, height, and boolean for whether
     *                              the image is an intermediate size. False on failure.
     */
    public function maybe_generate_intermediate_image($downsize, $id, $size) {
        /**
         * No need to generate if array $size is provided. WordPress itself does not generate it,
         * but instead find the best match image size. {@see image_get_intermediate_size()}
         */
        if ($this->bypass_image_downsize || $downsize !== false || is_array($size)) {
            return $downsize;
        }
        
        $existing_image = $this->find_existing_image($id, $size);
        
        // Image already exists, return it.
        if ($existing_image) {
            return $existing_image;
        }
        
        require_once plugin_dir_path(__FILE__) . 'class-fr-thumbnails-folder-image-resizer.php';
        
        $image_resizer  = new Fr_Thumbnails_Folder_Image_Resizer(array(
                            'id'    => $id, 
                            'size'  => $size
                        ));
        $result         = $image_resizer->resize();
        
        return $result ? $result : $downsize;
    }
    
    /**
     * Get the URL of an image attachment without handing `image_downsize` filter.
     *
     * @since 1.0.0
     * @param int $id               Image attachment ID.
     * @param string|array $size    Optional. Image size to retrieve. Accepts any valid image size, or an array
     *                              of width and height values in pixels (in that order). Default 'thumbnail'.
     * @param bool $icon            Optional. Whether the image should be treated as an icon. Default false.
     * @return string|false         Attachment URL or false if no image is available.
     */
    public function get_attachment_image_url($id, $size = 'thumbnail', $icon = false) {
        $this->bypass_image_downsize    = true;
        $image_url                      = wp_get_attachment_image_url($id, $size, $icon);
        $this->bypass_image_downsize    = false;
        
        return $image_url;
    }
    
    /**
     * Find an existing image size.
     * 
     * @since 1.0.0
     * @param int $id               Attachment ID.
     * @param array|string $size    Size of image. Image size or array of width and height values (in that order).
     * @return null|array           Array containing the image URL, width, height, and boolean for whether
     *                              the image is an intermediate size. Null if does not exist.
     */
    protected function find_existing_image($id, $size) {       
        $image_size = image_get_intermediate_size($id, $size);
        
        if (!$image_size) {
            return;
        }
        
        $image_url = $this->get_attachment_image_url($id, $size);
                
        if (!$image_url) {
            return;
        }
        
        return array(
            $image_url,
            $image_size['width'],
            $image_size['height'],
            true,
        );
    }
}
