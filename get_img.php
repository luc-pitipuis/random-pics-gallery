<?php
include("Imgur.php");
//Set up your api key and secret
$api_key = "";
$api_secret = "";

$imgur = new Imgur($api_key, $api_secret);

$results = $imgur->gallery()->get('random', 'random', rand(0,50));
$out_json = Array();
foreach ($results['data'] as $res) {
	if(!$res['is_album']) $out_json[] = Array("link" => $res['link'], "aspect_ratio" => $res['width'] / $res['height']);
}
 print(json_encode($out_json,JSON_HEX_QUOT));

?>
