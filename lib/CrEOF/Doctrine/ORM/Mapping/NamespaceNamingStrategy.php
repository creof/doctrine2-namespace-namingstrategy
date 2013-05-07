<?php
/**
 * Copyright (C) 2013 Derek J. Lambert
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

namespace CrEOF\Doctrine\ORM\Mapping;

use Doctrine\ORM\Mapping\NamingStrategy;

/**
 * Doctrine2 naming strategy incorporating class namespace into names
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class NamespaceNamingStrategy implements NamingStrategy
{
    protected $entityNamespace;

    /**
     * {@inheritdoc}
     */
    public function classToTableName($className)
    {
        if ($this->entityNamespace && strpos($className, $this->entityNamespace) === 0) {
            return str_replace('\\', '_', substr($className, strlen($this->entityNamespace) + 1));
        }

        if (strpos($className, '\\') !== false) {
            return substr($className, strrpos($className, '\\') + 1);
        }

        return $className;
    }

    /**
     * {@inheritdoc}
     */
    public function propertyToColumnName($propertyName, $className = null)
    {
        return $propertyName;
    }

    /**
     * {@inheritdoc}
     */
    public function referenceColumnName()
    {
        return 'id';
    }

    /**
     * {@inheritdoc}
     */
    public function joinColumnName($propertyName)
    {
        return $this->referenceColumnName() . ucfirst($propertyName);
    }

    /**
     * {@inheritdoc}
     */
    public function joinTableName($sourceEntity, $targetEntity, $propertyName = null)
    {
        //return $this->classToTableName($sourceEntity) . '_' . implode('_', $this->singularLast($this->splitCamelCase($propertyName)));
        return $this->classToTableName($sourceEntity) . '_' . ucfirst($propertyName);
    }

    /**
     * {@inheritdoc}
     */
    public function joinKeyColumnName($entityName, $referencedColumnName = null)
    {
        return $this->referenceColumnName() . $this->getShortName($entityName);
//        return strtolower($this->classToTableName($entityName) . '_' .
//                ($referencedColumnName ?: $this->referenceColumnName()));
    }

    /**
     * @param string $entityNamespace
     */
    public function setEntityNamespace($entityNamespace)
    {
        $this->entityNamespace = $entityNamespace;
    }

    /**
     * @param string $string
     *
     * @return array
     */
    private function splitCamelCase($string)
    {
        $pieces = preg_split('/(?=[A-Z])/', $string);

        array_walk($pieces,
            function(&$value)
            {
                $value = ucfirst($value);
            }
        );

        return $pieces;
    }

    /**
     * @param array $pieces
     *
     * @return mixed
     */
    private function singularLast(array $pieces)
    {
        $patterns = array(
            0 => '/s$/'
        );

        $replacements = array(
            0 => ''
        );

        return preg_replace($patterns, $replacements, $pieces);
    }

    /**
     * @param string $className
     *
     * @return string
     */
    private function getShortName($className)
    {
        $pieces = explode('\\', substr($className, strlen($this->entityNamespace) + 1));

        switch (count($pieces)) {
            case 1:
                return $pieces[0];
            case 2:
                return $pieces[1];
            case 3:
                // no break
            case 4:
                return implode('', array_slice($pieces, -2));
            default:
                return implode('', $pieces);
        }
    }
}
