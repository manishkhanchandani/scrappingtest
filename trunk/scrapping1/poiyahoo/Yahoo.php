<?php
class Yahoo {
	public $url;
	private $yahooBaseFiles = "files/yah/base";
	private $yahooFirstFiles = "files/yah/first";
	private $yahooOthersFiles = "files/yah/otherpages";
	
	public function regexp($regexp, $input) {
		if(preg_match_all("/$regexp/siU", $input, $matches, PREG_SET_ORDER)) {
			return $matches;
		} else {
			return array();
		}
	}
	
	public function getReviewUrl($url) {
		//$input = "http://travel.yahoo.com/p-travelguide-6613036-alberta_park_portland-i"
		$regexp = "http:\/\/travel.yahoo.com\/p\-(.*)\-(.*)\-.*\-i";
		$matches = $this->regexp($regexp, $url);
		$reviewUrl = "http://travel.yahoo.com/p-reviews-".$matches[0][2]."-prod-travelguide-action-read-ratings_and_reviews-i;_ylt=";
		return $reviewUrl;
	}
	
	public function crawlSearchPage($province, $id) {
		if(!$this->url) return false;
		$dir = $this->yahooBaseFiles."/".$province;
		$file = $dir."/".$id.".html";
		if(!is_dir($dir)) {
			mkdir($dir, 0777);
			chmod($dir, 0777);
		}
		if(file_exists($file)) {
			$result = file_get_contents($file);	
		} else {
			$result = $this->curlPage($this->url);
			$fp = file_put_contents($file, $result);
			$sql = "update us_xml_yahoo set baseurl = '".$this->clean($this->url)."', baseurlflag = 1 WHERE id = '".$id."'";
			echo $sql."<br>";
			mysql_query($sql) or die(mysql_error());	
		}	
		$regexp = "<div class='ytsVttl'><div class=\"textA\"><a href=\"(.*)\".*>(.*)<\/a><\/div>";
		$matches = $this->regexp($regexp, $result);
		if($matches) {
			foreach($matches as $k=>$links) {
				$arr[$k]['url'] = $links[1];
				$arr[$k]['text'] = strip_tags($links[2]);
			}
			return $arr;
		} else {
			return false;
		}
	}
	
	public function crawlFirstPage($province, $id, $urls=array(), $pattern="") {
		if(!$this->url) return false;
		$dir = $this->yahooFirstFiles."/".$province;
		$file = $dir."/".$id.".html";
		if(!is_dir($dir)) {
			mkdir($dir, 0777);
			chmod($dir, 0777);
		}
		if(file_exists($file)) {
			$result = file_get_contents($file);	
			if(eregi($pattern, $result)) {
				$fp = file_put_contents($file, $result);
				$sql = "select firsturl from us_xml_yahoo WHERE id = '".$id."'";
				$rs = mysql_query($sql) or die(mysql_error());
				$rec = mysql_fetch_array($rs);
				$file = $rec['firsturl'];
				return $file;
			} else {
				return false;
			}
		} else {
			if(!$urls) {
				return false;
			} else {
				foreach($urls as $url) {
					$result = $this->curlPage($url);
					if(eregi($pattern, $result)) {
						$fp = file_put_contents($file, $result);
						$sql = "update us_xml_yahoo set firsturl = '".$this->clean($url)."', firsturlflag = 1 WHERE id = '".$id."'";
						echo $sql."<br>";
						mysql_query($sql) or die(mysql_error());
						return $url;
					} else {
					
					}
				}
			}
		}	
		return false;
	}
	public function updateGotPoi($id, $gotPoi, $baseurl, $firsturl, $reviewurl) {
		$sql = "update us_xml_yahoo set gotpoi = '".$gotPoi."', flag = 1, reviewurl = '".$this->clean($reviewurl)."' WHERE id = '".$id."'";
		echo $sql."<br>";
		mysql_query($sql) or die(mysql_error());
	}
	private function clean($text) {
		$text = addslashes(stripslashes(trim($text)));
		return $text;
	}
	public function checkSearchResultUrl($url) {
		$result = $this->curlPage($this->url);
	}
	
	public function crawlReviewPage() {
	
	}
	
	public function crawlPaginatedPages() {
	
	}
	
	public function curlPage($url, $params="") {
		$ch = curl_init();    // initialize curl handle
		curl_setopt($ch, CURLOPT_URL,$url); // set url to post to
		curl_setopt($ch, CURLOPT_FAILONERROR, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);// allow redirects
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); // return into a variable
		curl_setopt($ch, CURLOPT_TIMEOUT, 3); // times out after 4s
		curl_setopt($ch, CURLOPT_POST, 1); // set POST method
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params); // add POST fields
		$result = curl_exec($ch); // run the whole process
		curl_close($ch);  
		return $result;
	}
}
?>