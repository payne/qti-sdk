<?php

namespace qtismtest\runtime\expressions;

use qtism\common\datatypes\QtiDuration;
use qtism\common\datatypes\QtiFloat;
use qtism\data\ItemSessionControl;
use qtism\runtime\expressions\ExpressionEngine;
use qtismtest\QtiSmTestCase;

class ExpressionEngineTest extends QtiSmTestCase
{
    public function testExpressionEngineBaseValue()
    {
        $expression = $this->createComponentFromXml('<baseValue baseType="duration">P2D</baseValue>');
        $engine = new ExpressionEngine($expression);
        $result = $engine->process();
        $this->assertInstanceOf(QtiDuration::class, $result);
        $this->assertEquals(2, $result->getDays());
    }

    public function testExpressionEngineSum()
    {
        $expression = $this->createComponentFromXml('
			<sum> <!-- 60 -->
				<product> <!-- 50 -->
					<baseValue baseType="integer">10</baseValue>
					<baseValue baseType="integer">5</baseValue>
				</product>
				<divide> <!-- 10 -->
					<baseValue baseType="integer">50</baseValue>
					<baseValue baseType="integer">5</baseValue>
				</divide>
			</sum>
		');

        $engine = new ExpressionEngine($expression);
        $result = $engine->process();
        $this->assertInstanceOf(QtiFloat::class, $result);
        $this->assertEquals(60.0, $result->getValue());
    }

    public function testCreateWrongExpressionType()
    {
        $expression = new ItemSessionControl();

        $this->setExpectedException(
            '\\InvalidArgumentException',
            "The ExpressionEngine class only accepts QTI Data Model Expression objects to be processed."
        );

        $engine = new ExpressionEngine($expression);
    }
}
