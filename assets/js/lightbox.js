var MANGAPRESS = MANGAPRESS || {};

(function () {

    /**
     * Lightbox Object
     * @constructor
     */
    function Lightbox() {
        // declare properties
        this.trigger = false;
        this.lightbox = false;
        this.content = false;

        // declare methods
        this.domLoaded = function(event) {
            this.trigger = document.getElementById('mangapress-lightbox-trigger');
            this.lightbox = document.getElementById('mangapress-lightbox');
            this.content = document.getElementById('mangapress-lightbox-content');

            if (!this.trigger || !this.lightbox || !this.content) {
                return;
            }

            this.trigger.addEventListener('click', this.open);
            this.lightbox.addEventListener('click', this.close);
            this.buildImg();
        }.bind(this);


        /**
         * Toggle a CSS class's state (add or remove)
         * @param klass CSS Class to toggle
         */
        this.toggle = function(klass) {
            this.lightbox.classList.toggle(klass);
        };


        /**
         * Open lightbox
         *
         * @type {function(this:Lightbox)}
         */
        this.open = function(event) {
            event.preventDefault();
            this.toggle('show');
        }.bind(this);


        /**
         * Close lightbox
         *
         * @type {function(this:Lightbox)}
         */
        this.close = function(event) {
            event.preventDefault();
            this.toggle('show');
        }.bind(this);


        /**
         * Get the image src from data attribute
         * @returns {*}
         */
        this.getSrc = function() {
            if (typeof this.trigger.dataset.src === 'undefined') {
                return false;
            }

            return this.trigger.dataset.src;
        };


        /**
         * Get the image width from data attribute
         * @returns {*}
         */
        this.getImgWidth = function () {
            if (typeof this.trigger.dataset.imgWidth === 'undefined') {
                return false;
            }

            return this.trigger.dataset.imgWidth;
        };


        /**
         * Get the image height from data attribute
         * @returns {*}
         */
        this.getImgHeight = function () {
            if (typeof this.trigger.dataset.imgHeight === 'undefined') {
                return false;
            }

            return this.trigger.dataset.imgHeight;
        };


        /**
         * Create image (img) node and add to content
         */
        this.buildImg = function () {
            var img = document.createElement('img');
            var imgSrc = this.getSrc();
            var imgWidth = this.getImgWidth();
            var imgHeight = this.getImgHeight();

            img.setAttribute('src', imgSrc);
            img.setAttribute('width', imgWidth);
            img.setAttribute('height', imgHeight);

            this.content.innerHTML = img.outerHTML;
        };

        document.addEventListener("DOMContentLoaded", this.domLoaded);
    }

    new Lightbox();
}());