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
 * Copyright (c) 2019 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Julien Sébire, <julien@taotesting.com>
 * @license GPLv2
 */

namespace qtismtest\data\results;

use PHPUnit\Framework\TestCase;
use qtism\data\results\Context;
use qtism\data\results\SessionIdentifier;

class ContextTest extends TestCase
{
    public function testAddSessionIdentifier()
    {
        $sourceId = 'a source id';
        $identifier = "string with\n\rtabulations\tand\r\nnew lines\nto be replaced\rby spaces";
        $normalizedIdentifier = 'string with  tabulations and  new lines to be replaced by spaces';
        
        $subject = new Context();
        $this->assertFalse($subject->hasSessionIdentifiers());
        
        $subject->addSessionIdentifier($sourceId, $identifier);
        $this->assertTrue($subject->hasSessionIdentifiers());
        
        $sessionIdentifierCollection = $subject->getSessionIdentifiers();
        $this->assertCount(1, $sessionIdentifierCollection);
        
        $sessionIdentifier = $sessionIdentifierCollection->current();
        $this->assertInstanceOf(SessionIdentifier::class, $sessionIdentifier);
        /** @var SessionIdentifier $sessionIdentifier */
        
        $this->assertEquals($sourceId, $sessionIdentifier->getSourceID()->getValue());
        $this->assertEquals($normalizedIdentifier, $sessionIdentifier->getIdentifier()->getValue());
    }
}