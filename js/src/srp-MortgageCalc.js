/**
 * Document-Ready Events
 */
jQuery(document).ready(function(){
	jQuery('.srp-tabs').css({"display" : "block"});

	jQuery('input[id^="widget-srp_mortgagecalc-"]').live('keyup', function(){
		var id = jQuery(this).attr("id");
		var num = id.replace(/\D/g,"");
		var price_of_home	=	jQuery("input#widget-srp_mortgagecalc-" + num + "-price_of_home").asNumber();
		if(price_of_home > 0){
			srp_MortgageCalc_calculate(num);
		}
	});

	jQuery('input[id^="widget-srp_affordabilitycalc-"]').live('keyup', function(e){
		var id = jQuery(this).attr("id");
		var num = id.replace(/\D/g,"");
		srp_Affordability_calculate(num);
	});

	jQuery('input[id$="price_of_home"]').live('blur', function(){
    	jQuery(this).formatCurrency( {dropDecimals:true, symbol:''});
    });

	jQuery('a#srp_help').live('click', function(){
		jQuery("#srp_help_text > div.additional-info").css({"background" : "#F3F6FB", "border" : "1px solid #D2DFFF", "padding" : "5px"}).toggle('slow');
	});

	jQuery('input[id^="widget-srp_closingcosts-"]').live('keyup', function(e){
		var id = jQuery(this).attr("id");
		var num = id.replace(/\D/g,"");
		srp_ClosingCosts_calculate(num);
	});

	jQuery('<div id="srp-dialog"><div id="srp-dialog-content"></div></div>').appendTo('body');

	//BEGIN check pre-filled values
	srp_check_prefilled();
	//END check pre-filled values

});

/**
 * FUNCTIONS
 */

function srp_check_prefilled(){
    jQuery('input[id$="price_of_home"]').each(function(i) {
		if(jQuery(this).val() != ''){
			var id = jQuery(this).attr("id");
			var num = id.replace(/\D/g,"");
			srp_MortgageCalc_calculate(num);
		}
	});

	jQuery('input[id$="loan_amount"]').each(function(i) {
		if(jQuery(this).val() != ''){
			var id = jQuery(this).attr("id");
			var num = id.replace(/\D/g,"");
			srp_ClosingCosts_calculate(num);
		}
	});
}

function showClosingDetails(id){
	if(jQuery('#'+id).is(':hidden')){
		jQuery('#'+id).slideDown("slow");
	}else{
		jQuery('#'+id).slideUp("slow");
	}
}

function srp_removeThickBoxEvents() {
        jQuery('.thickbox').each(function(i) {
            jQuery(this).unbind('click');
        });
}

function srp_bindThickBoxEvents() {
        srp_removeThickBoxEvents();
        //tb_closeImage = tmp_tb_closeImage;
		//tb_pathToImage = tmp_tb_pathToImage;
		tb_init('a.thickbox, area.thickbox, input.thickbox');
}


