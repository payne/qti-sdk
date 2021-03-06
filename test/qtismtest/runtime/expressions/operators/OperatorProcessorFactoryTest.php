<?php

namespace qtismtest\runtime\expressions\operators;

use qtism\common\datatypes\QtiInteger;
use qtism\common\datatypes\QtiString;
use qtism\common\enums\BaseType;
use qtism\runtime\common\OrderedContainer;
use qtism\runtime\expressions\operators\OperandsCollection;
use qtism\runtime\expressions\operators\OperatorProcessorFactory;
use qtismtest\QtiSmTestCase;

require_once(dirname(__FILE__) . '/custom/custom_operator_autoloader.php');

class OperatorProcessorFactoryTest extends QtiSmTestCase
{
    public function setUp()
    {
        parent::setUp();
        // register testing custom operators autoloader.
        spl_autoload_register('custom_operator_autoloader');
    }

    public function tearDown()
    {
        parent::tearDown();
        // unregister testing custom operators autoloader.
        spl_autoload_unregister('custom_operator_autoloader');
    }

    public function testCreateProcessor()
    {
        // get a fake sum expression.
        $expression = $this->createComponentFromXml(
            '<sum>
				<baseValue baseType="integer">2</baseValue>
				<baseValue baseType="integer">2</baseValue>
			</sum>'
        );

        $factory = new OperatorProcessorFactory();
        $operands = new OperandsCollection([new QtiInteger(2), new QtiInteger(2)]);
        $processor = $factory->createProcessor($expression, $operands);
        $this->assertInstanceOf('qtism\\runtime\\expressions\\operators\\SumProcessor', $processor);
        $this->assertEquals('sum', $processor->getExpression()->getQtiClassName());
        $this->assertEquals(4, $processor->process()->getValue()); // x)
    }

    public function testInvalidOperatorClass()
    {
        $expression = $this->createComponentFromXml('<baseValue baseType="string">String!</baseValue>');
        $factory = new OperatorProcessorFactory();

        $this->setExpectedException('\\InvalidArgumentException');
        $processor = $factory->createProcessor($expression);
    }

    public function testCustomOperator()
    {
        // Fake expression...
        $expression = $this->createComponentFromXml(
            '<customOperator xmlns:qtism="http://www.qtism.org/xsd/custom_operators/explode" class="org.qtism.test.Explode" qtism:delimiter="-">
	            <baseValue baseType="string">this-is-a-test</baseValue>
	        </customOperator>'
        );

        $factory = new OperatorProcessorFactory();
        $operands = new OperandsCollection([new QtiString('this-is-a-test')]);
        $processor = $factory->createProcessor($expression, $operands);
        $this->assertInstanceOf('org\\qtism\\test\\Explode', $processor);
        $this->assertEquals('customOperator', $processor->getExpression()->getQtiClassName());
        $this->assertTrue($processor->process()->equals(new OrderedContainer(BaseType::STRING, [new QtiString('this'), new QtiString('is'), new QtiString('a'), new QtiString('test')])));
    }

    public function testCustomOperatorWithoutClassAttribute()
    {
        // Fake expression...
        $expression = $this->createComponentFromXml(
            '<customOperator xmlns:qtism="http://www.qtism.org/xsd/custom_operators/explode" qtism:delimiter="-">
	            <baseValue baseType="string">this-is-a-test</baseValue>
	        </customOperator>'
        );

        $factory = new OperatorProcessorFactory();

        $this->setExpectedException(
            '\\RuntimeException',
            "Only custom operators with a 'class' attribute value can be processed."
        );

        $factory->createProcessor($expression);
    }

    public function testUnknownCustomOperator()
    {
        // Fake expression...
        $expression = $this->createComponentFromXml(
            '<customOperator xmlns:qtism="http://www.qtism.org/xsd/custom_operators/explode" class="org.qtism.test.Unknown" qtism:delimiter="-">
	            <baseValue baseType="string">this-is-a-test</baseValue>
	        </customOperator>'
        );

        $factory = new OperatorProcessorFactory();

        $this->setExpectedException(
            '\\RuntimeException',
            "No custom operator implementation found for class 'org.qtism.test.Unknown'"
        );

        $factory->createProcessor($expression);
    }
}
