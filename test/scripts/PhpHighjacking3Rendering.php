<?php

/**
 * I don't even understand how it is correctly parsed...
 * But it's safe!
 */

use qtism\data\storage\xml\XmlDocument;
use qtism\runtime\rendering\markup\xhtml\XhtmlRenderingEngine;

require_once(dirname(__FILE__) . '/../../vendor/autoload.php');

$doc = new XmlDocument();
$doc->load(dirname(__FILE__) . '/../samples/rendering/php_highjacking_3.xml', true);

$renderer = new XhtmlRenderingEngine();
$rendering = $renderer->render($doc->getDocumentComponent());
$rendering->formatOutput = true;

echo $rendering->saveXML();
