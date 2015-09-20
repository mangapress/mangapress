(function ($) {
    if (typeof(localStorage) != 'object') {
        console.log('Browser does not support LocalStorage');
        return;
    }

    $(function() {
        Bookmark.init();
        // KISS
        $('#bookmark-comic').on('click', function (e) {
            e.preventDefault();

            // store date, page title, and URL
            Bookmark.bookmark();
        });

        $('#show-comic-bookmark-history').on('click', function (e) {
            // show a list of recently bookmarked comics, starting with most recent
        });

    });

    var Bookmark = {
        storage : null,

        init: function() {
            this.storage = localStorage;
        },

        bookmark : function() {
            var href = window.location.href,
                pageTitle = window.document.title,
                data = {};

            data = {
                url : href,
                title : pageTitle,
                date : Date.now()
            }

            this.storage.setItem('bookmark', JSON.stringify(data));
            console.log(JSON.parse(this.storage.getItem('bookmark')));
        },

        history : function() {

        },

        goto : function() {

        }
    };
}(jQuery));
