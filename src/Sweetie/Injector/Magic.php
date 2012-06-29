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
     * @see Sweetie.Injector::inject()
     */
    public function inject(Blueprint $blueprint)
    {
        $class = $blueprint->getClass();
        $actualObject = new $class();

        $reflection = new \ReflectionObject($actualObject);
        foreach ($blueprint as $blueprintProperty) {
            /* @var $reflectionProperty ReflectionProperty */
            $reflectionProperty = $reflection->getProperty($blueprintProperty->getName());

            if (!$reflectionProperty->isPublic()) {
                $reflectionProperty->setAccessible(true);
            }

            $objectToBind = $this->_getDependency($blueprintProperty);
            $reflectionProperty->setValue($actualObject, $objectToBind);
        }

        return $actualObject;
    }
}