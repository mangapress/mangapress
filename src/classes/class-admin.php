<?php
/**
 * MangaPress admin class file.
 *
 * @package MangaPress
 */

namespace MangaPress;

/**
 * MangaPress Admin class
 */
class Admin {
	use Singleton;

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
	public function init() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'display_post_states', array( $this, 'display_post_states' ), 20, 2 );
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
		require_once MP_ABSPATH . '/src/pages/options.php';
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
	 * @param string $option_tab Option tab to display.
	 *
	 * @return array
	 */
	public function get_help_tabs( string $option_tab ): array {
		$help_tabs = array(
			'basic'      => array(
				'id'      => 'help_basic',
				'title'   => __( 'Basic Options Help', 'mangapress' ),
				'content' => $this->get_help_tab_contents(),
			),
			'comic_page' => array(
				'id'      => 'help_comic_page',
				'title'   => __( 'Comic Page Options Help', 'mangapress' ),
				'content' => $this->get_help_tab_contents( 'comic_page' ),
			),
			'nav'        => array(
				'id'      => 'help_nav',
				'title'   => __( 'Navigation Options Help', 'mangapress' ),
				'content' => $this->get_help_tab_contents( 'nav' ),
			),
		);

		return $help_tabs[ $option_tab ];
	}


	/**
	 * Get help tab contents from file
	 *
	 * @param string $help_tab Name of tab content to get.
	 *
	 * @return string
	 */
	public function get_help_tab_contents( string $help_tab = 'basic' ): string {
		ob_start();
		switch ( $help_tab ) {
			case 'basic':
				require_once MP_ABSPATH . '/src/pages/help-basic.php';
				break;
			case 'comic_page':
				require_once MP_ABSPATH . '/src/pages/help-comic-page.php';
				break;
			case 'nav':
				require_once MP_ABSPATH . '/src/pages/help-nav.php';
				break;
			default:
				// have a default response.
		}

		return ob_get_clean();
	}


	/**
	 * Display options tabs
	 *
	 * @return void
	 */
	public function options_page_tabs() {
		$current = filter_input( INPUT_GET, 'tab' ) ?: 'basic';

		$options = Options::get_instance();
		$tabs    = $options->options_sections();

		$links = array();
		foreach ( $tabs as $tab => $tab_data ) {
			if ( $tab === $current ) {
				$links[] = "<a class=\"nav-tab nav-tab-active\" href=\"?page=mangapress-options-page&tab={$tab}\">{$tab_data['title']}</a>";
			} else {
				$links[] = "<a class=\"nav-tab\" href=\"?page=mangapress-options-page&tab={$tab}\">{$tab_data['title']}</a>";
			}
		}

		echo '<h2 class="nav-tab-wrapper">';

		foreach ( $links as $link ) {
			echo wp_kses( $link, wp_kses_allowed_html( 'post' ) );
		}

		echo '</h2>';
	}


	/**
	 * Create options page tabs
	 *
	 * @return string
	 */
	public function get_current_tab() {
		$tabs = Options::get_instance()->get_options_sections();

		$current_tab = filter_input( INPUT_GET, 'tab' );
		if ( in_array( $current_tab, $tabs, true ) ) {
			return $current_tab;
		} else {
			return 'basic';
		}
	}

	/**
	 * Add to statuses to indicate what pages do what
	 *
	 * @param string[] $post_statuses Array of post status.
	 * @param \WP_Post $post Current post in the loop.
	 *
	 * @return string[]
	 */
	public function display_post_states( array $post_statuses, \WP_Post $post ): array {
		if ( ! is_admin() || 'page' !== get_post_type( $post ) ) {
			return $post_statuses;
		}

		$post_slug = get_post_field( 'post_name', $post );

		$latestcomic_page  = Settings::get_option( 'basic', 'latestcomic_page' );
		$comicarchive_page = Settings::get_option( 'basic', 'comicarchive_page' );

		if ( $latestcomic_page === $post_slug ) {
			$post_statuses[] = __( 'Latest Comic Page', 'mangapress' );
		}

		if ( $comicarchive_page === $post_slug ) {
			$post_statuses[] = __( 'Comic Archive Page', 'mangapress' );
		}

		return $post_statuses;
	}
}
