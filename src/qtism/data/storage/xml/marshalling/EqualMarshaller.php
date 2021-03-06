<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 *
 * Copyright (c) 2013-2020 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 * @license GPLv2
 */

namespace qtism\data\storage\xml\marshalling;

use DOMElement;
use qtism\common\utils\Format;
use qtism\data\expressions\operators\Equal;
use qtism\data\expressions\operators\ToleranceMode;
use qtism\data\QtiComponent;
use qtism\data\QtiComponentCollection;

/**
 * A complex Operator marshaller focusing on the marshalling/unmarshalling process
 * of equal QTI operators.
 */
class EqualMarshaller extends OperatorMarshaller
{
    /**
     * @see \qtism\data\storage\xml\marshalling\OperatorMarshaller::marshallChildrenKnown()
     */
    protected function marshallChildrenKnown(QtiComponent $component, array $elements)
    {
        $element = self::getDOMCradle()->createElement($component->getQtiClassName());
        $this->setDOMElementAttribute($element, 'toleranceMode', ToleranceMode::getNameByConstant($component->getToleranceMode()));

        $tolerance = $component->getTolerance();
        if (!empty($tolerance)) {
            $this->setDOMElementAttribute($element, 'tolerance', implode("\x20", $tolerance));
        }

        if ($component->doesIncludeLowerBound() === false) {
            $this->setDOMElementAttribute($element, 'includeLowerBound', false);
        }

        if ($component->doesIncludeUpperBound() === false) {
            $this->setDOMElementAttribute($element, 'includeUpperBound', false);
        }

        foreach ($elements as $elt) {
            $element->appendChild($elt);
        }

        return $element;
    }

    /**
     * @see \qtism\data\storage\xml\marshalling\OperatorMarshaller::unmarshallChildrenKnown()
     */
    protected function unmarshallChildrenKnown(DOMElement $element, QtiComponentCollection $children)
    {
        $object = new Equal($children);

        if (($toleranceMode = $this->getDOMElementAttributeAs($element, 'toleranceMode')) !== null) {
            $toleranceMode = ToleranceMode::getConstantByName($toleranceMode);
            $object->setToleranceMode($toleranceMode);
        }

        if (($tolerance = $this->getDOMElementAttributeAs($element, 'tolerance')) !== null) {
            $tolerance = explode("\x20", $tolerance);

            if (count($tolerance) < 1) {
                $msg = "No 'tolerance' could be extracted from element '" . $element->localName . "'.";
                throw new UnmarshallingException($msg, $element);
            } elseif (count($tolerance) > 2) {
                $msg = "'tolerance' attribute not correctly formatted in element '" . $element->localName . "'.";
                throw new UnmarshallingException($msg, $element);
            } else {
                $finalTolerance = [];
                foreach ($tolerance as $t) {
                    $finalTolerance[] = (Format::isFloat($t)) ? floatval($t) : $t;
                }

                $object->setTolerance($finalTolerance);
            }
        }

        if (($includeLowerBound = $this->getDOMElementAttributeAs($element, 'includeLowerBound', 'boolean')) !== null) {
            $object->setIncludeLowerBound($includeLowerBound);
        }

        if (($includeUpperBound = $this->getDOMElementAttributeAs($element, 'includeUpperBound', 'boolean')) !== null) {
            $object->setIncludeUpperBound($includeUpperBound);
        }

        return $object;
    }
}
