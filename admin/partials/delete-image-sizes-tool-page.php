<?php

/**
 * Provide a delete image sizes tool area view for the plugin.
 * 
 * @link       https://profiles.wordpress.org/fahrirusliyadi
 * @since      1.0.0
 * @package    Fr_Thumbnails_Folder
 * @subpackage Fr_Thumbnails_Folder/admin/partials
 */
?>

<?php settings_errors('fr_thumbnails_folder_delete_image_sizes_tool') ?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()) ?></h1>
    
    <p class="description"><?php _e('Delete all existing thumbnails.', 'fr-thumbnails-folder') ?></p>
    
    <p class="submit"><button id="fr-thumbnails-folder-delete-image-sizes-button" class="button button-primary"><?php _e('Delete', 'fr-thumbnails-folder') ?></button></p>
</div>