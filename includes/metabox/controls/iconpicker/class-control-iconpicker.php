<?php
/**
 * Iconpicker control class.
 *
 */

/**
 * Iconpicker control class.
 *
 * @since  1.0.0
 * @access public
 */
class Hoo_ButterBean_Control_Iconpicker extends ButterBean_Control {

	/**
	 * The type of control.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $type = 'iconpicker';

	/**
	 * Adds custom data to the json array. This data is passed to the Underscore template.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function to_json() {
		parent::to_json();
		$this->json['value'] = $this->get_value();
	}
}
