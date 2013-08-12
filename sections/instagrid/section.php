<?php
/*
	Section: InstaGrid
	Author: Aleksander Hansson
	Author URI: http://ahansson.com
	Demo: http://instagrid.ahansson.com
	Version: 1.0
	Class Name: InstaGrid
	Description:
	Cloning:false
	V3: true
*/

class InstaGrid extends PageLinesSection {

	function section_styles(){

		wp_enqueue_script('jquery');

		wp_enqueue_script( 'masonry', $this->base_url.'/js/masonry.pkgd.min.js' );

		wp_enqueue_script( 'imagesloaded', $this->base_url.'/js/imagesloaded.pkgd.min.js' );

		wp_enqueue_script( 'klass', $this->base_url.'/js/klass.min.js' );

		wp_enqueue_script( 'photoswipe-jquery', $this->base_url.'/js/code.photoswipe.jquery-3.0.5.min.js' );

	}

	function section_head() {

		$clone_id = $this->get_the_id();

		$prefix = ($clone_id != '') ? '-clone-'.$clone_id : '';

		?>

			<script type="text/javascript">

				var instagridContainer = '#instagrid<?php echo $prefix; ?>',
					instagridOverlay = '.instagrid-overlay<?php echo $prefix; ?>',
					instagridImage = '.instagrid-image<?php echo $prefix; ?>',
					insagridMore = '#instagrid-more<?php echo $prefix; ?>';

				jQuery(function(){
					jQuery(instagridContainer).imagesLoaded( function() {
					 	jQuery(instagridContainer).masonry({
					    	itemSelector : instagridImage,
						});
						jQuery(instagridImage).animate({
    						opacity: 1,
  						},500);
  						jQuery(insagridMore).html('<i class="icon-picture"></i> Load more')
					});
				});

			    jQuery(document).ready(function() {

			    	jQuery(instagridOverlay).hover(function(){
						jQuery(this).stop(true, true).animate({
    						opacity: 1,
  						},500);
					},function(){
						jQuery(this).stop(true, true).animate({
	    					opacity: 0,
	  					},500);
					});

			    	<?php
						$special = ( $this->opt( 'special', $this->oset ) ) ? $this->opt( 'special', $this->oset ) : '';
						if ($special) {
							?>
								jQuery(instagridImage + ':nth-child(<?php echo $special; ?>n)').addClass( "instagrid-image-special" );
							<?php
						};
					?>

			      	jQuery(insagridMore).click(function() {
			      		jQuery(insagridMore).html('<i class="icon-spin icon-spinner"></i> Loading');

			        	var tag   = jQuery(this).data('tag'),
			        		username   = jQuery(this).data('username'),
			        		longitude   = jQuery(this).data('longitude'),
			        		latitude   = jQuery(this).data('latitude'),
			        		distance   = jQuery(this).data('distance'),
			        		count   = jQuery(this).data('count'),
			            	maxid = jQuery(this).data('maxid'),
			            	type = jQuery(this).data('type'),
			            	token = jQuery(this).data('token');

			        	jQuery.ajax({
			          		type: 'GET',
			          		url: '<?php printf( "%s/ajax.php", $this->base_url ) ?>' + '?maxid=' + maxid + '&tag=' + tag + '&username=' + username + '&longitude=' + longitude + '&latitude=' + latitude + '&distance=' + distance + '&count=' + count + '&type=' + type + '&token=' + token,
				          	dataType: 'json',
				          	cache: false,
				          	success: function(data) {
				            	// Output data

			            		jQuery(insagridMore).attr('data-maxid', data.next_id);

				            		var items = data.contents;

									console.log(data);

									for (var i=0;i<items.length;i++){
										var img = '<div class="instagrid-image instagrid-image<?php echo $prefix; ?>"><img src="' + items[i].image + '"><a id="instagrid-link" href="' + items[i].link + '" target="_blank"><div class="instagrid-overlay instagrid-overlay<?php echo $prefix; ?>"><div class="instagrid-txt"><i class="icon-heart"></i> ' + items[i].like + '</div></div></a></div>';
										console.log(img);
										jQuery(instagridContainer).append(img).masonry('reloadItems');
									}

									<?php
										$special = ( $this->opt( 'special', $this->oset ) ) ? $this->opt( 'special', $this->oset ) : '';

										if ($special) {
											?>
												jQuery(instagridImage + ':nth-child(<?php echo $special; ?>n)').addClass( "instagrid-image-special" );
											<?php
										};
									?>


				            		jQuery(instagridOverlay).hover(function(){
										jQuery(this).stop(true, true).animate({
				    						opacity: 1,
				  						},500);
									},function(){
										jQuery(this).stop(true, true).animate({
					    					opacity: 0,
					  					},500);
									});

				            		jQuery(instagridContainer).imagesLoaded( function() {
									  	jQuery(instagridContainer).masonry();
									  	jQuery(instagridImage).animate({
				    						opacity: 1,
				  						},500);
									  	jQuery(insagridMore).html('<i class="icon-picture"></i> Load more');
									});

				          	}
			        	});
			        	return false;
			      	});

				});

			</script>
		<?php

	}

