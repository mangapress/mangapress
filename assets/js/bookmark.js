(function ($) {
    if (typeof(localStorage) != 'object') {
        console.log('Browser does not support LocalStorage');
        return;
    }

    $(function() {
        BookMark.init();
        // KISS
        $('#bookmark-comic').on('click', function (e) {

        });

        $('#show-comic-bookmark-history').on('click', function (e) {

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