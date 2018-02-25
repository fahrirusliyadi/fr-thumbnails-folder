<?php

/**
 * <code>fr-thumbnails-folder delete-thumbnails</code> command.
 *
 * @since 1.3.0
 * @author Fahri Rusliyadi <fahri.rusliyadi@gmail.com>
 */
class Fr_Thumbnails_Folder_Console_Command_Delete_Thumbnails {
    /**
     * Delete all of the existing image thumbnails.
     * 
     * @since 1.3.0
     */
    public function __invoke() {
        $query_args     = array(
                            'paged'          => 0,
                            'posts_per_page' => 10,
                            'order'          => 'ASC',
                            'orderby'        => 'ID',
                            'post_mime_type' => 'image',
                            'post_status'    => 'any',
                            'post_type'      => 'attachment',
                        );
        $found_posts    = false;
        $deleted        = 0;
        
        while ($found_posts === false || $deleted < $found_posts) {
            $query                  = new WP_Query();
            $query_args['paged']    += 1;
            $attachments            = $query->query($query_args);
        
            $this->delete($attachments);
            
            $deleted        += $query->post_count;
            $found_posts    = $query->found_posts;
        }
    }
    
    /**
     * Delete the thumbnails.
     * 
     * @since 1.3.0
     * @param WP_Post[] $attachments Attachment posts.
     */
    protected function delete($attachments) {  
        foreach ($attachments as $attachment) {
            $metadata = wp_get_attachment_metadata($attachment->ID);

            if (!isset($metadata['sizes']) || !is_array($metadata['sizes'])) {
                continue;
            }

            fr_thumbnails_folder()->get_image_sizes()->delete_all_image_sizes($attachment->ID);

            foreach ($metadata['sizes'] as $size => $sizeinfo) {
                /* translators: 1: image filename, 2: thumbnail name */                
                WP_CLI::success(sprintf(__('%1$s (%2$s) deleted', 'fr-thumbnails-folder' ), $sizeinfo['file'], $size));
            }
        } 
    }
}
