<?php
/*
	Section: InstaGrid
	Author: Aleksander Hansson
	Author URI: http://ahansson.com
	Demo: http://instagrid.ahansson.com
	Class Name: InstaGrid
	Description: InstaGrid is a section that lets you display Instagram images on your site.
	Cloning: false
	V3: true
	Filter: gallery, social
*/

class InstaGrid extends PageLinesSection {

	function section_styles(){

		wp_enqueue_script('jquery');

		wp_enqueue_script( 'masonry', $this->base_url.'/js/masonry.pkgd.min.js' );

		wp_enqueue_script( 'imagesloaded', $this->base_url.'/js/imagesloaded.pkgd.min.js' );

		wp_enqueue_script( 'klass', $this->base_url.'/js/klass.min.js' );

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

				jQuery(document).ready(function() {
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
						$special = ( $this->opt( 'special' ) ) ? $this->opt( 'special' ) : '';
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

									for (var i=0;i<items.length;i++){
										<?php
											if ( $this->opt( 'link' ) ) {
												?>
													var link_open = '';
													var link_closing = '';
												<?php
											} else {
												?>
													var link_open = '<a href="' + items[i].link + '" target="_blank">';
													var link_closing = '</a>';
												<?php
											}
										?>
										var img = '<div class="instagrid-image instagrid-image<?php echo $prefix; ?>"><img src="' + items[i].image + '">' + link_open + '<div class="instagrid-overlay instagrid-overlay<?php echo $prefix; ?>"><div class="instagrid-txt"><i class="icon-heart"></i> ' + items[i].like + '</div></div>' + link_closing + '</div>';
										console.log(img);
										jQuery(instagridContainer).append(img).masonry('reloadItems');
									}

									<?php
										$special = ( $this->opt( 'special' ) ) ? $this->opt( 'special' ) : '';

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

		if (version_compare(phpversion(), '5.3', '<')) {
    		echo '<h2 class="tac">php version 5.3 or above is required for Instagrid to work. Contact your hosting provider to make them upgrade.</h2>';
		} else {
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

	   		$token = ( $this->opt( 'access_token' ) ) ? $this->opt( 'access_token' ) : '';

	   		$type = ( $this->opt( 'type' ) ) ? $this->opt( 'type' ) : 'popular';

	   		$count = ( $this->opt( 'count' ) ) ? $this->opt( 'count' ) : "8";

	   		$tag_actual = ( $this->opt( 'tag' ) ) ? $this->opt( 'tag' ) : 'sweden';

	   		$username = ( $this->opt( 'username' ) ) ? $this->opt( 'username' ) : '';

	   		$lat = ( $this->opt( 'latitude' ) ) ? $this->opt( 'latitude' ) : '48.850000';
		    $lng = ( $this->opt( 'longitude' ) ) ? $this->opt( 'longitude' ) : '2.2600000';
		   	$distance = ( $this->opt( 'distance' ) ) ? $this->opt( 'distance' ) : '1000';

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

			    $link_closing = ( $this->opt( 'link' ) ) ? '' : '</a>';

			    	?>
			    		<div id="instagrid<?php echo $prefix; ?>">

						    <?php
							    $i=0;
							    foreach ($media as $m) if ($i < $count ) {
							    	$link_open = ( $this->opt( 'link' ) ) ? '' : sprintf('<a href="%s" target="_blank">', $m->getLink() );
							        echo '<div class="instagrid-image instagrid-image'.$prefix.'"><img src="'.$m->getStandardRes()->url.'">'.$link_open.'<div class="instagrid-overlay instagrid-overlay'.$prefix.'"><div class="instagrid-txt"><i class="icon-heart"></i> '.$m->getLikesCount().'</div></div>'.$link_closing.'</div>';
							    	$i +=1;
							    }
						    ?>
			    		</div>
			    	<?php

			    $button = ( $this->opt( 'button' ) ) ? $this->opt( 'button' ) : 'yes';

			    $button_type = ( $this->opt( 'button_type' ) ) ? $this->opt( 'button_type' ) : 'btn-primary';

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

	}


	function section_opts() {

		$options = array();

		$how_to_use = __( '
		<strong>Read the instructions below before asking for additional help:</strong>
		</br></br>
		<strong>1.</strong> In the frontend editor, drag the InstaGrid section to a template of your choice.
		</br></br>
		<strong>2.</strong> Add Access Token and edit settings og your choice.
		</br></br>
		<strong>3.</strong> When you are done, hit "Publish" and refresh to see changes.
		</br></br>
		<div class="row zmb">
				<div class="span6 tac zmb">
					<a class="btn btn-info" href="http://forum.pagelines.com/71-products-by-aleksander-hansson/" target="_blank" style="padding:4px 0 4px;width:100%"><i class="icon-ambulance"></i>          Forum</a>
				</div>
				<div class="span6 tac zmb">
					<a class="btn btn-info" href="http://betterdms.com" target="_blank" style="padding:4px 0 4px;width:100%"><i class="icon-align-justify"></i>          Better DMS</a>
				</div>
			</div>
			<div class="row zmb" style="margin-top:4px;">
				<div class="span12 tac zmb">
					<a class="btn btn-success" href="http://shop.ahansson.com" target="_blank" style="padding:4px 0 4px;width:100%"><i class="icon-shopping-cart" ></i>          My Shop</a>
				</div>
			</div>
		', 'instagrid' );

		$options[] = array(
			'key' => 'instagrid_help',
			'type'     => 'template',
			'template'      => do_shortcode( $how_to_use ),
			'title' =>__( 'How to use:', 'instagrid' ) ,
		);

		$options[] = array(
			'key' => 'instagrid_settings',
			'title' => __( 'Settings', 'instagrid' ),
			'type'	=> 'multi',
			'opts'	=> array(

				array(
	    	   		'key' => 'access_token',
	    	    	'type'    =>  'text',
	    	    	'label'  =>  __('Instagram Access Token', 'instagrid'),
		            'help'	=> __('<a href="http://www.pinceladasdaweb.com.br/instagram/access-token/" target="_blank">Find your access token here</a>.', 'instagrid' ),
	    	    ),

	    	    array(
	    	    	'key' => 'type',
					'label' => __('Type', 'instagrid'),
					'type' => 'select',
					'default' => 'popular',
					'opts' => array(
						'tag'   => array( 'name' => __('Tag'	, 'instagrid' )),
						'location'   => array( 'name' => __('Location'	, 'instagrid' )),
						'user'   => array( 'name' => __('User'	, 'instagrid' )),
						'popular'   => array( 'name' => __('Popular'	, 'instagrid' ))
					),
	    	    ),

	    	    array(
	    	    	'key' => 'count',
					'label' => __('Number of images you want to display', 'instagrid'),
					'type' => 'count_select',
					'default' => '8',
					'count_start'   => 1,
		           	'count_number'  => 20,
		           	'help'	=> __('Sometimes there is a bug in the Instagram API which prevents to get 20 images.', 'instagrid' ),
	    	    ),

	    	    array(
	    	    	'key' => 'special',
					'label' => __('Every nth image is special', 'instagrid'),
					'type' => 'count_select',
					'count_start'   => 1,
		           	'count_number'  => 10,
		           	'help'	=> __('This option gives every nth image a special class.', 'instagrid' ),
	    	    ),

	    	    array(
	    	    	'key' => 'button',
					'label' => __('Button?', 'instagrid'),
					'type' => 'select',
					'default' => 'yes',
					'opts' => array(
						'yes'   => array( 'name' => __('Yes'	, 'instagrid' )),
						'no'   => array( 'name' => __('No'	, 'instagrid' )),
					),
	    	    ),

	    	    array(
	    	    	'key' => 'button_type',
					'label' => __('Button type', 'instagrid'),
					'type' => 'select_button',
					'default' => 'btn-primary',
					'help'	=> __('Choose the type of button you want.', 'instagrid' ),
	    	    ),

	    	    array(
	    	    	'key' => 'link',
					'label' => __('Remove link to Instagram?', 'instagrid'),
					'type' => 'select',
					'default' => false,
					'opts' => array(
						true	=> array( 'name' => __('Yes'	, 'instagrid' )),
						false   => array( 'name' => __('No'	, 'instagrid' )),
					),
	    	    ),
			)
		);

		$options[] = array(
			'key' => 'instagrid_tag_settings',
			'title'     =>  __('Tag Options', 'instagrid'),
			'type'     => 'multi',
			'opts'   => array(
				array(
	    	   		'key' => 'tag',
	    	    	'type'    =>  'text',
	    	    	'label'  =>  __('Instagram Tag', 'instagrid'),
		            'help'	=> __('Do not use # in front of tag! You can only use one tag!', 'instagrid' ),
		            'default' => '',
	    	    ),
			),
		);

		$options[] = array(
			'key' => 'instagrid_location_settings',
			'title'     =>  __('Location Options', 'instagrid'),
			'type'     => 'multi',
			'opts'   => array(
				array(
	    	   		'key' => 'latitude',
	    	    	'type'    =>  'text',
	    	    	'label'  =>  __('Latitude', 'instagrid'),
		            'help'	=> __('Latitude of the location. For example: "48.850000"', 'instagrid' ),
	    	    ),
	    	    array(
	    	   		'key' => 'longitude',
	    	    	'type'    =>  'text',
	    	    	'label'  =>  __('Longitude', 'instagrid'),
		            'help'	=> __('Longitude of the location. For example: "2.260000"', 'instagrid' ),
	    	    ),
	    	    array(
	    	   		'key' => 'distance',
	    	    	'type'    =>  'text',
	    	    	'label'  =>  __('Distance', 'instagrid'),
		            'help'	=> __('The distance you want images pulled from in meters. (default: 1000)', 'instagrid' ),
	    	    ),
			),
		);

		$options[] = array(
			'key' => 'instagrid_user_settings',
			'title'     =>  __('User Options', 'instagrid'),
			'type'     => 'multi',
			'opts'   => array(
				array(
	    	   		'key' => 'username',
	    	    	'type'    =>  'text',
	    	    	'label'  =>  __('Username', 'instagrid'),
		            'help'	=> __('Instagram Username', 'instagrid' ),
	    	    ),
			),
		);

		return $options;
	}

}