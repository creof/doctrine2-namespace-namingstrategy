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
use CrEOF\Doctrine\ORM\Mapping\Tests\OrmTest;

/**
 * Class NamespaceNamingStrategyTest
 *
 * @package CrEOF\Doctrine\ORM\Mapping\Tests
 */
class NamespaceNamingStrategyTest extends OrmTest
{
    const ENTITY_NAMESPACE = 'CrEOF\Doctrine\ORM\Mapping\Tests\Fixtures\\';

    const ENTITY_NAMESPACE_UNDERSCORED = 'CrEOF_Doctrine_ORM_Mapping_Tests_Fixtures_';

    const OTHER_NAMESPACE = 'Acme\AcmeBundle\Entities\\';

    const OTHER_NAMESPACE_UNDERSCORED = 'Acme_AcmeBundle_Entities_';

    /**
     * Return NamespaceNamingStrategy with no configuration
     *
     * @return NamespaceNamingStrategy
     */
    static private function namespaceNamingNoConfig()
    {
        return new NamespaceNamingStrategy();
    }

    /**
     * Return NamespaceNamingStrategy with no entity namespaces defined and
     * trimFallback configure to use full class name
     *
     * @return NamespaceNamingStrategy
     */
    static private function namespaceNamingFallbackFull()
    {
        return new NamespaceNamingStrategy(array(
            'trimFallback' => NamespaceNamingStrategy::FALLBACK_USE_FULL
        ));
    }

    /**
     * Return NamespaceNamingStrategy with singular entity namespace defined
     *
     * @return NamespaceNamingStrategy
     */
    static private function namespaceNaming()
    {
        return new NamespaceNamingStrategy(array(
            'entityNamespaces' => array(self::ENTITY_NAMESPACE)
        ));
    }

    /**
     * Return NamespaceNamingStrategy with singular entity namespace defined
     * and camel case split with '.'
     *
     * @return NamespaceNamingStrategy
     */
    static private function namespaceNamingSplitCamel()
    {
        return new NamespaceNamingStrategy(array(
            'entityNamespaces'   => array(self::ENTITY_NAMESPACE),
            'splitCamelCase'     => true,
            'camelCaseSeparator' => '.'
        ));
    }

    /**
     * Return NamespaceNamingStrategy with singular entity namespace defined,
     * join column order set to append, and join column separator set to underscore
     *
     * @return NamespaceNamingStrategy
     */
    static private function namespaceNamingJoinColumnAppendUnderscore()
    {
        return new NamespaceNamingStrategy(array(
            'entityNamespaces'    => array(self::ENTITY_NAMESPACE),
            'joinColumnSeparator' => '_',
            'joinColumnOrder'     => NamespaceNamingStrategy::ORDER_APPEND
        ));
    }

    /**
     * Return NamespaceNamingStrategy with singular entity namespace defined
     * and uppercase names
     *
     * @return NamespaceNamingStrategy
     */
    static private function namespaceNamingUC()
    {
        return new NamespaceNamingStrategy(array(
            'entityNamespaces' => array(self::ENTITY_NAMESPACE),
            'case'             => NamespaceNamingStrategy::CASE_UPPER
        ));
    }

    /**
     * Return NamespaceNamingStrategy with singular entity namespace defined
     * and lowercase names
     *
     * @return NamespaceNamingStrategy
     */
    static private function namespaceNamingLC()
    {
        return new NamespaceNamingStrategy(array(
            'entityNamespaces' => array(self::ENTITY_NAMESPACE),
            'case'             => NamespaceNamingStrategy::CASE_LOWER
        ));
    }

    /**
     * Return NamespaceNamingStrategy with singular entity namespace defined
     * and reference column named key
     *
     * @return NamespaceNamingStrategy
     */
    static private function namespaceNamingReferenceKey()
    {
        return new NamespaceNamingStrategy(array(
            'entityNamespaces'    => array(self::ENTITY_NAMESPACE),
            'referenceColumnName' => 'key'
        ));
    }

    /**
     * Return NamespaceNamingStrategy with singular entity namespace defined,
     * reference column named key, and upper case names
     *
     * @return NamespaceNamingStrategy
     */
    static private function namespaceNamingReferenceKeyUC()
    {
        return new NamespaceNamingStrategy(array(
            'entityNamespaces'    => array(self::ENTITY_NAMESPACE),
            'referenceColumnName' => 'key',
            'case'                => NamespaceNamingStrategy::CASE_UPPER
        ));
    }

    /**
     * Return NamespaceNamingStrategy with multiple entity namespaces defined
     *
     * @return NamespaceNamingStrategy
     */
    static private function namespaceNamingMultipleNS()
    {
        return new NamespaceNamingStrategy(array(
            'entityNamespaces' => array(
                self::OTHER_NAMESPACE,
                self::ENTITY_NAMESPACE
            )
        ));
    }

