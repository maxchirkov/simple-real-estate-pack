<?php
if ( function_exists('get_pages_with_active_listings') ) {
?>
<!-- list of listings -->
<?php
	$pageposts = get_pages_with_active_listings('','highprice');
?>

<?php if ($pageposts): ?>
<div id="activelistings">
<h2><span>Active Listings</span></h2>
	<?php foreach ($pageposts as $post): ?>
		<?php setup_postdata($post); ?>
		<?php setup_listingdata($post); ?>
		<?php $line1 = ''; $line2 = ''; $line3 = ''; ?>

	<div class="prop-box-avail">
	<h3><a href="<?php the_permalink() ?>" rel="bookmark" title="More about <?php the_title(); ?>"><?php the_listing_listprice();?> &mdash; <?php  the_title(); ?></a></h3>
	<div class="prop-thumb span-5">
		<div class="box">
			<a href="<?php the_permalink() ?>" rel="bookmark" title="More about <?php the_title(); ?>"><?php the_listing_thumbnail(); ?></a>
		</div>
	</div>

    <div class="box">
	  	<div>
 	<?php the_listing_blurb(); ?>
      	</div>
	<?php if ($bedrooms = get_listing_bedrooms())
		$line1 .= "<div>$bedrooms Bedrooms</div>"; ?>
	<?php if ($bathrooms = get_listing_bathrooms()) {
		$line1 .= "<div>$bathrooms Full ";
		if ($halfbaths = get_listing_halfbaths())
			$line1 .= "&amp; $halfbaths Half ";
		$line1 .= " Baths</div>";
              }	?>
	<?php if (get_listing_garage())
		$line1 .= "<div>" . get_listing_garage() . " Garage Spaces</div>"; ?>
	<?php if (get_listing_acsf())
		$line2 .= "<div>" . get_listing_acsf() ." Sq/Ft Under Air</div>"; ?>
	<?php if (get_listing_totsf()) $line2 .= "<div>" .get_listing_totsf(). " Sq/Ft Total</div>"; ?>
	<?php if (get_listing_acres()) $line2 .= "<div>" .get_listing_acres()." Acres</div>"; ?>
	<?php if (get_listing_haspool()) $line3 .= "<div>Private Pool</div>"; ?>
	<?php if (get_listing_haswater()) $line3 .= "<div>Waterfront</div>"; ?>
	<?php if (get_listing_hasgolf()) $line3 .= "<div>On Golf Course</div>"; ?>
 	<?php if ($line1 || $line2 || $line3) { ?>
      <div class='propdata'>
	<?php if ($line1) echo "<div class='propdata-line'>$line1</div>"; ?>
	<?php if ($line2) echo "<div class='propdata-line'>$line2</div>"; ?>
	<?php if ($line3) echo "<div class='propdata-line propfeatures'>$line3</div>"; ?>
      </div>
	<?php } ?>
  			<h4><?php the_listing_status(); ?>
		<?php if (get_listing_listprice()) { ?>
		- Offered at <?php the_listing_listprice(); } ?>
			</h4>
			<a class="view-details-link" href="<?php the_permalink() ?>" rel="bookmark" title="View Listing Details: <?php the_title(); ?>">View Details</a>
    	</div>
  	</div>
  <?php endforeach; ?>

  </div>
 <?php endif; ?>

<!-- list of pending sales -->
<?php

 $pageposts = get_pages_with_pending_listings('','highprice');

?>

<?php if ($pageposts): ?>
  <div id="pendingsales">
  <h2><span>Pending Sale</span></h2>
  <?php foreach ($pageposts as $post): ?>
		<?php setup_postdata($post); ?>
		<?php setup_listingdata($post); ?>
		<?php $line1 = ''; $line2 = ''; $line3 = ''; ?>

	<div class="prop-box-avail">
	<h3><a href="<?php the_permalink() ?>" rel="bookmark" title="More about <?php the_title(); ?>"><?php the_listing_listprice();?> &mdash; <?php  the_title(); ?></a></h3>
	<div class="prop-thumb span-5">
		<div class="box">
			<a href="<?php the_permalink() ?>" rel="bookmark" title="More about <?php the_title(); ?>"><?php the_listing_thumbnail(); ?></a>
		</div>
	</div>

    <div class="box">
	  	<div>
 	<?php the_listing_blurb(); ?>
      	</div>
	<?php if ($bedrooms = get_listing_bedrooms())
		$line1 .= "<div>$bedrooms Bedrooms</div>"; ?>
	<?php if ($bathrooms = get_listing_bathrooms()) {
		$line1 .= "<div>$bathrooms Full ";
		if ($halfbaths = get_listing_halfbaths())
			$line1 .= "&amp; $halfbaths Half ";
		$line1 .= " Baths</div>";
              }	?>
	<?php if (get_listing_garage())
		$line1 .= "<div>" . get_listing_garage() . " Garage Spaces</div>"; ?>
	<?php if (get_listing_acsf())
		$line2 .= "<div>" . get_listing_acsf() ." Sq/Ft Under Air</div>"; ?>
	<?php if (get_listing_totsf()) $line2 .= "<div>" .get_listing_totsf(). " Sq/Ft Total</div>"; ?>
	<?php if (get_listing_acres()) $line2 .= "<div>" .get_listing_acres()." Acres</div>"; ?>
	<?php if (get_listing_haspool()) $line3 .= "<div>Private Pool</div>"; ?>
	<?php if (get_listing_haswater()) $line3 .= "<div>Waterfront</div>"; ?>
	<?php if (get_listing_hasgolf()) $line3 .= "<div>On Golf Course</div>"; ?>
 	<?php if ($line1 || $line2 || $line3) { ?>
      <div class='propdata'>
	<?php if ($line1) echo "<div class='propdata-line'>$line1</div>"; ?>
	<?php if ($line2) echo "<div class='propdata-line'>$line2</div>"; ?>
	<?php if ($line3) echo "<div class='propdata-line propfeatures'>$line3</div>"; ?>
      </div>
	<?php } ?>
  			<h4><?php the_listing_status(); ?>
		<?php if (get_listing_listprice()) { ?>
		- Offered at <?php the_listing_listprice(); } ?>
			</h4>
			<a class="view-details-link" href="<?php the_permalink() ?>" rel="bookmark" title="View Listing Details: <?php the_title(); ?>">View Details</a>
    	</div>
  	</div>
  <?php endforeach; ?>
  </div>
 <?php endif; ?>


<!-- list of sold -->
<?php

 $pageposts = get_pages_with_sold_listings('','saledate');

?>

<?php if ($pageposts): ?>
  <div id="soldlistings">
  <h2><span>Recently Sold Properties</span></h2>
  <?php foreach ($pageposts as $post): ?>
		<?php setup_postdata($post); ?>
		<?php setup_listingdata($post); ?>
		<?php $line1 = ''; $line2 = ''; $line3 = ''; ?>

	<div class="prop-box-avail">
	<h3><a href="<?php the_permalink() ?>" rel="bookmark" title="More about <?php the_title(); ?>"><?php the_listing_listprice();?> &mdash; <?php  the_title(); ?></a></h3>
	<div class="prop-thumb span-5">
		<div class="box">
			<a href="<?php the_permalink() ?>" rel="bookmark" title="More about <?php the_title(); ?>"><?php the_listing_thumbnail(); ?></a>
		</div>
	</div>

    <div class="box">
	  	<div>
 	<?php the_listing_blurb(); ?>
      	</div>
	<?php if ($bedrooms = get_listing_bedrooms())
		$line1 .= "<div>$bedrooms Bedrooms</div>"; ?>
	<?php if ($bathrooms = get_listing_bathrooms()) {
		$line1 .= "<div>$bathrooms Full ";
		if ($halfbaths = get_listing_halfbaths())
			$line1 .= "&amp; $halfbaths Half ";
		$line1 .= " Baths</div>";
              }	?>
	<?php if (get_listing_garage())
		$line1 .= "<div>" . get_listing_garage() . " Garage Spaces</div>"; ?>
	<?php if (get_listing_acsf())
		$line2 .= "<div>" . get_listing_acsf() ." Sq/Ft Under Air</div>"; ?>
	<?php if (get_listing_totsf()) $line2 .= "<div>" .get_listing_totsf(). " Sq/Ft Total</div>"; ?>
	<?php if (get_listing_acres()) $line2 .= "<div>" .get_listing_acres()." Acres</div>"; ?>
	<?php if (get_listing_haspool()) $line3 .= "<div>Private Pool</div>"; ?>
	<?php if (get_listing_haswater()) $line3 .= "<div>Waterfront</div>"; ?>
	<?php if (get_listing_hasgolf()) $line3 .= "<div>On Golf Course</div>"; ?>
 	<?php if ($line1 || $line2 || $line3) { ?>
      <div class='propdata'>
	<?php if ($line1) echo "<div class='propdata-line'>$line1</div>"; ?>
	<?php if ($line2) echo "<div class='propdata-line'>$line2</div>"; ?>
	<?php if ($line3) echo "<div class='propdata-line propfeatures'>$line3</div>"; ?>
      </div>
	<?php } ?>
  			<h4><?php the_listing_status(); ?>
		<?php if (get_listing_listprice()) { ?>
		for <?php the_listing_listprice(); } ?>
			</h4>
			<a class="view-details-link" href="<?php the_permalink() ?>" rel="bookmark" title="View Listing Details: <?php the_title(); ?>">View Details</a>
    	</div>
  	</div>
  <?php endforeach; ?>
  </div>

 <?php endif;
}
 ?>