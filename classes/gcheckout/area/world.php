<?php defined('SYSPATH') or die('No direct script access.');
/**
 * The <world-area> tag represents the entire world. This tag indicates that a
 * shipping option is available worldwide or that a particular tax rule
 * applies worldwide.
 *
 * For shipping options, the <world-area> tag can only appear as a subtag of
 * the allowed-areas tag. (Including the <world-area> tag as a subtag of the
 * excluded-areas tag would make the corresponding shipping option unavailable
 * to all shipping addresses.) However, you can use the <world-area> tag to
 * indicate that a shipping option is available worldwide and then identify
 * specific excluded areas where the shipping option is unavailable.
 * Those excluded areas could identify regions that are covered by other
 * shipping options or regions where you do not ship items.
 *
 * For tax rules, Google Checkout will select the first tax rule that matches
 * the customer's shipping address. As such, if you use the <world-area> tag to
 * define the area where a tax rule applies, that tax rule must appear last in
 * the list of tax rules in your API request.
 *
 * @package    Google Checkout
 * @copyright  (c) 2009 Woody Gilk
 * @author     Woody Gilk
 * @license    MIT
 */
class gCheckout_Area_World extends gCheckout_Area {

	/**
	 * @return  gCheckout_Area_World
	 */
	public static function factory()
	{
		return new self;
	}

	public function build(DOMDocument $xml, DOMElement $list)
	{
		$list->appendChild($xml->createElement('world-area'));
	}

} // End gCheckout_Area_World