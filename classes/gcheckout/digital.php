<?php defined('SYSPATH') or die('No direct script access.');

class gCheckout_Digital {

	public static function factory()
	{
		return new gCheckout_Digital;
	}

	// Digital content data
	protected $_data = array();

	public function description($value)
	{
		$value = htmlentities($value, ENT_NOQUOTES, Kohana::$charset);

		$this->_data['description'] = $value;

		return $this;
	}

	public function email_delivery($value)
	{
		$this->_data['email_delivery'] = $value ? 'true' : 'false';

		return $this;
	}

	public function key($value)
	{
		$this->_data['key'] = (string) $value;

		return $this;
	}

	public function url($value)
	{
		$this->_data['url'] = (string) $value;

		return $this;
	}

	public function display_disposition($value)
	{
		$this->_data['url'] = strtolower($value) === 'optimistic' ? 'OPTIMISTIC' : 'PESSIMISTIC';

		return $this;
	}

	public function build(DOMDocument $xml, DOMElement $digital)
	{
		if (isset($this->_data['description']))
		{
			$digital->appendChild($xml->createElement('description', $this->_data['description']));
		}

		if (isset($this->_data['email_delivery']))
		{
			$digital->appendChild($xml->createElement('email-delivery', $this->_data['email_delivery']));
		}

		if (isset($this->_data['key']))
		{
			$digital->appendChild($xml->createElement('key', $this->_data['key']));
		}

		if (isset($this->_data['url']))
		{
			$digital->appendChild($xml->createElement('url', $this->_data['url']));
		}

		if (isset($this->_data['display_disposition']))
		{
			$digital->appendChild($xml->createElement('display-disposition', $this->_data['display_disposition']));
		}
	}

} // End gCheckout_Item_Digital
