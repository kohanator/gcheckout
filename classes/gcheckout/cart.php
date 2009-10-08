<?php defined('SYSPATH') or die('No direct script access.');
/**
 * The <checkout-shopping-cart> tag is the root tag for a Checkout API request
 * and contains all checkout-related information.
 *
 * @package    Google Checkout
 * @copyright  (c) 2009 Woody Gilk
 * @author     Woody Gilk
 * @license    MIT
 */
class gCheckout_Cart extends gCheckout {

	/**
	 * @param   string   merchant id
	 * @param   string   merchant key
	 * @param   boolean  sandbox mode
	 * @param   string   three-letter currency code
	 * @return  gCheckout_Cart
	 */
	public static function factory($merchant_id, $merchant_key, $sandbox = NULL, $currency = NULL)
	{
		return new self($merchant_id, $merchant_key, $sandbox, $currency);
	}

	/**
	 * @var  string  three-letter currency code
	 */
	public $currency = 'USD';

	/**
	 * @var  array  gCheckout_Item objects
	 */
	public $items = array();

	/**
	 * @var  string  request a credit card auth notification?
	 */
	public $request_auth;

	/**
	 * @var  string  cart expiration date (ISO 8601 formatted)
	 */
	public $good_until;

	/**
	 * @var  string  merchant private data (XML)
	 */
	public $merchant_data;

	/**
	 * @var  string  URL for the customer to edit the cart
	 */
	public $edit_cart_url;

	/**
	 * @var  string  URL for the customer to return to shopping
	 */
	public $continue_shopping_url;

	/**
	 * @var  array  gCheckout_Tax objects
	 */
	public $default_tax_table = array();

	/**
	 * @var  array  gCheckout_Tax_Table objects
	 */
	public $alternate_tax_tables = array();

	/**
	 * @var  array  gCheckout_Shipping objects
	 */
	public $shipping_methods = array();

	/**
	 * @var  array  gCheckout_Calculation objects
	 */
	public $merchant_calculations = array();

	/**
	 * @var  boolean  request buyer phone number?
	 */
	public $request_buyer_phone_number;

	/**
	 * @var  string  platform provider merchant id
	 */
	public $platform_id;

	/**
	 * @var  string  Google Analytics data
	 */
	public $analytics_data;

	/**
	 * @var  array  parameterized urls
	 */
	public $parameterized_urls = array();

	public function __construct($merchant_id, $merchant_key, $sandbox = NULL, $currency = NULL)
	{
		parent::__construct($merchant_id, $merchant_key, $sandbox);

		if ($currency)
		{
			$this->currency = (string) $currency;
		}
	}

	/**
	 * The <item> tag contains information about an individual item listed in
	 * the customer's shopping cart.
	 *
	 * [!!] Item currency will be overwritten by the cart currency because
	 * Google Checkout does not support items with multiple currencies.
	 *
	 * @param   object   gCheckout_Item
	 * @return  $this
	 */
	public function item(gCheckout_Item $item)
	{
		// Overwrite the item currency
		$item->currency = $this->currency;

		$this->items[] = $item;

		return $this;
	}

	/**
	 * The <good-until-date> tag identifies the date that the order in a
	 * Checkout API request expires.
	 *
	 * @param   integer   UNIX timestamp
	 * @return  $this
	 */
	public function good_until($value)
	{
		if ( ! is_string($value))
		{
			// Convert UNIX timestamp into a ISO 8601 timestamp
			$value = date(DATE_ISO8601, $value);
		}

		$this->good_until = $value;

		return $this;
	}

	/**
	 * The <merchant-private-data> tag contains any well-formed XML sequence that
	 * should accompany an order. Google Checkout will return this XML in the
	 * <merchant-calculation-callback> and the <new-order-notification> for the order.
	 *
	 * @param   string  XML data
	 * @return  $this
	 */
	public function merchant_data($value)
	{
		// Create a new document to validate and hold the XML
		$xml = new DOMDocument('1.0', Kohana::$charset);

		// Load the XML with merchant-private-data as the root node
		$xml->loadXML('<merchant-private-data>'.$value.'</merchant-private-data>');

		// Create a reference to the root node
		$this->merchant_data = $xml->getElementsByTagName('merchant-private-data')->item(0);

		return $this;
	}

