<?php
/**
 * Class for loading settings
 *
 * @package MangaPress
 */

namespace MangaPress;

/**
 * Manga+Press Settings Class
 */
class Settings {

	/**
	 * Options array
	 *
	 * @var array
	 */
	protected array $options;

	/**
	 * Manga+Press Settings object
	 *
	 * @var Settings|null
	 */
	protected static ?Settings $instance = null;

	/**
	 * Get instance of object
	 *
	 * @return Settings
	 */
	public static function get_instance(): Settings {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Initialize class
	 *
	 * @return void
	 */
	public function init() {
		$this->set_options();
	}

	/**
	 * Set MangaPress options. This method should run every time
	 * MangaPress options are updated.
	 *
	 * @uses init()
	 * @see Bootstrap::init()
	 *
	 * @return void
	 */
	public function set_options() {
		$this->options = get_option( 'mangapress_options' );
	}


	/**
	 * Get MangaPress options
	 *
	 * @return array
	 */
	public function get_options() {
		return $this->options;
	}


	/**
	 * Get one option from options array
	 *
	 * @param string $section Option section.
	 * @param string $option_name Option name.
	 *
	 * @return boolean|mixed
	 */
	public function get_option( string $section, string $option_name ) {
		if ( ! isset( $this->options[ $section ][ $option_name ] ) ) {
			return false;
		}

		return $this->options[ $section ][ $option_name ];
	}
}
