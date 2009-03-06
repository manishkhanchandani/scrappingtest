<?php
set_time_limit(0); //execution timeout for script
////////////////////////////////////////////////////////////
////////////Dom Utilities///////////////////////////////////
////////////You can do whatever you wish with this code.////
////////////////////////////////////////////////////////////

function get_include_contents($filename,$method='GET',$postdata='',&$responseheaders='nothing', $requestheaders='', $user_agent='Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.8.0.5) Gecko/20060719 Firefox/1.5.0.5') {
	//$method can be GET or POST.
	//if $method is POST, then $postdata is an array of the form:
	//array(
    //   'var1' => 'some content',
    //   'var2' => 'doh'
   	// )
   	//in both cases we pretend to be an ordinary browser of type $user_agent
   	//the variable $responseheaders is used to:
   	//1. add additional headers on the input direction - receives a string of the form: var1=val1&var2=val2...\r\nvarx=valx&varx+1=valx+1...
   	//2. to return the response headers on the output direction - returns indexed array
   	if($method=='GET') {
   		$a = array(
					'method'  => 'GET',
					'user_agent' => $user_agent); //change this to whatever you want
		if($requestheaders!='') $a['header'] = $requestheaders . "\r\n";
   		$opts = array('http' => $a);

		$context  = stream_context_create($opts);
		$s = @file_get_contents($filename, false, $context);
		if($responseheaders!='nothing') $responseheaders=$http_response_header;
		return $s;
   		}
   	if($method=='POST') {
   		$pd = http_build_query($postdata);
   		$h = 'Content-type: application/x-www-form-urlencoded';
   		if($requestheaders!='') $h .= "\r\n" . $requestheaders . "\r\n";
   		$opts = array(
   				'http' => array(
			   		'method'  => 'POST',
			   	    'header'  => $h,
			   	    'content' => $pd,
			   	    'user_agent' => $user_agent)); //change this to whatever you want
		
		$context  = stream_context_create($opts);
		$s = @file_get_contents($filename, false, $context);
		if($responseheaders!='nothing') $responseheaders=$http_response_header;
		return $s;
   		}
   return false;
}

function convertNodeToDOM($domNode) {
	//this function receives a node and converts it to Dom document
	$doc = new DOMDocument();
	$doc->appendChild($doc->importNode($domNode,true));
	return $doc;
	}

function getNodeBetweenBreaks($domNode,$lineNumber) {
	//This function recieves a node in who's upper level, there are zero or more
	//<br> tags (for example, a text). It rerutns a new DOMDocument object
	//that consists of all the nodes in line number $lineNumber (i.e. located
	//between <br> tags $lineNumber-1 and $lineNumber.
	//$lineNumber starts from 1
	$doc = new DOMDocument();
	$curLineNum = 1;
	$curChildNum = 0;
	while(($curLineNum<$lineNumber) && ($curChildNum<$domNode->childNodes->length)) {
		if($domNode->childNodes->item($curChildNum)->nodeName=='br')
			$curLineNum++;
		$curChildNum++;
		}
	while(($curChildNum<$domNode->childNodes->length) && ($domNode->childNodes->item($curChildNum)->nodeName!='br')) {
		$doc->appendChild($doc->importNode($domNode->childNodes->item($curChildNum),true));
		$curChildNum++;
		}
	return $doc;
	}

function getAttributesArray($domNode) {
	//returns an associative array of attributes of the given domNode
	$attrArray = false;
	$attr = $domNode->attributes;
	for($i=0;$i<$attr->length;$i++)
		$attrArray[$attr->item($i)->nodeName] = $attr->item($i)->nodeValue;
	return $attrArray;
}


