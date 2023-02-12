<?php

$client->request('GET', $url);


$crawler = $client->waitForVisibility('.jobsearch-DesktopStickyContainer');
$client->wait(1);
$crawler = $crawler->filter('.jobsearch-DesktopStickyContainer #applyButtonLinkContainer a');
if(count($crawler)) {
	foreach($crawler as $item) {
		$text = $item->getText();
		if(stripos($text, 'apply') !== false)
			$results['positive']['apply_button'] = $text;

	}
}

$crawler = $client->waitForVisibility('.jobsearch-DesktopStickyContainer');
$crawler = $crawler->filter('.jobsearch-DesktopStickyContainer');
if(count($crawler)) {
	$text = $crawler->text();
	if(stripos($text, 'expired') !== false)
		$results['negative']['expired_text'] = $text;
}
