<?php
//error_reporting(E_ALL);
 //ini_set("display_errors", 1);

function getDom($path) {
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
	print_r($tr);
		foreach ($tr as $element) {
			echo sprintf('<a href="/?username=%s">%s</a><br />', $element->getAttribute('id'),  $element->getElementsByTagName('td')->item(0)->nodeValue);
		}
	//printElements($tr);
}
exit;

$url = 'https://usermap.cvut.cz/profile/valenta/';
$url = 'profile.html';//'https://usermap.cvut.cz/profile/'.?_GET['username'];

$Dom = getDom($url);
$phone  = $Dom->xpath('//div[@id="prf"]/table[1]/tbody/tr[3]/td[2]');
$room = $Dom->xpath('//div[@id="prf"]/table[1]/tbody/tr[2]/td[2]/a');
$number = $Dom->xpath('//div[@id="prf"]/table[2]/tr[2]/td[2]');
$email = $Dom->xpath('//div[@id="prf"]/table[1]/tbody/tr[5]/td[2]/a[1]');
//print_r( $phone);
//print_r( $numb);
echo 'Contacts:', "\n";

echo $phone[0], "\n";
//echo $room[0], "\n";
echo $email[0], "\n";
//echo $number[0], "\n";
$number = $number[0];
$url = 'https://timetable.fit.cvut.cz/public/en/ucitele/'.
substr($number, 0, 2).'/'.
substr($number, 2, 2).'/u'.$number.'000.html';


$url = 'schedule.html';
$Dom = getDom($url);
$rooms = $Dom->xpath('//div[@id="content"]/table/tbody/tr[3]/td');
echo 'Today at:', "\n";

$colspanSum = 0;
foreach ($rooms as $key => $room) {
	if ( $key == 0 ) continue;
	$span = $room->attributes()->colspan;
	//echo $room->attributes()->colspan, ".\n";
	if ($room->a[1][0]) {
		echo $room->a[1][0], ' ', $timematrix[$colspanSum], '-', $timematrix[$colspanSum+$span], "\n";
	}
	$colspanSum += $span;
}
//echo $rooms[0];