function srp_MortgageCalc_calculate(num){
	jQuery("#srp_mortgagecalc-" + num + " div.additional-info").hide();

	var price_of_home	=	jQuery("input#widget-srp_mortgagecalc-" + num + "-price_of_home").asNumber();
	var down_payment	=	jQuery("input#widget-srp_mortgagecalc-" + num + "-down_payment").asNumber();
	var mortgage_term	=	jQuery("input#widget-srp_mortgagecalc-" + num + "-mortgage_term").asNumber();
	var interest_rate	=	jQuery("input#widget-srp_mortgagecalc-" + num + "-interest_rate").asNumber();

    if (down_payment == 100
        || down_payment > 100)

    {
        alert('Down Payment can not be equal of larger than the price of home.');
        jQuery("input#widget-srp_mortgagecalc-" + num + "-down_payment").val('');
        srp_MortgageCalc_calculate(num);
        return false;
    }


	jQuery("input#widget-srp_mortgagecalc-" + num + "-price_of_home").removeClass("highlight");
	if(!price_of_home){ jQuery("input#widget-srp_mortgagecalc-" + num + "-price_of_home").addClass("highlight"); var error = true; }
	if(!mortgage_term || mortgage_term == 0){ jQuery("input#widget-srp_mortgagecalc-" + num + "-mortgage_term").addClass("highlight"); var error = true; }
	if(!interest_rate || interest_rate == 0){ jQuery("input#widget-srp_mortgagecalc-" + num + "-interest_rate").addClass("highlight"); var error = true; }

	if(error){
		alert("Please fill out the highlighted fields.");
	}
	//alert(price_of_home.length);
	if(!error){
		var apr						= interest_rate/100;
		var down_payment_amount		= Math.round(price_of_home * down_payment / 100*100)/100;
		var monthly_interest_rate	= apr/12;
		var months_term				= mortgage_term * 12;
		var loan_amount				= Math.round((price_of_home - down_payment_amount)*100)/100;
		var b = 1+monthly_interest_rate;
		var c = months_term*(-1);
		var a = 1-(Math.pow(b, c));
		var monthly_payments = Math.round(loan_amount*(monthly_interest_rate/(a))*100)/100;
		var additional_charges = 0;
		var additional_charges_text	= '';

		// calculate tax, insurance and pmi
			var property_tax_rate	= jQuery("input#property_tax_rate").asNumber();
			var home_insurance_rate	= jQuery("input#home_insurance_rate").asNumber();
			var pmi					= jQuery("input#pmi").asNumber();
			//alert(extended);

			var monthly_tax			= Math.round(price_of_home*property_tax_rate/100/12*100)/100;
			var monthly_insurance	= Math.round(price_of_home*home_insurance_rate/100/12*100)/100;
			if(down_payment < 20){
				var monthly_pmi			= Math.round(price_of_home*pmi/100/12*100)/100;
			}else{
				var monthly_pmi = 0;
			}

			additional_charges = monthly_tax + monthly_insurance + monthly_pmi;
			additional_charges_text	=	"<div>Principal & Interest:		" + srp_cl(monthly_payments) + "</div>" +
											"<div>Mo. Tax:				" + srp_cl(monthly_tax) + "</div>" +
											"<div>Mo. Home Insurance:	" + srp_cl(monthly_insurance) + "</div>" +
											"<div>Mo. PMI:				" + srp_cl(monthly_pmi) + "</div>";

		var params = {
            form_complete: 1,
            sale_price: price_of_home,
            down_percent: down_payment,
            year_term: mortgage_term,
            annual_interest_percent: interest_rate,
            show_progress: 1
        };

		var ammortization = jQuery('<a href="#" title="Mortgage Amortization Schedule">Amortization Schedule</a>')
			.css('cursor', 'pointer')
			.data(params)
			.on('click', function(e)
			{
				e.preventDefault();
				e.stopPropagation();

				var data = {
					action: 'srp_getAmortizationSchedule',
					params: jQuery(this).data()
				};

				jQuery.post(srp.ajaxurl, data, function(response)
				{
					if (response)
					{
						jQuery('#srp-dialog-content').html(response);

						setTimeout(function(){
                            tb_show('Mortgage Amortization Schedule',
                                    '#TB_inline?&height=500&width=650&inlineId=srp-dialog-content',
                                    null);
						}, 50);
					}
				});
			});


		var additional_info	=	additional_charges_text +
								"<div>Down Payment:		" + srp_cl(down_payment_amount) + "</div>" +
								"<div class='srp_tb srp_bb'>Financed Amount:	" + srp_cl(loan_amount) + "</div>" +
								"<div class='srp_result_link'></div>";
		var additional_info_obj = jQuery(document.createDocumentFragment());
		additional_info_obj = additional_info_obj.append(additional_info);
        additional_info_obj.find('.srp_result_link').append(ammortization);

		jQuery("input#widget-srp_mortgagecalc-" + num + "-monthly_payment").val(Math.round((monthly_payments + additional_charges)*100)/100).formatCurrency( {symbol:''});
		jQuery("input#widget-srp_mortgagecalc-" + num + "-monthly_payment").addClass("total");
		jQuery("#widget-srp_mortgagecalc-" + num + "-result").html( additional_info_obj ).slideDown("slow").show();
		//srp_bindThickBoxEvents();
	}
}