	/**
	 * The <request-initial-auth-details> tag indicates whether Google should
	 * send an <authorization-amount-notification> when a credit card is
	 * authorized for a new order.
	 *
	 * @param   boolean  request a credit card auth notification?
	 * @return  $this
	 */
	public function request_auth($value)
	{
		$value = $value ? 'true' : 'false';

		$this->request_auth = $value;

		return $this;
	}

	/**
	 * The <edit-cart-url> tag contains a URL that allows the buyer to make
	 * changes to the shopping cart before confirming an order.
	 *
	 * @param   string   URL
	 * @return  $this
	 */
	public function edit_cart_url($url)
	{
		if (strpos($url, '://') === FALSE)
		{
			// Make a complete URL
			$url = URL::base(TRUE).$url;
		}

		$this->edit_cart_url = (string) $url;

		return $this;
	}

	/**
	 * The <continue-shopping-url> tag contains a URL that allows the buyer to
	 * continue shopping after confirming an order.
	 *
	 * @param   string  URL
	 * @return  $this
	 */
	public function continue_shopping_url($url)
	{
		if (strpos($url, '://') === FALSE)
		{
			// Make a complete URL
			$url = URL::base(TRUE).$url;
		}

		$this->continue_shopping_url = (string) $url;

		return $this;
	}

	/**
	 * The <default-tax-rule> tag contains information about a tax rule that
	 * should be applied in a particular area. Each tax rule must contain
	 * either exactly one <tax-area> tag or exactly one <tax-areas> tag.
	 *
	 * @param   array  tax tables
	 * @return  $this
	 */
	public function default_tax(gCheckout_Tax $tax)
	{
		$this->default_tax_table[] = $tax;

		return $this;
	}

	/**
	 * The <alternate-tax-tables> tag contains a list of one or more
	 * alternate-tax-table elements. Alternate tax tables override the rules
	 * defined in the default-tax-table.
	 *
	 * @param   object  gCheckout_Tax
	 * @return  $this
	 */
	public function alternate_tax_table(gCheckout_Tax_Table $table)
	{
		$this->alternate_tax_tables[] = $table;

		return $this;
	}

	public function shipping(gCheckout_Shipping $method)
	{
		$this->shipping_methods[] = $method;

		return $this;
	}

	public function calculation(gCheckout_Calculation $calc)
	{
		$this->merchant_calculations[] = $calc;

		return $this;
	}

	/**
	 * The <request-buyer-phone-number> tag indicates whether the customer
	 * must enter a phone number to complete an order. If this tag's value
	 * is true, the customer must enter a number, which Google Checkout will
	 * return in the new order notification for the order.
	 *
	 * @param   boolean   request buyer phone number?
	 * @return  $this
	 */
	public function request_phone_number($value)
	{
		$this->request_buyer_phone_number = $value ? 'true' : 'false';

		return $this;
	}

	/**
	 * The <platform-id> tag should only be used by e-commerce providers who
	 * make API requests on behalf of a merchant. The tag's value contains a
	 * Google Checkout merchant ID that identifies the e-commerce provider.
	 *
	 * [!!] Note for e-commerce Providers: The Google Checkout Terms and Conditions
	 *  require e-commerce providers that manage websites for merchants who are
	 * affiliated with Google Checkout to identify themselves. The value of the
	 * <platform-id> tag should be a Google Checkout Merchant ID that uniquely
	 * identifies the e-commerce provider.
	 *
	 * In keeping with this requirement, e-commerce providers should sign up for
	 * a Google Checkout merchant account. They should then use the merchant ID
	 * for that account as the <platform-id> tag value in Checkout API requests
	 * for all merchants using the provider's platform. However, providers
	 * should still use the merchant IDs and merchant keys assigned to the
	 * individual merchants to encode API requests from those merch
	 *
	 * @param   string   provider merchant id
	 * @return  $this
	 */
	public function platform_id($value)
	{
		$this->platform_id = $value;

		return $this;
	}

