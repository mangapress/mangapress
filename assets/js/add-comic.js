/**
 * Plugin namespace
 * @type namespace
 */
var MANGAPRESS = MANGAPRESS || {};

(function($){

    MANGAPRESS.library_frame = false;

    $(document).on('click', '#choose-from-library-link', function(e){
        e.preventDefault();
        
        var $thumbnailInput = $('#js-mangapress-comic-image'),
            $imageFrame     = $('#js-image-frame'),
            nonce           = $(this).attr('data-nonce'),
            action          = $(this).attr('data-action');
        
        if (MANGAPRESS.library_frame) {
            MANGAPRESS.library_frame.open();
            return;
        }

        MANGAPRESS.library_frame = wp.media.frames.mangapress_library_frame = wp.media({
            className: 'media-frame mangapress-media-frame',
            frame: 'select',
            multiple: false,
            title: MANGAPRESS.title,
            library: {
                type: 'image'
            },
            button: {
                text:  MANGAPRESS.button
            }
        });

        MANGAPRESS.library_frame.on('select', function(){
            // Grab our attachment selection and construct a JSON representation of the model.
            var media_attachment = MANGAPRESS.library_frame.state().get('selection').first().toJSON(),
                data = {
                    id     : media_attachment.id,
                    nonce  : nonce,
                    action : action
                };
            
            // need Ajax call to get attachment HTML
            $.post(ajaxurl, data, function(data) {                
                $imageFrame.html(data.html);
            });
            
            // Send the attachment URL to our custom input field via jQuery.
            $thumbnailInput.val(media_attachment.id);
        });

        MANGAPRESS.library_frame.open();
    });
    
    $(document).on('click', '#js-remove-comic-thumbnail', function(e){
        e.preventDefault();
        var $thumbnailInput = $('#js-mangapress-comic-image'),
            $imageFrame     = $('#js-image-frame'),           
            nonce           = $(this).attr('data-nonce'),
            action          = $(this).attr('data-action'),
            data = {
                nonce  : nonce,
                action : action
            };

        $thumbnailInput.val('');
        $.post(ajaxurl, data, function(data){
             $imageFrame.html(data.html);
        });
    });
}(jQuery));
