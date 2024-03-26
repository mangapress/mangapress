<?php
/**
 * MangaPress Admin class
 */
final class MangaPress_Admin {

	/**
	 * Page slug constant
	 *
	 * @var string
	 */
	const ADMIN_PAGE_SLUG = 'mangapress-options-page';

	/**
	 * Constructor method
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
	}

	/**
	 * Load our admin page
	 *
	 * @return void
	 */
	public function admin_menu() {
		global $mangapress_page_hook;

		$mangapress_page_hook = add_options_page(
			__( 'Manga+Press Options', 'mangapress' ),
			__( 'Manga+Press Options', 'mangapress' ),
			'manage_options',
			self::ADMIN_PAGE_SLUG,
			array( $this, 'load_page' )
		);

		add_action( "load-{$mangapress_page_hook}", array( $this, 'load_help_tabs' ) );
	}


	/**
	 * Load the admin page
	 *
	 * @return void
	 */
	public function load_page() {
		require_once MP_ABSPATH . '/includes/pages/options.php';
	}


	/**
	 * Load contextual help tabs
	 *
	 * @return void
	 */
	public function load_help_tabs() {
		$screen = get_current_screen();

		$tab = $this->get_current_tab();
		$screen->add_help_tab( $this->get_help_tabs( $tab ) );
	}


	/**
	 * Get help tab data for current option tab
	 *
	 * @param string $option_tab
	 * @return array
	 */
	public function get_help_tabs( $option_tab ) {
		$help_tabs = array(
			'basic'      => array(
				'id'      => 'help_basic',
				'title'   => __( 'Basic Options Help' ),
				'content' => $this->get_help_tab_contents(),
			),
			'comic_page' => array(
				'id'      => 'help_comic_page',
				'title'   => __( 'Comic Page Options Help' ),
				'content' => $this->get_help_tab_contents( 'comic_page' ),
			),
			'nav'        => array(
				'id'      => 'help_nav',
				'title'   => __( 'Navigation Options Help' ),
				'content' => $this->get_help_tab_contents( 'nav' ),
			),
		);

		return $help_tabs[ $option_tab ];
	}


	/**
	 * Get help tab contents from file
	 *
	 * @param string $help_tab Name of tab content to get
	 * @return string
	 */
	public function get_help_tab_contents( $help_tab = 'basic' ) {
		ob_start();
		switch ( $help_tab ) {
			case 'basic':
				require_once MP_ABSPATH . '/includes/pages/help-basic.php';
				break;
			case 'comic_page':
				require_once MP_ABSPATH . '/includes/pages/help-comic-page.php';
				break;
			case 'nav':
				require_once MP_ABSPATH . '/includes/pages/help-nav.php';
				break;
			default:
				// have a default response
		}

		return ob_get_clean();
	}


	/**
	 * Display options tabs
	 *
	 * @param string $current Current tab
	 * @return void
	 */
	public function options_page_tabs( $current = 'basic' ) {
		$current = filter_input( INPUT_GET, 'tab' )
						? filter_input( INPUT_GET, 'tab' ) : 'basic';

		$options = MangaPress_Bootstrap::get_instance()->get_helper( 'options' );
		$tabs    = $options->options_sections();

		$links = array();
		foreach ( $tabs as $tab => $tab_data ) {
			if ( $tab == $current ) {
				$links[] = "<a class=\"nav-tab nav-tab-active\" href=\"?page=mangapress-options-page&tab={$tab}\">{$tab_data['title']}</a>";
			} else {
				$links[] = "<a class=\"nav-tab\" href=\"?page=mangapress-options-page&tab={$tab}\">{$tab_data['title']}</a>";
			}
		}

		echo '<h2 class="nav-tab-wrapper">';

		foreach ( $links as $link ) {
			echo $link;
		}

		echo '</h2>';
	}


	/**
	 * Create options page tabs
	 *
	 * @return string
	 */
	public function get_current_tab() {
		$options = MangaPress_Bootstrap::get_instance()->get_helper( 'options' );
		$tabs    = $options->get_options_sections();

		$current_tab = filter_input( INPUT_GET, 'tab' );
		if ( in_array( $current_tab, $tabs, true ) ) {
			return $current_tab;
		} else {
			return 'basic';
		}
	}
}
