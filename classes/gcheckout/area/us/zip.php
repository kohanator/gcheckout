<?php defined('SYSPATH') or die('No direct script access.');
/**
 * As a subtag of <allowed-areas>, the <us-zip-area> tag contains a zip code or
 * range of zip codes where a particular shipping option is available.
 *
 * As a subtag of <excluded-areas>, the <us-zip-area> tag contains a zip code or
 * range of zip codes where a particular shipping option is unavailable.
 *
 * As a subtag of <tax-area> or <tax-areas>, the <us-zip-area> tag contains a
 * zip code or range of zip codes where a tax rule should be applied.
 *
 * @package    Google Checkout
 * @copyright  (c) 2009 Woody Gilk
 * @author     Woody Gilk
 * @license    MIT
 */
class gCheckout_Area_US_Zip extends gCheckout_Area {

	/**
	 * The <zip-pattern> tag contains a zip code or a range of zip codes.
	 * To specify a range of zip codes, use an asterisk as a wildcard operator.
	 * For example, you can specify that a shipping option is available or a
	 * tax rule applies to zip codes 94040 through 94049 by entering 9404* as
	 * the <zip-pattern> value.
	 *
	 * @param   string  zip code pattern
	 * @return  gCheckout_Area_US_Zip
	 */
	public static function factory($zip_pattern)
	{
		return new self($zip_pattern);
	}

	/**
	 * @var  string   zip code pattern
	 */
	public $zip_pattern;

	public function __construct($zip_pattern)
	{
		$this->zip_pattern = $zip_pattern;
	}

	public function build(DOMDocument $xml, DOMElement $list)
	{
		$list->appendChild($state = $xml->createElement('us-zip-area'));
		$state->appendChild($xml->createElement('zip-pattern', $this->zip_pattern));
	}

} // End gCheckout_Area_US_State