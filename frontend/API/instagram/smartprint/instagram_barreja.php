<?php

// get cURL resource
$ch = curl_init();

// set url
curl_setopt($ch, CURLOPT_URL, 'https://app.scrapingbee.com/api/v1/?api_key=YJDUFRURLGCOTDVNTDZJG1PQ4PBO9G2Q7LNFE0UKH66IVT0GDJK9HULQP8QHX7UZZ9SD8Z4ARZYF7GKZ&url=https%3A%2F%2Fwww.instagram.com%2Fexplore%2Ftags%2Fadoptdontshop&render_js=false&premium_proxy=true&country_code=us');
//curl_setopt($ch, CURLOPT_URL, 'https://app.scrapingbee.com/api/v1/?api_key=YJDUFRURLGCOTDVNTDZJG1PQ4PBO9G2Q7LNFE0UKH66IVT0GDJK9HULQP8QHX7UZZ9SD8Z4ARZYF7GKZ&url=https%3A%2F%2Fwww.digital-centre.com&render_js=false&premium_proxy=true&country_code=us');
// set method
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

// return the transfer as a string
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);



// send the request and save response to $response
$response = curl_exec($ch);

 $shards = explode('window._sharedData = ', $response);
    $insta_json = explode(';</script>', $shards[1]); 
    $insta_array = json_decode($insta_json[0], TRUE);
   print_r($insta_array);

// stop if fails
if (!$response) {
  die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
}

echo 'HTTP Status Code: ' . curl_getinfo($ch, CURLINFO_HTTP_CODE) . PHP_EOL;
echo 'Response Body: ' . $response . PHP_EOL;

// close curl resource to free up system resources
curl_close($ch);
?>