function srp_Affordability_calculate(num){
	var mo_gross_income		= jQuery("input#widget-srp_affordabilitycalc-" + num + "-mo_gross_income").asNumber();
	var mo_debt_expences	= jQuery("input#widget-srp_affordabilitycalc-" + num + "-mo_debt_expences").asNumber();
	var down_payment		= jQuery("input#widget-srp_affordabilitycalc-" + num + "-down_payment").asNumber();
	var interest_rate		= jQuery("input#widget-srp_affordabilitycalc-" + num + "-interest_rate").asNumber();
	var property_tax		= jQuery("input#widget-srp_affordabilitycalc-" + num + "-property_tax").asNumber();
	var home_insurance		= jQuery("input#widget-srp_affordabilitycalc-" + num + "-home_insurance").asNumber();
	var pmi					= jQuery("input#widget-srp_affordabilitycalc-" + num + "-pmi").asNumber();

	var front_end_ratio_payment	= mo_gross_income * 0.28;
	var funds_available			= mo_gross_income*0.36 - mo_debt_expences;
	if(front_end_ratio_payment < funds_available){
		smaller = Math.round(front_end_ratio_payment);
	}else{
		smaller = Math.round(funds_available);
	}

	/*
	**Mortgage Calculation
	*/
	var monthly_interest_rate = interest_rate/100/12;
	var month_term = 360;
	var power = -(month_term);
    var denom = Math.pow((1 + monthly_interest_rate), power);
    var a = monthly_interest_rate / (1 - denom);
	var b = (home_insurance + property_tax + pmi)/100 / 12;
	var principal = (smaller / (a + b));

	if(down_payment > 0){
		principal = (smaller - down_payment*b) / (a + b);
	}

	var pmi_text = 'Tax, insurance & PMI';
	var dp_percent = down_payment * 100 / (principal + down_payment);
	if(dp_percent >= 20){
		//alert(dp_percent);
		pmi = 0;
		b = (home_insurance + property_tax + pmi)/100 / 12;
		principal = Math.round((smaller - down_payment*b) / (a + b));
		dp_percent = down_payment * 100 / (principal + down_payment);
		var pmi_text = 'Tax and insurance';
	}

	var total_amount =  Math.round(principal + down_payment);

	var deductions = Math.round((home_insurance + property_tax + pmi)* total_amount/100/12);

	var loan_text = '';
	if(down_payment > 0){
		var loan_text = '<div>Downpayment: ' + '<span class="srp_amnt">' + Math.round(dp_percent*100)/100 + '%</span></div><div>Loan Amount: ' + srp_cl( Math.round(principal)) + '</div>';
	}

	/*---------------------------------------*/

	// var query = "?type=affordability&mo_gross_income="+ mo_gross_income + "&mo_debt_expences=" + mo_debt_expences + "&down_payment=" + down_payment + '&interest_rate=' + interest_rate;
	// var thickbox = "&height=700&width=600";
	// var result_link = "<a href=\"" + srp.srp_inc + "/srp-AffordabilityResult.php" + query + thickbox + "\" class=\"thickbox\" title=\"Home Mortgage Affordability\">View Calculation Details</a>";

    var params = {
        type: 'affordability',
        mo_gross_income: mo_gross_income,
        mo_debt_expences: mo_debt_expences,
        down_payment: down_payment,
        interest_rate: interest_rate
    };

    var affordabilityLink = jQuery('<a href="#" title="Home Mortgage Affordability">View Calculation Details</a>')
        .css('cursor', 'pointer')
        .data(params)
        .on('click', function(e)
        {
            e.preventDefault();
            e.stopPropagation();

            var data = {
                action: 'srp_getAffordabilityDetails',
                params: jQuery(this).data()
            };

            jQuery.post(srp.ajaxurl, data, function(response)
            {
                if (response)
                {
                    jQuery('#srp-dialog-content').html(response);

                    setTimeout(function(){
                        tb_show('Home Mortgage Affordability',
                                '#TB_inline?&height=700&width=600&inlineId=srp-dialog-content',
                                null);
                    }, 50);
                }
            });
        });

	var calc = {
		shouldAfford: function()
		{
			if (total_amount < 1)
				return 0;

			return total_amount;
		},
		pmi: function()
		{
            if (total_amount < 1)
                return 0;

            return smaller - deductions;
		},
		deductions: function()
		{
            if (total_amount < 1)
                return 0;

            return deductions;
		},
		smaller: function()
		{
            if (total_amount < 1)
                return 0;

            return smaller;
		},
		link: function()
		{
            if (total_amount < 1)
                return '';

            return affordabilityLink;
		}
	}

	var html =	'<div class="srp_bb">You Should Afford: ' + srp_cl(calc.shouldAfford()) + '</div>' +
					loan_text +
					'<div>Principal & Interest: ' + srp_cl(calc.pmi()) + '</div>' +
					'<div>' + pmi_text + ': ' + srp_cl(calc.deductions()) + ' </div>' +
					'<div class="srp_tb srp_bb">Total Payments (mo): ' + srp_cl(calc.smaller()) + '</div>' +
					'<div class="srp_result_link"></div>';

    var fragment = jQuery(document.createDocumentFragment());
    var affordabilityInfo = fragment.append(html);
    affordabilityInfo.find('.srp_result_link').append(calc.link());

	if(mo_gross_income > 0 && mo_debt_expences >= 0 && interest_rate > 0){
		jQuery('#widget-srp_affordabilitycalc-' + num + '-result').html(affordabilityInfo).slideDown("slow").addClass("total");
		//srp_bindThickBoxEvents();
	}
}

