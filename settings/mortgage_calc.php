<?php
function simpleMortgageCalc_options_page() {
    if(!$options = get_option('srp_mortgage_calc_options')){
        $default_options = _default_settings_MortgageCalc();
	add_option('srp_mortgage_calc_options', $default_options);
        $options = get_option('srp_mortgage_calc_options');
    }

 echo '<div class="wrap srp">';
  echo '<h2>Mortgage Calculators</h2>';
  srp_updated_message();
  ?>
<div class="postbox-container" style="width:70%;">
		<div class="metabox-holder">
			<div class="meta-box-sortables">
  <form method="post" action="options.php">
  <?php settings_fields('srp-mortgage-calc-options'); ?>
  <div class="postbox">
	<div class="handlediv" title="Click to toggle"><br /></div>
		<h3 class="hndle"><span>Mortgage Calc Estimate Default Values</span></h3>
			<div class="inside">
  <table class="form-table">
	<tr valign="bottom">
	  <th scope="row"><div align="right">Annual Interest Rate: </div></th>
	  <td><input name="srp_mortgage_calc_options[annual_interest_rate]" type="text" value="<?php echo $options['annual_interest_rate'];?>" size="10" />
	    %</td>
    </tr>
	<tr valign="bottom">
	  <th scope="row"><div align="right">Mortgage Term: </div></th>
	  <td><input name="srp_mortgage_calc_options[mortgage_term]" type="text" value="<?php echo $options['mortgage_term'];?>" size="10" />
	    years</td>
    </tr>
	<tr valign="bottom">
	  <th scope="row"><div align="right">Property Tax Rate: </div></th>
	  <td><input name="srp_mortgage_calc_options[property_tax_rate]" type="text" value="<?php echo $options['property_tax_rate'];?>" size="10" />
	    %</td>
    </tr>
	<tr valign="bottom">
	  <th scope="row"><div align="right">Home Insurance Rate: </div></th>
	  <td><input name="srp_mortgage_calc_options[home_insurance_rate]" type="text" value="<?php echo $options['home_insurance_rate'];?>" size="10" />
      %</td>
    </tr>
	<tr valign="bottom">
	  <th scope="row"><div align="right">Premium Mortgage Insurance (PMI): </div></th>
	  <td><input name="srp_mortgage_calc_options[pmi]" type="text" value="<?php echo $options['pmi'];?>" size="10" />
      %</td>
    </tr>
  </table>
  	</div>
  </div>
  <div class="postbox">
	<div class="handlediv" title="Click to toggle"><br /></div>
		<h3 class="hndle"><span>Closing Cost Estimate Default Values</span></h3>
			<div class="inside">
  <table class="form-table">
	<tr valign="bottom">
	  <th scope="row"><div align="right">Origination Fee: </div></th>
	  <td><input name="srp_mortgage_calc_options[origination_fee]" type="text" value="<?php echo $options['origination_fee'];?>" size="10" />
	    %</td>
    </tr>
	<tr valign="bottom">
	  <th scope="row"><div align="right">Lender Fees (processing/underwriting): </div></th>
	  <td><input name="srp_mortgage_calc_options[lender_fees]" type="text" value="<?php echo $options['lender_fees'];?>" size="10" />
	    </td>
    </tr>
	<tr valign="bottom">
	  <th scope="row"><div align="right">Credit Report Fee: </div></th>
	  <td><input name="srp_mortgage_calc_options[credit_report_fee]" type="text" value="<?php echo $options['credit_report_fee'];?>" size="10" />
      </td>
    </tr>
	<tr valign="bottom">
	  <th scope="row"><div align="right">Appraisal: </div></th>
	  <td><input name="srp_mortgage_calc_options[appraisal]" type="text" value="<?php echo $options['appraisal'];?>" size="10" />
      </td>
    </tr>
	<tr valign="bottom">
	  <th scope="row"><div align="right">Title Insurance: </div></th>
	  <td><input name="srp_mortgage_calc_options[title_insurance]" type="text" value="<?php echo $options['title_insurance'];?>" size="10" />
      </td>
    </tr>
	<tr valign="bottom">
	  <th scope="row"><div align="right">Reconveyance Fee: </div></th>
	  <td><input name="srp_mortgage_calc_options[reconveyance_fee]" type="text" value="<?php echo $options['reconveyance_fee'];?>" size="10" />
      </td>
    </tr>
	<tr valign="bottom">
	  <th scope="row"><div align="right">Recording Fee: </div></th>
	  <td><input name="srp_mortgage_calc_options[recording_fee]" type="text" value="<?php echo $options['recording_fee'];?>" size="10" />
      </td>
    </tr>
	<tr valign="bottom">
	  <th scope="row"><div align="right">Wire and Courier Fees: </div></th>
	  <td><input name="srp_mortgage_calc_options[wire_courier_fee]" type="text" value="<?php echo $options['wire_courier_fee'];?>" size="10" />
      </td>
    </tr>
	<tr valign="bottom">
	  <th scope="row"><div align="right">Endorsement Fee: </div></th>
	  <td><input name="srp_mortgage_calc_options[endorsement_fee]" type="text" value="<?php echo $options['endorsement_fee'];?>" size="10" />
      </td>
    </tr>
	<tr valign="bottom">
	  <th scope="row"><div align="right">Title Closing Fee: </div></th>
	  <td><input name="srp_mortgage_calc_options[title_closing_fee]" type="text" value="<?php echo $options['title_closing_fee'];?>" size="10" />
      </td>
    </tr>
	<tr valign="bottom">
	  <th scope="row"><div align="right">Title Document Prep Fee: </div></th>
	  <td><input name="srp_mortgage_calc_options[title_doc_prep_fee]" type="text" value="<?php echo $options['title_doc_prep_fee'];?>" size="10" />
      </td>
    </tr>
  </table>
	<p class="submit">
	<input name="simpleRealEstatePack_submit" type="submit" class="button-primary" value="<?php _e('Save All Changes') ?>" />
	</p>
	</form>
					</div>
				</div>
			</div>
		</div>
	</div>

  <?php
  echo srp_settings_right_column();
  echo '</div>';
}
?>
