<?php

namespace qtismtest\runtime\expressions\operators;

use qtism\common\datatypes\QtiBoolean;
use qtism\common\datatypes\QtiFloat;
use qtism\common\datatypes\QtiInteger;
use qtism\common\datatypes\QtiPoint;
use qtism\runtime\common\RecordContainer;
use qtism\runtime\expressions\operators\GtProcessor;
use qtism\runtime\expressions\operators\OperandsCollection;
use qtismtest\QtiSmTestCase;

class GtProcessorTest extends QtiSmTestCase
{
    public function testGt()
    {
        $expression = $this->createFakeExpression();
        $operands = new OperandsCollection();
        $operands[] = new QtiInteger(1);
        $operands[] = new QtiFloat(0.5);
        $processor = new GtProcessor($expression, $operands);
        $result = $processor->process();
        $this->assertInstanceOf('qtism\\common\\datatypes\\QtiBoolean', $result);
        $this->assertTrue($result->getValue());

        $operands->reset();
        $operands[] = new QtiFloat(0.5);
        $operands[] = new QtiInteger(1);
        $result = $processor->process();
        $this->assertInstanceOf('qtism\\common\\datatypes\\QtiBoolean', $result);
        $this->assertFalse($result->getValue());

        $operands->reset();
        $operands[] = new QtiInteger(1);
        $operands[] = new QtiInteger(1);
        $result = $processor->process();
        $this->assertInstanceOf('qtism\\common\\datatypes\\QtiBoolean', $result);
        $this->assertFalse($result->getValue());
    }

    public function testNull()
    {
        $expression = $this->createFakeExpression();
        $operands = new OperandsCollection();
        $operands[] = new QtiInteger(1);
        $operands[] = null;
        $processor = new GtProcessor($expression, $operands);
        $result = $processor->process();
        $this->assertSame(null, $result);
    }

    public function testWrongBaseTypeOne()
    {
        $expression = $this->createFakeExpression();
        $operands = new OperandsCollection();
        $operands[] = new QtiInteger(1);
        $operands[] = new QtiBoolean(true);
        $processor = new GtProcessor($expression, $operands);
        $this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
        $result = $processor->process();
    }

    public function testWrongBaseTypeTwo()
    {
        $expression = $this->createFakeExpression();
        $operands = new OperandsCollection();
        $operands[] = new QtiPoint(1, 2);
        $operands[] = new QtiInteger(2);
        $processor = new GtProcessor($expression, $operands);
        $this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
        $result = $processor->process();
    }

    public function testWrongCardinality()
    {
        $expression = $this->createFakeExpression();
        $operands = new OperandsCollection();
        $operands[] = new RecordContainer(['A' => new QtiInteger(1)]);
        $operands[] = new QtiInteger(2);
        $processor = new GtProcessor($expression, $operands);
        $this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
        $result = $processor->process();
    }

    public function testNotEnoughOperands()
    {
        $expression = $this->createFakeExpression();
        $operands = new OperandsCollection();
        $this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
        $processor = new GtProcessor($expression, $operands);
    }

    public function testTooMuchOperands()
    {
        $expression = $this->createFakeExpression();
        $operands = new OperandsCollection([new QtiInteger(1), new QtiInteger(2), new QtiInteger(3)]);
        $this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
        $processor = new GtProcessor($expression, $operands);
    }

    public function createFakeExpression()
    {
        return $this->createComponentFromXml('
			<gt>
				<baseValue baseType="integer">10</baseValue>
				<baseValue baseType="float">9.9</baseValue>
			</gt>
		');
    }
}
