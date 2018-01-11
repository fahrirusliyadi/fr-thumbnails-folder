/**
 * All of the code for admin-facing JavaScript source.
 * 
 * @since 1.0.0
 * @author Fahri Rusliyadi <fahri.rusliyadi@gmail.com>
 * @param {jQuery} $
 * @returns {undefined}
 */
/* global ajaxurl, fr_thumbnails_folder */
(function( $ ) {
    'use strict';

    /**
     * All of the code for your admin-facing JavaScript source
     * should reside in this file.
     *
     * Note: It has been assumed you will write jQuery code here, so the
     * $ function reference has been prepared for usage within the scope
     * of this function.
     *
     * This enables you to define handlers, for when the DOM is ready:
     *
     * $(function() {
     *
     * });
     *
     * When the window is loaded:
     *
     * $( window ).load(function() {
     *
     * });
     *
     * ...and/or other possibilities.
     *
     * Ideally, it is not considered best practise to attach more than a
     * single DOM-ready or window-load handler for a particular page.
     * Although scripts in the WordPress core, Plugins and Themes may be
     * practising this, we should strive to set a better example in our own work.
     */
    
    /**
     * Delete image sizes functionality.
     * 
     * @since 1.0.0
     * @type DeleteImageSizes
     */
    var DeleteImageSizes = (function () {
        /**
         * Construct.
         * 
         * @since 1.0.0
         * @returns {DeleteImageSizes} 
         */
        function DeleteImageSizes() {
            /**
             * Document.
             * 
             * @since 1.0.0
             * @type jQuery
             */
            this.$document = $(document);
            
            /**
             * The delete button wrapper.
             * 
             * @since 1.0.0
             * @type jQuery
             */
            this.$buttonWrapper = this.$document.find('#fr-thumbnails-folder-delete-image-sizes-button-wrapper');
            
            /**
             * The retry delete button wrapper.
             * 
             * @since 1.0.0
             * @type jQuery
             */
            this.$retryButtonWrapper = this.$document.find('#fr-thumbnails-folder-retry-delete-image-sizes-button-wrapper');
            
            /**
             * Current page number.
             * 
             * @since 1.2.0
             * @type int
             */
            this.paged = 1;
            
            this._init();
        }
        
        /**
         * Initialize.
         * 
         * @since 1.0.0
         * @returns {undefined}
         */
        DeleteImageSizes.prototype._init = function () {
            this.$document.on('click.fr-thumbnails-folder', '#fr-thumbnails-folder-delete-image-sizes-button', $.proxy(this.deleteImageSizes, this));
            this.$document.on('click.fr-thumbnails-folder', '#fr-thumbnails-folder-retry-delete-image-sizes-button', $.proxy(this.retryDeleteImageSizes, this));
        };
        
        /**
         * Delete button click event handler.
         * 
         * @since 1.0.0
         * @param {$.Event} event
         * @returns {undefined}
         */
        DeleteImageSizes.prototype.deleteImageSizes = function(event) {                
            this.$buttonWrapper.html(fr_thumbnails_folder.l10n.start);
                
            this._requestDeleteImageSizes();
        };
        
        /**
         * Retry delete button click event handler.
         * 
         * @since 1.2.0
         * @param {$.Event} event
         * @returns {undefined}
         */
        DeleteImageSizes.prototype.retryDeleteImageSizes = function(event) {     
            this.$retryButtonWrapper.addClass('hidden');
            
            this._requestDeleteImageSizes();
        };
        
        /**
         * Send delete request to WordPress server.
         * 
         * @since 1.0.0
         * @returns {undefined}
         */
        DeleteImageSizes.prototype._requestDeleteImageSizes = function() {
            var _this = this;
            
            $.post(ajaxurl, {
                action: 'fr_thumbnails_folder_delete_image_sizes',
                nonce:  fr_thumbnails_folder.nonce,
                paged:  this.paged
            }, null, 'json')
            .done(function(data, textStatus, jqXHR) {
                _this.$buttonWrapper.html(fr_thumbnails_folder.l10n.status.replace('%1$s', data.deleted + '/' + data.total));
        
                if (data.deleted < data.total) {
                    _this.paged += 1;
                    
                    _this._requestDeleteImageSizes();
                }
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                var message = textStatus;
        
                if (errorThrown) {
                    message += ': ' + errorThrown;
                }
        
                _this.$buttonWrapper.html(message);
                _this.$retryButtonWrapper.removeClass('hidden');
            });
        };
        
        return DeleteImageSizes;
    }());
    
    new DeleteImageSizes();

})( jQuery );
