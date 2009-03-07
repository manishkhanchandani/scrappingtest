<?php

class Yahoo {
	public $url;
	private $yahooBaseFiles = "files/yah/base";
	private $yahooFirstFiles = "files/yah/first";
	private $yahooOthersFiles = "files/yah/otherpages";
	private $yahooreviewFiles = "files/yah/reviewpages";
	
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
	
	
	public function crawlSearchPageImproved($province, $id) {
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
		
		//$regexp = "<div class='ytsVttl'><div class=\"textA\"><a href=\"(.*)\".*>(.*)<\/a><\/div>";
		$regexp = "<div class=\"textA\"><a href=\"(.*)\">(.*)<\/a><\/div>";
		
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
	
	public function getTotalReview($province, $id) {
		$file = $this->yahooFirstFiles."/".$province."/".$id.".html";
		$contents = file_get_contents($file);
		$reviewfound = false;
		require_once "domutilities.php";
		$DOMdoc = new DOMDocument();
		ob_start();
		@ $DOMdoc->loadHTML($contents);
		ob_end_clean();
		$data = $this->parseYelm($DOMdoc, 'div', 'class', 'rating', false);
		//var_dump($data);
		if (empty ($data)) {
			print ("<br>\n There are no reviews for the following hotel \n<br>");
		}
		$pattern = '/Read [0-9]* Reviews/';
		preg_match($pattern, $data[0], $matches);
		//var_dump($matches);
		
		if (!empty ($matches)) {
//			print_r($matches);
			$reviews_array = explode(' ',$matches[0]);
			//print_r($reviews_array);
			$totalreviews = $reviews_array[1]; 
			print($totalreviews);
			$reviewPageUrl = $this->getReviewpageLink($DOMdoc);
			print "<br><br>" . $reviewPageUrl . "<br><br>";
			$this->updatereviewfound($id,$totalreviews,$reviewPageUrl);
			$reviewfound = true;
			}else{
			$this->updatereviewfound($id,0,"");
			}
		
		 if($reviewfound){
			$dir = $this->yahooreviewFiles."/".$province;
			$filetosave = $dir."/".$id.".html";
			if(!is_dir($dir)) {
				mkdir($dir, 0777);
				chmod($dir, 0777);
			}
			if(!file_exists($filetosave)) {
				$contents = file_get_contents($reviewPageUrl);
				$fp = file_put_contents($filetosave, $contents);
			}
		 }
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


	private function parseYelm($DOMDoc, $tag, $attr, $val, $fullmatch = true) {
		global $nextpage;
		if ($DOMDoc->parentNode) //this is a node
			$DOMDoc = convertNodeToDOM($DOMDoc);

		$j = 0;
		$arrOut = array ();
		$nodes = $DOMDoc->getElementsByTagName($tag);
		//var_dump($nodes);
		for ($i = 0; $i < $nodes->length; $i++)
			if (($fullmatch && (checkAttValue($nodes->item($i), $attr) == $val)) || (!$fullmatch && (strpos(checkAttValue($nodes->item($i), $attr), $val) !== false))) {
				$arrOut[$j] = $nodes->item($i)->nodeValue;
				$j++;
			}

		if (count($arrOut) == 0)
			return NULL;
		return $arrOut;
	}

	private function getReviewpageLink($doc) {
		print"<pre>";
		$links = array ();
		foreach ($doc->getElementsByTagName('a') as $a) {
			$href = $a->getAttribute('href');
			//print"\n<br>";
			//print_r($href);
		//	$url = $this->get_absolute_url('http://travel.yahoo.com/', $href);
//			if ($href AND $url) {
			if ($href) {
				$pattern = '/p-reviews-[0-9]*-prod-travelguide-action-read-ratings_and_reviews-i/';
//				preg_match($pattern, $url, $matches);
				preg_match($pattern, $href, $matches);

				if (!empty ($matches)) {
					$url = $this->get_absolute_url('http://travel.yahoo.com/', $href);
					$urls[] = $url;
//					return ($url);
				}
			}
		}
		//print"<\pre>";
		//print_r($urls);
		//exit;
		if(empty($urls))
			return (false);
		else
			return ($urls[1]);
	}

	private function get_absolute_url($current_url, $path) {
		// path is already absolute
		if ($this->strbeginswith($path, 'http://') || $this->strbeginswith($path, 'https://'))
			return $path;
		// link is javascript, not valid
		if ($this->strbeginswith($path, 'javascript:'))
			return false;

		$parts = @ parse_url($current_url);
		if ($parts !== false) {

			// link is absolute to url
			if ($this->strbeginswith($path, '/'))
				return $parts['scheme'] . '://' . $parts['host'] . $path;
			// link is relative to path
			$folder = substr($parts['path'], 0, strrpos($parts['path'], '/') + 1);
			return $parts['scheme'] . '://' . $parts['host'] . $folder . $path;
		} else
			return false;
	}

	private function strbeginswith($haystack, $needle) {
		return substr($haystack, 0, strlen($needle)) == $needle;
	}

	public function updatereviewfound ($id, $totalreviews, $reviewurl) {
		$sql = "update us_xml_yahoo set reviewfound  = '".$totalreviews."', reviewurl = '".$this->clean($reviewurl)."' WHERE id = '".$id."'";
		echo $sql."<br>";
		mysql_query($sql) or die(mysql_error());
	}
	
	public function logic1($id, $province, $arrData, $pattern) {
		$file = $this->yahooBaseFiles."/".$province."/".$id.".html";
		$base_search_content = file_get_contents($file);
		if(eregi("Sorry we did not find", $base_search_content)){			
			$crawl = $this->crawlSearchPageMod($arrData, $pattern, $province, $id);
			if($crawl) return $crawl;
		} else {
			if(eregi("Travel Guides", $base_search_content)){					
				$dir = $this->yahooFirstFiles."/".$province;
				$file2 = $dir."/".$id.".html";			
				$regexp = "<div class=\"textA\"><a href=\"(.*)\">.*<\/a><\/div>";
				if(preg_match_all("/$regexp/siU", $base_search_content, $matches, PREG_SET_ORDER)) {					
					foreach($matches as $k=>$links) {	
						$arrTemp = explode('"',$links[1]);					
						$arr['url'][] = $arrTemp[0];	
						//echo "Searching: ".$arrTemp[0]."<br />";				
						//$result = $this->curlPage($arrTemp[0]);
						$result = file_get_contents($arrTemp[0]);
						if(eregi($pattern, $result) && eregi("Overview", $result)) {
							$fp = file_put_contents($file2, $result);
							$sql = "update us_xml_yahoo set firsturl = '".$this->clean($arrTemp[0])."', firsturlflag = 1, gotpoi = 1, flag_2nd = 1 WHERE id = '".$id."'";
							echo $sql;
							echo "<br>";							
							//mysql_query($sql) or die(mysql_error());
							return $arrTemp[0];
						} 			
					}					
				}					
			}		
		}
		$sql = "update us_xml_yahoo set flag_2nd = 1 WHERE id = '".$id."'";
		echo $sql;
		echo "<br>";
		//mysql_query($sql) or die(mysql_error());
		return false;
	}
	
	public function crawlSearchPageMod($arrData, $pattern, $province, $id) {
		$dir = $this->yahooFirstFiles."/".$province;
		$file = $dir."/".$id.".html";
		$new_search_url = "http://travel.yahoo.com/bin/search/travel;_ylt=?p=".urlencode($arrData['streetAddress1']." ".$arrData['city']);
		$new_contents = file_get_contents($new_search_url);
		if(eregi("Sorry we did not find", $new_contents)){
			return false;
		} else {				
			if(eregi("Travel Guides", $new_contents)){				
				$regexp = "<div class=\"textA\"><a href=\"(.*)\">.*<\/a><\/div>";
				if(preg_match_all("/$regexp/siU", $new_contents, $matches, PREG_SET_ORDER)) {					
					foreach($matches as $k=>$links) {	
						$arrTemp = explode('"',$links[1]);					
						$arr['url'][] = $arrTemp[0];						
						//$result = $this->curlPage($arrTemp[0]);
						$result = file_get_contents($arrTemp[0]);
						if(eregi($pattern, $result) && eregi("Overview", $result)) {
							$fp = file_put_contents($file, $result);
							$sql = "update us_xml_yahoo set firsturl = '".$this->clean($url)."', firsturlflag = 1, gotpoi = 1, baseurl = '".$this->clean($new_search_url)."', baseurlflag = 1, flag_2nd = 1 WHERE id = '".$id."'";
							echo $sql;
							echo "<br>";
							//mysql_query($sql) or die(mysql_error());
							return $arrTemp[0];
						} 			
					}					
				}					
			}							
		}
		return false;
	}
}
?>