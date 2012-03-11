<?php
// +----------------------------------------------------------+
// | Sweetie Dependency Injection 2012                        |
// +----------------------------------------------------------+

namespace Sweetie\Injector;
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
     * @see Sweetie.Injector::inject()
     */
    public function inject(ClassBindings $bindings)
    {
        $className = $bindings->getClass();
        $actualObject = new $className();

        $reflection = new \ReflectionObject($actualObject);
        foreach ($bindings->getProperties() as $name) {
            $property = $reflection->getProperty($name);

            if (!$property->isPublic()) {
                $property->setAccessible(true);
            }

            $referenceName = $bindings->getReference($name);
            $property->setValue($actualObject, new $referenceName());
        }

        return $actualObject;
    }
}