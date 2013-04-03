<?php
/**
 * MangaPress
 * 
 * @package nav-css
 * @author Jess Green <jgreen at psy-dreamer.com>
 * @version $Id$
 * @license GPL
 */
?>

<?php _e('Copy and paste this code into the <code>style.css</code> file of your theme.', MP_DOMAIN); ?>
<code style="display: block; width: 550px;"><pre class="brush: css;">

/* comic navigation */
.comic-navigation {
    text-align: center;
    margin: 5px 0 10px 0;
}

.comic-nav-span {
    padding: 3px 10px;
    text-decoration: none;
}

ul.comic-nav  {
    margin: 0;
    padding: 0;
    white-space: nowrap;
}

ul.comic-nav li {
    display: inline;
    list-style-type: none;
}

ul.comic-nav a {
    text-decoration: none;
    padding: 3px 10px;
}

ul.comic-nav a:link,
ul.comic-nav a:visited {
    color: #ccc;
    text-decoration: none;
}

ul.comic-nav a:hover { text-decoration: none; }
ul.comic-nav li:before{ content: ""; }

</pre></code>
<?php 