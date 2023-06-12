<?php

declare(strict_types=1);

function selector(string $selector): string {
    $xpathSelector = str_replace(
        [' > ', ' ', ','],
        ['/', '//', '|'],
        $selector
    );

    $xpathSelector = preg_replace_callback('/\[([^]]+)]/', function(array $matches) {
        return "[@" . $matches[1] . "]";
    }, $xpathSelector);

    $xpathSelector = preg_replace_callback('/:not\((.*?)\)/', function(array $matches) {
        $innerSelector = selector($matches[1]);
        return "[not($innerSelector)]";
    }, $xpathSelector);

    $xpathSelector = str_replace(':first-child', '[1]', $xpathSelector);

    $xpathSelector = preg_replace_callback('/\.([\w-]+)/', function(array $matches) {
        $className = $matches[1];
        return "[contains(concat(' ', normalize-space(@class), ' '), ' $className ')]";
    }, $xpathSelector);

    return preg_replace_callback('/#([\w-]+)/', function(array $matches) {
        $id = $matches[1];
        return "[@id='$id']";
    }, $xpathSelector);
}