    /**
     * Return NamespaceNamingStrategy with singular entity namespace defined
     * and trimAbstract set
     *
     * @return NamespaceNamingStrategy
     */
    static private function namespaceNamingTrimAbstract()
    {
        return new NamespaceNamingStrategy(array(
            'entityNamespaces' => array(self::ENTITY_NAMESPACE),
            'trimAbstract'     => true
        ));
    }

    /**
     * Return NamespaceNamingStrategy with singular entity namespace defined,
     * trimAbstract set, and hyphen namespace separator
     *
     * @return NamespaceNamingStrategy
     */
    static private function namespaceNamingTrimAbstractHyphenNS()
    {
        return new NamespaceNamingStrategy(array(
            'entityNamespaces'   => array(self::ENTITY_NAMESPACE),
            'trimAbstract'       => true,
            'namespaceSeparator' => '-'
        ));
    }

    /**
     * Data Provider for testClassToTableName
     *
     * @return array
     */
    static public function dataClassToTableName()
    {
        return array(
            array(self::namespaceNamingNoConfig(), 'AbstractMedia', self::ENTITY_NAMESPACE.'AbstractMedia'),
            array(self::namespaceNamingNoConfig(), 'Audio',         self::ENTITY_NAMESPACE.'Media\Audio'),
            array(self::namespaceNamingNoConfig(), 'Vinyl',         self::ENTITY_NAMESPACE.'Media\Audio\Vinyl'),

            array(self::namespaceNamingFallbackFull(), self::ENTITY_NAMESPACE_UNDERSCORED.'AbstractMedia',     self::ENTITY_NAMESPACE.'AbstractMedia'),
            array(self::namespaceNamingFallbackFull(), self::ENTITY_NAMESPACE_UNDERSCORED.'Media_Audio',       self::ENTITY_NAMESPACE.'Media\Audio'),
            array(self::namespaceNamingFallbackFull(), self::ENTITY_NAMESPACE_UNDERSCORED.'Media_Audio_Vinyl', self::ENTITY_NAMESPACE.'Media\Audio\Vinyl'),

            array(self::namespaceNaming(), 'AbstractMedia',     self::ENTITY_NAMESPACE.'AbstractMedia'),
            array(self::namespaceNaming(), 'Media_Audio',       self::ENTITY_NAMESPACE.'Media\Audio'),
            array(self::namespaceNaming(), 'Media_Audio_Vinyl', self::ENTITY_NAMESPACE.'Media\Audio\Vinyl'),

            array(self::namespaceNamingUC(), 'ABSTRACTMEDIA',     self::ENTITY_NAMESPACE.'AbstractMedia'),
            array(self::namespaceNamingUC(), 'MEDIA_AUDIO',       self::ENTITY_NAMESPACE.'Media\Audio'),
            array(self::namespaceNamingUC(), 'MEDIA_AUDIO_VINYL', self::ENTITY_NAMESPACE.'Media\Audio\Vinyl'),

            array(self::namespaceNamingLC(), 'abstractmedia',     self::ENTITY_NAMESPACE.'AbstractMedia'),
            array(self::namespaceNamingLC(), 'media_audio',       self::ENTITY_NAMESPACE.'Media\Audio'),
            array(self::namespaceNamingLC(), 'media_audio_vinyl', self::ENTITY_NAMESPACE.'Media\Audio\Vinyl'),

            array(self::namespaceNamingMultipleNS(), 'AbstractVehicle',         self::OTHER_NAMESPACE.'AbstractVehicle'),
            array(self::namespaceNamingMultipleNS(), 'Vehicle_Car',             self::OTHER_NAMESPACE.'Vehicle\Car'),
            array(self::namespaceNamingMultipleNS(), 'Vehicle_Truck_LightDuty', self::OTHER_NAMESPACE.'Vehicle\Truck\LightDuty'),
            array(self::namespaceNamingMultipleNS(), 'AbstractMedia',           self::ENTITY_NAMESPACE.'AbstractMedia'),
            array(self::namespaceNamingMultipleNS(), 'Media_Audio',             self::ENTITY_NAMESPACE.'Media\Audio'),
            array(self::namespaceNamingMultipleNS(), 'Media_Audio_Vinyl',       self::ENTITY_NAMESPACE.'Media\Audio\Vinyl'),

            array(self::namespaceNamingTrimAbstract(), 'Media',             self::ENTITY_NAMESPACE.'AbstractMedia'),
            array(self::namespaceNamingTrimAbstract(), 'Media_Audio',       self::ENTITY_NAMESPACE.'Media\Audio'),
            array(self::namespaceNamingTrimAbstract(), 'Media_Audio_Vinyl', self::ENTITY_NAMESPACE.'Media\Audio\Vinyl'),

            array(self::namespaceNamingTrimAbstractHyphenNS(), 'Media',             self::ENTITY_NAMESPACE.'AbstractMedia'),
            array(self::namespaceNamingTrimAbstractHyphenNS(), 'Media-Audio',       self::ENTITY_NAMESPACE.'Media\Audio'),
            array(self::namespaceNamingTrimAbstractHyphenNS(), 'Media-Audio-Vinyl', self::ENTITY_NAMESPACE.'Media\Audio\Vinyl'),

            array(self::namespaceNamingSplitCamel(), 'Abstract.Media',           self::ENTITY_NAMESPACE.'AbstractMedia'),
            array(self::namespaceNamingSplitCamel(), 'Media_Audio',              self::ENTITY_NAMESPACE.'Media\Audio'),
            array(self::namespaceNamingSplitCamel(), 'Media_Audio_Compact.Disc', self::ENTITY_NAMESPACE.'Media\Audio\CompactDisc'),
        );
    }

