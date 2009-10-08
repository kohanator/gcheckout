<?php defined('SYSPATH') or die('No direct script access.');
/**
 * The <postal-area> tag specifies a geographic region somewhere in the world.
 *
 * As a subtag of <allowed-areas>, the <postal-area> tag contains a country
 * where a particular shipping option is available. The tag may also contain
 * a postal code or range of postal codes in that country where the shipping
 * option is offered.
 *
 * As a subtag of <excluded-areas>, the <postal-area> tag contains a country
 * (and possibly a zip code or range of zip codes within that country) where
 * a particular shipping option is unavailable.
 *
 * As a subtag of <tax-area> or <tax-areas>, the <postal-area> tag contains a
 * country and possibly a zip code or range of zip codes where a tax rule
 * should be applied.
 *
 * @package    Google Checkout
 * @copyright  (c) 2009 Woody Gilk
 * @author     Woody Gilk
 * @license    MIT
 */
class gCheckout_Area_Postal extends gCheckout_Area {

	/**
	 * As a subtag of <postal-area>, the <country-code> tag specifies a country
	 * associated with a tax area, shipping restriction or address filter
	 *
	 * [!!] Note that Google Checkout does not allow you to ship orders to
	 * countries that are export-embargoed for the United States. Google Checkout
	 * will return an error if your shipping restrictions or address filters
	 * include one of these countries in a shipping area. For example, Iran (IR)
	 * and North Korea (KP) may not be included in a shipping area.
	 *
	 * In all other contexts, the <country-code> tag identifies the country code
	 * associated with an order's billing address or shipping address. The value
	 * of this tag must be a two-letter ISO 3166 country code.
	 *
	 * @param   string  country code
	 * @param   string  postal code pattern
	 * @return  gCheckout_Area_Postal
	 */
	public static function factory($country, $postal_code = NULL)
	{
		return new self($country, $postal_code);
	}

	/**
	 * @var  string   ISO 3166 country code
	 */
	public $country;

	/**
	 * @var  string
	 */
	public $postal_code;

	public function __construct($country, $postal_code = NULL)
	{
		$this->country = (string) $country;

		if ($postal_code)
		{
			$this->postal_code = $postal_code;
		}
	}

	/**
	 * The <postal-code-pattern> tag contains a postal code or a range of
	 * postal codes for a specific country. To specify a range of postal codes,
	 * use an asterisk as a wildcard operator. For example, you can provide
	 * a <postal-code-pattern> value of SW* to indicate that a shipping option
	 * is available or a tax rule applies in any postal code beginning with the
	 * characters SW.
	 *
	 * @param   string  postal code pattern
	 * @return  $this
	 */
	public function postal_code($value)
	{
		$this->postal_code = $value;

		return $this;
	}

	public function build(DOMDocument $xml, DOMElement $list)
	{
		$list->appendChild($area = $xml->createElement('postal-area'));
		$area->appendChild($xml->createElement('country-code', $this->country));

		if ($this->postal_code)
		{
			$area->appendChild($xml->createElement('postal-code-pattern', $this->postal_code));
		}
	}

} // End gCheckout_Area_Postal