	function section_template( ) {

		$clone_id = $this->get_the_id();

		$prefix = ($clone_id != '') ? '-clone-'.$clone_id : '';

		require_once 'api/BaseObjectAbstract.php';
		require_once 'api/Instagram.php';
		require_once 'api/Proxy.php';
		require_once 'api/ClientInterface.php';
		require_once 'api/ApiException.php';
		require_once 'api/ApiAuthException.php';
		require_once 'api/CollectionAbstract.php';
		require_once 'api/MediaCollection.php';
		require_once 'api/UserCollection.php';
		require_once 'api/LocationCollection.php';
		require_once 'api/TagMediaCollection.php';
		require_once 'api/ApiResponse.php';
		require_once 'api/CurlClient.php';
   		require_once 'api/Location.php';
   		require_once 'api/Media.php';
   		require_once 'api/Tag.php';
   		require_once 'api/User.php';

   		$token = ( $this->opt( 'access_token', $this->oset ) ) ? $this->opt( 'access_token', $this->oset ) : '';

   		$type = ( $this->opt( 'type', $this->oset ) ) ? $this->opt( 'type', $this->oset ) : '';

   		$count = ( $this->opt( 'count', $this->oset ) ) ? $this->opt( 'count', $this->oset ) : "8";

   		$tag_actual = ( $this->opt( 'tag', $this->oset ) ) ? $this->opt( 'tag', $this->oset ) : 'sweden';

   		$username = ( $this->opt( 'username', $this->oset ) ) ? $this->opt( 'username', $this->oset ) : '';

   		$lat = ( $this->opt( 'latitude', $this->oset ) ) ? $this->opt( 'latitude', $this->oset ) : '48.850000';
	    $lng = ( $this->opt( 'longitude', $this->oset ) ) ? $this->opt( 'longitude', $this->oset ) : '2.2600000';
	   	$distance = ( $this->opt( 'distance', $this->oset ) ) ? $this->opt( 'distance', $this->oset ) : '1000';

	   	$instagram = new Instagram\Instagram;
	    $instagram->setAccessToken( $token );

	    try {
	        if ($type == 'tag') {

				$tag = $instagram->getTag( $tag_actual );
				$media = $tag->getMedia( );

		    } elseif( $type == 'location' ) {

		    	$locations = $instagram->searchLocations( $lat, $lng, array( $distance ) );

				foreach($locations as $location) {
					$location_id = $location->getId();
					$location = $instagram->getLocation( isset( $location_id ) ? $location_id : 4774498 );
				}
				$media = $location->getMedia( );

		    } elseif ( $type == 'user' ) {

				$user = $instagram->getUserByUsername( $username );
				$media = $user->getMedia( );

		    } else {

		    	$media = $instagram->getPopularMedia();

		    }

		    $next = ( $media->getNext() ) ? $media->getNext(): '';

		    	?>
		    		<div id="instagrid<?php echo $prefix; ?>">

					    <?php
						    $i=0;
						    foreach ($media as $m) if ($i < $count) {
						        echo '<div class="instagrid-image instagrid-image'.$prefix.'"><img src="'.$m->getStandardRes()->url.'"><a id="instagrid-link" href="'.$m->getLink().'" target="_blank"><div class="instagrid-overlay instagrid-overlay'.$prefix.'"><div class="instagrid-txt"><i class="icon-heart"></i> '.$m->getLikesCount().'</div></div></a></div>';
						    	$i +=1;
						    }
					    ?>
		    		</div>
		    	<?php

		    $button = ( $this->opt( 'button', $this->oset ) ) ? $this->opt( 'button', $this->oset ) : 'yes';

		    $button_type = ( $this->opt( 'button_type', $this->oset ) ) ? $this->opt( 'button_type', $this->oset ) : 'btn-primary';

		    if ( $type == 'user') {
		    	echo '';
		    } else {
		    	if ($button == 'yes') {
		   		    echo '<div class="instagrid-button-container"><a href="#" class="btn '.$button_type.' btn-large instagrid-more" id="instagrid-more'.$prefix.'" data-username="'.$username.'" data-longitude="'.$lng.'" data-latitude="'.$lat.'" data-distance="'.$distance.'" data-count="'.$count.'" data-maxid="'.$next.'" data-tag="'.$tag_actual.'" data-type="'.$type.'" data-token="'.$token.'"><i class="icon-spin icon-spinner"></i> Loading</a></div>';
		    	}
		    }
	    }
	    /**
	     * Authorization Exception thrown
	     * Clear the session and redirect to the auth page
	     */
	    catch ( \Instagram\Core\ApiException $e ) {
			echo setup_section_notify($this, __('Please set a valid access token!', 'instagrid'));
	    }
	}