	/**
	 * The <analytics-data> tag enables merchants that submit server-to-server
	 * Checkout API requests to use Google Analytics to track Checkout orders.
	 * The [Using Google Analytics to Track Google Checkout Orders][1] document explains
	 * how to retrieve the appropriate value for this tag.
	 *
	 * [1]: http://code.google.com/apis/checkout/developer/checkout_analytics_integration.html
	 *
	 * @param   string   Google Analytics data
	 * @return  $this
	 */
	public function analytics($value)
	{
		$this->analytics_data = $value;

		return $this;
	}

	/**
	 * The <parameterized-urls> tag contains information about all of the web
	 * beacons that the merchant wants to add to the Google Checkout order
	 * confirmation page.
	 *
	 * @param   object  gCheckout_URL
	 * @return  $this
	 */
	public function parameterized_url(gCheckout_URL $url)
	{
		$this->parameterized_urls[] = $url;

		return $this;
	}

	public function build()
	{
		$xml = new DOMDocument('1.0', Kohana::$charset);
		$xml->formatOutput = TRUE;

		// Create the root element
		$xml->appendChild($root = $xml->createElementNS('http://checkout.google.com/schema/2', 'checkout-shopping-cart'));

		// Create the cart and items
		$root->appendChild($cart = $xml->createElement('shopping-cart'));
		$cart->appendChild($items = $xml->createElement('items'));

		foreach ($this->items as $item)
		{
			$item->build($xml, $items);
		}

		if ($this->good_until)
		{
			$cart->appendChild($node = $xml->createElement('cart-expiration'));
			$node->appendChild($xml->createElement('good-until-date', $this->good_until));
		}

		if ($this->merchant_data)
		{
			$cart->appendChild($xml->importNode($this->merchant_data, TRUE));
		}

		if ($this->request_auth)
		{
			$root->appendChild($node = $xml->createElement('order-processing-support'));
			$node->appendChild($xml->createElement('request-initial-auth-details', $this->request_auth));
		}

		$root->appendChild($flow = $xml->createElement('checkout-flow-support'));
		$flow->appendChild($flow = $xml->createElement('merchant-checkout-flow-support'));

		if ($this->edit_cart_url)
		{
			$flow->appendChild($xml->createElement('edit-cart-url', $this->edit_cart_url));
		}

		if ($this->continue_shopping_url)
		{
			$flow->appendChild($xml->createElement('continue-shopping-url', $this->continue_shopping_url));
		}

		if ($this->default_tax_table)
		{
			$flow->appendChild($tax_tables = $xml->createElement('tax-tables'));

			$tax_tables->appendChild($table = $xml->createElement('default-tax-table'));
			$table->appendChild($table = $xml->createElement('tax-rules'));

			foreach ($this->default_tax_table as $tax)
			{
				$table->appendChild($rule = $xml->createElement('default-tax-rule'));

				$tax->build($xml, $rule);
			}

			if ($this->alternate_tax_tables)
			{
				$tax_tables->appendChild($table = $xml->createElement('alternate-tax-tables'));

				foreach ($this->alternate_tax_tables as $tax)
				{
					$tax->build($xml, $table);
				}
			}
		}

		if ($this->request_buyer_phone_number)
		{
			$flow->appendChild($xml->createElement('request-buyer-phone-number', $this->request_buyer_phone_number));
		}

		if ($this->platform_id)
		{
			$flow->appendChild($xml->createElement('platform-id', $this->platform_id));
		}

		if ($this->analytics_data)
		{
			$flow->appendChild($xml->createElement('analytics-data', $this->analytics_data));
		}

		if ($this->parameterized_urls)
		{
			$flow->appendChild($list = $xml->createElement('parameterized-urls'));

			foreach ($this->parameterized_urls as $url)
			{
				$url->build($xml, $list);
			}
		}

		return $xml->saveXML();
	}

	public function execute($redirect = FALSE)
	{
		$xml = parent::execute();

		if ($redirect)
		{
			// Attempt to locate the redirect URL
			$redirect = $xml->getElementsByTagName('redirect-url');

			if ($redirect->length)
			{
				// Follow the redirect
				Request::instance()->redirect($redirect->item(0)->nodeValue);
			}
		}

		return $xml;
	}

} // End gCheckout_Cart