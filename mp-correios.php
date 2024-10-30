<?php
/*
MarketPress Shipping Plugin CORREIOS
Author: Thiago Quadros
Version: 0.1
E-mail: tmquadros@hotmail.com
Web:	http://www.artboxstudio.net/	Art for a better Web
		http://www.3dmais.art.br/		More for a better 3D
		http://www.acombe.com/			A Marketplace for a better life
*/

class MP_Shipping_Correios extends MP_Shipping_API {

	//private shipping method name. Lowercase alpha (a-z) and dashes (-) only please!
	var $plugin_name = 'correios';
  
	//public name of your method, for lists and such.
	public $public_name = '';

	//set to true if you need to use the shipping_metabox() method to add per-product shipping options
	public $use_metabox = true;

	//set to true if you want to add per-product extra shipping cost field
	public $use_extra = true;

	//set to true if you want to add per-product weight shipping field
	public $use_weight = true;

	//Production Live URI for CORREIOS Rates API
	public $correios_uri = 'http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?nCdEmpresa=&sDsSenha=&sCepOrigem=';
		
	// Variaveis Correios - INI
	public $services = array();
	// COMPRIMENTO LARGURA ALTURA
	public $measure = array();
	public $weight;
	private $settings = '';
	private $correios_settings;
    // Variaveis Correios - FIM
	
	//Set to display any errors in the Rate calculations.
	private $rate_error = '';
	
  /**
   * Runs when your class is instantiated. Use to setup your plugin instead of __construct()
   */
  function on_creation() {
    //declare here for translation
    $this->public_name = __('Correios', 'mp');
	
	//Correios Nacional
	$this->services = array(
		'PAC'			=> new Correio_Service('41106',	__('PAC', 'mp')),
		'SEDEX'			=> new Correio_Service('40010',	__('SEDEX', 'mp')),
		'SEDEX A COBRAR'	=> new Correio_Service('40045',	__('SEDEX a Cobrar', 'mp')),
		'SEDEX HOJE'		=> new Correio_Service('40290', __('SEDEX Hoje','mp')),
		'SEDEX 10'		=> new Correio_Service('40215',	__('SEDEX 10', 'mp')),
		'SEM FRETE'		=> new Correio_Service('0', __('Sem Frete','mp'))
	);
	
	// COMPRIMENTO LARGURA ALTURA
	$this->measure = array(
		'length'	=> 16,
		'width'		=> 12,
		'height'	=> 4	
	);
	
	$this->weight = 0;
	
	// Get settings for convenience sake
	$this->settings = get_option('mp_settings');
	$this->correios_settings = $this->settings['shipping']['correios'];
  }

  /**
   * Echo anything you want to add to the top of the shipping screen
   */
	function before_shipping_form($content) {
		return $content;
  }
  
  /**
   * Echo anything you want to add to the bottom of the shipping screen
   */
	function after_shipping_form($content) {
		return $content;
  }
  
  /**
   * Echo a table row with any extra shipping fields you need to add to the shipping checkout form
   */
	function extra_shipping_field($content) {
		return $content;
  }
  
  /**
   * Use this to process any additional field you may add. Use the $_POST global,
   *  and be sure to save it to both the cookie and usermeta if logged in.
   */
	function process_shipping_form() {

  }
	
	/**
   * Echo a settings meta box with whatever settings you need for you shipping module.
   *  Form field names should be prefixed with mp[shipping][plugin_name], like "mp[shipping][plugin_name][mysetting]".
   *  You can access saved settings via $settings array.
   */
	function shipping_settings_box($settings) {
		global $mp;
		
		$this->settings = $settings;
		$this->correios_settings = $this->settings['shipping']['correios'];
		$system = $this->settings['shipping']['system']; //Current Unit settings english | metric
	?>
	
	 <div id="mp_correios" class="postbox">
	 
	 <table class="form-table">
	 	<tr>
			<th scope="row"><?php _e('Domestic Services of CORREIOS', 'mp') ?></th>
			<td>
				<?php foreach($this->services as $name => $service) : ?>
					<label>
						<input type="checkbox" name="mp[shipping][correios][services][<?php echo $name; ?>]" value="1" <?php checked($this->correios_settings['services'][$name]); ?> />&nbsp;<?php echo $service->name; ?>
					</label><br />
				<?php endforeach; ?>
			</td>
        </tr>
	 	<tr>
			<th scope="row"><?php _e('Base ZIP code', 'mp') ?></th>
			<td>
				<input type="text" name="mp[shipping][correios][cep]" value="<?php echo esc_attr($this->correios_settings['cep']); ?>" size="10" maxlength="10" />
			</td>
        </tr>
	 </table>
	 
	 </div>
	
	<?php	
		
  }
  
  /**
   * Filters posted data from your form. Do anything you need to the $settings['shipping']['plugin_name']
   *  array. Don't forget to return!
   */
	function process_shipping_settings($settings) {

    return $settings;
  }
  
