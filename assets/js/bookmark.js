(function () {
	// 	var $bookmark         = $( '#bookmark-comic' ),
	// 		$bookmarkComicHistory = $( '#bookmark-comic-history' );
	//
	// 	if (typeof(localStorage) !== 'object') {
	// 		console.log( 'Browser does not support LocalStorage' );
	// 		$bookmark.hide();
	// 		$bookmarkComicHistory.hide();
	// 		return;
	// 	}
	//
	// 	Bookmark.init();
	//
	// 	// KISS
	// 	$bookmark.on(
	// 		'click',
	// 		function (e) {
	// 			e.preventDefault();
	//
	// 			// store date, page title, and URL
	// 			Bookmark.bookmark();
	// 		}
	// 	);
	//
	// 	$bookmarkComicHistory.on(
	// 		'click',
	// 		function (e) {
	// 			// show a list of recently bookmarked comics, starting with most recent
	// 			e.preventDefault();
	// 			Bookmark.history();
	// 		}
	// 	);
	//
	// }

	function Bookmark () {
		this.storage         = null;
		this.bookmarkHistory = 'mangapress-bookmark-history';
		this.bookmarkElem    = 'mangapress-bookmark';

		this.init = function () {

			this.storage = localStorage;
			this.checkItem();

			if ( ! this.hasHistory()) {
				this.setHistory( [] ); // set a blank array if no history exists.
			}
		}

		this.checkItem = function () {
			const bookmark = this.getBookmark();

			this.$bookmark    = $( '#bookmark-comic' );
			this.$bookmarkNav = $( '#comic-bookmark-navigation' );
			const id          = this.$bookmark.data( 'id' );

			if (this.bookmarkExists( id )) {
				this.$bookmark.text( this.$bookmark.data( 'bookmarkedLabel' ) );
			}
		}

		this.bookmark = function () {
			const href      = (this.$bookmark.data( 'href' ) !== undefined) ? this.$bookmark.data( 'href' ) : window.location.href;
			const pageTitle = (this.$bookmark.data( 'title' ) !== undefined) ? this.$bookmark.data( 'title' ) : window.document.title;
			const data      = {
				id : this.$bookmark.data( 'id' ),
				url : href,
				title : pageTitle,
				date : Date.now()
			};

			// add the bookmark to history
			if (this.bookmarkExists( data.id ) === 0) {
				this.addToHistory( data );

				// change label state
				this.$bookmark.text( this.$bookmark.data( 'bookmarkedLabel' ) );
				this.setBookmark( data );
			} else {
				// if it exists, remove from history
				this.removeFromHistory( data );
				this.$bookmark.text( this.$bookmark.data( 'label' ) );
			}
		}

		this.history = function () {
			const self               = this;
			const revBookmarkHistory = self.getHistory();
			const isOpen             = $( 'body' ).find( '#bookmark-history-modal' ).length;
			const $historyModal      = $( '<div id="bookmark-history-modal" class="bookmark-history-modal"><div id="bookmark-history-content" class="bookmark-history-content"></div><p style="text-align: center;">[<a href="#" id="bookmark-history-close" class="bookmark-history-close">' + MANGAPRESS.bookmarkCloseLabel + '</a>]</p></div>' );

			if (isOpen) {
				return;
			}

			if ( ! this.$bookmark.data( 'noStyling' )) {
				$historyModal.css( MANGAPRESS.bookmarkStyles );
			}

			$historyModal.find( '#bookmark-history-content' ).html(
				function () {
					if (revBookmarkHistory.length === 0) {
						return '<p class="bookmark-no-history">' + MANGAPRESS.bookmarkNoHistory + '</p>';
					}

					var htmlString  = '<table class="bookmark-table">',
					bookmarkHistory = revBookmarkHistory.reverse();

					htmlString += "<thead><tr><th>" + MANGAPRESS.bookmarkTitle + "</th><th>" + MANGAPRESS.bookmarkDate + "</th></tr></thead>";

					for (var i = 0; i < bookmarkHistory.length; i++) {
						var columns = [],
						bookmark    = bookmarkHistory[i],
						d           = new Date( bookmark.date ),
						date        = (d.getMonth() + 1) + '/' + d.getDate() + '/' + d.getFullYear(),
						link        = "<a href=\"" + bookmark.url + "\">" + bookmark.title + "</a>";

						columns.push( link, date );

						htmlString += "<tr><td>" + columns.join( '</td><td>' ) + "</td></tr>"
					}

					return htmlString + "</table>";
				}
			);

			// append
			self.$bookmarkNav.append( $historyModal );

			// add event for closing modal
			$( '#bookmark-history-close' ).on(
				'click',
				function (e) {
					e.preventDefault();
					$historyModal.remove();
				}
			);
		}

		this.getBookmark = function () {
			return JSON.parse( this.storage.getItem( this.BOOKMARK ) );
		}

		this.setBookmark = function (bookmark) {
			this.storage.setItem(
				this.BOOKMARK,
				JSON.stringify( bookmark )
			);
		}

		this.getHistory = function () {
			const history = JSON.parse( this.storage.getItem( this.BOOKMARK_HISTORY ) );
			if (history === null) {
				return []; // return empty array
			}

			return history;
		}

		this.setHistory = function (history) {
			this.storage.setItem(
				this.BOOKMARK_HISTORY,
				JSON.stringify( history )
			);
		}

		this.addToHistory = function (bookmark) {
			const history = this.getHistory();

			history.push( bookmark );
			this.setHistory( history );
		}

		this.removeFromHistory = function (bookmark) {
			const history    = this.getHistory();
			const newHistory = [];
			const i = this.getIndexOfBookmark( bookmark.id );

			delete history[i];

			// OFFS JS
			for (let j in history) {
				if (typeof history[j] !== 'undefined') {
					newHistory.push( history[j] )
				}
			}

			this.setHistory( newHistory );
		}

		this.hasHistory = function () {
			return this.getHistory().length;
		}

		this.hasBookmark = function () {
			return this.getBookmark();
		}

		this.bookmarkExists = function (id) {
			const history = this.getHistory();

			return history.filter(
				function (e) {
					return e.id === id;
				}
			).length;
		}

		this.getIndexOfBookmark = function (id) {
			const history = this.getHistory();
			for (let i in history) {
				if (history[i].id === id) {
					return i;
				}
			}
		}
	}

	new Bookmark()
}());
