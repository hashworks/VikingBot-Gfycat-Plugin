<?php

class gfycatPlugin extends basePlugin {

	/**
	 * Called when messages are posted on the channel
	 * the bot are in, or when somebody talks to it
	 *
	 * @param string $from
	 * @param string $channel
	 * @param string $msg
	 */
	public function onMessage($from, $channel, $msg) {
		preg_match_all("/http[s]{0,1}:\/\/\S*\.gif\S*/i", $msg, $matches);
		$matches = array_splice($matches[0], 0, 5);
		$webmUrls = array();
		foreach ($matches as $link) {
			$webmUrl = $this->getGfycatWebM($link);
			if ($webmUrl !== false) {
				$webmUrls[] = $webmUrl;
			}
		}
		$string = join(' ', $webmUrls);
		if (!empty($string)) {
			$this->sendMessage($channel, "[GIF to WebM] " . $string);
		}
	}

	private function getGfycatWebM($giflink) {
		$data = @file_get_contents('http://upload.gfycat.com/transcode?fetchUrl=' . $giflink);
		if (!empty($data) && ($data = json_decode($data, true)) !== NULL) {
			if (isset($data['webmUrl']) && !empty(trim($data['webmUrl']))) {
				return trim($data['webmUrl']);
			}
		}
		return false;
	}

	/**
	 * @param string $to
	 * @param string $msg
	 * @param string|array $highlight = NULL
	 */
	private function sendMessage($to, $msg, $highlight = NULL) {
		if ($highlight !== NULL) {
			if (is_array($highlight)) {
				$highlight = join(", ", $highlight);
			}
			$msg = $highlight . ": " . $msg;
		}
		sendMessage($this->socket, $to, $msg);
	}
}