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
 * Injector which works with the constructor of the object
 *
 * @author Christopher Marchfelder <marchfelder@googlemail.com>
 * @license MIT
 */
class Constructor extends Injector
{
	/**
     * @see Sweetie.Injector::inject()
     */
    public function inject(Blueprint $blueprint)
    {
        $ref = new \ReflectionClass($blueprint->getClass());

        $objectsToInject = array();
        foreach ($blueprint as $blueprintProperty) {
            $objectsToInject[] = $this->_getDependency($blueprintProperty);
        }

        return $ref->newInstanceArgs($objectsToInject);
    }
}