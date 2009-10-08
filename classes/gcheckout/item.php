<?php defined('SYSPATH') or die('No direct script access.');

class gCheckout_Item {

	public static function factory($name, $description, $quantity, $price, $currency = NULL)
	{
		return new gCheckout_Item($name, $description, $quantity, $price, $currency);
	}

	public $name;
	public $description;
	public $quantity;
	public $price;
	public $currency = 'USD';
	public $weight;
	public $unit = 'LB';
	public $tax_table;
	public $digital_content;
	public $merchant_id;
	public $merchant_data;

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

	public function weight($value, $unit = NULL)
	{
		$this->weight = (int) round($value, 0);

		if ($unit)
		{
			$this->unit = (string) $unit;
		}

		return $this;
	}

	public function tax_table($value)
	{
		$this->tax_table = (string) $value;

		return $this;
	}

	public function digital_content(gCheckout_Digital $value)
	{
		$this->digital_content = $value;

		return $this;
	}

	public function merchant_id($value)
	{
		$this->merchant_id = (string) $value;

		return $this;
	}

	public function merchant_data($value)
	{
		$this->merchant_data = $value;

		return $this;
	}

	public function build(DOMDocument $xml, DOMElement $items)
	{
		$items->appendChild($item = $xml->createElement('item'));

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

			// Build digital content
			$this->digital_content->build($xml, $digital);
		}

		if ($this->merchant_id)
		{
			$item->appendChild($xml->createElement('merchant-item-id', $this->merchant_id));
		}

		if ($this->merchant_data)
		{
			
		}

		return $this;
	}

} // End gCheckout_Item