function getDeepNode($domNode,$strHierarchy) {
	//this function "digs" into the hierarchy of the given node
	//and fetches the node according to the given spec.
	//strHierarchy spec: childNumber:childType;childNumber:childtype;...
	//child number: starts from 1
	//child type: for example: a, td, tr, * (for any) this is case insensitive
	//if an error occurs, returns false

	$hArray = explode(";", strtolower($strHierarchy));
	$curNode = $domNode;
	foreach($hArray as $curLevel) {
		$ntArray = explode(":", $curLevel);
		$j = 0;
		for($i=0;$i<$curNode->childNodes->length && $j<$ntArray[0]; $i++)
			if($ntArray[1]=='*' || strtolower($curNode->childNodes->item($i)->nodeName)==$ntArray[1]) $j++;
		if($j<$ntArray[0])
			return false;
		else
			$curNode = $curNode->childNodes->item($i-1);
		}
	return $curNode;
}

function findRelativeSiblingOfType($refNode,$destinationNodeType,$siblingNumber, $attrParams = array()) {
	//this function navigates in a constant depth in the document hierarchy
	//example: if $node is a given 'a' node,
	//findRelativeSiblingOfType($node,'table',3, array('attr'=>'border','val'=>'1','fullmatch'=>true))
	//will return the third table with attribute border="1" in the same depth level as our
	//'a' node
	//
	//if siblingNumber is positive, goes forward, negative - goes backwards
	//returns false if fails
	//
	//the optional parameter $attrParams, gives the possibility to count and find
	//only nodes with specific attribute and value of it.
	//this parameter is of the structure: array('attr'=>'something','val'=>'something2','fullmatch'=>true or false)
	//the behavior of this parameter is documented in the function "findElementWithTagAttrValue" below
	$dt = strtolower($destinationNodeType);
	$ans = $refNode;
	$i = 0;
	if(count($attrParams)>0) {
		$attCondExists=true;
		$attr = $attrParams['attr'];
		$val = $attrParams['val'];
		$fullmatch = $attrParams['fullmatch'];
		}
	else
		$attCondExists=false;
	if($siblingNumber<0) {
		while(($i>$siblingNumber) && ($ans = $ans->previousSibling))
			if((strtolower($ans->nodeName)==$dt) && (!$attCondExists || (($fullmatch && (checkAttValue($ans,$attr)==$val)) || (!$fullmatch && (strpos(checkAttValue($ans,$attr),$val)!==false)))))
				$i--;
		if($i>$siblingNumber) return false;
		}
	else {
		while(($i<$siblingNumber) && ($ans = $ans->nextSibling))
			if((strtolower($ans->nodeName)==$dt) && (!$attCondExists || (($fullmatch && (checkAttValue($ans,$attr)==$val)) || (!$fullmatch && (strpos(checkAttValue($ans,$attr),$val)!==false)))))
				$i++;
		if($i<$siblingNumber) return false;
		}
	return $ans;
	}


function findSiblingOfTypeFromAbove($refNode,$destinationNodeType,$siblingNumber=1, $attrParams = array()) {
	//returns false if fails
	//
	//this function operates similar to the previous on, only this time the navigation is vertical
	//
	//the optional parameter $attrParams, gives the possibility to count and find
	//only nodes with specific attribute and value of it.
	//this parameter is of the structure: array('attr'=>'something','val'=>'something2','fullmatch'=>true or false)
	//the behavior of this parameter is documented in the function "findElementWithTagAttrValue" below
	$dt = strtolower($destinationNodeType);
	$ans = $refNode;
	$i = 0;
	if(count($attrParams)>0) {
		$attCondExists=true;
		$attr = $attrParams['attr'];
		$val = $attrParams['val'];
		$fullmatch = $attrParams['fullmatch'];
		}
	else
		$attCondExists=false;
	while(($i<$siblingNumber) && ($ans = $ans->parentNode))
		if((strtolower($ans->nodeName)==$dt) && (!$attCondExists || (($fullmatch && (checkAttValue($ans,$attr)==$val)) || (!$fullmatch && (strpos(checkAttValue($ans,$attr),$val)!==false)))))
			$i++;
	if($i<$siblingNumber) return false;
	return $ans;
	}


