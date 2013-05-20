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
    /**
     * When no entity namespace matched use namespaced class name
     */
    const FALLBACK_USE_FULL = 1;

    /**
     * When no entity namespace matched use class name
     */
    const FALLBACK_USE_CLASS = 2;

    /**
     * Return mixed case (untouched) names
     */
    const CASE_MIXED = 0;

    /**
     * Return lowercase names
     */
    const CASE_LOWER = 1;

    /**
     * Returned uppercase names
     */
    const CASE_UPPER = 2;

    /**
     * Prepend reference column name for join column
     */
    const ORDER_PREPEND = 0;

    /**
     * Append reference column name for join column
     */
    const ORDER_APPEND = 1;

    /**
     * When true trim 'Abstract' from the beginning of class names
     *
     * @var bool
     */
    protected $trimAbstract = false;

    /**
     * Array containing entity namespaces
     *
     * @var array
     */
    protected $entityNamespaces = array();

    /**
     * Namespace path separator
     *
     * @var string
     */
    protected $namespaceSeparator = '_';

    /**
     * Join column name separator
     *
     * @var string
     */
    protected $joinColumnSeparator = '';

    /**
     * Join table name separator
     *
     * @var string
     */
    protected $joinTableSeparator = '_';

    /**
     * Fallback trim type if no entity namespace matched
     *
     * @var int
     */
    protected $trimFallback = self::FALLBACK_USE_CLASS;

    /**
     * Generated name case
     *
     * @var int
     */
    protected $case = self::CASE_MIXED;

    /**
     * Reference column name
     *
     * @var string
     */
    protected $referenceColumnName = 'id';

    /**
     * Reference column name placement in join column name
     *
     * @var int
     */
    protected $joinColumnOrder = self::ORDER_PREPEND;

    /**
     * Target name placement in join table name
     *
     * @var int
     */
    protected $joinTableOrder = self::ORDER_APPEND;

    /**
     * Split camel cased names with separator
     *
     * @var bool
     */
    protected $splitCamelCase = false;

    /**
     * Camel case hump separator
     *
     * @var string
     */
    protected $camelCaseSeparator = '';

    /**
     * @param string $string
     *
     * @return array
     */
    protected function splitCamelCase($string)
    {
        $pieces = preg_split('/(?=(?<!^)(?<=[a-z])[A-Z])/', $string);

        return implode($this->getCamelCaseSeparator(), $pieces);
    }

    /**
     * Return the last name in a namespace string
     *
     * @param string $className
     *
     * @return string
     */
    protected function getClassName($className)
    {
        return substr($className, strrpos($className, '\\') + 1);
    }

    /**
     * Remove namespace prefix from class name if matched
     *
     * @param string $className
     * @param string $namespace
     *
     * @return bool|string
     */
    protected function trimClassNameByNamespace($className, $namespace)
    {
        if (strpos($className, $namespace) === 0) {
            return substr($className, strlen($namespace) + 1);
        }

        return false;
    }

    /**
     * Trim entity class name from class or apply default trim
     *
     * @param string $className
     *
     * @return bool|string
     * @throws \Exception
     */
    protected function trimClassNameByEntityNamespaces($className)
    {
        foreach ($this->getEntityNamespaces() as $entityNamespace) {
            $result = $this->trimClassNameByNamespace($className, $entityNamespace);

            if ($result) {
                return $result;
            }
        }

        switch ($this->getTrimFallback()) {
            case self::FALLBACK_USE_CLASS:
                return $this->getClassName($className);
            case self::FALLBACK_USE_FULL:
                return $className;
            default:
                // TODO: define custom exception
                throw new \Exception('Unknown trim fallback');
        }
    }

    /**
     * Adjust text case of value
     *
     * @param string $value
     *
     * @return string
     * @throws \Exception
     */
    protected function applyCase($value)
    {
        if ($this->isSplitCamelCase()) {
            $value = $this->splitCamelCase($value);
        }

        switch ($this->getCase()) {
            case self::CASE_MIXED:
                return $value;
            case self::CASE_LOWER:
                return strtolower($value);
            case self::CASE_UPPER:
                return strtoupper($value);
            default:
                // TODO: define custom exception
                throw new \Exception('Unknown case');
        }
    }

    /**
     * Trim 'Abstract' from beginning of class name
     *
     * @param string $className
     *
     * @return string
     */
    protected function trimAbstract($className) {
        $pieces     = explode('\\', $className);
        $classPiece = count($pieces) - 1;

        if (0 === strpos($pieces[$classPiece], 'Abstract')) {
            $pieces[$classPiece] = substr($pieces[$classPiece], 8);
        }

        return implode('\\', $pieces);
    }

    /**
     * Join two names in the order specified with separator
     *
     * @param string $rootName
     * @param string $addName
     * @param int    $order
     * @param string $separator
     *
     * @return string
     * @throws \Exception
     */
    protected function joinNames($rootName, $addName, $order, $separator)
    {
        switch ($order) {
            case self::ORDER_PREPEND:
                $one = $addName;
                $two = $rootName;
                break;
            case self::ORDER_APPEND:
                $one = $rootName;
                $two = $addName;
                break;
            default:
                // TODO: define custom exception
                throw new \Exception('Unknown join order');
        }

        if ($this->getCase() !== self::CASE_LOWER && ! $separator) {
            $two = ucfirst($two);
        }

        return $one . $separator . $two;
    }

    protected function getTableName($className)
    {
        $name = $this->trimClassNameByEntityNamespaces($className);

        if ($this->isTrimAbstract()) {
            $name = $this->trimAbstract($name);
        }

        $name = str_replace('\\', $this->getNamespaceSeparator(), $name);

        return $name;
    }


    /**
     * Construct NamespaceNamingStrategy with optional config
     *
     * @param array $config
     */
    public function __construct(array $config = array())
    {
        $this->setConfig($config);
    }

    /**
     * Configure options from array
     *
     * @param array $config
     *
     * @return self
     */
    public function setConfig(array $config)
    {
        foreach ($config as $key => $value) {
            $method = 'set' . ucfirst($key);

            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }

        return $this;
    }

    /**
     * Set the value of the trimAbstract flag
     *
     * @param bool $flag
     *
     * @return self
     */
    public function setTrimAbstract($flag = true)
    {
        $this->trimAbstract = (bool) $flag;

        return $this;
    }

    /**
     * Get the value of the trimAbstract flag
     *
     * @return bool
     */
    public function isTrimAbstract()
    {
        return $this->trimAbstract;
    }

    /**
     * Set entity namespaces
     *
     * @param array $namespaces
     *
     * @return self
     */
    public function setEntityNamespaces(array $namespaces)
    {
        foreach ($namespaces as $namespace) {
            $this->addEntityNamespace($namespace);
        }

        return $this;
    }

    /**
     * Add namespace to entity namespace array
     *
     * @param string $namespace
     *
     * @return self
     */
    public function addEntityNamespace($namespace)
    {
        $this->entityNamespaces[] = rtrim($namespace, '\\');

        return $this;
    }

    /**
     * Get entity namespaces
     *
     * @return array
     */
    public function getEntityNamespaces()
    {
        return $this->entityNamespaces;
    }

    /**
     * Set namespace path separator
     *
     * @param string $separator
     *
     * @return self
     */
    public function setNamespaceSeparator($separator)
    {
        $this->namespaceSeparator = $separator;

        return $this;
    }

    /**
     * Get namespace path separator
     *
     * @return string
     */
    public function getNamespaceSeparator()
    {
        return $this->namespaceSeparator;
    }

    /**
     * Set join column name separator
     *
     * @param string $separator
     *
     * @return self
     */
    public function setJoinColumnSeparator($separator)
    {
        $this->joinColumnSeparator = $separator;

        return $this;
    }

    /**
     * Get join column name separator
     *
     * @return string
     */
    public function getJoinColumnSeparator()
    {
        return $this->joinColumnSeparator;
    }

    /**
     * Set join table name separator
     *
     * @param string $separator
     *
     * @return self
     */
    public function setJoinTableSeparator($separator)
    {
        $this->joinTableSeparator = $separator;

        return $this;
    }

    /**
     * Get join table name separator
     *
     * @return string
     */
    public function getJoinTableSeparator()
    {
        return $this->joinTableSeparator;
    }

    /**
     * Set fallback trim type if no entity namespace matched
     *
     * @param int $fallback
     *
     * @return self
     */
    public function setTrimFallback($fallback)
    {
        $this->trimFallback = $fallback;

        return $this;
    }

    /**
     * Get fallback trim type if no entity namespace matched
     *
     * @return int
     */
    public function getTrimFallback()
    {
        return $this->trimFallback;
    }

    /**
     * Set generated name case
     *
     * @param int $case
     *
     * @return self
     */
    public function setCase($case)
    {
        $this->case = $case;

        return $this;
    }

    /**
     * Get generated name case
     *
     * @return int
     */
    public function getCase()
    {
        return $this->case;
    }

    /**
     * Set reference column name
     *
     * @param string $name
     *
     * @return self
     */
    public function setReferenceColumnName($name)
    {
        $this->referenceColumnName = $name;

        return $this;
    }

    /**
     * Get reference column name
     *
     * @return string
     */
    public function getReferenceColumnName()
    {
        return $this->referenceColumnName;
    }

    /**
     * Set reference column name placement in join column name
     *
     * @param int $order
     *
     * @return self
     */
    public function setJoinColumnOrder($order)
    {
        $this->joinColumnOrder = $order;

        return $this;
    }

    /**
     * Get reference column name placement in join column name
     *
     * @return int
     */
    public function getJoinColumnOrder()
    {
        return $this->joinColumnOrder;
    }

    /**
     * Set target name placement in join table name
     *
     * @param int $order
     *
     * @return self
     */
    public function setJoinTableOrder($order)
    {
        $this->joinTableOrder = $order;

        return $this;
    }

    /**
     * Get target name placement in join table name
     *
     * @return int
     */
    public function getJoinTableOrder()
    {
        return $this->joinTableOrder;
    }

    /**
     * Set split camel cased names flag
     *
     * @param bool $split
     *
     * @return self
     */
    public function setSplitCamelCase($split)
    {
        $this->splitCamelCase = (bool) $split;

        return $this;
    }

    /**
     * Get split camel cases names flag
     *
     * @return bool
     */
    public function isSplitCamelCase()
    {
        return $this->splitCamelCase;
    }

    /**
     * Set camel case hump separator
     *
     * @param string $separator
     *
     * @return self
     */
    public function setCamelCaseSeparator($separator)
    {
        $this->camelCaseSeparator = $separator;

        return $this;
    }

    /**
     * Get camel case hump separator
     *
     * @return string
     */
    public function getCamelCaseSeparator()
    {
        return $this->camelCaseSeparator;
    }

    /**
     * {@inheritdoc}
     */
    public function classToTableName($className)
    {
        return $this->applyCase($this->getTableName($className));
    }

    /**
     * {@inheritdoc}
     */
    public function propertyToColumnName($propertyName, $className = null)
    {
        return $this->applyCase($propertyName);
    }

    /**
     * {@inheritdoc}
     */
    public function referenceColumnName()
    {
        return $this->applyCase($this->getReferenceColumnName());
    }

    /**
     * {@inheritdoc}
     */
    public function joinColumnName($propertyName)
    {
        return $this->joinNames($this->propertyToColumnName($propertyName),
            $this->referenceColumnName(),
            $this->getJoinColumnOrder(),
            $this->getJoinColumnSeparator()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function joinTableName($sourceEntity, $targetEntity, $propertyName = null)
    {
        return $this->joinNames($this->classToTableName($sourceEntity),
            $this->classToTableName($targetEntity),
            $this->getJoinTableOrder(),
            $this->getJoinTableSeparator()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function joinKeyColumnName($entityName, $referencedColumnName = null)
    {
        return $this->joinNames($this->classToTableName($entityName),
            ($referencedColumnName ? $this->applyCase($referencedColumnName) : $this->referenceColumnName()),
            $this->getJoinColumnOrder(),
            $this->getJoinColumnSeparator()
        );
    }
}
