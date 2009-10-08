<?php defined('SYSPATH') or die('No direct script access.');

class gCheckout_Tax {

	public static function factory($rate, $shipping_taxed = NULL)
	{
		return new gCheckout_Tax($rate, $shipping_taxed);
	}

	public $standalone;

	public $rate;

	public $shipping_taxed;

	public $areas = array();

	public function __construct($rate, $shipping_taxed = NULL)
	{
		$this->rate = (string) $rate;

		if (isset($shipping_taxed))
		{
			$this->shipping_taxed = (bool) $shipping_taxed;
		}
	}

	public function area(gCheckout_Area $area)
	{
		$this->areas[] = $area;

		return $this;
	}

	public function build(DOMDocument $xml, DOMElement $rule)
	{
		$rule->appendChild($xml->createElement('rate', $this->rate));

		if (isset($this->shipping_taxed))
		{
			$rule->appendChild($xml->createElement('shipping_taxed', $this->shipping_taxed));
		}

		$rule->appendChild($areas = $xml->createElement('tax-areas'));

		foreach ($this->areas as $area)
		{
			$area->build($xml, $areas);
		}
	}

} // End gCheckout_Tax_Table