function strongTrim($str) {
	if(is_object($str)) //this is a node
		$str = $str->textContent;
	return trim(str_replace(chr(194) . chr(160)," ",$str));
	}

function checkAttValue($node,$strAtt) {
	//returns the value of this attribute
	//in the given node. if does not exist,
	//returns NULL
	$attArr = getAttributesArray($node);
	if(!is_array($attArr) || !array_key_exists($strAtt, $attArr))
		return NULL;
	return $attArr[$strAtt];
	}

function findElementWithTagAttrValue($DOMDoc, $tag, $attr, $val, $fullmatch=true) {
	//finds an element with the given tag name, with attribute attr, and value val
	//by default, searches for the element in which attr exactly matches val.
	//if fullmatch is set to false, then val can be any substring of attr.
	//note, this function does not necessarily return the first element with this
	//property

	//the function can receive also a node as argument for $DOMDoc
	if($DOMDoc->parentNode) //this is a node
		$DOMDoc = convertNodeToDOM($DOMDoc);

	$nodes = $DOMDoc->getElementsByTagName($tag);
	for($i=0;$i<$nodes->length;$i++)
		if(($fullmatch && (checkAttValue($nodes->item($i),$attr)==$val)) || (!$fullmatch && (strpos(checkAttValue($nodes->item($i),$attr),$val)!==false)))
			return $nodes->item($i);
	return NULL;
	}

function findElementsArrayWithTagAttrValue($DOMDoc, $tag, $attr, $val, $fullmatch=true) {
	//returns an array of elements with the given tag name, with attribute attr, and value val
	//by default, searches for elements in which attr exactly matches val.
	//if fullmatch is set to false, then val can be any substring of attr.

	//the function can receive also a node as argument for $DOMDoc
	//if($DOMDoc->parentNode) //this is a node
		$DOMDoc = convertNodeToDOM($DOMDoc);

	$j = 0;
	$arrOut = array();
	$nodes = $DOMDoc->getElementsByTagName($tag);
	for($i=0;$i<$nodes->length;$i++)
		if(($fullmatch && (checkAttValue($nodes->item($i),$attr)==$val)) || (!$fullmatch && (strpos(checkAttValue($nodes->item($i),$attr),$val)!==false))) {
			$arrOut[$j] = $nodes->item($i);			
			$j++;			
			}
			
	if(count($arrOut)==0)
		return NULL;
	return $arrOut;
}



function findElementWithTagTextContent($DOMDoc, $tag, $textContent, $fullmatch=true) {
	//finds an element with the given tagname and given text contents
	//all data is trimmed
	//if fullmatch is set to false, then textContent can be any substring of the text contents of the node.
	//note that only the text of the most upper level of the node, participates in the comparison
	//note, this function does not necessarily return the first element with this
	//property
	//the function can receive also a node as argument for $DOMDoc
	if($DOMDoc->parentNode) //this is a node
		$DOMDoc = convertNodeToDOM($DOMDoc);

	$nodes = $DOMDoc->getElementsByTagName($tag);
	for($i=0;$i<$nodes->length;$i++) {
		//echo $i . '. ' . getTextOfNode($nodes->item($i)) . '   ' . strpos(strongTrim(getTextOfNode($nodes->item($i))),$textContent) . "\r\n\r\n\r\n";
		if(($fullmatch && (strongTrim(getTextOfNode($nodes->item($i)))==$textContent)) || (!$fullmatch && (strpos(strongTrim(getTextOfNode($nodes->item($i))),$textContent)!==false)))
			return $nodes->item($i);
		}
	return NULL;
	}