    /**
     * Data Provider for testPropertyToColumnName
     *
     * @return array
     */
    static public function dataPropertyToColumnName()
    {
        return array(
            array(self::namespaceNamingNoConfig(), 'someProperty',  'someProperty'),
            array(self::namespaceNamingNoConfig(), 'SOME_PROPERTY', 'SOME_PROPERTY'),
            array(self::namespaceNamingNoConfig(), 'some_property', 'some_property'),

            array(self::namespaceNamingUC(), 'SOMEPROPERTY', 'someProperty'),
            array(self::namespaceNamingUC(), 'SOME_PROPERTY', 'some_property'),
            array(self::namespaceNamingUC(), 'SOME_PROPERTY', 'SOME_PROPERTY'),

            array(self::namespaceNamingLC(), 'someproperty', 'someProperty'),
            array(self::namespaceNamingLC(), 'some_property', 'some_property'),
            array(self::namespaceNamingLC(), 'some_property', 'SOME_PROPERTY'),

            array(self::namespaceNamingSplitCamel(), 'some.Property',        'someProperty'),
            array(self::namespaceNamingSplitCamel(), 'yet.Another.Property', 'yetAnotherProperty'),
            array(self::namespaceNamingSplitCamel(), 'one_More.Property',    'one_MoreProperty'),
        );
    }

    /**
     * Data Provider for testReferenceColumnName
     *
     * @return array
     */
    static public function dataReferenceColumnName()
    {
        return array(
            array(self::namespaceNaming(), 'id'),

            array(self::namespaceNamingLC(), 'id'),

            array(self::namespaceNamingUC(), 'ID'),

            array(self::namespaceNamingReferenceKey(), 'key'),

            array(self::namespaceNamingReferenceKeyUC(), 'KEY'),
        );
    }

    /**
     * Data Provider for testJoinColumnName
     *
     * @return array
     */
    static public function dataJoinColumnName()
    {
        return array(
            array(self::namespaceNaming(), 'idSomeColumn',  'someColumn',  null),
            array(self::namespaceNaming(), 'idSome_column', 'some_column', null),

            array(self::namespaceNamingLC(), 'idsomecolumn', 'someColumn', null),

            array(self::namespaceNamingUC(), 'IDSOMECOLUMN', 'someColumn', null),

            array(self::namespaceNamingReferenceKey(), 'keySomeColumn', 'someColumn', null),

            array(self::namespaceNamingReferenceKeyUC(), 'KEYSOMECOLUMN', 'someColumn', null),

            array(self::namespaceNamingJoinColumnAppendUnderscore(), 'someColumn_id', 'someColumn', null),

            array(self::namespaceNamingSplitCamel(), 'id.One_More.Property', 'one_MoreProperty')
        );
    }

