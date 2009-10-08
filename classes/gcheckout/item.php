<?php defined('SYSPATH') or die('No direct script access.');
/**
 * The <item> tag contains information about an individual item listed in the
 * customer's shopping cart.
 *
 * @package    Google Checkout
 * @copyright  (c) 2009 Woody Gilk
 * @author     Woody Gilk
 * @license    MIT
 */
class gCheckout_Item {

	/**
	 * @param   string   item name
	 * @param   string   item description
	 * @param   integer  quantity
	 * @param   float    price
	 * @return  gCheckout_Item
	 */
	public static function factory($name, $description, $quantity, $price)
	{
		return new self($name, $description, $quantity, $price);
	}

	/**
	 * @var  string  item name
	 */
	public $name;

	/**
	 * @var  string  item description
	 */
	public $description;

	/**
	 * @var  integer  quantity
	 */
	public $quantity;

	/**
	 * @var  decimal  price
	 */
	public $price;

	/**
	 * @var  string   currency code
	 */
	public $currency = 'USD';

	/**
	 * @var  float  item weight
	 */
	public $weight;

	/**
	 * @var  string   weight unit
	 */
	public $unit = 'LB';

	/**
	 * @var  string  tax table name
	 */
	public $tax_table;

	/**
	 * @var  object  gCheckout_Digital
	 */
	public $digital_content;

	/**
	 * @var  string   merchant item id (SKU)
	 */
	public $merchant_id;

	/**
	 * @var  string  merchant item data
	 */
	public $merchant_data;

	/**
	 * @param   string   item name
	 * @param   string   item description
	 * @param   integer  quantity
	 * @param   float    price
	 */
	public function __construct($name, $description, $quantity, $price)
	{
		// Set name and description
		$this->name        = (string) $name;
		$this->description = (string) $description;

		// Set quantity
		$this->quantity = (int) $quantity;

		// Format price
		$this->price = number_format($price, 2, '.', '');
	}

	/**
	 * The <item-weight> tag specifies the weight of an individual item in the
	 * customer's shopping cart.
	 *
	 * @param   float   item weight
	 * @param   string  weight unit
	 * @return  $this
	 */
	public function weight($value, $unit = NULL)
	{
		$this->weight = (string) $value;

		if ($unit)
		{
			$this->unit = (string) $unit;
		}

		return $this;
	}

	/**
	 * The <tax-table-selector> tag identifies an alternate tax table that
	 * should be used to calculate tax for a particular item. The value of the
	 * <tax-table-selector> tag should correspond to the value of the name
	 * attribute of an alternate-tax-table.
	 *
	 * @param   string   alternate tax table name
	 * @return  $this
	 */
	public function tax_table($value)
	{
		$this->tax_table = (string) $value;

		return $this;
	}

	/**
	 * The <digital-content> tag contains information relating to digital
	 * delivery of an item.
	 *
	 * @param   object  gCheckout_Digital
	 * @return  $this
	 */
	public function digital_content(gCheckout_Digital $value)
	{
		$this->digital_content = $value;

		return $this;
	}

	/**
	 * The <merchant-item-id> tag contains a value, such as a stock keeping
	 * unit (SKU), that you use to uniquely identify an item. Google Checkout
	 * will include this value in the merchant calculation callbacks and the
	 * new order notification for the order. This value also appears in the
	 * order information displayed in the Merchant Center.
	 * 
	 * [!!] To use the <merchant-item-id> to modify the shipping information for
	 * an item in an order, you must have provided the same <merchant-item-id>
	 * for that item in the Checkout API request for the order.
	 * 
	 * @param   string   merchant item id
	 */
	public function merchant_id($value)
	{
		$this->merchant_id = (string) $value;

		return $this;
	}

	/**
	 * The <merchant-private-item-data> tag contains any well-formed XML sequence
	 * that should accompany an individual item in an order. Google Checkout will
	 * return this XML in the <merchant-calculation-callback> and the
	 * <new-order-notification> for the order.
	 * 
	 * @param   string   XML string
	 * @return  $this
	 */
	public function merchant_data($value)
	{
		$xml = new DOMDocument('1.0', Kohana::$charset);

		// Add the value to a root node
		$xml->loadXML('<merchant-private-item-data>'.$value.'</merchant-private-item-data>');

		// Capture the root node
		$this->merchant_data = $xml->getElementsByTagName('merchant-private-item-data')->item(0);;

		return $this;
	}

	public function build(DOMDocument $xml, DOMElement $list)
	{
		$list->appendChild($item = $xml->createElement('item'));

		$item->appendChild($xml->createElement('item-name', $this->name));
		$item->appendChild($xml->createElement('item-description', $this->description));

		$item->appendChild($node = $xml->createElement('unit-price', $this->price));
		$node->setAttribute('currency', $this->currency);

		$item->appendChild($xml->createElement('quantity', $this->quantity));

		if ($this->weight)
		{
			$item->appendChild($node = $xml->createElement('item-weight', $this->weight));
			$node->setAttribute('unit', $this->unit);
		}

		if ($this->tax_table)
		{
			$item->appendChild($xml->createElement('tax-table-selector', $this->tax_table));
		}

		if ($this->digital_content)
		{
			$item->appendChild($digital = $xml->createElement('digital-content'));

			$this->digital_content->build($xml, $digital);
		}

		if ($this->merchant_id)
		{
			$item->appendChild($xml->createElement('merchant-item-id', $this->merchant_id));
		}

		if ($this->merchant_data)
		{
			$item->appendChild($xml->importNode($this->merchant_data, TRUE));
		}
	}

} // End gCheckout_Item