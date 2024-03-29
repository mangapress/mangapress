<?php
/**
 * Singleton Trait
 *
 * @package MangaPress
 */

namespace MangaPress;

trait Singleton {
	/**
	 * Instance of class
	 *
	 * @var ?null
	 */
	protected static $instance = null;

	/**
	 * Return instance of class
	 *
	 * @return ?Singleton|null
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Initialization method
	 *
	 * @return void
	 */
	abstract public function init();
}
