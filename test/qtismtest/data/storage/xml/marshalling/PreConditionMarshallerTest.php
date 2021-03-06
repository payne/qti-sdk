<?php

namespace qtismtest\data\storage\xml\marshalling;

use DOMDocument;
use qtism\common\enums\BaseType;
use qtism\data\expressions\BaseValue;
use qtism\data\rules\PreCondition;
use qtismtest\QtiSmTestCase;

class PreConditionMarshallerTest extends QtiSmTestCase
{
    public function testMarshall()
    {
        $component = new PreCondition(new BaseValue(BaseType::BOOLEAN, true));
        $marshaller = $this->getMarshallerFactory('2.1.0')->createMarshaller($component);
        $element = $marshaller->marshall($component);

        $this->assertInstanceOf('\\DOMElement', $element);
        $this->assertEquals('preCondition', $element->nodeName);
        $this->assertEquals('baseValue', $element->getElementsByTagName('baseValue')->item(0)->nodeName);
    }

    public function testUnmarshall()
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->loadXML(
            '
			<preCondition xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1">
				<baseValue baseType="boolean">true</baseValue>
			</preCondition>
			'
        );
        $element = $dom->documentElement;

        $marshaller = $this->getMarshallerFactory('2.1.0')->createMarshaller($element);
        $component = $marshaller->unmarshall($element);

        $this->assertInstanceOf('qtism\\data\\rules\\PreCondition', $component);
        $this->assertInstanceOf('qtism\\data\\expressions\\BaseValue', $component->getExpression());
    }
}
