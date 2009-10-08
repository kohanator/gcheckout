<?php defined('SYSPATH') or die('No direct script access.');

class gCheckout_Cart extends gCheckout {

	public static function factory($merchant_id, $merchant_key, $sandbox = NULL, $currency = NULL)
	{
		return new gCheckout_Cart($merchant_id, $merchant_key, $sandbox, $currency);
	}

	public $currency = 'USD';

	public $items = array();

	public $request_auth;

	public $good_until;

	public $merchant_data;

	public $edit_cart_url;

	public $continue_shopping_url;

	public $default_tax_table = array();

	public $alternate_tax_table = array();

	public $shipping_methods = array();

	public $merchant_calculations = array();

	public $request_buyer_phone_number;

	public $platform_id;

	public $analytics_data;

	public $parameterized_urls = array();

	// Build the merchant-checkout-flow-support tag
	protected $_flow = FALSE;

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
	 * @param   boolean  value of request-initial-auth-details
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
		$this->_flow = TRUE;

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
		$this->_flow = TRUE;

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
		$this->_flow = TRUE;

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
		$this->_flow = TRUE;

		$this->alternate_tax_table[] = $table;

		return $this;
	}

	public function shipping(gCheckout_Shipping $method)
	{
		$this->_flow = TRUE;

		$this->shipping_methods[] = $method;

		return $this;
	}

	public function calculation(gCheckout_Calculation $calc)
	{
		$this->_flow = TRUE;

		$this->merchant_calculations[] = $calc;

		return $this;
	}

	public function request_phone_number($value)
	{
		$this->_flow = TRUE;

		$this->request_buyer_phone_number = $value ? 'true' : 'false';

		return $this;
	}

	public function platform_id($value)
	{
		$this->_flow = TRUE;

		$this->platform_id = $value;

		return $this;
	}

	public function analytics($value)
	{
		$this->_flow = TRUE;

		$this->analytics_data = $value;

		return $this;
	}

	public function parameterized_url(gCheckout_URL $url)
	{
		$this->_flow = TRUE;

		$this->parameterized_urls[] = $url;

		return $this;
	}

	public function build()
	{
		$xml = new DOMDocument('1.0', Kohana::$charset);
		$xml->formatOutput = TRUE;

		// Append create the root elements
		$xml->appendChild($root = $xml->createElementNS('http://checkout.google.com/schema/2', 'checkout-shopping-cart'));
		$root->appendChild($cart = $xml->createElement('shopping-cart'));
		$cart->appendChild($items = $xml->createElement('items'));

		foreach ($this->items as $item)
		{
			// Add each item
			$item->build($xml, $items);
		}

		if ($this->good_until)
		{
			// Set the cart expiration date
			$cart->appendChild($node = $xml->createElement('cart-expiration'));
			$node->appendChild($xml->createElement('good-until-date', $this->good_until));
		}

		if ($this->merchant_data)
		{
			// Append merchant data
			$cart->appendChild($xml->importNode($this->merchant_data, TRUE));
		}

		if ($this->request_auth)
		{
			// Request initial credit auth
			$root->appendChild($node = $xml->createElement('order-processing-support'));
			$node->appendChild($xml->createElement('request-initial-auth-details', $this->request_auth));
		}

		if ($this->_flow)
		{
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

				if ($this->alternate_tax_table)
				{
					$tax_tables->appendChild($table = $xml->createElement('alternate-tax-tables'));

					foreach ($this->alternate_tax_table as $tax)
					{
						$tax->build($xml, $table);
					}
				}
			}
		}

		// if ($this->flow)
		// {
		// 	$root->appendChild($flow = $xml->createElement('checkout-flow-support'));
		// 	$flow->appendChild($flow = $xml->createElement('merchant-checkout-flow-support'));
		//
		// 	$this->flow->build($flow, $xml);
		//
		// 	// edit-cart-url?, continue-shopping-url?, tax-tables?, shipping-methods?, merchant-calculations?, request-buyer-phone-number?, platform-id?, analytics-data?, parameterized-urls
		// }

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