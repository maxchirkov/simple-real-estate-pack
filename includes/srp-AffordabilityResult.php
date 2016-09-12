<?php
$output = false;

if($_GET['type'] == 'affordability'){

	// Get Posted Values
	$mo_gross_income		= $_GET['mo_gross_income'];
	$mo_debt_expences		= $_GET['mo_debt_expences'];
	$down_payment   		= $_GET['down_payment'];
	$annual_interest_rate 	= $_GET['interest_rate'];

	$front_end_ratio_payment	= $mo_gross_income * 0.28;
	$funds_available			= $mo_gross_income*0.36 - $mo_debt_expences;
	$percentage_available		= number_format(($funds_available*100/$mo_gross_income), 2);
	$back_end_ratio_payment		= $mo_gross_income * 0.36;


	$home_insurance = 0.5; //0.5%
	$property_tax	= 1; //1%
	$pmi			= 0;

	//finding out which is smalle $back_end_ratio_payment or $funds_available
	if($front_end_ratio_payment < $funds_available){
		$smaller = $front_end_ratio_payment;
	}else{
		$smaller = $funds_available;
	}

	$monthly_interest_rate = $annual_interest_rate/100/12;
	$month_term = 360;
	$power = -($month_term);
    $denom = pow((1 + $monthly_interest_rate), $power);
    $a = $monthly_interest_rate / (1 - $denom);
	$b = ($home_insurance + $property_tax)/100 / 12;
	$principal = ($smaller / ($a + $b)) + $down_payment;
	if($down_payment > 0){
		$principal = ($smaller - $down_payment*$b) / ($a + $b);
	}
	//Home insurance, Tax and PMI + Principal = 100%, then $principal_ = X%+(Hi+Tx+PMI)% = 100%
	$dp_percent = $down_payment * 100 / ($principal + $down_payment);
	$deductions = 'Less: taxes and insurance*';
	if($dp_percent < 20){
		$pmi = 0.5;
		$x = $home_insurance + $property_tax + $pmi;
		$b = $x/100/12;
		$principal = ($smaller - $down_payment*$b) / ($a + $b);
		$deductions = 'Less: taxes, insurance and PMI<sup>[2]</sup>';
	}
	$total_amount = $principal + $down_payment;

	$monthly_tax_insurance = $total_amount * ($home_insurance + $property_tax + $pmi)/100/12;

	$deductions_ = '<hr />
					<div><small>
						<sup>[1]</sup> Calculations are based on the following estimate values:
						<ul>
							<li>Annual Property Tax - '.$property_tax.'% [$'. number_format($property_tax/100*$total_amount) .'/yr. or $'. number_format($property_tax/100*$total_amount/12) .'/mo]</li>
							<li>Annual Home Insurance - '.$home_insurance.'% [$'. number_format($home_insurance/100*$total_amount) .'/yr. or $'. number_format($home_insurance/100*$total_amount/12) .']</li>';
							if($pmi > 0){ $deductions_ .= '<li>Premium Mortgage Insurance - '.$pmi.'% [$'. number_format($pmi/100*$total_amount) .'/yr. or $'. number_format($pmi/100*$total_amount/12) .'/mo.]</li>'; }
		$deductions_ .= '</ul>	';
						if($pmi > 0){ $deductions_ .= '<sup>[2]</sup> PMI (Premium Mortgage Insurance) only being calculated when the down payment is less than 20% of the price of the property.'; }
$deductions_ .= '</small></div>';

	if($down_payment > 0){
		$dp = 'your total mortgage amount equals $'.number_format($principal).', plus your downpayment $'.number_format($down_payment) .' brings your home affordability up to $'.number_format($principal+$down_payment).'.';
	}else{
		$dp = 'your total mortgage amount (and home affordability) equals $'.number_format($principal).'.';
	}

	$table = array(
		'Front-End Ratio (28%)'	=> array(
			'Monthly gross income'						=> '$'.number_format($mo_gross_income),
			'Front-End Ratio'							=> '28%',
			'Calculated payment for front-end ratio' 	=> '$'.number_format($front_end_ratio_payment),
			'Explanation'								=> 'Your total monthly housing allowance should not ecceed 28% of your gross income, or $'.number_format($front_end_ratio_payment) . ' per month.',
		),
		'Back-End Ratio (36%)'	=> array(
			'Debt and obligations'						=> '$'.number_format($mo_debt_expences),
			'Percent of gross income'					=> number_format(($mo_debt_expences/$mo_gross_income*100), 2) . '%',
			'Maximum percentage available for mortgage payment'	=> $percentage_available . '%',
			'Calculated payment for back-end ratio'		=> '$'.number_format($funds_available),
			'Explanation'								=> '36% of your total income is '. '$'.number_format($back_end_ratio_payment) .', minus your monthly debt ('. '$'.number_format($mo_debt_expences) .'), equals '. '$'.number_format($funds_available) .' for your housing allowance, including insurance and taxes.',
		),
		'Payment Calculation'	=> array(
			'Smaller of the two ratio options'			=> '$'.number_format($smaller),
			$deductions									=> '$'.number_format($monthly_tax_insurance).'<sup>[1]</sup>',
			'Equals: maximum allowable payment'			=> '$'.number_format($smaller - $monthly_tax_insurance),
			'Calculated mortgage amount'				=> '$'.number_format($principal),
			'Down payment'								=> '$'.number_format($down_payment), //Get value
			'Explanation'								=> 'Smaller amount of the two ratios ('.'$'.number_format($smaller).') minus property tax & home insurance ($'.number_format($monthly_tax_insurance).') comes to $'.number_format($smaller - $monthly_tax_insurance) .' funds available for principal and interest. Based on this, '. $dp,
		),
		'Home value you should be able to afford'	=> '$'.number_format($principal+$down_payment),

	);

	$tr = '';
    $i = 0;
	foreach($table as $k => $row){
		if(is_array($row)){
			$tr .=  '<tr class="srp_subtitle">
						<td colspan="2">'.$k.'</td>
					</tr>';
			foreach($row as $name => $value){
				if($name == 'Explanation'){
					$tr .= '<tr>
							<td colspan="2"><div class="srp_additional-info"><small><strong>'.$name.':</strong> '.$value.'</small></div></td>
						</tr>';
				}else{
					$tr .= '<tr>
							<td>'.$name.':</td>
							<td>'.$value.'</td>
						</tr>';
				}
			}
		}else{
			$tr .=  '<tr class="srp_subtitle">
						<td>'.$k.':</td>
						<td>'.$table[$k].'</td>
					</tr>';
		}

		$i++;
	}
	$output .= '<h3>Here is what you should be able to afford based on the data you provided:</h3>';
	$output .= '<p>Usually lenders will cap monthly housing allowance (including taxes and insurance) by lesser of the two ratios: 28% (from your gross income) and 36% (from your gross income including other monthly debt and payment obligations).<p>';
	$output .= '<table class="srp_result_table">'.$tr.'</table>';
	$output .= $deductions_;
	$output .= '<p><small>Disclaimer: the calculations are estimated and can only be used as guidance. For specific information contact your real estate agent or lending company.</small></p>';
}

function get_interest_factor($year_term, $monthly_interest_rate) {
        global $base_rate;

        $factor      = 0;
        $base_rate   = 1 + $monthly_interest_rate;
        $denominator = $base_rate;
        for ($i=0; $i < ($year_term * 12); $i++) {
            $factor += (1 / $denominator);
            $denominator *= $base_rate;
        }
        return $factor;
}

print $output;