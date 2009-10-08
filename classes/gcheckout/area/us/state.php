<?php defined('SYSPATH') or die('No direct script access.');
/**
 * As a subtag of <allowed-areas>, the <us-state-area> tag contains a state
 * where a particular shipping option is available.
 *
 * As a subtag of <excluded-areas>, the <us-state-area> tag contains a state
 * where a particular shipping option is unavailable.
 *
 * As a subtag of <tax-area> or <tax-areas>, the <us-state-area> tag contains
 * a state where a tax rule should be applied.
 *
 * @package    Google Checkout
 * @copyright  (c) 2009 Woody Gilk
 * @author     Woody Gilk
 * @license    MIT
 */
class gCheckout_Area_US_State extends gCheckout_Area {

	/**
	 * The <state> tag identifies a state where a particular tax rule is applied
	 * or where a particular shipping option is available or unavailable.
	 *
	 * @param   string  two-letter US state code
	 */
	public static function factory($state)
	{
		return new self($state);
	}

	/**
	 * @var  string   two-letter US state code
	 */
	public $state;

	public function __construct($state)
	{
		$this->state = $state;
	}

	public function build(DOMDocument $xml, DOMElement $list)
	{
		$list->appendChild($state = $xml->createElement('us-state-area'));
		$state->appendChild($xml->createElement('state', $this->state));
	}

} // End gCheckout_Area_US_State