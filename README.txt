=== Plugin Name ===
Contributors: fahrirusliyadi
Donate link: https://paypal.me/FahriRusliyadi
Tags: thumbnail, thumbnails, image, images, folder, directory
Requires at least: 4.4.0
Tested up to: 6.6.2
Stable tag: 1.4.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Move thumbnails file location to `{$upload_path}/thumbnails` or `{$upload_path}/sites/{$blog_id}/thumbnails` for multisite.

== Description ==

WordPress generates thumbnails in the same directory with the original image. This plugin change this behavior by generating the thumbnails in a separate directory. The thumbnails will only be generated when they are needed and has not been generated.

This plugin will only move the thumbnails location for new uploaded images. To move all thumbnails, you need to delete all existing thumbnails (see FAQ).

== Installation ==

1. Upload `fr-thumbnails-folder` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the *Plugins* menu in WordPress

== Frequently Asked Questions ==

= Uninstallation Instructions =

1. Deactivate and delete the plugin through the *Plugins* menu in WordPress
1. Regenerate thumbnails using [Regenerate Thumbnails](https://wordpress.org/plugins/regenerate-thumbnails/) plugin
1. Delete `{$upload_path}/thumbnails` directory

= How to delete all existing thumbnails? =

- WP-CLI

    This is the recommended way, especially if you have a lot of images.

        wp fr-thumbnails-folder delete-thumbnails

    For multisite:

        wp --url=<site address url> fr-thumbnails-folder delete-thumbnails

- GUI

    Tools &rarr; Delete Thumbnails

= Why did you create this plugin? =

When backing up files, I only want the original images, not the thumbnails. So to make things easier, I need to separate its location.

== Screenshots ==

1. Folder tree view

== Changelog ==

= 1.4.0 =
* Add support for PDF.
* Fix only resize if smaller than the original size.

= 1.3.1 =
* Fix broken image path on Windows.

= 1.3.0 =
* Add filter hooks to allow modification of the thumbnail location.
* Add `wp fr-thumbnails-folder delete-thumbnails` command.

= 1.2.0 =
* Add ability to retry deleting thumbnails.
* Delete thumbnails one by one.
* Fix plugin cannot display default thumbnail location.

= 1.0.1 =
* Fix incorrect `srcset` URLs.

= 1.0.0 =
* Initial release.
