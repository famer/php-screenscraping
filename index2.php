<?php
error_reporting(E_ALL);
 ini_set("display_errors", 1);

function getDom($path) {
  $tidy = tidy_parse_file($path, array("numeric-entities" => true, "output-xhtml" => true), 'utf8');
  $tidy->cleanRepair();
  $xhtml = (string) $tidy;
$doc = new DOMDocument();
$doc->loadHTMLFile($path);
$xhtml =  $doc->saveHTML();
  //$xhtml = file_get_contents($path);
  $dom = simplexml_load_string($xhtml);
  $dom->registerXPathNamespace("xhtml", "http://www.w3.org/1999/xhtml");
  return $dom;
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

if (!$_GET['q']) {
	echo '<form><input type="text" name="q" /><input type="submit" /></form>';
}
$_GET['q'] = 'valenta';
if ($_GET['q']) {

	$url = "https://usermap.cvut.cz/search/?js=true&attrs=exchPersonalId&format=HTML&query=" . $_GET['q'];
	$Dom = getDom($url);
	$xPath = '//xhtml:*[@id="profile"]/xhtml:tbody/xhtml:tr';
	$tr = $Dom->xpath($xPath);
	echo $tr;
	print_r($tr);
}
exit;

$url = 'https://usermap.cvut.cz/profile/valenta/';
$url = 'profile.html';//'https://usermap.cvut.cz/profile/'.?_GET['username'];

$Dom = getDom($url);
$phone  = $Dom->xpath('//xhtml:div[@id="prf"]/xhtml:table[1]/xhtml:tbody/xhtml:tr[3]/xhtml:td[2]');
$room = $Dom->xpath('//xhtml:div[@id="prf"]/xhtml:table[1]/xhtml:tbody/xhtml:tr[2]/xhtml:td[2]/xhtml:a');
$number = $Dom->xpath('//xhtml:div[@id="prf"]/xhtml:table[2]/xhtml:tr[2]/xhtml:td[2]');
$email = $Dom->xpath('//xhtml:div[@id="prf"]/xhtml:table[1]/xhtml:tbody/xhtml:tr[5]/xhtml:td[2]/xhtml:a[1]');
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
$rooms = $Dom->xpath('//xhtml:div[@id="content"]/xhtml:table/xhtml:tbody/xhtml:tr[3]/xhtml:td');
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