  /**
   * Echo any per-product shipping fields you need to add to the product edit screen shipping metabox
   *
   * @param array $shipping_meta, the contents of the post meta. Use to retrieve any previously saved product meta
   * @param array $settings, access saved settings via $settings array.
   */
	function shipping_metabox($shipping_meta, $settings) {	

  }

  /**
   * Save any per-product shipping fields from the shipping metabox using update_post_meta
   *
   * @param array $shipping_meta, save anything from the $_POST global
   * return array $shipping_meta
   */
	function save_shipping_metabox($shipping_meta) {
	
    return $shipping_meta;
  }
  
  /**
		* Use this function to return your calculated price as an integer or float
		*
		* @param int $price, always 0. Modify this and return
		* @param float $total, cart total after any coupons and before tax
		* @param array $cart, the contents of the shopping cart for advanced calculations
		* @param string $address1
		* @param string $address2
		* @param string $city
		* @param string $state, state/province/region
		* @param string $zip, postal code
		* @param string $country, ISO 3166-1 alpha-2 country code
		* @param string $selected_option, if a calculated shipping module, passes the currently selected sub shipping option if set
		*
		* return float $price
		*/
	function calculate_shipping($price, $total, $cart, $address1, $address2, $city, $state, $zip, $country, $selected_option) {
		global $mp;

		if(! $this->crc_ok())
		{
			//Price added to this object
			$this->shipping_options($cart, $address1, $address2, $city, $state, $zip, $country);
		}

		$price = floatval($_SESSION['mp_shipping_info']['shipping_cost']);
		return $price;
	}

	/**
	* For calculated shipping modules, use this method to return an associative array of the sub-options. The key will be what's saved as selected
	*  in the session. Note the shipping parameters won't always be set. If they are, add the prices to the labels for each option.
	*
	* @param array $cart, the contents of the shopping cart for advanced calculations
	* @param string $address1
	* @param string $address2
	* @param string $city
	* @param string $state, state/province/region
	* @param string $zip, postal code
	* @param string $country, ISO 3166-1 alpha-2 country code
	*
	* return array $shipping_options
	*/
	function shipping_options($cart, $address1, $address2, $city, $state, $zip, $country) {	
		$shipping_options = array();

		$this->address1 = $address1;
		$this->address2 = $address2;
		$this->city = $city;
		$this->state = $state;
		$this->destination_zip = preg_replace('([^0-9])', '', $zip);
		$this->country = $country;
		
		if( is_array($cart) ) {
			$shipping_meta['weight'] = (is_numeric($shipping_meta['weight']) ) ? $shipping_meta['weight'] : 0;
			foreach ($cart as $product_id => $variations) {
				$shipping_meta = get_post_meta($product_id, 'mp_shipping', true);
				foreach($variations as $variation => $product) {
					$qty = $product['quantity'];
					if(empty($product['mp_file'])){
						if(empty($shipping_meta['weight'])) {
							$this->weight += 1 * $qty;
						}else{
							$this->weight += floatval($shipping_meta['weight']) * $qty;
						}
					}
				}
			}
		}
		
		// Got our totals  make sure we're in decimal pounds.
		//$this->weight = $this->as_pounds($this->weight);

		if ($this->weight == 0) :
			$shipping_options['Sem Frete'] = $this->format_shipping_option('Sem Frete', 0.00);
			return $shipping_options;
		ENDIF;

		$shipping_options = $this->rate_request();

		return $shipping_options;
  }
  
	/**
	* rate_request - Makes the actual call to correios
	*/
	function rate_request() {
		global $mp;

		$shipping_options = $this->correios_settings['services'];
		//Clear any old price
		unset($_SESSION['mp_shipping_info']['shipping_cost']);
		
		if(! is_array($shipping_options)) $shipping_options = array();
		$mp_shipping_options = $shipping_options;
		foreach($shipping_options as $service => $options){
			$ws_correios = $this->correios_uri.$this->correios_settings['cep']."&sCepDestino=".$this->destination_zip."&nVlPeso=".$this->weight."&nCdFormato=1&nVlComprimento=".$this->measure['length']."&nVlAltura=".$this->measure['height']."&nVlLargura=".$this->measure['width']."&sCdMaoPropria=n&nVlValorDeclarado=0&sCdAvisoRecebimento=n&nCdServico=".$this->services[$service]->code."&nVlDiametro=0&StrRetorno=xml";
			$xml = simplexml_load_file($ws_correios);
			if($xml->cServico->Erro == '0'){
				$total = str_replace(",", ".", $xml->cServico->Valor);
				$delivery = (int)$xml->cServico->PrazoEntrega;
				$handling = str_replace(",", ".", $xml->cServico->ValorMaoPropria);
				$mp_shipping_options[$service] = array('rate' => $total, 'delivery' => $delivery, 'handling' => $handling);
				//match it up if there is already a selection
				if (! empty($_SESSION['mp_shipping_info']['shipping_sub_option'])){
					if ($_SESSION['mp_shipping_info']['shipping_sub_option'] == $service){
						$_SESSION['mp_shipping_info']['shipping_cost'] = $total;
					}
				}
			} else {
				unset($mp_shipping_options[$service]);
			}
		}
		uasort($mp_shipping_options, array($this,'compare_rates') );
		
		$shipping_options = array();
		foreach($mp_shipping_options as $service => $options){
			$shipping_options[$service] = (string)$this->format_shipping_option($service, $options['rate'], $delivery);
		}
		
		//Update the session. Save the currently calculated CRCs
		$_SESSION['mp_shipping_options'] = $mp_shipping_options;
		$_SESSION['mp_cart_crc'] = $this->crc($mp->get_cart_cookie());
		$_SESSION['mp_shipping_crc'] = $this->crc($_SESSION['mp_shipping_info']);

		return $shipping_options;
  }
  
