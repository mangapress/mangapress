(function ($) {
    if (typeof(localStorage) != 'object') {
        console.log('Browser does not support LocalStorage');
        return;
    }

    $(function() {
        BookMark.init();
        // KISS
        $('#bookmark-comic').on('click', function (e) {
            e.preventDefault();

            // store date, page title, and URL
        });

        $('#show-comic-bookmark-history').on('click', function (e) {
            // show a list of recently bookmarked comics, starting with most recent
        });

    });

    var BookMark = {
        storage : null,

        init: function() {
            this.storage = localStorage;
        },

        bookmark : function() {

        },

        history : function() {

        },

        goto : function() {

        }
    };
}(jQuery));
