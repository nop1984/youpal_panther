<?php

$client->request('GET', $url);

$crawler = $client->waitForVisibility('.public-layout__content-wrapper');
$crawler = $crawler->filter('.public-layout__content-wrapper a[type="button"]');
if(count($crawler))
	$results['positive']['apply_button'] = $crawler->text();

$crawler = $client->waitForVisibility('.public-layout__content-wrapper');
$crawler = $crawler->filter('.public-layout__content-wrapper span[data-testid="message-box-text"]');
if(count($crawler))
	$results['negative']['closed_label'] = $crawler->text();