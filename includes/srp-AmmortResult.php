<?php
    
    /* --------------------------------------------------- *
     * Set Form DEFAULT values
     * --------------------------------------------------- */
    $default_sale_price              = "150000";
    $default_annual_interest_percent = 7.0;
    $default_year_term               = 30;
    $default_down_percent            = 10;
    $default_show_progress           = TRUE;
    /* --------------------------------------------------- */
    


    /* --------------------------------------------------- *
     * Initialize Variables
     * --------------------------------------------------- */
    $sale_price                      = 0;
    $annual_interest_percent         = 0;
    $year_term                       = 0;
    $down_percent                    = 0;
    $this_year_interest_paid         = 0;
    $this_year_principal_paid        = 0;
    $form_complete                   = false;
    $show_progress                   = false;
    $monthly_payment                 = false;
    $show_progress                   = false;
    $error                           = false;
    /* --------------------------------------------------- */


    /* --------------------------------------------------- *
     * Set the USER INPUT values
     * --------------------------------------------------- */
    if (isset($_REQUEST['form_complete'])) {
        $sale_price                      = $_REQUEST['sale_price'];
        $annual_interest_percent         = $_REQUEST['annual_interest_percent'];
        $year_term                       = $_REQUEST['year_term'];
        $down_percent                    = $_REQUEST['down_percent'];
        $show_progress                   = (isset($_REQUEST['show_progress'])) ? $_REQUEST['show_progress'] : false;
        $form_complete                   = $_REQUEST['form_complete'];
    }
    /* --------------------------------------------------- */
    
    
    // If HTML headers have not already been sent, we'll print some here    
    if (!headers_sent()) {
//        print("<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'><HTML>");
//        print("<head><title>Mortgage Calculator</title></HEAD><BODY>");
//        print("<body bgcolor='#ffffff'>");
//        print("<h2><span>Mortgage Amortization Schedule</span></h2>");
//        $print_footer = TRUE;
    } else {
       $print_footer = FALSE;
    }


    /* --------------------------------------------------- */
    // This function does the actual mortgage calculations
    // by plotting a PVIFA (Present Value Interest Factor of Annuity)
    // table...
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
    /* --------------------------------------------------- */

    // If the form is complete, we'll start the math
    if ($form_complete) {
        // We'll set all the numeric values to JUST
        // numbers - this will delete any dollars signs,
        // commas, spaces, and letters, without invalidating
        // the value of the number
        $sale_price              = preg_replace( "[^0-9.]", "", $sale_price);
        $annual_interest_percent = preg_replace("[^0-9.]", "", $annual_interest_percent);
        $year_term               = preg_replace("[^0-9.]", "", $year_term);
        $down_percent            = preg_replace("[^0-9.]", "", $down_percent);
        
        if (((float) $year_term <= 0) || ((float) $sale_price <= 0) || ((float) $annual_interest_percent <= 0)) {
            $error = "You must enter a <b>Sale Price of Home</b>, <b>Length of Motgage</b> <i>and</i> <b>Annual Interest Rate</b>";
        }
        
        if (!$error) {
            $month_term              = $year_term * 12;
            $down_payment            = $sale_price * ($down_percent / 100);
            $annual_interest_rate    = $annual_interest_percent / 100;
            $monthly_interest_rate   = $annual_interest_rate / 12;
            $financing_price         = $sale_price - $down_payment;
            $monthly_factor          = get_interest_factor($year_term, $monthly_interest_rate);
            $monthly_payment         = $financing_price / $monthly_factor;
        }
    } else {
        if (!$sale_price)              { $sale_price              = $default_sale_price;              }
        if (!$annual_interest_percent) { $annual_interest_percent = $default_annual_interest_percent; }
        if (!$year_term)               { $year_term               = $default_year_term;               }
        if (!$down_percent)            { $down_percent            = $default_down_percent;            }
        if (!$show_progress)           { $show_progress           = $default_show_progress;           }
    }
    
    if ($error) {
        print("<font color=\"red\">" . $error . "</font><br><br>\n");
        $form_complete   = false;
    }
	

                    $assessed_price          = ($sale_price * .85);
                    $residential_yearly_tax  = ($assessed_price / 1000) * 14;
                    $residential_monthly_tax = $residential_yearly_tax / 12;
                    
                    if (isset($pmi_per_month)) {
                        $pmi_text = "PMI and ";
                    }


    // This prints the calculation progress and 
    // the instructions of HOW everything is figured
    // out
    if ($form_complete && $show_progress) {
        $step = 1;

        // Set some base variables
        $principal     = $financing_price;
        $current_month = 1;
        $current_year  = 1;
        // This basically, re-figures out the monthly payment, again.
        $power = -($month_term);
        $denom = pow((1 + $monthly_interest_rate), $power);
        $monthly_payment = $principal * ($monthly_interest_rate / (1 - $denom));
        
        print("<div><a name=\"amortization\"></a>Amortization For Monthly Payment: <b>\$" . number_format($monthly_payment, "2", ".", ",") . "</b> over " . $year_term . " years</div>\n");
        print("<table cellpadding=\"5\" cellspacing=\"0\" width=\"100%\" id=\"amortization-table\">\n");
        
        // This LEGEND will get reprinted every 12 months
		$legend  = "\t<tr valign=\"top\" >\n";
        $legend .= "\t\t<td><b>Month</b></td>\n";
        $legend .= "\t\t<td><b>Interest Paid</b></td>\n";
        $legend .= "\t\t<td><b>Principal Paid</b></td>\n";
        $legend .= "\t\t<td><b>Remaing Balance</b></td>\n";
        $legend .= "\t</tr>\n";
        
        echo $legend;
                
        // Loop through and get the current month's payments for 
        // the length of the loan 
        while ($current_month <= $month_term) {        
            $interest_paid     = $principal * $monthly_interest_rate;
            $principal_paid    = $monthly_payment - $interest_paid;
            $remaining_balance = $principal - $principal_paid;
            
            $this_year_interest_paid  = $this_year_interest_paid + $interest_paid;
            $this_year_principal_paid = $this_year_principal_paid + $principal_paid;
            
			($current_month % 2) ? $class = 'class="tr_odd"' : $class = 'class="tr_even"';
            print("\t<tr valign=\"top\" " . $class . ">\n");
            print("\t\t<td>" . $current_month . "</td>\n");
            print("\t\t<td>\$" . number_format($interest_paid, "2", ".", ",") . "</td>\n");
            print("\t\t<td>\$" . number_format($principal_paid, "2", ".", ",") . "</td>\n");
            print("\t\t<td>\$" . number_format($remaining_balance, "2", ".", ",") . "</td>\n");
            print("\t</tr>\n");
    
            ($current_month % 12) ? $show_legend = FALSE : $show_legend = TRUE;
    
            if ($show_legend) {
                print("\t<tr valign=\"top\">\n");
                print("\t\t<td colspan=\"4\"><b>Totals for year " . $current_year . "</td>\n");
                print("\t</tr>\n");
                
                $total_spent_this_year = $this_year_interest_paid + $this_year_principal_paid;
                print("\t<tr valign=\"top\">\n");
                print("\t\t<td>&nbsp;</td>\n");
                print("\t\t<td colspan=\"3\">\n");
                print("\t\t\tYou will spend \$" . number_format($total_spent_this_year, "2", ".", ",") . " on your house in year " . $current_year . "<br>\n");
                print("\t\t\t\$" . number_format($this_year_interest_paid, "2", ".", ",") . " will go towards INTEREST<br>\n");
                print("\t\t\t\$" . number_format($this_year_principal_paid, "2", ".", ",") . " will go towards PRINCIPAL<br>\n");
                print("\t\t</td>\n");
                print("\t</tr>\n");
    
                print("\t<tr valign=\"top\" bgcolor=\"#ffffff\">\n");
                print("\t\t<td colspan=\"4\">&nbsp;<br><br></td>\n");
                print("\t</tr>\n");
                
                $current_year++;
                $this_year_interest_paid  = 0;
                $this_year_principal_paid = 0;
                
                if (($current_month + 6) < $month_term) {
                    echo $legend;
                }
            }
    
            $principal = $remaining_balance;
            $current_month++;
        }
        print("</table>\n");
    }
?>
<br>