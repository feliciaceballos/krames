define( 'KRAMES_CLIENT_ID', 'insert here' );
define( 'KRAMES_CLIENT_SECRET', 'insert here' );

// Display posts from Krames content library in Genesis custom block with WP REST API 
function fc_krames_content($search) {	
	
// Step 1: retrieve access token
	$key = urlencode( KRAMES_CLIENT_ID );
	$secret = urlencode( KRAMES_CLIENT_SECRET );
	$concatenated = $key . ':' . $secret;
	$encoded = base64_encode( $concatenated );
	
	$args = array(
		'headers' => array(
			'Authorization' => 'Basic ' . $encoded,
			'Content-Type' => 'application/x-www-form-urlencoded;charset=UTF-8'
		),
		'body' => 'grant_type=client_credentials'
	);
	
	$response = wp_remote_post( 'https://identity.krames.com/connect/token', $args );
	
	$body = wp_remote_retrieve_body( $response );
	$body = json_decode( $body, true );
	$access_token = $body['access_token'];

// Step 2: search content library
	$krames_search_endpoint = "https://api.krames.com/v3/content/Search?terms=".$search;
	
	$args_2 = array(
		'headers' => array(
			'Authorization' => 'Bearer ' . $access_token,
		),
	);
	
	$response_2 = wp_remote_get( $krames_search_endpoint, $args_2 );
	
	$body_2 = wp_remote_retrieve_body( $response_2 );
	$body_2 = json_decode( $body_2, true );
	$articles = $body_2['data'];
	
// Step 3: display search results as accordion blocks with content inside
	if( !empty( $articles ) ) { 
  		foreach( $articles as $article ) { 
	
			$post_id = $article['ContentTypeID'].'-'.$article['ContentID'];
			$krames_content_endpoint = "https://api.krames.com/v3/content/".$post_id;
			$blog_post = wp_remote_get( $krames_content_endpoint, $args_2 );
 
			if( !is_wp_error( $blog_post ) && $blog_post['response']['code'] == 200 ) {
				$post_data = json_decode( $blog_post['body'] );
				$post_content = $post_data->body;	
			}
	
	  		echo '<div class="wp-block-genesis-blocks-gb-accordion gb-block-accordion"><details><summary class="gb-accordion-title">' 
				. $article['Title'] 
				. '</summary><div class="gb-accordion-text"><!-- wp:paragraph --><p>' 
				. $post_content . '</p><!-- /wp:paragraph --></div></details></div>';
		} 
	} 
	
}
