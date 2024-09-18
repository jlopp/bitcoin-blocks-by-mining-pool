<?php
// run this script to fill in the gap missing from the static `blocks.csv` file in this repository
// set this to be the height after the last entry in blocks.csv
$currentHeight = 861880;

if (isset($argv[1]) && is_int($argv[1])) {
	$currentHeight = $argv[1];
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://mempool.space/api/blocks/tip/height");
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

$result = curl_exec($ch);
$endHeight = json_decode($result);

while ($currentHeight < $endHeight) {
	curl_setopt($ch, CURLOPT_URL, "https://mempool.space/api/v1/blocks/$currentHeight");

	$result = curl_exec($ch);
	$resultJson = json_decode($result);

	$blocks = array();
	foreach ($resultJson as $block) {
		$blocks[$block->height] = array("height" => $block->height, "id" => $block->id, "pool" => $block->extras->pool->slug);
	}
	ksort($blocks);

	foreach ($blocks as $block) {
		echo $block['height'] . "," . $block['id'] . "," . $block['pool'] . "\n";
	}
	$currentHeight += 15;
}
