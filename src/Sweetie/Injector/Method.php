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
 * Injector which with "setSomething()" methods
 *
 * @author Christopher Marchfelder <marchfelder@googlemail.com>
 * @license MIT
 */
class Method extends Injector
{
	/**
     * @see Sweetie.Injector::inject()
     */
    public function inject(Blueprint $blueprint)
    {
        $actualObject = $this->_createObject($blueprint);

        foreach ($blueprint as $blueprintProperty) {
            $method = sprintf('set%s', ucfirst($blueprintProperty->getName()));

            if (!method_exists($actualObject, $method)) {
                throw new \BadMethodCallException(sprintf('Unknown method "%s"', $method));
            }

            $actualObject->$method($this->_getDependency($blueprintProperty));
        }

        return $actualObject;
    }
}