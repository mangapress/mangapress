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
        BOOKMARK_HISTORY : 'mangapress-bookmark-history',
        BOOKMARK : 'mangapress-bookmark',
        init: function() {
            this.storage = localStorage;
            this.checkItem();
        },

        checkItem : function() {
            var bookmark = JSON.parse(this.storage.getItem(this.BOOKMARK)),
                pageHref = window.location.href,
                $bookmark = $('#bookmark-comic');

            if (bookmark.url == pageHref) {
                $bookmark.text($bookmark.data('bookmarkedLabel'));
            }
        },

        bookmark : function() {
            var href = window.location.href,
                pageTitle = window.document.title,
                data = {},
                d = new Date();

            var existingBookmarkData = this.storage.getItem(this.BOOKMARK);

            if (existingBookmarkData) {
                var bookmarkHistory = JSON.parse(this.storage.getItem(this.BOOKMARK_HISTORY));
                bookmarkHistory.push(existingBookmarkData);
                this.storage.setItem(this.BOOKMARK_HISTORY, JSON.stringify(bookmarkHistory));
            }

            data = {
                url : href,
                title : pageTitle,
                date : Date.now()
            };

            this.storage.setItem(this.BOOKMARK, JSON.stringify(data));
        },

        history : function() {

        },

        goto : function() {

        }
    };
}(jQuery));
