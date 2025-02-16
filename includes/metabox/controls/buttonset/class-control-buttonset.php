<?php
/**
 * Buttonset control class.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Buttonset control
 *
 * @since  1.0.0
 * @access public
 */
class Hoo_ButterBean_Control_Buttonset extends ButterBean_Control {

	/**
	 * The type of control.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $type = 'buttonset';

	/**
	 * Get the value for the setting.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $setting
	 * @return mixed
	 */
	public function get_value( $setting = 'default' ) {

		$value  = parent::get_value( $setting );
		$object = $this->get_setting( $setting );

		return ! $value && $object ? $object->default : $value;
	}

}