	function section_opts() {

		$options = array();

		$options[] = array(

			'title' => __( 'Settings', 'beefy' ),
			'type'	=> 'multi',
			'opts'	=> array(

				array(
	    	   		'key' => 'access_token',
	    	    	'type'    =>  'text',
	    	    	'label'  =>  __('Instagram Access Token', 'beefy'),
		            'help'	=> __('<a href="http://www.pinceladasdaweb.com.br/instagram/access-token/" target="_blank">Find your access token here</a>.', 'beefy' ),
	    	    ),

	    	    array(
	    	    	'key' => 'type',
					'label' => __('Type', 'beefy'),
					'type' => 'select',
					'default' => '',
					'opts' => array(
						'tag'   => array( 'name' => __('Tag'	, 'beefy' )),
						'location'   => array( 'name' => __('Location'	, 'beefy' )),
						'user'   => array( 'name' => __('User'	, 'beefy' )),
						''   => array( 'name' => __('Popular'	, 'beefy' ))
					),
	    	    ),

	    	    array(
	    	    	'key' => 'count',
					'label' => __('Number of images you want to display', 'beefy'),
					'type' => 'count_select',
					'default' => '9',
					'count_start'   => 1,
		           	'count_number'  => 20,
		           	'help'	=> __('Sometimes there is a bug in the Instagram API which prevents to get 20 images.', 'beefy' ),
	    	    ),

	    	    array(
	    	    	'key' => 'special',
					'label' => __('Every nth image is special', 'beefy'),
					'type' => 'count_select',
					'default' => '',
					'count_start'   => 1,
		           	'count_number'  => 10,
		           	'help'	=> __('This option gives every nth image a special class.', 'beefy' ),
	    	    ),

	    	    array(
	    	    	'key' => 'button',
					'label' => __('Button?', 'beefy'),
					'type' => 'select',
					'default' => 'yes',
					'opts' => array(
						'yes'   => array( 'name' => __('Yes'	, 'beefy' )),
						'no'   => array( 'name' => __('No'	, 'beefy' )),
					),
	    	    ),

	    	    array(
	    	    	'key' => 'button_type',
					'label' => __('Button type', 'beefy'),
					'type' => 'select_button',
					'default' => 'btn-primary',
					'help'	=> __('Choose the type of button you want.', 'beefy' ),
	    	    ),
			)
		);

		$options[] = array(
			'title'     =>  __('Tag Options', 'beefy'),
			'type'     => 'multi',
			'opts'   => array(
				array(
	    	   		'key' => 'tag',
	    	    	'type'    =>  'text',
	    	    	'label'  =>  __('Instagram Tag', 'beefy'),
		            'help'	=> __('Do not use # in front of tag! You can only use one tag!', 'beefy' ),
		            'default' => '',
	    	    ),
			),
		);

		$options[] = array(
			'title'     =>  __('Location Options', 'beefy'),
			'type'     => 'multi',
			'opts'   => array(
				array(
	    	   		'key' => 'latitude',
	    	    	'type'    =>  'text',
	    	    	'label'  =>  __('Latitude', 'beefy'),
		            'help'	=> __('Latitude of the location. For example: "48.850000"', 'beefy' ),
	    	    ),
	    	    array(
	    	   		'key' => 'longitude',
	    	    	'type'    =>  'text',
	    	    	'label'  =>  __('Longitude', 'beefy'),
		            'help'	=> __('Longitude of the location. For example: "2.260000"', 'beefy' ),
	    	    ),
	    	    array(
	    	   		'key' => 'distance',
	    	    	'type'    =>  'text',
	    	    	'label'  =>  __('Distance', 'beefy'),
		            'help'	=> __('The distance you want images pulled from in meters. (default: 1000)', 'beefy' ),
	    	    ),
			),
		);

		$options[] = array(
			'title'     =>  __('User Options', 'beefy'),
			'type'     => 'multi',
			'opts'   => array(
				array(
	    	   		'key' => 'username',
	    	    	'type'    =>  'text',
	    	    	'label'  =>  __('Username', 'beefy'),
		            'help'	=> __('Instagram Username', 'beefy' ),
	    	    ),
			),
		);

		return $options;
	}

}