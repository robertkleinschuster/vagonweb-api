<?php

declare(strict_types=1);

function text_content(DOMNode $node): string
{
    $text = '';

    foreach ($node->childNodes as $childNode) {
        if ($childNode->nodeType === XML_TEXT_NODE) {
            $text .= $childNode->nodeValue;
        }
    }

    return $text;
}