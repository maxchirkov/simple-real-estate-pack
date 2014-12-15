<?php if (function_exists('get_listing_status')) { ?>
	<?php getandsetup_listingdata(); ?>
<div id="srp_listing_details">
    <h2 class="listing_address"><?php echo $property_address = get_listing_address() . ', ' .get_listing_city() . ' ' . get_listing_state() . ', ' . get_listing_postcode();?></h2>
    <div class="clearfix">
    <div class="listing-slideshow span-10">
            <div class="box">
            <?php //echo nggShowSlideshow(get_listing_galleryid(), $width='356', $height='267');
            echo srp_gre_slideshow_image(get_listing_galleryid(), $width = 356, $height = 267)
            ?>
            </div>
    </div>
    <div class="page-propdata-box span-5 last">
            <div class="box">
            <h2><span>Property Details</span></h2>
            <?php
                    $details = array();
            ?>

            <div class="page-blurb"><?php the_listing_blurb(); ?></div>
            <?php
                    if ($bedrooms = get_listing_bedrooms())
                            $details[] = "$bedrooms Bedrooms";
                    if ($bathrooms = get_listing_bathrooms()) {
                            $bath = "$bathrooms Full ";
                    if ($halfbaths = get_listing_halfbaths())
                            $bath .= "&amp; $halfbaths Half ";
                            $bath .= " Baths";
                  }
                            $details[] = $bath;
                    if ($garage = get_listing_garage())
                            $details[] = "$garage Garage Spaces";
                    if ($acsf = get_listing_acsf())
                            $details[] = "$acsf Sq/Ft Under Air";
                    if ($totsf = get_listing_totsf())
                            $line2 .= "$totsf Sq/Ft Total";
                            $acres = get_listing_acres();
                    if ($acres > 0)
                            $details[] = "$acres Acres";
                    if (get_listing_haspool())
                            $details[] = "Private Pool";
                    if (get_listing_haswater())
                            $details[] = "Waterfront";
                    if (get_listing_hasgolf())
                            $details[] = "On Golf Course";
                    if (!empty($details)) { ?>
          <div class='propdata'>
                    <ul>
            <?php
                    $i = 0;
                    foreach($details as $line){
                            $i++;
                            if($i&1){ $class = 'class="odd"'; }else{$class = 'class="even"';}
                            print "<li $class>$line</li>";

                    }
            ?>
                    </ul>
                    <h4><?php the_listing_status(); ?>
                    <?php if (get_listing_hasclosed()) { ?>
                    <?php the_listing_saledate(); ?> for <?php the_listing_saleprice(); ?> - last offered<?php } else { ?>- Offered<?php } ?>  at <?php the_listing_listprice(); ?></h4>
          </div>
            </div>
    </div>
    </div>
            <?php } ?>

    <?php } else { ?>
    <?php //the_content(); // plugin disabled, just spit out the normal content ?>
    <?php } ?>

    <?php if (function_exists('get_listing_status')) { ?>
    <div id="listing-container">

        <h4 class="callus"><?php echo srp_gre_listing_contact();?></h4>

		<?php
			$listing_description = srp_buffer('the_listing_description_content');
			echo str_replace('<h2>Property Details</h2>', '<h2><span>Property Description</span></h2>', $listing_description);
		?>

        <?php
            /* Begin SRP Template Code */
            global $srp_property_values;
            $srp_property_values = array(
                'lat' => get_listing_latitude(),
                'lng' => get_listing_longitude(),
                'address' => get_listing_address(),
                'city' => get_listing_city(),
                'state' => get_listing_state(),
                'zip_code' => get_listing_postcode(),
                'listing_price' => get_listing_listprice(),
                'bedrooms'  => get_listing_bedrooms(),
                'bathrooms' => get_listing_bathrooms(),
                'html' => '<div style="text-align: center;">' . get_listing_thumbnail() . '</div><div style="text-align: center;font-size: 14px;line-height: normal;"><strong>' . get_listing_listprice() . '</strong></div><p style="text-align: center;font-size: 12px;line-height: normal;">' . get_listing_address() . ',<br />' . get_listing_city() . ' ' . get_listing_state() . ' ' . get_listing_postcode() . '</p>',

            );

            if(function_exists('srp_profile')){
                srp_profile();
            }else{
                ?>
                <ul id="tabnav">
                <?php the_listing_map_tab(); // recommend this be first ?>
                <?php //the_listing_description_tab(); ?>
                <?php //the_listing_gallery_tab(); ?>
                <?php the_listing_video_tab(); ?>
                <?php the_listing_panorama_tab(); ?>
                <?php the_listing_downloads_tab(); ?>
                <?php the_listing_community_tab(); ?>
                </ul>
                <?php the_listing_map_content(); // recommend this be first ?>
                <?php the_listing_description_content(); ?>
                <?php the_listing_gallery_content(); ?>
                <?php the_listing_video_content(); ?>
                <?php the_listing_panorama_content(); ?>
                <?php the_listing_downloads_content(); ?>
                <?php the_listing_community_content();
            }
                    /* End SRP Template Code */
            ?>

            <?php
            srp_inquiry_form();
            ?>
    <?php } ?>
    </div>
</div>