function findElementsArrayWithTagTextContent($DOMDoc, $tag, $textContent, $fullmatch=true) {
	//returns an array of elements with the given tagname and given text contents
	//all data is trimmed
	//if fullmatch is set to false, then textContent can be any substring of the text contents of the node.
	//note that only the text of the most upper level of the node, participates in the comparison
	//the function can receive also a node as argument for $DOMDoc
	if($DOMDoc->parentNode) //this is a node
		$DOMDoc = convertNodeToDOM($DOMDoc);

	$j = 0;
	$arrOut = array();
	$nodes = $DOMDoc->getElementsByTagName($tag);
	for($i=0;$i<$nodes->length;$i++)
		if(($fullmatch && (strongTrim(getTextOfNode($nodes->item($i)))==$textContent)) || (!$fullmatch && (strpos(strongTrim(getTextOfNode($nodes->item($i))),$textContent)!==false))) {
			$arrOut[$j] = $nodes->item($i);
			$j++;
			}
	if(count($arrOut)==0)
		return NULL;
	return $arrOut;
	}


function findElementWithTagAttValueTextContent($DOMDoc, $tag, $attr, $val, $attrfullmatch, $textContent, $textfullmatch) {
	//finds an element with the given tagname, given text contents, and given attribute value
	//all data is trimmed
	//the function can receive also a node as argument for $DOMDoc
	if($DOMDoc->parentNode) //this is a node
		$DOMDoc = convertNodeToDOM($DOMDoc);

	$nodes = $DOMDoc->getElementsByTagName($tag);
	for($i=0;$i<$nodes->length;$i++)
		if((($attrfullmatch && (checkAttValue($nodes->item($i),$attr)==$val)) || (!$attrfullmatch && (strpos(checkAttValue($nodes->item($i),$attr),$val)!==false))) && (($textfullmatch && (strongTrim(getTextOfNode($nodes->item($i)))==$textContent)) || (!$textfullmatch && (strpos(strongTrim(getTextOfNode($nodes->item($i))),$textContent)!==false))))
			return $nodes->item($i);
	return NULL;
	}

function findElementsArrayWithTagAttValueTextContent($DOMDoc, $tag, $attr, $val, $attrfullmatch, $textContent, $textfullmatch) {
	//returns an array of elements with the given tagname, given text contents, and given attribute value
	//all data is trimmed
	//the function can receive also a node as argument for $DOMDoc
	if($DOMDoc->parentNode) //this is a node
		$DOMDoc = convertNodeToDOM($DOMDoc);

	$j = 0;
	$arrOut = array();
	$nodes = $DOMDoc->getElementsByTagName($tag);
	for($i=0;$i<$nodes->length;$i++)
		if((($attrfullmatch && (checkAttValue($nodes->item($i),$attr)==$val)) || (!$attrfullmatch && (strpos(checkAttValue($nodes->item($i),$attr),$val)!==false))) && (($textfullmatch && (strongTrim(getTextOfNode($nodes->item($i)))==$textContent)) || (!$textfullmatch && (strpos(strongTrim(getTextOfNode($nodes->item($i))),$textContent)!==false)))) {
			$arrOut[$j] = $nodes->item($i);
			$j++;
			}
	if(count($arrOut)==0)
		return NULL;
	return $arrOut;
	}

function createCookiesStr($headersArray) {
	//receives a raw headers array that is returned by the variable http_response_header
	//and returns a string of the form: "Cookie: aaa=bbb&ccc=ddd..."
	$ans = '';
	for($i=0;$i<count($headersArray);$i++) {
		if(substr($headersArray[$i],0,12)=='Set-Cookie: ') {
			$j = strpos($headersArray[$i],';');
			$ans .= '&' . substr($headersArray[$i],12,$j-12);
			}
		}
	if($ans == '') return '';
	return 'Cookie: ' . substr($ans,1);
	}

function getTextOfNode($node) {
	//returns the text contents of a node without it's subnodes  '
	if(is_object($node) && ($node->parentNode)) { 	 //check weather this is a node at all
		if(!($node->hasChildNodes())) return '';
		$res = '';
		$coll = $node->childNodes;
		for($i=0;$i<$coll->length;$i++)
			if($coll->item($i)->nodeName=='#text') $res .= $coll->item($i)->textContent;
		return $res;
		}
	return '';
	}
?>