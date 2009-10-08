<?php defined('SYSPATH') or die('No direct script access.');
/**
 * The <parameterized-url> tag contains information about an individual web
 * beacon that will be included on the Google Checkout order confirmation page.
 *
 * The <parameterized-url> tag has one required attribute, which identifies the
 * base URL for the web beacon. This tag also has one optional subtag, which
 * contains the list of dynamic variables that Google Checkout should add to
 * the base URL.
 *
 * @package    Google Checkout
 * @copyright  (c) 2009 Woody Gilk
 * @author     Woody Gilk
 * @license    MIT
 */
class gCheckout_URL {

	/**
	 * The url attribute identifies the base URL for a web beacon that will be
	 * included on the Google Checkout order confirmation page. The base URL
	 * may include some query string parameters that have a fixed value, such
	 * as a partner ID that identifies the merchant to the third-party
	 * tracking provider.
	 *
	 * @param   string  base URL
	 * @param   array   list of parameters, name => type
	 * @return  gCheckout_URL
	 */
	public static function factory($url, array $params = NULL)
	{
		return new self($url, $params);
	}

	/**
	 * @var  string   base URL
	 */
	public $url;

	/**
	 * @var  array   list of parameters, name => type
	 */
	public $parameters = array();

	public function __construct($url, array $params = NULL)
	{
		$this->url = htmlentities($url, ENT_QUOTES, Kohana::$charset, FALSE);

		if ($params)
		{
			$this->parameters += $params;
		}
	}

	/**
	 * The <url-parameter> tag contains information about an individual
	 * parameter that will be added to the base URL for a web beacon.
	 *
	 * The <url-parameter> tag has two required attributes: the parameter key
	 * and the parameter value that Google should include in the web beacon URL.
	 *
	 * See the [url-parameter documentation][1] for a list of valid types.
	 *
	 * [1]: http://code.google.com/apis/checkout/developer/Google_Checkout_XML_API_Tag_Reference.html#tag_url-parameter
	 *
	 * @param   string   parameter name
	 * @param   string   replacement type
	 * @return  $this
	 */
	public function parameter($name, $type)
	{
		$this->parameters[$name] = $type;

		return $this;
	}

	public function build(DOMDocument $xml, DOMElement $list)
	{
		$list->appendChild($url = $xml->createElement('parameterized-url'));
		$url->setAttribute('url', $this->url);

		$url->appendChild($params = $xml->createElement('parameters'));

		foreach ($this->parameters as $name => $type)
		{
			$params->appendChild($param = $xml->createElement('url-parameter'));
			$param->setAttribute('name', $name);
			$param->setAttribute('type', $type);
		}
	}

} // End gCheckout_URL
