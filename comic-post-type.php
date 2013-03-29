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
class ComicPostType extends MangaPress_PostType
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
            ->set_view(new MangaPress_View(
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
            ->init();
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
        $series_tax = new MangaPress_Taxonomy(
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

        return array($series_tax->get_name());
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
     * Meta-box callback for outputting Add Comic box.
     * 
     * @global type $post_ID 
     */
    public function comic_meta_box_cb()
    {
        global $post_ID;
        
        $thumbnail_id = get_post_thumbnail_id($post_ID);
        
        if ($thumbnail_id == '') {
            $image_popup_url = $this->_get_iframe_src_url($post_ID);
        ?>
        <a href="<?php echo $image_popup_url; ?>" title="<?php esc_attr__( 'Set Comic Image', MP_DOMAIN ) ?>" id="set-comic-image" class="thickbox">Set Comic Image</a>        
        <?php
        
        } else {
            echo $this->admin_post_comic_html($thumbnail_id, $post_ID);
        }
        
    }
    
    /**
     * Private helper function for creating Media Library popup box.
     * 
     * @param integer $post_ID Post ID
     * @return string 
     */
    private function _get_iframe_src_url($post_ID)
    {
        $iframe_url = add_query_arg(array(
                        'post_id'   => $post_ID,
                        'tab'       => 'library',
                        'post_type' => 'mangapress_comic',
                        'TB_iframe' => 1,
                        'width'     => '640',
                        'height'    => '322'
                    ),
                    admin_url('media-upload.php')
                );
        
        return $iframe_url;
    }
    
    /**
     * Public helper function for returning Add Comic thumbnail html
     * 
     * @global array $_wp_additional_image_sizes
     * @param integer $thumbnail_id
     * @param integer $post_parent
     * @return string
     */
    public function admin_post_comic_html($thumbnail_id = '', $post_parent = '')
    {
        global $_wp_additional_image_sizes;
        
	$set_thumbnail_link = '<p class="hide-if-no-js"><a title="' 
                            . esc_attr__( 'Set Comic Image', MP_DOMAIN ) . '" href="' 
                            . esc_url( $this->_get_iframe_src_url($post_parent) ) 
                            . '" id="set-comic-image" class="thickbox">%s</a></p>';
        
	$content = sprintf($set_thumbnail_link, esc_html__(  'Set Comic Image', MP_DOMAIN ));

	if ( $thumbnail_id && get_post( $thumbnail_id ) ) {
            
            if ( !isset( $_wp_additional_image_sizes['comic-page'] ) ) {
                $thumbnail_html = wp_get_attachment_image( $thumbnail_id, 'medium');
            } else {
                $thumbnail_html = wp_get_attachment_image( $thumbnail_id, 'comic-page' );
            }
            
            if ( !empty( $thumbnail_html ) ) {
                    $ajax_nonce = wp_create_nonce( "set_comic_thumbnail-{$post_parent}" );
                    $content = sprintf($set_thumbnail_link, $thumbnail_html);
                    $content .= '<p class="hide-if-no-js">'
                             . '<a href="#" data-nonce="' . $ajax_nonce . '" data-post-parent="' . $post_parent . '" id="remove-comic-thumbnail">'
                             . esc_html__( 'Remove Comic image', MP_DOMAIN ) . '</a></p>';
            }
                        
	}

	return apply_filters( 'mangapress_admin_post_thumbnail_html', $content );
        
    }

    /**
     * Retrieves post-type slug
     *
     * @return string
     */
    public function get_name()
    {
        return $this->_name;
    }

}
