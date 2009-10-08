<?php defined('SYSPATH') or die('No direct script access.');
/**
 * As a subtag of <allowed-areas>, the <us-country-area> tag identifies a
 * region of the United States where a particular shipping option is available.
 *
 * As a subtag of <excluded-areas>, the <us-country-area> tag identifies a
 * region of the United States where a particular shipping option is unavailable.
 *
 * As a subtag of <tax-area> or <tax-areas>, the <us-country-area> tag identifies
 * a region of the United States where a tax rule should be applied.
 *
 * @package    Google Checkout
 * @copyright  (c) 2009 Woody Gilk
 * @author     Woody Gilk
 * @license    MIT
 */
class gCheckout_Area_US_Country extends gCheckout_Area {

	/**
	 * The country-area attribute identifies a region of the United States.
	 * Valid values for this attribute are:
	 *
	 * Name           | Description
	 * ---------------|--------------------------------------------------------
	 * CONTINENTAL_48 | All U.S. states except Alaska and Hawaii
	 * FULL_50_STATES | All U.S. states
	 * ALL            | All U.S. postal service addresses, including military addresses, U.S. insular areas, etc.
	 *
	 * @param   string   country area
	 * @return  gCheckout_Area_US_Country
	 */
	public static function factory($country_area = NULL)
	{
		return new self($country_area);
	}

	/**
	 * @var  string  country area
	 */
	public $country_area = 'CONTINENTAL_48';

	public function __construct($country_area = NULL)
	{
		if ($country_area)
		{
			$this->country_area = $country_area;
		}
	}

	public function build(DOMDocument $xml, DOMElement $list)
	{
		$list->appendChild($area = $xml->createElement('us-country-area'));
		$area->setAttribute('country-area', $this->type);
	}

} // End gCheckout_Area_US_Country