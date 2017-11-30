/**
 * All of the code for admin-facing JavaScript source.
 * 
 * @since 1.0.0
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
            this.$buttonWrapper;
            
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
        };
        
        /**
         * Delete button click event handler.
         * 
         * @since 1.0.0
         * @param {$.Event} event
         * @returns {undefined}
         */
        DeleteImageSizes.prototype.deleteImageSizes = function(event) {
            this.$buttonWrapper = $(event.target).closest('p');
                
            this.$buttonWrapper.html(fr_thumbnails_folder.l10n.start);
                
            this._requestDeleteImageSizes({
                paged: 1
            });
        };
        
        /**
         * Send delete request to WordPress server.
         * 
         * @since 1.0.0
         * @param {Object} params
         * @returns {undefined}
         */
        DeleteImageSizes.prototype._requestDeleteImageSizes = function(params) {
            var _this = this;
            
            $.post(ajaxurl, {
                action: 'fr_thumbnails_folder_delete_image_sizes',
                nonce:  fr_thumbnails_folder.nonce,
                paged:  params.paged
            }, null, 'json')
            .done(function(data, textStatus, jqXHR) {
                _this.$buttonWrapper.html(fr_thumbnails_folder.l10n.status.replace('%1$s', data.deleted + '/' + data.total));
        
                if (data.deleted < data.total) {
                    var newParams   = $.extend({}, params);
                    newParams.paged = params.paged + 1;
                    
                    _this._requestDeleteImageSizes(newParams);
                }
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                _this.$buttonWrapper.html(textStatus + ': ' + errorThrown);
            });
        };
        
        return DeleteImageSizes;
    }());
    
    new DeleteImageSizes();

})( jQuery );
