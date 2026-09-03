<?php
function scrape_insta_user_images($username) {
	//$insta_source = file_get_contents('https://www.instagram.com/'.$username.'/'); // instagram user url
    $tag=$username;    
        
        
         $ch = curl_init();

    // set url
     //EJJOPIV08JNTN8UY2S7E8CY5WLRQT51E0EM1E4F1F3OYLBJKCNMV5JFU3EJ0H8R9GXA99QJ8RJ1HMW99 fins 19 setembre, queden 800 credits
    //Z72C8DDGQ55G130BPS2OS8BL8595J5QNNYRW5CYXU9X3FEM6FJ1PSYSP02ZDKK2PEGR29RFFYYLS50WP fins 15 Agost 1000 credits
     // podem donar d'alta nou usuari test per 1000/mes peticions mes o pagar $99 per 1000000/mes a https://app.scrapingbee.com/
     //test nou amb scinstapi@gmail.com // digital36

    //curl_setopt($ch, CURLOPT_URL, 'https://app.scrapingbee.com/api/v1/?api_key=Z72C8DDGQ55G130BPS2OS8BL8595J5QNNYRW5CYXU9X3FEM6FJ1PSYSP02ZDKK2PEGR29RFFYYLS50WP&url=https%3A%2F%2Fwww.instagram.com%2Fexplore%2Ftags%2F'.$tag.'%2F');

//provem amb un altre proxy
//curl "https://api.webscraping.ai/html?api_key=169a6f9b-3513-45e3-a830-ad0ab3dd0bff&url=https://example.com"
curl_setopt($ch, CURLOPT_URL, 'https://api.webscraping.ai/html?api_key=169a6f9b-3513-45e3-a830-ad0ab3dd0bff&url=https%3A%2F%2Fwww.instagram.com%2Fexplore%2Ftags%2F'.$tag.'%2F');
    // set method
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

    // return the transfer as a string
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
 // send the request and save response to $insta_source
    $insta_source = curl_exec($ch);
        
        
	$shards = explode('window._sharedData = ', $insta_source);
	$insta_json = explode(';</script>', $shards[1]); 
	$insta_array = json_decode($insta_json[0], TRUE);
	return $insta_array; // this return a lot things print it and see what else you need
}

$username = $_GET['username']; // user for which you want images 
$results_array = scrape_insta_user_images($username);
echo '<pre>';
print_r($results_array);
echo '<pre>';
$limit = 200; // provide the limit thats important because one page only give some images.
$image_array= array(); // array to store images.
	for ($i=0; $i < $limit; $i++) { 	
		//new code to get images from json 	
		if(isset($results_array['entry_data']['ProfilePage'][0]['graphql']['user']['edge_owner_to_timeline_media']['edges'][$i])){
			$latest_array = $results_array['entry_data']['ProfilePage'][0]['graphql']['user']['edge_owner_to_timeline_media']['edges'][$i]['node'];
			// print "<pre>";
			// 		print_r($latest_array);

		 	$image_data  = '<img data-likes="'.$latest_array['edge_liked_by']['count'].'" src="'.$latest_array['thumbnail_src'].'">'; // thumbnail and same sizes 
		 	//$image_data  = '<img src="'.$latest_array['display_src'].'">'; actual image and different sizes 
			
			$image_array_likes[$latest_array['edge_liked_by']['count']] = $image_data;
			array_push($image_array, $image_data);
			krsort($image_array_likes);
		}
	}
	foreach ($image_array_likes as $image) {
		echo $image;// this will echo the images wrap it in div or ul li what ever html structure 
	}
	// for getting all images have to loop function for more pages 
	// for confirmation  you are getting correct images view 
	//https://www.instagram.com/username


  ?>
