<?php
//error_reporting(E_ALL);
 //ini_set("display_errors", 1);

function getDom($path) {
	$cred = sprintf('Authorization: Basic %s', base64_encode('tatartim:raFxegFj2TyV') );
	$opts = array(
			'http' => array(
				'user_agent' => 'PHP libxml agent',
				'method' => 'GET',
				'header' => $cred,
				)
		     );

	$context = stream_context_create($opts);
	libxml_set_streams_context($context);
	$doc = new DOMDocument();
	$doc->loadHTMLFile($path);
	$xPath = new DOMXPath($doc);
	$xPath->registerNamespace("xhtml", "http://www.w3.org/1999/xhtml");
	return $xPath;
}

$timematrix = array(
	'7:30',
	'8:15',
	'9:15',
	'10:00',
	'11:00',
	'11:45',
	'12:45',
	'13:30',
	'14:30',
	'15:15',
	'16:15',
	'17:00',
	'18:00',
	'18:45',
	'20:30'

	);
function printElements($elements) {
	if (!is_null($elements)) {
		foreach ($elements as $element) {
			echo  $element->nodeName. ": " . $element->nodeValue;
		}
	}
}

if (!$_GET['q']) {
	echo '<form><input type="text" name="q" /><input type="submit" /></form>';
}
if (PHP_SAPI == 'cli') $_GET['q'] = 'valenta';
if ($_GET['q']) {

	$url = "https://usermap.cvut.cz/search/?js=true&attrs=exchPersonalId&format=HTML&query=" . $_GET['q'];
	$Dom = getDom($url);
	$xPath = '//table[@id="profile"]/tbody/tr';
	$tr = $Dom->query($xPath);
	foreach ($tr as $element) {
		echo sprintf('<a href="/?username=%s">%s</a><br />', $element->getAttribute('id'),  $element->getElementsByTagName('td')->item(0)->nodeValue);
	}
}

if (PHP_SAPI == 'cli') $_GET['username'] = 'valenta';
if ( !$_GET['username'] ) {
	exit;
}
$url = 'https://usermap.cvut.cz/profile/valenta/';
$url = 'https://usermap.cvut.cz/profile/'.$_GET['username']; // valenta

$Dom = getDom($url);
$phone  = $Dom->query('//div[@id="prf"]/table[1]/tbody/tr[3]/td[2]')->item(0)->nodeValue;
$room = $Dom->query('//div[@id="prf"]/table[1]/tbody/tr[2]/td[2]/a')->item(0)->nodeValue;
$number = $Dom->query('//div[@id="prf"]/table[2]/tr[2]/td[2]')->item(0)->nodeValue;
$email = $Dom->query('//div[@id="prf"]/table[1]/tbody/tr[5]/td[2]/a[1]')->item(0)->nodeValue;
echo 'Contacts:', PHP_EOL;
echo $phone, PHP_EOL;
echo $room, PHP_EOL;
echo $email, PHP_EOL;
//echo $number, PHP_EOL;
$url = 'schedule.html';
$url = 'https://timetable.fit.cvut.cz/public/en/ucitele/'.
substr($number, 0, 2).'/'.
substr($number, 2, 2).'/u'.$number.'000.html';


$Dom = getDom($url);
$dayNumber = @date('N');
if ( $dayNumber > 5 ) {
	echo 'Today is not working day';
	exit;
}
$rooms = $Dom->query('//div[@id="content"]/table/tbody/tr['.$dayNumber.']/td');
echo 'Today at:', "\n";
$colspanSum = 0;
foreach ($rooms as $key => $room) {
	if ( $key == 0 ) continue;
	$span = $room->getAttribute('colspan');
	if ($room->getElementsByTagName('a')->item(1)->nodeValue) {
		echo $room->getElementsByTagName('a')->item(1)->nodeValue, ' ', $timematrix[$colspanSum], '-', $timematrix[$colspanSum+$span], "\n";
		$notAbsent = true;
	}
	$colspanSum += $span;
}
if ( is_null($notAbsent) ) {
	echo 'Today not in university';
	exit;
}

