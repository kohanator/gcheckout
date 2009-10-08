<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Base class for all area tags.
 *
 * @package    Google Checkout
 * @copyright  (c) 2009 Woody Gilk
 * @author     Woody Gilk
 * @license    MIT
 */
abstract class gCheckout_Area {

	/**
	 * Attach the area to the given list.
	 *
	 * @param   object   DOMDocument
	 * @param   object   DOMElement container
	 * @return  void
	 */
	abstract public function build(DOMDocument $xml, DOMElement $list);

} // End gCheckout_Area