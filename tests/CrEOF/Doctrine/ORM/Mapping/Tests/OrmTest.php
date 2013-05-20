<?php
/**
 * Copyright (C) 2012-2013 Derek J. Lambert
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace CrEOF\Doctrine\ORM\Mapping\Tests;

use CrEOF\Doctrine\ORM\Mapping\NamespaceNamingStrategy;

/**
 * Abstract ORM test class
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
abstract class OrmTest extends \Doctrine\Tests\OrmFunctionalTestCase
{
    /**
     * @var bool
     */
    protected static $_setup = false;

    const ABSTRACT_MEDIA = 'CrEOF\Doctrine\ORM\Mapping\Tests\Fixtures\AbstractMedia';
    const KEYWORD        = 'CrEOF\Doctrine\ORM\Mapping\Tests\Fixtures\Keyword';
    const AUDIO          = 'CrEOF\Doctrine\ORM\Mapping\Tests\Fixtures\Media\Audio';
    const VIDEO          = 'CrEOF\Doctrine\ORM\Mapping\Tests\Fixtures\Media\Video';
    const VINYL          = 'CrEOF\Doctrine\ORM\Mapping\Tests\Fixtures\Media\Audio\Vinyl';
    const COMPACT_DISC   = 'CrEOF\Doctrine\ORM\Mapping\Tests\Fixtures\Media\Audio\CompactDisc';

    protected function setUp()
    {
        parent::setUp();

        if ( ! static::$_setup) {
            static::$_setup = true;

            $entityPaths      = array(__DIR__ . '/Fixtures');
            $entityNamespaces = array('CrEOF\Doctrine\ORM\Mapping\Tests\Fixtures');

            $this->_em->getConfiguration()->setNamingStrategy(new NamespaceNamingStrategy(array(
                    'trimAbstract' => true,
                    'entityNamespaces'  => $entityNamespaces
                )));
            $this->_em->getConfiguration()->getMetadataDriverImpl()->addPaths($entityPaths);

            $this->_schemaTool->createSchema(
                array(
                    $this->_em->getClassMetadata(self::ABSTRACT_MEDIA),
                    $this->_em->getClassMetadata(self::KEYWORD),
                    $this->_em->getClassMetadata(self::AUDIO),
                    $this->_em->getClassMetadata(self::VIDEO),
                    $this->_em->getClassMetadata(self::VINYL),
                    $this->_em->getClassMetadata(self::COMPACT_DISC)
                )
            );
        }
    }

    protected function tearDown()
    {
        parent::tearDown();

        $conn = static::$_sharedConn;

        $this->_sqlLoggerStack->enabled = false;

//        $conn->executeUpdate('DELETE FROM Media_Audio');
//        $conn->executeUpdate('DELETE FROM Media_Video');

        $this->_em->clear();
    }
}
