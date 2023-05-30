<?php

function loancalculator(){
	ob_start();
?>
	<div class="container multi-field-wrapper">
		<div class="row">
			<div class="col-12">
				<button type="button" class="add-field cbtn sp">Split your Loan</button>
			</div>
		</div>

		<div class="row multi-fields">

			<div class="col multi-field v-border">
				<button type="button" class="remove-field cbtn sp">x</button>

				<form id="loan-form" action="" method="POST">

					<div class="field d-flex">
						<label class="m-0 lable">Interest Only</label>
						<input type="checkbox" name="loan_loan_type" id="loan-loan-type" class="loan-loan-type required value" value="" style="height: 25px; width: 25px;">
					</div>

					<div class="field">
						<label class="m-0 lable">Loan Amount $</label>
						<input type="text" name="loan_total_amount_label" id="loan-total-amount" class="loan-total-amount required value" placeholder="150,000" value="150,000">
					</div>

					<div class="field">
						<label class="m-0 lable">Interest Rate</label>
						<input type="text" name="loan_interest_rate_label" id="loan-interest-rate" class="loan-interest-rate required value" placeholder="4.60" value="4.60">
						<span class="parentage sign">%</span>
					</div>

					<div class="field">
							<label class="m-0 lable">Time To Repay</label>
							<label id="years-box" style="float: right;position: absolute;right: 0px;top: 30px;width: auto !important;margin: 0px;"><span id="years">1</span> <span id="years-t" class="sign">Year</span></label>
							<input type="range" name="loan_years_number_label" id="loan-number-years" class="loan-number-years value" value="15" min="1" max="30" step="1">						
					</div>

					<div class="field">
						<label class="m-0 lable">Repayment Frequency</label>
						<select name="loan_repayment_frequency_label" id="loan-repayment-frequency" class="loan-repayment-frequency value">
							<option value="52"> Weekly </option>
							<option value="26"> Fortnightly </option>
							<option value="12"> Monthly </option>
						</select>
					</div>

					<div class="field" style="text-align: center; display:none;">
						<input type="submit" class="cbtn sp m-auto loan-submit" id="loan-submit" value="Calculate">
					</div>

					<div id="loan-output" class="d-none">
					</div>

				</form>
			</div>

		</div>
		
		<div class="row">
			<div class="col-12">
				<div class="field text-center">
					<input type="button" class="cbtn sp m-auto" id="click-on-all" value="Calculate">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col col0 v-border d-none">
			</div>
			<div class="col col1 v-border d-none">
			</div>
			<div class="col col2 v-border d-none">
			</div>
			<div class="col col3 v-border d-none">
			</div>
		</div>
		
		<div class="row">				
				<form name="calculation-email" class="calculation-email" method="post">
					<div class="field text-center email-results d-none d-block d-flex">
						<input type="text" name="name" value="" class="form-control form-control-lg text-dark rounded-pill px-4 py-2 name" placeholder="Full Name" required>
						<input type="email" name="email" value="" class="form-control form-control-lg text-dark rounded-pill px-4 py-2 email" placeholder="Email" required>
						<input type="submit" name="email-results" value="Email Results" class="cbtn email form-control form-control-lg text-dark rounded-pill p-2 w-25 loader">
					</div>
				</form>
				<div class="field text-center email-results d-none d-block">
					<a href="/contact/" class="cbtn">Talk To Us</a>
				</div>
				<p class="text-white text-center"><strong>Note:</strong> This payment amount is an estimate only and final figures will be confirmed by the lender in your loan documentation</p>
		</div>
		
	</div>
<style>
.form-control::placeholder {
	color:  #3795d2 !important;
	opacity: 1 !important;
}
.loading {
    background: white url("https://i.gifer.com/ZZ5H.gif") !important;
    background-size: 28px 28px !important;
    background-position: calc(100% - 10px) center !important;
    background-repeat: no-repeat !important;
    color: white !important;
}
</style>
<?php
	return ob_get_clean();
}
	add_shortcode( 'LOANCALCULATOR', 'loancalculator' );
?>
<?php
add_filter( 'wp_nav_menu', 'do_shortcode' );
add_filter( 'nav_menu_link_attributes', 'sc_login', 10, 4 ); 
add_filter( 'nav_menu_link_attributes', 'sc_signup', 10, 4 ); 
// Add shortcode function on "init"
add_shortcode( 'LOGIN', 'sc_login' );
add_shortcode( 'SIGNUP', 'sc_signup' );

function sc_login($atts, $content = "") 
{
	if ( false !== strpos( $atts[ 'href' ], '[LOGIN]' ) ) 
	{
		// Simply overwrite the url with a set value
		$atts[ 'href' ] = get_option('login');
	}
	return $atts;
}
function sc_signup($atts, $content = "") 
{
	if ( false !== strpos( @$atts[ 'href' ], '[SIGNUP]' ) ) 
	{
		// Simply overwrite the url with a set value
		$atts[ 'href' ] = get_option('signup');
	}
	return $atts;
}