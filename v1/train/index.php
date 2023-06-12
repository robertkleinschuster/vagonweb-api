<?php

declare(strict_types=1);

require_once '../../utils/text_content.php';
require_once '../../utils/html_content.php';
require_once '../../utils/selector.php';

ini_set('display_errors', 1);

$nr = $_GET['nr'];
$operator = $_GET['operator'];
$year = date('Y');

$url = "https://www.vagonweb.cz/razeni/vlak.php?zeme={$operator}&cislo={$nr}&rok=$year&lang=de";

header('content-type: application/json');
header('Access-Control-Allow-Origin: *');

$response = [];

$dom = new DOMDocument();
$dom->loadHTMLFile($url, LIBXML_NOWARNING | LIBXML_NOERROR);

$finder = new DOMXPath($dom);
$nodes = $finder->query('//*' . selector('.trasa'));
if ($nodes && $nodes->length > 0) {
    $node = $nodes->item(0);
    $response['route'] = text_content($node);
}

$nodes = $finder->query('//*' . selector('.omezeni_bord div'));
if ($nodes && $nodes->length > 0) {
    $node = $nodes->item(0);
    $response['info'] = html_content($node);
}

$nodes = $finder->query('//*' . selector('.pikto'));
if ($nodes && $nodes->length > 0) {
    $badges = [];
    /** @var DOMNode $node */
    foreach ($nodes as $node) {
        $badges[] = [
            'src' => $node->attributes->getNamedItem('src')?->nodeValue,
            'title' => $node->attributes->getNamedItem('title')?->nodeValue,
        ];
    }
    $response['badges'] = array_unique($badges, SORT_ASC);
    sort($response['badges']);
}

$nodes = $finder->query('//*' . selector('#planovane_razeni table'));
$nodes = $finder->query('.//*' . selector('.vagonek img'), $nodes->item(0));
if ($nodes && $nodes->length > 0) {
    $carriages = [];
    /** @var DOMNode $node */
    foreach ($nodes as $node) {
        $carriages[] = [
            'src' => 'https://www.vagonweb.cz/' . $node->attributes->getNamedItem('src')?->nodeValue,
            'width' => $node->attributes->getNamedItem('width')?->nodeValue,
            'height' => $node->attributes->getNamedItem('height')?->nodeValue,
        ];
    }
    $response['carriages'] = $carriages;

}

$nodes = $finder->query('//*' . selector('.rad a'));
if ($nodes && $nodes->length > 0) {
    $links = [];
    /** @var DOMNode $node */
    foreach ($nodes as $node) {
        $links[] = [
            'href' => $node->attributes->getNamedItem('href')?->nodeValue,
            'name' => $node->textContent,
        ];
    }
    $response['links'] = $links;
}

echo json_encode($response);
