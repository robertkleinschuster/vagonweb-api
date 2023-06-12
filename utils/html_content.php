<?php

declare(strict_types=1);

function html_content(DOMNode $node): string
{
    $doc = new DOMDocument();
    $doc->appendChild($doc->importNode($node, true));

    $html = '';
    foreach ($doc->childNodes as $childNode) {
        $html .= $doc->saveHTML($childNode);
    }

    return $html;
}