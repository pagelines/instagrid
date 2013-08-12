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