<?php
	/**
	 * Instagram PHP API
	 */

	$type = 'tag';

	require_once 'api/BaseObjectAbstract.php';
	require_once 'api/Instagram.php';
	require_once 'api/Proxy.php';
	require_once 'api/ClientInterface.php';
	require_once 'api/ApiException.php';
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


	$username = isset( $_GET['username'] ) ? $_GET['username'] : '';

	$lat = isset( $_GET['latitude'] ) ? $_GET['latitude'] : '48.850000';
	$lng = isset( $_GET['longitude'] ) ? $_GET['longitude'] : '2.2600000';
   	$distance = isset( $_GET['distance'] ) ? $_GET['distance'] : '1000';

   	$count = isset( $_GET['count'] ) ? $_GET['count'] : '9';

   	$tag_actual = isset( $_GET['tag'] ) ? $_GET['tag'] : 'sweden';

   	$type = isset( $_GET['type'] ) ? $_GET['type'] : '';

	$instagram = new Instagram\Instagram;

   	$token = isset( $_GET['token'] ) ? $_GET['token'] : '';

	$instagram->setAccessToken( $token);

	if ($type == 'tag') {

		$tag = $instagram->getTag( isset( $tag_actual ) ? $tag_actual : 'sweden' );
		$media = $tag->getMedia( isset( $_GET['maxid'] )  ? array( 'maxid' => $_GET['maxid'] ) : null );

	} elseif( $type == 'location' ) {

		$lat = '48.850000';
		$lng = '2.2600000';
		$distance = '50000';

		$locations = $instagram->searchLocations( $lat, $lng, array( $distance ) );

		foreach($locations as $location) {
			$location_id = $location->getId();
			$location = $instagram->getLocation( isset( $location_id ) ? $location_id : 4774498 );
		}
		$media = $location->getMedia( isset( $_GET['maxid'] ) ? array( 'maxid' => $_GET['maxid'] ) : null );

	} elseif ( $type == 'user' ) {

		$user = $instagram->getUserByUsername( $username );
		$media = $user->getMedia( array( 'maxid' => $_GET['maxid'] ) );

	} else {

		$media = $instagram->getPopularMedia( isset( $_GET['maxid'] ) ? array( 'maxid' => $_GET['maxid'] ) : null );

	}

	// Collect everything for json output
		$contents = array();
		$i=1;
		foreach ($media as $m) if ($i < $count) {
			$contents[] = array(
				'image' => $m->getStandardRes()->url,
				'like' => $m->getLikesCount(),
				'link' => $m->getLink(),
			);
			$i +=1;
		}

	echo json_encode(array(
		'next_id' => $media->getNext(),
		'contents'  => $contents
	));
?>