	/**
		* For calculated shipping modules, use this method to return an associative array of the sub-options. The key will be what's saved as selected
		*  in the session. Note the shipping parameters won't always be set. If they are, add the prices to the labels for each option.
		*
		* @param array $cart, the contents of the shopping cart for advanced calculations
		* @param string $address1
		* @param string $address2
		* @param string $city
		* @param string $state, state/province/region
		* @param string $zip, postal code
		* @param string $country, ISO 3166-1 alpha-2 country code
		*
		* return array $shipping_options 
		*/
	/*function shipping_options($cart, $address1, $address2, $city, $state, $zip, $country) {
		
		$shipping_options = array();
		
		return $shipping_options;
	}*/

	/**For uasort below
	*/
	function compare_rates($a, $b){
		if($a['rate'] == $b['rate']) return 0;
		return ($a['rate'] < $b['rate']) ? -1 : 1;
	}
	
	/**Used to detect changes in shopping cart between calculations
	* @param (mixed) $item to calculate CRC of
	*
	* @return CRC32 of the serialized item
	*/
	public function crc($item = ''){
		return crc32(serialize($item));
	}

	/**
	* Tests the $_SESSION cart cookie and mp_shipping_info to see if the data changed since last calculated
	* Returns true if the either the crc for cart or shipping info has changed
	*
	* @return boolean true | false
	*/
	private function crc_ok(){
		global $mp;

		//Assume it changed
		$result = false;

		//Check the shipping options to see if we already have a valid shipping price
		if(isset($_SESSION['mp_shipping_options'])){
			//We have a set of prices. Are they still valid?
			//Did the cart change since last calculation
			if ( is_numeric($_SESSION['mp_shipping_info']['shipping_cost'])){

				if($_SESSION['mp_cart_crc'] == $this->crc($mp->get_cart_cookie())){
					//Did the shipping info change
					if($_SESSION['mp_shipping_crc'] == $this->crc($_SESSION['mp_shipping_info'])){
						$result = true;
					}
				}
			}
		}
		return $result;
	}

	// Conversion Helpers

	/**
	* Formats a choice for the Shipping options dropdown
	* @param array $shipping_option, a $this->services key
	* @param float $price, the price to display
	*
	* @return string, Formatted string with shipping method name delivery time and price
	*
	*/
	private function format_shipping_option($shipping_option = '', $price = '', $delivery = '', $handling=''){
		global $mp;
		if ( isset($this->services[$shipping_option])){
			$option = $this->services[$shipping_option]->name;
		}

		$price = is_numeric($price) ? $price : 0;
		$handling = is_numeric($handling) ? $handling : 0;

		$option .=  sprintf(__(' - Delivery %1$s day(s) - %2$s ', 'mp'), $delivery, $mp->format_currency('', $price + $handling) );
		return $option;
	}

	/**
	* Returns an inch measurement depending on the current setting of [shipping] [system]
	* @param float $units
	*
	* @return float, Converted to the current units_used
	*/
	private function as_inches($units){
		$units = ($this->settings['shipping']['system'] == 'metric') ? floatval($units) / 2.54 : floatval($units);
		return round($units,2);
	}

	/**
	* Returns a pounds measurement depending on the current setting of [shipping] [system]
	* @param float $units
	*
	* @return float, Converted to pounds
	*/
	private function as_pounds($units){
		$units = ($this->settings['shipping']['system'] == 'metric') ? floatval($units) * 2.2 : floatval($units);
		return round($units, 2);
	}
}

if(! class_exists('Correio_Service') ):
class Correio_Service
{
	public $code;
	public $name;

	function __construct($code, $name)
	{
		$this->code = $code;
		$this->name = $name;
	}
}
endif;

//register plugin - uncomment to register
mp_register_shipping_plugin('MP_Shipping_Correios', 'correios', __('Correios', 'mp'), true);
?>