    /**
     * Data Provider for testJoinTableName
     *
     * @return array
     */
    static public function dataJoinTableName()
    {
        return array(
            array(self::namespaceNaming(), 'AbstractMedia_Keyword',           self::ENTITY_NAMESPACE.'AbstractMedia', self::ENTITY_NAMESPACE.'Keyword',           null),
            array(self::namespaceNaming(), 'AbstractMedia_Media_Audio_Vinyl', self::ENTITY_NAMESPACE.'AbstractMedia', self::ENTITY_NAMESPACE.'Media\Audio\Vinyl', null),

            array(self::namespaceNamingLC(), 'abstractmedia_keyword',           self::ENTITY_NAMESPACE.'AbstractMedia', self::ENTITY_NAMESPACE.'Keyword',           null),
            array(self::namespaceNamingLC(), 'abstractmedia_media_audio_vinyl', self::ENTITY_NAMESPACE.'AbstractMedia', self::ENTITY_NAMESPACE.'Media\Audio\Vinyl', null),

            array(self::namespaceNamingTrimAbstract(), 'Media_Keyword',           self::ENTITY_NAMESPACE.'AbstractMedia', self::ENTITY_NAMESPACE.'Keyword',           null),
            array(self::namespaceNamingTrimAbstract(), 'Media_Media_Audio_Vinyl', self::ENTITY_NAMESPACE.'AbstractMedia', self::ENTITY_NAMESPACE.'Media\Audio\Vinyl', null),
            array(self::namespaceNamingTrimAbstract(), 'Media_Media',             self::ENTITY_NAMESPACE.'AbstractMedia', self::ENTITY_NAMESPACE.'AbstractMedia',     null),

            array(self::namespaceNamingTrimAbstractHyphenNS(), 'Media_Keyword',           self::ENTITY_NAMESPACE.'AbstractMedia', self::ENTITY_NAMESPACE.'Keyword',           null),
            array(self::namespaceNamingTrimAbstractHyphenNS(), 'Media_Media-Audio-Vinyl', self::ENTITY_NAMESPACE.'AbstractMedia', self::ENTITY_NAMESPACE.'Media\Audio\Vinyl', null)
        );
    }

    /**
     * Data Provider for testJoinKeyColumnName
     *
     * @return array
     */
    static public function dataJoinKeyColumnName()
    {
        return array(
            array(self::namespaceNaming(), 'idAbstractMedia',         self::ENTITY_NAMESPACE.'AbstractMedia', null,         null),
            array(self::namespaceNaming(), 'identifierAbstractMedia', self::ENTITY_NAMESPACE.'AbstractMedia', 'identifier', null),

            array(self::namespaceNamingReferenceKey(), 'keyAbstractMedia', self::ENTITY_NAMESPACE.'AbstractMedia', null, null),

            array(self::namespaceNamingTrimAbstract(), 'idMedia', self::ENTITY_NAMESPACE.'AbstractMedia', null, null),

            array(self::namespaceNamingJoinColumnAppendUnderscore(), 'AbstractMedia_id',     self::ENTITY_NAMESPACE.'AbstractMedia',     null, null),
            array(self::namespaceNamingJoinColumnAppendUnderscore(), 'Media_Audio_Vinyl_id', self::ENTITY_NAMESPACE.'Media\Audio\Vinyl', null, null),
        );
    }

    /**
     * @dataProvider dataClassToTableName
     *
     * @param NamespaceNamingStrategy $strategy
     * @param string                  $expected
     * @param string                  $className
     */
    public function testClassToTableName(NamespaceNamingStrategy $strategy, $expected, $className)
    {
        $this->assertEquals($expected, $strategy->classToTableName($className));
    }

    /**
     * @dataProvider dataPropertyToColumnName
     *
     * @param NamespaceNamingStrategy $strategy
     * @param string                  $expected
     * @param string                  $propertyName
     */
    public function testPropertyToColumnName(NamespaceNamingStrategy $strategy, $expected, $propertyName)
    {
        $this->assertEquals($expected, $strategy->propertyToColumnName($propertyName));
    }

    /**
     * @dataProvider dataReferenceColumnName
     *
     * @param NamespaceNamingStrategy $strategy
     * @param string                  $expected
     */
    public function testReferenceColumnName(NamespaceNamingStrategy $strategy, $expected)
    {
        $this->assertEquals($expected, $strategy->referenceColumnName());
    }

    /**
     * @dataProvider dataJoinColumnName
     *
     * @param NamespaceNamingStrategy $strategy
     * @param string                  $expected
     * @param string                  $propertyName
     */
    public function testJoinColumnName(NamespaceNamingStrategy $strategy, $expected, $propertyName)
    {
        $this->assertEquals($expected, $strategy->joinColumnName($propertyName));
    }

    /**
     * @dataProvider dataJoinTableName
     *
     * @param NamespaceNamingStrategy $strategy
     * @param string                  $expected
     * @param string                  $ownerEntity
     * @param string                  $associatedEntity
     * @param string                  $propertyName
     */
    public function testJoinTableName(NamespaceNamingStrategy $strategy, $expected, $ownerEntity, $associatedEntity, $propertyName = null)
    {
        $this->assertEquals($expected, $strategy->joinTableName($ownerEntity, $associatedEntity, $propertyName));
    }

    /**
     * @dataProvider dataJoinKeyColumnName
     *
     * @param NamespaceNamingStrategy $strategy
     * @param string                  $expected
     * @param string                  $propertyEntityName
     * @param string                  $referencedColumnName
     * @param string                  $propertyName
     */
    public function testJoinKeyColumnName(NamespaceNamingStrategy $strategy, $expected, $propertyEntityName, $referencedColumnName = null, $propertyName = null)
    {
        $this->assertEquals($expected, $strategy->joinKeyColumnName($propertyEntityName, $referencedColumnName, $propertyName));
    }
}
