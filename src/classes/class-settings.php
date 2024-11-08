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
	use Singleton;

	/**
	 * Options array
	 *
	 * @var array
	 */
	protected static array $options;

	/**
	 * Initialize class
	 *
	 * @return void
	 */
	public function init() {
		self::set_options();
	}

	/**
	 * Set MangaPress options. This method should run every time
	 * MangaPress options are updated.
	 *
	 * @return void
	 */
	private function set_options() {
		self::$options = get_option( 'mangapress_options' );
	}


	/**
	 * Get MangaPress options
	 *
	 * @return array
	 */
	public static function get_options(): array {
		return self::$options;
	}


	/**
	 * Get one option from options array
	 *
	 * @param string $section Option section.
	 * @param string $option_name Option name.
	 * @param mixed  $default Default option value if not populated
	 *
	 * @return boolean|mixed
	 */
	public static function get_option( string $section, string $option_name, $default = false ) {
		if ( ! isset( self::$options[ $section ][ $option_name ] ) ) {
			return $default;
		}

		return self::$options[ $section ][ $option_name ];
	}
}
