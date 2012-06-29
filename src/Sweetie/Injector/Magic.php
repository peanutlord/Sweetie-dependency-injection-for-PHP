<?php
// +----------------------------------------------------------+
// | Sweetie Dependency Injection 2012                        |
// +----------------------------------------------------------+

namespace Sweetie\Injector;
use Sweetie\Blueprint;

use Sweetie\Binder;

use Sweetie\ClassBindings;

use Sweetie\Injector;

/**
 * Injector which works the Reflection
 *
 * @author Christopher Marchfelder <marchfelder@googlemail.com>
 * @license MIT
 */
class Magic extends Injector
{
	/**
     * @see Sweetie.Injector::_doInject()
     */
    protected function _doInject(Blueprint $blueprint)
    {
        $class = $blueprint->getClass();
        $actualObject = new $class();

        $reflection = new \ReflectionObject($actualObject);
        foreach ($blueprint as $name => $ref) {
            /* @var $property ReflectionProperty */
            $property = $reflection->getProperty($name);

            if (!$property->isPublic()) {
                $property->setAccessible(true);
            }

            $objectToBind = $this->_getDependencyFromReference($ref);
            $property->setValue($actualObject, $objectToBind);
        }

        return $actualObject;
    }
}