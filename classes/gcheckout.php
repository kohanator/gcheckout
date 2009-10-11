<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @package    Google Checkout
 * @copyright  (c) 2009 Woody Gilk
 * @author     Woody Gilk
 * @license    MIT
 */
abstract class gCheckout {

	const URL_SANDBOX    = 'https://sandbox.google.com/checkout/api/checkout/v2/request/Merchant/';
	const URL_PRODUCTION = 'https://checkout.google.com/api/checkout/v2/request/Merchant/';

	public $sandbox = TRUE;

	public $merchant_id;
	public $merchant_key;

	public function __construct($merchant_id, $merchant_key, $sandbox = NULL)
	{
		$this->merchant_id  = $merchant_id;
		$this->merchant_key = $merchant_key;

		if (isset($sandbox))
		{
			$this->sandbox = (bool) $sandbox;
		}
	}

	/**
	 * @return  DOMDocument
	 */
	public function execute()
	{
		// Set the API access URL
		$url = ($this->sandbox ? gCheckout::URL_SANDBOX : gCheckout::URL_PRODUCTION).$this->merchant_id;

		// Build the XML for the request
		$xml = $this->build();

		// Get the server response
		$response = Remote::get($url, array(

			// Use Basic auth
			CURLOPT_USERPWD    => $this->merchant_id.':'.$this->merchant_key,

			// Send the XML request using POST
			CURLOPT_POST       => TRUE,
			CURLOPT_POSTFIELDS => $xml,

		));

		// Create a new DOMDocument for the response
		$xml = new DOMDocument;
		$xml->formatOutput = TRUE;
		$xml->loadXML($response);

		return $xml;
	}

	/**
	 * Build the XML for this request.
	 *
	 * @return  string  XML request
	 */
	abstract public function build();

} // End gCheckout
