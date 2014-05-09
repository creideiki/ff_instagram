<?php

include 'RSSGenerator.php';
include 'Insta_func.php';

class ff_Instagram extends Plugin
{
	function about() {
		return array(
			1.0, // version
			'Gnerates feeds from Instagram URLs', // description
			'feader', // author
			false, // is_system
		);
	}

	function api_version() {
		return 2;
	}

	function init($host) {
		$host->add_hook($host::HOOK_FETCH_FEED, $this);
	}

	function hook_fetch_feed($feed_data, $fetch_url) {
		if(preg_match('%^http://instagram.com/\w+$%i',  $fetch_url) !== 1 || $feed_data)
			return $feed_data;

		$json = Insta\extract_Insta_JSON($fetch_url);
		#var_dump($json);
		$feed = new RSSGenerator\Feed();
		$feed->link($fetch_url);
		$feed->title(sprintf("%s / Instagram", Insta\get_Insta_username($json)));

		foreach(Insta\get_Insta_user_data($json) as $post) {
			/*unset($post["comments"]);
			unset($post["likes"]);
			var_dump($post);
			*/
			$item = Insta\convert_Insta_data_to_RSS($post);
			#var_dump($item);
			$feed->new_item($item);
		}

		return $feed->saveXML();
	}

}
?>