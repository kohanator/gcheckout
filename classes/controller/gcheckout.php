<?php defined('SYSPATH') or die('No direct script access.');

class Controller_gCheckout extends Controller {

	public function action_index()
	{
		$config = Kohana::config('gcheckout');

		$cart = gCheckout_Cart::factory($config->merchant_id, $config->merchant_key)
			->default_tax(gCheckout_Tax::factory(0.065)
				->area(gCheckout_Area_US_State::factory('MN')))

			->alternate_tax_table(gCheckout_Tax_Table::factory('tax_exempt')
				->tax_rule(gCheckout_Tax::factory(0.0)
					->area(gCheckout_Area_World::factory())))

			->item(gCheckout_Item::factory('Hello MP3 Player', 'Simple MP3 player', 1, '100.00'))
			->item(gCheckout_Item::factory('Hello MP3 Software', '', 1, '10.00')
				->tax_table('tax_exempt')
				->digital_content(gCheckout_Digital::factory()
					->key('abcd-1234-0000-wxyz')
					->url('http://example.com/downloads/mp3sync.dmg')))
			;

		$this->request->headers['Content-Type'] = 'text/plain';
		print_r($cart->build());
	}

} // End gCheckout
