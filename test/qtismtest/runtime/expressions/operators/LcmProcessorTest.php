<?php

namespace qtismtest\runtime\expressions\operators;

use qtism\common\datatypes\QtiInteger;
use qtism\common\datatypes\QtiString;
use qtism\common\enums\BaseType;
use qtism\runtime\common\MultipleContainer;
use qtism\runtime\common\OrderedContainer;
use qtism\runtime\common\RecordContainer;
use qtism\runtime\expressions\operators\LcmProcessor;
use qtism\runtime\expressions\operators\OperandsCollection;
use qtismtest\QtiSmTestCase;

class LcmProcessorTest extends QtiSmTestCase
{
    /**
     * @dataProvider lcmProvider
     *
     * @param array $operands
     * @param integer $expected
     */
    public function testLcm(array $operands, $expected)
    {
        $expression = $this->createFakeExpression();
        $operands = new OperandsCollection($operands);
        $processor = new LcmProcessor($expression, $operands);
        $this->assertSame($expected, $processor->process()->getValue());
    }

    public function testNotEnoughOperands()
    {
        $expression = $this->createFakeExpression();
        $operands = new OperandsCollection();
        $this->setExpectedException('qtism\\runtime\\expressions\\operators\\OperatorProcessingException');
        $processor = new LcmProcessor($expression, $operands);
    }

    public function testWrongBaseType()
    {
        $expression = $this->createFakeExpression();
        $operands = new OperandsCollection([new MultipleContainer(BaseType::STRING, [new QtiString('String!')]), new QtiInteger(10)]);
        $processor = new LcmProcessor($expression, $operands);
        $this->setExpectedException('qtism\\runtime\\expressions\\operators\\OperatorProcessingException');
        $result = $processor->process();
    }

    public function testWrongCardinality()
    {
        $expression = $this->createFakeExpression();
        $operands = new OperandsCollection([new QtiInteger(10), new QtiInteger(20), new RecordContainer(['A' => new QtiInteger(10)]), new QtiInteger(30)]);
        $processor = new LcmProcessor($expression, $operands);
        $this->setExpectedException('qtism\\runtime\\expressions\\operators\\OperatorProcessingException');
        $result = $processor->process();
    }

    /**
     * @dataProvider lcmWithNullValuesProvider
     *
     * @param array $operands
     */
    public function testGcdWithNullValues(array $operands)
    {
        $expression = $this->createFakeExpression();
        $operands = new OperandsCollection($operands);
        $processor = new LcmProcessor($expression, $operands);
        $this->assertSame(null, $processor->process());
    }

    public function lcmProvider()
    {
        return [
            [[new QtiInteger(0)], 0],
            [[new QtiInteger(0), new MultipleContainer(BaseType::INTEGER, [new QtiInteger(0)])], 0],
            [[new QtiInteger(0), new QtiInteger(0)], 0],
            [[new QtiInteger(330), new QtiInteger(0)], 0],
            [[new QtiInteger(0), new QtiInteger(330)], 0],
            [[new QtiInteger(330), new QtiInteger(0), new QtiInteger(15)], 0],
            [[new QtiInteger(330), new QtiInteger(65), new QtiInteger(15)], 4290],
            [[new QtiInteger(-10), new QtiInteger(-5)], 10],
            [[new QtiInteger(330)], 330],
            [[new QtiInteger(330), new MultipleContainer(BaseType::INTEGER, [new QtiInteger(65)]), new QtiInteger(15)], 4290],
            [[new OrderedContainer(BaseType::INTEGER, [new QtiInteger(330)]), new MultipleContainer(BaseType::INTEGER, [new QtiInteger(65)]), new MultipleContainer(BaseType::INTEGER, [new QtiInteger(15)])], 4290],
            [[new OrderedContainer(BaseType::INTEGER, [new QtiInteger(330), new QtiInteger(65)]), new MultipleContainer(BaseType::INTEGER, [new QtiInteger(65)])], 4290],
        ];
    }

    public function lcmWithNullValuesProvider()
    {
        return [
            [[null]],
            [[null, new QtiInteger(10)]],
            [[new QtiInteger(10), null]],
            [[new QtiInteger(10), null, new QtiInteger(10)]],
            [[new QtiInteger(10), new MultipleContainer(BaseType::INTEGER)]],
            [[new OrderedContainer(BaseType::INTEGER, [new QtiInteger(10), null])]],
        ];
    }

    public function createFakeExpression()
    {
        return $this->createComponentFromXml('
			<lcm>
				<baseValue baseType="integer">330</baseValue>
				<baseValue baseType="integer">65</baseValue>
				<baseValue baseType="integer">15</baseValue>
			</lcm>
		');
    }
}
