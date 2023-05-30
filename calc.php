<?php
/**
 * Plugin Name: Calculator
 * Decalcription: Adds a custom admin pages with sample styles and calcripts.
 * Plugin URI: https://kiwiwebsitedesign.nz/
 * Author: Inovedia Teams
 * Author URI: https://kiwiwebsitedesign.nz
 * Version: 1.0.0
 * License: GPL2
 * Text Domain: text-domain
 * Domain Path: domain/path
 */

/*
    Copyright (C) Year  Author  Email

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('CALC') ) :

/**
 * CALC
 */
class CALC
{

	/** @var string The plugin version number. */
	var $version = '1.0.0';
	
	/** @var array The plugin settings array. */
	var $settings = array();
	
	/** @var array The plugin data array. */
	var $data = array();
	
	/** @var array Storage for class instances. */
	var $instances = array();
	
	/**
	 * __construct
	 *
	 * A dummy constructor to ensure CALC is only setup once.
	 *
	 * @date	09/03/2023
	 * @since	1.0.0
	 *
	 * @param	void
	 * @return	void
	 */	
	function __construct() {
		// Do nothing.
	}
	
	/**
	 * initialize
	 *
	 * Sets up the CALC plugin.
	 *
	 * @date	09/03/2023
	 * @since	1.0.0
	 *
	 * @param	void
	 * @return	void
	 */
	function initialize() {
		
		// Define constants.
		$this->define( 'CALC', true );
		$this->define( 'CALC_DOMAIN', 'system-configuration' );
		$this->define( 'CALC_ICON', plugins_url('assets/images/calc.png', __FILE__ ) );
		$this->define( 'CALC_PATH', plugin_dir_path( __FILE__ ) );
		$this->define( 'CALC_BASENAME', plugin_basename( __FILE__ ) );
		$this->define( 'CALC_VERSION', $this->version );
		$this->define( 'CALC_FILE_MANAGER_PATH', plugin_dir_path(__FILE__));

		// Include utility functions.
		include_once( CALC_PATH . 'shortcode.php');
		include_once( CALC_PATH . 'widget.php');

		add_action( 'admin_menu', array($this, 'calc_admin_menu'), 5 );
		add_action( 'admin_enqueue_scripts', array($this, 'calc_admin_things'), 10 );

		add_action( 'wp_enqueue_scripts', array($this, 'calc_wp_things'), 10 );

		add_action( 'wp_ajax_nopriv_loan_calculation', array($this, 'loan_calculation') );
		add_action( 'wp_ajax_loan_calculation', array($this, 'loan_calculation') );

		add_action( 'wp_ajax_nopriv_loan_email_send', array($this, 'loan_email_send') );
		add_action( 'wp_ajax_loan_email_send', array($this, 'loan_email_send') );

		// Register Theme opations fields
		register_setting('calc_replace_urls_misc', 'calc_replace_urls_misc');

		register_setting( 'calc-system-configuration-group', 'home' );
		register_setting( 'calc-system-configuration-group', 'siteurl' );
		register_setting( 'calc-system-configuration-group', 'api_url' );
		register_setting( 'calc-system-configuration-group', 'login' );
		register_setting( 'calc-system-configuration-group', 'signup' );
	}

	/* Admin Things */
	function calc_admin_things()
	{
		wp_enqueue_script( 'calc-widget-script', plugins_url('admin/js/widget-script.js', __FILE__), array('jquery'), 1.0, true );
		wp_enqueue_style ( 'calc-styles', plugins_url('admin/css/styles.css', __FILE__ ));
	}

	/* WP Things */
	function calc_wp_things()
	{
		/**
		 * frontend ajax requests.
		 */
		wp_enqueue_script( 'calc-validate',	plugins_url('assets/js/jquery.validate.min.js', __FILE__), array('jquery'), 1.0, true );		
		wp_enqueue_script( 'calc-script',	plugins_url('assets/js/script.js', __FILE__), array('jquery'), 1.0, true );
		wp_enqueue_style ( 'calc-styles', 	plugins_url('assets/css/styles.css', __FILE__ ));

		wp_localize_script( 'calc-script', 'calc_ajax_object',
			array( 
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'data_var_1' => 'value 1',
				'data_var_2' => 'value 2',
			)
		);
		
		wp_localize_script( 'calc-script', 'calc_loan_email_send',
			array( 
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'data_var_1' => 'value 1',
				'data_var_2' => 'value 2',
			)
		);
		
	}

	/**
	 * calc_admin_menu
	 *
	 * This function responsible to add menus and pages at sidebar.
	 *
	 * @date	09/03/2023
	 * @since	1.0.0
	 *
	 * @param	void
	 * @return	void
	 */

	function calc_admin_menu() 
	{
		if (is_admin())
		{	// admin actions
			add_menu_page('Setting', 'Setting', 'manage_options', 'calc-setting', array($this, 'calc_admin_page_contents'), CALC_ICON, 3);
			//add_menu_page    ( $page_title, $menu_title, $capability, $menu_slug, $function = '', $icon_url = '', $position = null );
			//add_submenu_page('system-configuration', 'Configuration', 'Configuration', 'manage_options', 'configuration', array($this, 'calc_admin_page_contents'));
			add_submenu_page('calc-setting', 'Replace URLs', 'Replace URLs', 'manage_options', 'replace-urls', array($this, 'calc_admin_page_replace_urls'));
		}
		else
		{
			// non-admin enqueues, actions, and filters
		}
	}

