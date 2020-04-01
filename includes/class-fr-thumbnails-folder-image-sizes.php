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
     * Remove the all image sizes that will automatically generated when uploading a non-image mime types.
     *
     * Hooked on `fallback_intermediate_image_sizes` filter.
     * 
     * @since 1.4.0
     * @param string[] $fallback_sizes An array of image size names.
     * @param array    $metadata       Current attachment metadata.
     * @return array
     */
    public function disable_image_sizes_generation_for_non_image_mime_types() {
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
         
        $metadata = wp_get_attachment_metadata($id);
                
        // Skip if
        if (
            // thumbnail exists,
            isset($metadata['sizes'][$size]) && 
            // but still in the default location.
            (!isset($metadata['sizes'][$size]['path']) || stripos($metadata['sizes'][$size]['path'], $this->get_image_sizes_path($id, $size)) === false)
        ) {
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
	 * Convert non-image attachment to image.
     * 
     * Since we have disabled non-image image sizes, WordPress will not convert it, so we need to do it manually.
     * @link https://github.com/WordPress/WordPress/blob/9ff4499281663b0c772787fd4a60538288f842e9/wp-admin/includes/image.php#L574-L606
	 *
     * Hooked on `wp_generate_attachment_metadata` filter.
	 *
     * @since 1.4.0
	 * @param array  $metadata      An array of attachment meta data.
	 * @param int    $attachment_id Current attachment ID.
	 * @param string $context       Additional context. Can be 'create' when metadata was initially created for new attachment
	 *                              or 'update' when the metadata was updated.
     * @return array
	 */
    public function maybe_convert_to_image($metadata, $attachment_id, $context) {
        // Metadata was not initially created for new attachment.
        if ($context !== 'create') {
            return $metadata;
        }

        // Image already exists.
        if (isset($metadata['sizes']['full'])) {
            return $metadata;
        }

        // It's an image, no need to convert.
        if (wp_attachment_is_image($attachment_id)) {
            return $metadata;
        }

        $metadata = $metadata ? $metadata : array();
        $file = get_attached_file($attachment_id);
        $image_editor = wp_get_image_editor($file);

        // No support for this type of file.
        if (!is_wp_error($image_editor)) {
            /*
             * PDFs may have the same file filename as JPEGs.
             * Ensure the PDF preview image does not overwrite any JPEG images that already exist.
             */
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            $filename = $image_editor->generate_filename($ext, null, 'jpg');
            $uploaded = $image_editor->save($filename, 'image/jpeg');

            if (!is_wp_error($uploaded)) {
                $metadata['sizes']['full'] = $uploaded;
            }
        }

        return $metadata;
    }
    
    /**
     * Delete intermediate image sizes generated by this plugin.
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
                    wp_delete_file($sizeinfo['path']);
                }
            }
        }
    }
    
    /**
     * Modify image's 'srcset' sources.
     * 
     * Replace the source URLs with our custom thumbnails URLs.
     * 
     * Hooked on `wp_calculate_image_srcset` filter.
     *
     * @since 1.0.1
     * @param array  $sources {
     *     One or more arrays of source data to include in the 'srcset'.
     *
     *     @type array $width {
     *         @type string $url        The URL of an image source.
     *         @type string $descriptor The descriptor type used in the image candidate string,
     *                                  either 'w' or 'x'.
     *         @type int    $value      The source width if paired with a 'w' descriptor, or a
     *                                  pixel density value if paired with an 'x' descriptor.
     *     }
     * }
     * @param array  $size_array    Array of width and height values in pixels (in that order).
     * @param string $image_src     The 'src' of the image.
     * @param array  $image_meta    The image meta data as returned by 'wp_get_attachment_metadata()'.
     * @param int    $attachment_id Image attachment ID or 0.
     * @return array                Modified 'srcset' source data.
     */
    public function modify_srcset_sources($sources, $size_array, $image_src, $image_meta, $attachment_id) {
        if (!isset($image_meta['sizes'])) {
            return $sources;
        }
        
        foreach ($sources as $width => $source) {
            $basename = basename($source['url']);
            
            foreach ($image_meta['sizes'] as $size => $size_data) {
                if (!isset($size_data['path']) || stripos($size_data['path'], $this->get_image_sizes_path($attachment_id, $size)) === false) {
                    continue;
                }
        
                if ($basename == $size_data['file'] && $url = $this->get_image_size_url($attachment_id, $size)) {
                    $sources[$width]['url'] = $url;
                }
            }
        }
        
        return $sources;
    }
    
    /**
     * Delete all intermediate image sizes.
     *
     * @since 1.0.0
     * @param int $post_id Attachment ID.
     */
    public function delete_all_image_sizes($post_id) {
        $file       = get_attached_file($post_id);
        $metadata   = wp_get_attachment_metadata($post_id);
        $uploadpath = wp_get_upload_dir();

        if (isset($metadata['sizes']) && is_array($metadata['sizes'])) {
            foreach ($metadata['sizes'] as $sizeinfo) {
                if (isset($sizeinfo['path'])) {
                    wp_delete_file($sizeinfo['path']);
                } else {
                    $intermediate_file      = str_replace(basename($file), $sizeinfo['file'], $file);
                    $intermediate_file_path = path_join($uploadpath['basedir'], $intermediate_file);

                    wp_delete_file($intermediate_file_path);
                }
            }

            $metadata['sizes'] = array();

            wp_update_attachment_metadata($post_id, $metadata);
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
        $image_size = image_get_intermediate_size($id, $size);
        
        if (!$image_size) {
            return;
        }
        
        $upload_dir = wp_get_upload_dir();    
        $image_url  = str_replace($upload_dir['basedir'], $upload_dir['baseurl'], $image_size['path']);
        
        /**
         * Filters the thumbnail URL.
         * 
         * @since 1.3.0
         * @param string $image_url The thumbnail URL.
         * @param int $id           Attachment ID.
         * @param string $size      Size of image.     
         */
        return apply_filters('fr_thumbnails_folder_image_sizes_get_image_size_url', $image_url, $id, $size);
    }
    
    /**
     * Get the image sizes path.
     * 
     * The $id and $size parameters are not used here, but may be used by other plugins 
     * to customize the image sizes path.
     * 
     * @since 1.2.0
     * @param int $id       Attachment ID.
     * @param string $size  Size of image.     
     * @return string
     */
    public function get_image_sizes_path($id, $size) {        
        $folder             = $this->get_image_sizes_folder($id, $size);
        $upload_dir         = wp_get_upload_dir();
        $image_sizes_path   = path_join($upload_dir['basedir'], $folder);
        
        /**
         * Filters the image sizes path.
         * 
         * @since 1.3.0
         * @param string $image_sizes_path  The image sizes path.
         * @param int $id                   Attachment ID.
         * @param string $size              Size of image.     
         */
        return apply_filters('fr_thumbnails_folder_image_sizes_get_image_sizes_path', $image_sizes_path, $id, $size);
    }
    
    /**
     * Get the image sizes folder.
     * 
     * The attachment ID is not used here, but may be used by other plugins to customize 
     * the image sizes folder.
     * 
     * @since 1.0.0
     * @param int $id       Attachment ID.
     * @param string $size  Size of image.     
     * @return string
     */
    protected function get_image_sizes_folder($id, $size) {
        /**
         * Filter the image sizes folder.
         * 
         * @since 1.3.0
         * @param string $image_sizes_folder    The image sizes folder.
         * @param int $id                       Attachment ID.
         * @param string $size                  Size of image.    
         */
        return apply_filters('fr_thumbnails_folder_image_sizes_get_image_sizes_folder', 'thumbnails', $id, $size);
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
