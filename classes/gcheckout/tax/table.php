<?php defined('SYSPATH') or die('No direct script access.');

class gCheckout_Tax_Table {

	public static function factory($name = NULL, $standalone = NULL)
	{
		return new self($name, $standalone);
	}

	public $standalone;

	public $name;

	public $tax_rules = array();

	public function __construct($name = NULL, $standalone = NULL)
	{
		if ($name)
		{
			$this->name = $name;
		}

		if (isset($standalone))
		{
			$this->standalone = $standalone ? 'true' : 'false';
		}
	}

	public function tax_rule(gCheckout_Tax $tax)
	{
		$this->tax_rules[] = $tax;

		return $this;
	}

	public function build(DOMDocument $xml, DOMElement $tables)
	{
		$tables->appendChild($table = $xml->createElement('alternate-tax-table'));

		if ($this->name)
		{
			$table->setAttribute('name', $this->name);
		}

		if ($this->standalone)
		{
			$table->setAttribute('standalone', $this->standalone);
		}

		$table->appendChild($table = $xml->createElement('alternate-tax-rules'));

		foreach ($this->tax_rules as $tax)
		{
			$table->appendChild($rule = $xml->createElement('alternate-tax-rule'));

			$tax->build($xml, $rule);
		}
	}

} // End gCheckout_Tax_Table