	function calc_admin_page_contents() 
	{
		include_once( CALC_PATH . 'form.php');
	}
	function calc_admin_page_replace_urls() 
	{
		include_once( CALC_PATH . 'replace-urls.php');
	}

	function loan_calculation() {
		extract($_POST);
		$loanType			 = preg_replace('/[^a-zA-]/', '', $loan_loan_type); // loan type
		$principal			 = preg_replace('/[^0-9. ]/', '', $loan_total_amount_label); // loan amount
		$annualInterestRate  = preg_replace('/[^0-9. ]/', '', $loan_interest_rate_label); // 6% annual interest rate
		$repaymentFrequency  = preg_replace('/[^0-9]/', '',	  $loan_repayment_frequency_label); 
		$numberOfYears		 = preg_replace('/[^0-9]/', '',	  $loan_years_number_label); 
		
		
		if($loanType == 'interest-only'){
			$months = $numberOfYears * $repaymentFrequency; // loan term in months

			$term = $numberOfYears * $repaymentFrequency; // loan term

			$interestRate = $annualInterestRate / ($repaymentFrequency * 100);

			$payment = $principal * $interestRate;
			$intrestPayable = $payment * $months;
			$amountPayableWithIntrest = $principal + $intrestPayable;

			if($repaymentFrequency == '52'){
				$paymentType = 'Weekly';
			}
			elseif($repaymentFrequency == '26')
			{
				$paymentType = 'Fortnightly';
			}
			else 
			{
				$paymentType = 'Monthly';
			}
			
		} 
		else 
		{
			$months = $numberOfYears * $repaymentFrequency; // loan term in months

			$term = $numberOfYears * $repaymentFrequency; // loan term

			$interestRate = $annualInterestRate / ($repaymentFrequency * 100);

			$payment = $principal * ($interestRate * pow(1 + $interestRate, $term)) / (pow(1 + $interestRate, $term) - 1);

			$amountPayableWithIntrest = $payment * $months;

			if($repaymentFrequency == '52'){
				$paymentType = 'Weekly';
			}
			elseif($repaymentFrequency == '26')
			{
				$paymentType = 'Fortnightly';
			}
			else 
			{
				$paymentType = 'Monthly';
			}
		}
		?>
		<div class="row">
			<div class="col-lg-6"><label>Loan Amount:</label></div>
			<div class="col-lg-6 t-right">
				<h4>$<?php echo number_format($principal,0,".",","); ?></h4>
			</div>
		</div>
		<div class="row">			
			<div class="col-lg-6"><label>Total <?php echo $paymentType; ?> Payment:</label></div>
			<div class="col-lg-6 t-right"><h4>$<?php echo number_format($payment,0,".",","); ?></h4></div>
		</div>
		<div class="row">
			<div class="col-lg-6"><label>Total Balance Payable With Interest:</label></div>
			<div class="col-lg-6 t-right"><h4>$<?php echo number_format($amountPayableWithIntrest,0,".",","); ?></h4></div>
		</div>
		<?php
		wp_die();
	}
	
	function loan_email_send() {
		extract($_POST);
		$data = json_decode(json_encode($jsonData), true);
		// print_r($data);
		$html = "";
		foreach($data as $key => $value)
		{
			$html .= "<strong>Loan " . $key + 1 . "</strong><br>";
			foreach($value['input'] as $inputKey => $inputValue)
			{
				$html .= "<strong>" . str_replace("_"," ", $inputKey) . "</strong>:" . $inputValue;
				$html .= "<br>";
			}
			foreach($value['output'] as $outputKey => $outputValue)
			{
				$html .= "<strong>" . str_replace("_"," ", $outputKey) . "</strong>" . $outputValue;
				$html .= "<br>";
			}
				$html .= "<br> ================================================================================ <br>";			
		}
		$html .= "";		
		$admin_email = get_option('admin_email');
		$blogname = get_option('blogname');

		$to = 'umar.waqas@kiwiwebsitedesign.nz';
		$subject = 'Mortgage Calculator';
		$body = $html;
		$headers = array('Content-Type: text/html; charset=UTF-8','From: '.$blogname.' <'.$admin_email.'>');

		wp_mail( $email, $subject, $body, $headers );
		
		wp_die();
	}


	/**
	 * init
	 *
	 * Completes the setup process on "init" of earlier.
	 *
	 * @date	09/03/2023
	 * @since	1.0.0
	 *
	 * @param	void
	 * @return	void
	 */
	function init() {
				
	}

	/**
	 * define
	 *
	 * Defines a constant if doesnt already exist.
	 *
	 * @date	09/03/2023
	 * @since	1.0.0
	 *
	 * @param	string $name The constant name.
	 * @param	mixed $value The constant value.
	 * @return	void
	 */
	function define( $name, $value = true ) {
		if( !defined($name) ) 
		{
			define( $name, $value );
		}
	}

}

endif;

/*
 * CALC
 *
 * The main function responsible for returning the one true acf Instance to functions everywhere.
 * Use this function like you would a global variable, except without needing to declare the global.
 *
 * Example: <?php $calc = calc(); ?>
 *
 * @date	09/03/2023
 * @since	1.0.0
 *
 * @param	void
 * @return	CALC
 */
function calc() {
	global $calc;
	
	// Instantiate only once.
	if( !isset($calc) ) {
		$calc = new CALC();
		$calc->initialize();
	}
	return $calc;
}
// Instantiate.
calc();

