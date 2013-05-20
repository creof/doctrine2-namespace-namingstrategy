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
     * Fallback trim type if no entity namespace matched
     *
     * @var int
     */
    protected $trimFallback = self::FALLBACK_USE_CLASS;

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

    protected function getClassName($className)
    {
        return substr($className, strrpos($className, '\\') + 1);
    }

    protected function trimClassNameByNamespace($className, $namespace)
    {
        if (strpos($className, $namespace) === 0) {
            return substr($className, strlen($namespace) + 1);
        }

        return false;
    }

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
                //no break
            default:
                return $className;
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
        if (0 === strpos($className, 'Abstract')) {
            return substr($className, 8);
        } else {
            return $className;
        }
    }

    /**
     * @param array $config
     */
    public function __construct(array $config = array())
    {
        $this->setConfig($config);
    }

    /**
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
        $this->entityNamespaces[] = $namespace;

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
     * {@inheritdoc}
     */
    public function classToTableName($className)
    {
        $name = $className;

        if (count($this->getEntityNamespaces())) {
            $name = $this->trimClassNameByEntityNamespaces($name);
        }

        $isNamespaced = strrpos($name, '\\');

        if (false !== $isNamespaced) {
            $entityName = substr($name, $isNamespaced + 1);
        } else {
            $entityName = $name;
        }

        if ($this->isTrimAbstract()) {
            $entityName = $this->trimAbstract($entityName);
        }

        if (false !== $isNamespaced) {
            $name = substr($name, 0, $isNamespaced + 1) . $entityName;
            $name = str_replace('\\', $this->getNamespaceSeparator(), $name);
        } else {
            $name = $entityName;
        }

        return $name;

//        if ($this->entityNamespace && strpos($className, $this->entityNamespace) === 0) {
//            return str_replace('\\', '_', substr($className, strlen($this->entityNamespace) + 1));
//        }
//
//        if (strpos($className, '\\') !== false) {
//            return substr($className, strrpos($className, '\\') + 1);
//        }
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
}
