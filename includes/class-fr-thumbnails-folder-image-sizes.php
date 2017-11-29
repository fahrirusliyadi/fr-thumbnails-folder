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
    public function disable_image_sizes_generation() {
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
        if ($downsize !== false || is_array($size)) {
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
     * Delete intermediate image sizes.
     *
     * Hooked on `delete_attachment` filter.
     * 
     * @since 1.0.0
     * @param int $post_id Attachment ID.
     */
    public function delete_image_sizes($post_id) {
        $metadata = wp_get_attachment_metadata($post_id);
        
	if (isset($metadata['sizes']) && is_array($metadata['sizes'])) {
            foreach ($metadata['sizes'] as $sizeinfo) {
                if (isset($sizeinfo['path'])) {
                    unlink($sizeinfo['path']);
                }
            }
	}
    }
    
    /**
     * Get the URL of an image attachment.
     *
     * @since 1.0.0
     * @param int $id               Image attachment ID.
     * @param string|array $size    Optional. Image size to retrieve. Accepts any valid image size, or an array
     *                              of width and height values in pixels (in that order). Default 'thumbnail'.
     * @return string|false         Attachment URL or false if no image is available.
     */
    public function get_image_size_url($id, $size) {
        $upload_dir = wp_get_upload_dir();
        
        if (!$upload_dir) {
            return;
        }
        
        $image_size = image_get_intermediate_size($id, $size);
        
        if (!$image_size) {
            return;
        }
        
        $image_url = str_replace($upload_dir['basedir'], $upload_dir['baseurl'], $image_size['path']);
        
        return $image_url;
    }
    
    /**
     * Get the image sizes folder.
     * 
     * @since 1.0.0
     * @return string
     */
    public function get_image_sizes_folder() {
        return 'sizes';
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
        
        $image_url = $this->get_image_size_url($id, $size);
                
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
