<?php

/**
 * MangaPress
 * 
 * @package MangaPress
 * @author Jess Green <jgreen@psy-dreamer.com> 
 * @version $Id$
 */

/**
 * ComicPostType
 * 
 * @package ComicPostType
 * @author Jess Green <jgreen@psy-dreamer.com> 
 */
class ComicPostType extends PostType
{

    /**
     * Post-type name.
     * 
     * @var string
     */
    protected $_name = 'mangapress_comic';

    /**
     * Human-readable post-type name (singular)
     * 
     * @var string
     */
    protected $_label_single;

    /**
     * Human-readable post-type name (plural)
     * 
     * @var string
     */
    protected $_label_plural;

    /**
     * PHP5 constructor function
     * 
     * @return void 
     */
    public function __construct()
    {

        wp_register_script(
            MP_DOMAIN . '-media-script',
            MP_URLPATH . 'js/add-comic.js',
            array('jquery'),
            MP_VERSION
        );
        
        /*
         * Now we put together our post-type
         */
        $this
                ->set_singlename(__('Comic', MP_DOMAIN))
                ->set_pluralname(__('Comics', MP_DOMAIN))
                ->set_taxonomies($this->get_taxonomies())
                ->set_arguments()
                ->set_view(new View(
                    array(
                        'path'       => MP_URLPATH, // plugin path
                        'post_type'  => $this->_name,
                        'hook'       => array(
                            'post.php',
                            'post-new.php',
                            'edit.php'
                        ),
                        'js_scripts' => array(
                            MP_DOMAIN . '-media-script'
                        ),
                        'ver'        => MP_VERSION,
                    )
                ))
                ->init()
        ;
    }
    
    /**
     * Sets post-type arguments
     * 
     * @param type $args
     * @return array|PostType
     */
    public function set_arguments($args = array()) {
        $args = array(
            'capability_type' => 'post',
            'supports' => array(
                'thumbnail',
                'author',
                'title',
                'comments',
            ),
            'menu_icon' => MP_URLPATH . 'images/menu_icon.png',
            'rewrite' => array('slug' => 'comic'),
        );

        return parent::set_arguments($args);
    }

    /**
     * Registers and sets taxonomies for post-type
     * 
     * @return array 
     */
    public function get_taxonomies()
    {
        /*
         * Assemble our taxonomies first
         */
        $series_tax = new Taxonomy(
                        array(
                            'name' => 'mangapress_series',
                            'singlename' => 'Series',
                            'pluralname' => 'Series',
                            'objects' => $this->_name,
                            'arguments' => array(
                                'hierarchical' => true,
                                'query_var' => 'series',
                                'rewrite' => array('slug' => 'series'),
                            )
                        )
        );

        return array($series_tax->name);
    }
    
    /**
     * Meta box call-back function.
     * 
     * @return void 
     */
    public function meta_box_cb()
    {
        add_meta_box(
            'comic-image',
            __('Comic Image', MP_DOMAIN),
            array($this, 'comic_meta_box_cb'), 
            $this->_name,
            'normal',
            'high'
        );
        
        /*
         * Because we don't need this...the comic image is the "Featured Image"
         * TODO add an option for users to override this "functionality"
         */
        remove_meta_box('postimagediv', 'mangapress_comic', 'side');
        
    }
    
    /**
     * Retrieves post-type slug
     * 
     * @return string
     */
    public function get_name() {
        return $this->_name;
    }

}
