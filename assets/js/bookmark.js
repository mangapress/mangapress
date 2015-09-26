var MANGAPRESS = MANGAPRESS || {};

(function ($) {
    /**
     * @todo Add l10n/i18n support
     */
    $(function() {
        var $bookmark = $('#bookmark-comic'),
            $bookmarkComicHistory = $('#bookmark-comic-history');

        if (typeof(localStorage) != 'object') {
            console.log('Browser does not support LocalStorage');
            $bookmark.hide();
            $bookmarkComicHistory.hide();
            return;
        }

        Bookmark.init();

        // KISS
        $bookmark.on('click', function (e) {
            e.preventDefault();

            // store date, page title, and URL
            Bookmark.bookmark();
        });

        $bookmarkComicHistory.on('click', function (e) {
            // show a list of recently bookmarked comics, starting with most recent
            e.preventDefault();
            Bookmark.history();
        });

    });

    var Bookmark = {
        storage : null,
        BOOKMARK_HISTORY : 'mangapress-bookmark-history',
        BOOKMARK : 'mangapress-bookmark',
        $bookmark : null,
        $bookmarkNav : null,
        $bookmarkHistory : null,
        init: function() {

            this.storage = localStorage;
            this.checkItem();

            if (!this.hasHistory()) {
                this.setHistory([]); // set a blank array if no history exists.
            }
        },

        checkItem : function() {
            var bookmark = this.getBookmark();

            this.$bookmark = $('#bookmark-comic');
            this.$bookmarkNav = $('#comic-bookmark-navigation');
            var id = this.$bookmark.data('id');

            if (this.bookmarkExists(id)) {
                this.$bookmark.text( this.$bookmark.data('bookmarkedLabel') );
            }
        },

        bookmark : function() {
            var href = (this.$bookmark.data('href') !== undefined) ? this.$bookmark.data('href') : window.location.href,
                pageTitle = (this.$bookmark.data('title') !== undefined) ? this.$bookmark.data('title') : window.document.title,
                data = {
                    id : this.$bookmark.data('id'),
                    url : href,
                    title : pageTitle,
                    date : Date.now()
                };

            // add the bookmark to history
            console.log(this.bookmarkExists(data.id));
            if (this.bookmarkExists(data.id) == 0) {
                this.addToHistory( data );
            }

            // change label state
            this.$bookmark.text( this.$bookmark.data('bookmarkedLabel') );
            this.setBookmark(data);
        },

        history : function() {
            var self = this,
                revBookmarkHistory = self.getHistory(),
                $historyModal = $('<div id="bookmark-history-modal"><div id="bookmark-history-content"></div><p style="text-align: center;">[<a href="#" id="bookmark-history-close">close</a>]</p></div>')
                    .css(MANGAPRESS.bookmarkStyles);

            $historyModal.find('#bookmark-history-content').html(function(){
                if (revBookmarkHistory.length == 0) {
                    return '<p>No bookmark history available.</p>';
                }

                var htmlString = "<table>",
                    bookmarkHistory = revBookmarkHistory.reverse();

                htmlString += "<thead><tr><td>Title</td><td>Date</td></tr></thead>";

                for (var i = 0; i < bookmarkHistory.length; i++) {
                    var columns = [],
                        bookmark = bookmarkHistory[i],
                        d = new Date(bookmark.date),
                        date = d.getMonth() + '/' + d.getDate() + '/' + d.getFullYear(),
                        link = "<a href=\"" + bookmark.url + "\">" + bookmark.title + "</a>";

                    columns.push(link, date);

                    htmlString += "<tr><td>" + columns.join('</td><td>') + "</td></tr>"
                }

                return htmlString + "</table>";
            });

            // append
            self.$bookmarkNav.append($historyModal)

            // add event for closing modal
            $('#bookmark-history-close').on('click', function(e){
                e.preventDefault();
                $historyModal.remove();
            });
        },

        getBookmark : function() {
            return JSON.parse(this.storage.getItem(this.BOOKMARK));
        },

        setBookmark : function(bookmark) {
            this.storage.setItem(
                this.BOOKMARK,
                JSON.stringify(bookmark)
            );
        },

        getHistory : function() {
            var history = JSON.parse(this.storage.getItem(this.BOOKMARK_HISTORY));
            if (history == null) {
                return []; // return empty array
            }

            return history;
        },

        setHistory : function(history) {
            this.storage.setItem(
                this.BOOKMARK_HISTORY,
                JSON.stringify(history)
            );
        },

        addToHistory : function(bookmark) {
            var history = this.getHistory();

            history.push( bookmark );
            this.setHistory(history);
        },

        hasHistory : function() {
            return this.getHistory().length;
        },

        hasBookmark : function() {
            return this.getBookmark();
        },

        bookmarkExists : function(id) {
            var history = this.getHistory();

            return $.grep(history, function(e){
                return e.id == id;
            }).length;
        }
    };
}(jQuery));
