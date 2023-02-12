<?php

$client->request('GET', $url);

$crawler = $client->waitForVisibility('#sidebar');
$crawler = $crawler->filter('#sidebar .button-panel #singlejob-checkresume');
if(count($crawler))
	$results['positive']['apply_button'] = $crawler->text();

$crawler = $client->waitForVisibility('#singlejob');
$crawler = $crawler->filter('#singlejob .job-description .closed');
if(count($crawler))
	$results['negative']['closed_label'] = $crawler->text();