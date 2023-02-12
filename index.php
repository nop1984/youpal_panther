<?php

use Symfony\Component\Panther\Client;
use Workerman\Worker;
use Workerman\Protocols\Http\Request;
use Workerman\Protocols\Http\Response;

require __DIR__.'/vendor/autoload.php'; // Composer's autoloader

$browsers = [];

// #### http worker ####
$http_worker = new Worker('http://0.0.0.0:2345');

// 4 processes
$http_worker->count = 4;

// Emitted when data received
$http_worker->onMessage = function ($connection, Request $request) {
    //$request->get();
    //$request->post();
    //$request->header();
    //$request->cookie();
    //$request->session();
    //$request->uri();
    //$request->path();
    //$request->method();
	global $browsers;

	$params = $request->get();
	$url = @$params['url'];
	if(empty($url)) {
		$response = new Response(400,  [
			'Content-Type' => 'application/json',
		], json_encode([
			'error' => 'empty URL'
		]));
		$connection->send($response);
		return;
	}

	$start = microtime(true);
	$result = invoke($url);
	$time_elapsed_secs = microtime(true) - $start;

    // Send data to client
	$response = new Response(200, [
        'Content-Type' => 'application/json',
    ], json_encode([
		'url' => $url,
		'hi' => 'hi',
		'start' => $start,
		'duration' => $time_elapsed_secs,
		'instances' => count($browsers)
	] + $result));
    $connection->send($response);
};

// Run all workers
Worker::runAll();

function invoke(string $url, $take_screen = true) {
	global $browsers;
	$port = rand(5000, 6000);
	foreach($browsers as $b) {
		if($b['port'] == $port)
			$port = rand(5000, 6000);
	}
	
	$client = Client::createChromeClient(null,
	[
	//    '--disk-cache-dir=/home/mykola/yaki/cache',
	//    '--user-data-dir=/home/mykola/yaki/data',
		'--window-size=1200,1100',
		//'--headless',
		'--disable-gpu',
	], [
		'port' => $port
	]);
	$browsers[$port] = ['instance' => $client, 'port' => $port];

	$purl = parse_url($url);
	
	$results = ['positive'=>[], 'negative'=>[]];
	require 'sites_schemas/' . $purl['host'] . '.php';


	$return = [
		'port'=> $port,
		'the_result' => false,
	] + ['markers'=>$results];

	if(count(@$return['markers']['positive']) == 0) {
		$return['the_result'] = false;
	} elseif(count(@$return['markers']['negative']) == 0 && count(@$return['markers']['positive']) > 0) 
		$return['the_result'] = false;
	
	if($take_screen) {
		$fname = $url;
		$fname = str_replace(':', '', $fname);
		$fname = 'screenshots/'. str_replace('/', '__', $fname) . '.png';
		$client->takeScreenshot($fname); // Yeah, screenshot!echo 'done';
		$return['screenshot'] = $fname;
	}

	unset($browsers[$port]);
	unset($client);

	return $return;
}


