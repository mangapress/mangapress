/**
 * Plugin namespace
 * @type namespace
 */
if (typeof MANGAPRESS === 'undefined') {
    let MANGAPRESS = {
        title: '',
        button: ''
    };
}

(function ($) {

    MANGAPRESS.library_frame = false;

    $(document).on('click', '.js-choose-from-library-link', function (e) {
        e.preventDefault();

        const nonce = $(this).attr('data-nonce');
        const action = $(this).attr('data-action');
        const field = $(this).attr('data-field');
        const $thumbnailInput = $('#js-input-' + field);
        const $imageFrame = $('#js-image-frame--' + field);

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
                text: MANGAPRESS.button
            }
        });

        MANGAPRESS.library_frame.on('select', function () {
            // Grab our attachment selection and construct a JSON representation of the model.
            const media_attachment = MANGAPRESS.library_frame.state().get('selection').first().toJSON();
            const data = {
                id: media_attachment.id,
                nonce: nonce,
                action: action
            };

            // need Ajax call to get attachment HTML
            $.post(ajaxurl, data, function (data) {
                $imageFrame.html(data.html);
            });
            console.log($thumbnailInput);
            // Send the attachment URL to our custom input field via jQuery.
            $thumbnailInput.val(media_attachment.id);
        });

        MANGAPRESS.library_frame.open();
    });

    $(document).on('click', '.js-remove-mangapress-thumbnail', function (e) {
        e.preventDefault();

        const nonce = $(this).attr('data-nonce');
        const action = $(this).attr('data-action');
        const field = $(this).attr('data-field');
        const $thumbnailInput = $('#js-input-' + field);
        const $imageFrame = $('#js-image-frame--' + field);

        const data = {
            nonce: nonce,
            action: action
        };

        $thumbnailInput.val('');
        $.post(ajaxurl, data, function (data) {
            $imageFrame.html(data.html);
        });
    });
}(jQuery));