function srp_ClosingCosts_calculate(num){
	var loan_amount					= jQuery("input#widget-srp_closingcosts-" + num + "-loan_amount").asNumber();
	var discount_points				= jQuery("input#widget-srp_closingcosts-" + num + "-discount_points").asNumber();
	var origination_fee				= jQuery("input#widget-srp_closingcosts-" + num + "-origination_fee").asNumber();
	var lender_fees					= jQuery("input#widget-srp_closingcosts-" + num + "-lender_fees").asNumber();
	var credit_report_fee			= jQuery("input#widget-srp_closingcosts-" + num + "-credit_report_fee").asNumber();
	var appraisal					= jQuery("input#widget-srp_closingcosts-" + num + "-appraisal").asNumber();
	var title_insurance				= jQuery("input#widget-srp_closingcosts-" + num + "-title_insurance").asNumber();
	var reconveyance_fee			= jQuery("input#widget-srp_closingcosts-" + num + "-reconveyance_fee").asNumber();
	var recording_fee				= jQuery("input#widget-srp_closingcosts-" + num + "-recording_fee").asNumber();
	var wire_courier_fee			= jQuery("input#widget-srp_closingcosts-" + num + "-wire_courier_fee").asNumber();
	var endorsement_fee				= jQuery("input#widget-srp_closingcosts-" + num + "-endorsement_fee").asNumber();
	var title_closing_fee			= jQuery("input#widget-srp_closingcosts-" + num + "-title_closing_fee").asNumber();
	var title_doc_prep_fee			= jQuery("input#widget-srp_closingcosts-" + num + "-title_doc_prep_fee").asNumber();
	var other_fees					= jQuery("input#widget-srp_closingcosts-" + num + "-other_fees").asNumber();

	if(discount_points > 0){
		var discount_points_amount = discount_points/100*loan_amount;
	}else{
		var discount_points_amount = 0;
	}
	if(origination_fee > 0){
		var origination_fee_amount = origination_fee/100*loan_amount;
	}else{
		var origination_fee_amount = 0;
	}

	var result = discount_points_amount + origination_fee_amount + lender_fees + credit_report_fee + appraisal + title_insurance + reconveyance_fee + recording_fee + wire_courier_fee + endorsement_fee + title_closing_fee + title_doc_prep_fee + other_fees;
	var result_text = '<strong>Total Closing Cost: ' + srp_cl(result) + '</strong>';

	if(loan_amount > 1000 && result > 0){
		jQuery("#widget-srp_closingcosts-" + num + "-result").html(result_text).slideDown("slow").addClass("total");
	}
	//srp_currency();
}

function srp_cl(nStr){
	nStr += '';
	x = nStr.split('.');
	x1 = x[0];
	x2 = x.length > 1 ? '.' + x[1] : '';
	var rgx = /(\d+)(\d{3})/;
	while (rgx.test(x1)) {
		x1 = x1.replace(rgx, '$1' + ',' + '$2');
	}
	return '<span class="srp_amnt">$' + (x1+x2) + '</span>';
}