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

        $('#bookmark-comic-history').on('click', function (e) {
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
        init: function() {
            this.storage = localStorage;
            this.checkItem();
        },

        checkItem : function() {
            var bookmark = JSON.parse(this.storage.getItem(this.BOOKMARK)),
                pageHref = window.location.href;

            this.$bookmark = $('#bookmark-comic');
            this.$bookmarkNav = $('#comic-bookmark-navigation');

            if (bookmark == null) {
                return;
            }

            if (bookmark.url == pageHref) {
                this.$bookmark.text( this.$bookmark.data('bookmarkedLabel') );
            }
        },

        bookmark : function() {
            var href = (this.$bookmark.data('href') !== undefined) ? this.$bookmark.data('href') : window.location.href,
                pageTitle = (this.$bookmark.data('title') !== undefined) ? this.$bookmark.data('title') : window.document.title,
                data = {};

            var existingBookmarkData = this.storage.getItem(this.BOOKMARK);

            if (existingBookmarkData) {
                var bookmarkHistory = JSON.parse(this.storage.getItem(this.BOOKMARK_HISTORY));
                if (!bookmarkHistory) {
                    bookmarkHistory = [];
                }

                bookmarkHistory.push(existingBookmarkData);
                this.storage.setItem(this.BOOKMARK_HISTORY, JSON.stringify(bookmarkHistory));

                // change label state
                this.$bookmark.text( this.$bookmark.data('bookmarkedLabel') );
            }

            data = {
                url : href,
                title : pageTitle,
                date : Date.now()
            };

            this.storage.setItem(this.BOOKMARK, JSON.stringify(data));
        },

        history : function() {
            var self = this,
                revBookmarkHistory = JSON.parse(self.storage.getItem(self.BOOKMARK_HISTORY)),
                bookmarkHistory = revBookmarkHistory.reverse(),
                $historyModal = $('<div id="bookmark-history-modal"><div id="bookmark-history-content"></div><p>[<a href="#" id="bookmark-history-close">close</a>]</p></div>').css({
                    'width': '300px',
                    'z-index' : 9999,
                    'border' : '1px solid black',
                    'background-color' : '#fff',
                    'position' : 'absolute',
                    'padding' : '5px',
                    // 'top' : '25%',
                    'left' : '50%',
                    'margin-left' : '-150px'
                });

                $historyModal.find('#bookmark-history-content').html(function(){
                    var htmlString = "<table>";

                    htmlString = "<thead><tr><td>Title</td><td>Date</td></tr></thead>";

                    for (var i = 0; i < bookmarkHistory.length; i++) {
                        var columns = [],
                            bookmark = JSON.parse(bookmarkHistory[i]),
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
                $('#bookmark-history-close').one('click', function(e){
                    e.preventDefault();
                    $historyModal.remove();
                });
        },

        goto : function() {

        }
    };
}(jQuery));
