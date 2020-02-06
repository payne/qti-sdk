<?php

namespace qtismtest\runtime\expressions\operators;

use qtism\common\datatypes\QtiInteger;
use qtism\common\datatypes\QtiString;
use qtism\common\enums\BaseType;
use qtism\runtime\common\MultipleContainer;
use qtism\runtime\expressions\operators\OperandsCollection;
use qtism\runtime\expressions\operators\StringMatchProcessor;
use qtismtest\QtiSmTestCase;

class StringMatchProcessorTest extends QtiSmTestCase
{
    public function testStringMatch()
    {
        $expression = $this->createFakeExpression();
        $operands = new OperandsCollection([new QtiString('one'), new QtiString('one')]);
        $processor = new StringMatchProcessor($expression, $operands);
        $result = $processor->process();
        $this->assertInstanceOf('qtism\\common\\datatypes\\QtiBoolean', $result);
        $this->assertSame(true, $result->getValue());

        $operands = new OperandsCollection([new QtiString('one'), new QtiString('oNe')]);
        $processor->setOperands($operands);
        $result = $processor->process();
        $this->assertInstanceOf('qtism\\common\\datatypes\\QtiBoolean', $result);
        $this->assertSame(false, $result->getValue());

        $processor->setExpression($this->createFakeExpression(false));
        $result = $processor->process();
        $this->assertInstanceOf('qtism\\common\\datatypes\\QtiBoolean', $result);
        $this->assertSame(true, $result->getValue());

        // Binary-safe?
        $processor->setExpression($this->createFakeExpression(true));
        $operands = new OperandsCollection([new QtiString('它的工作原理'), new QtiString('它的工作原理')]);
        $processor->setOperands($operands);
        $result = $processor->process();
        $this->assertInstanceOf('qtism\\common\\datatypes\\QtiBoolean', $result);
        $this->assertSame(true, $result->getValue());

        $operands = new OperandsCollection([new QtiString('它的工作原理'), new QtiString('它的原理')]);
        $processor->setOperands($operands);
        $result = $processor->process();
        $this->assertInstanceOf('qtism\\common\\datatypes\\QtiBoolean', $result);
        $this->assertSame(false, $result->getValue());
    }

    public function testNull()
    {
        $expression = $this->createFakeExpression();
        $operands = new OperandsCollection([new QtiString(''), null]);
        $processor = new StringMatchProcessor($expression, $operands);
        $result = $processor->process();
        $this->assertSame(null, $result);
    }

    public function testWrongCardinality()
    {
        $expression = $this->createFakeExpression();
        $operands = new OperandsCollection([new QtiString('String!'), new MultipleContainer(BaseType::STRING, [new QtiString('String!')])]);
        $processor = new StringMatchProcessor($expression, $operands);
        $this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
        $result = $processor->process();
    }

    public function testWrongBaseType()
    {
        $expression = $this->createFakeExpression();
        $operands = new OperandsCollection([new QtiString('String!'), new QtiInteger(25)]);
        $processor = new StringMatchProcessor($expression, $operands);
        $this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
        $result = $processor->process();
    }

    public function testNotEnoughOperands()
    {
        $expression = $this->createFakeExpression();
        $operands = new OperandsCollection([new QtiString('String!')]);
        $this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
        $processor = new StringMatchProcessor($expression, $operands);
    }

    public function testTooMuchOperands()
    {
        $expression = $this->createFakeExpression();
        $operands = new OperandsCollection([new QtiString('String!'), new QtiString('String!'), new QtiString('String!')]);
        $this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
        $processor = new StringMatchProcessor($expression, $operands);
    }

    public function createFakeExpression($caseSensitive = true)
    {
        $str = ($caseSensitive === true) ? 'true' : 'false';

        return $this->createComponentFromXml('
			<stringMatch caseSensitive="' . $str . '">
				<baseValue baseType="string">This does</baseValue>
				<baseValue baseType="string">not match</baseValue>
			</stringMatch>
		');
    }
}
