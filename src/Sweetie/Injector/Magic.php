<?php
// +----------------------------------------------------------+
// | Sweetie Dependency Injection 2012                        |
// +----------------------------------------------------------+

namespace Sweetie\Injector;
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
    public function inject(ClassBindings $bindings)
    {
        $className = $bindings->getClassName();
        $actualObject = new $className();

        $reflection = new \ReflectionObject($actualObject);
        foreach ($bindings->getProperties() as $name) {
            // A reference may also be an ID (prefix: @id)
            $reference = $bindings->getReference($name);
            if ($this->_isIDReference($reference)) {
                // We load the id via binder
                $id = $this->_getIDFromReference($reference);

                // Add ids to a stack to prevent ids, which are referencing
                // itself, to create a endless loop
                if ($this->_stackContainsID($id)) {
                    // Purge the stacks
                    $this->_clearIDStack();

                    $message = sprintf('Reference-ID %s references itself', $id);
                    throw new \InvalidArgumentException($message);
                }

                $this->_pushToIDStack($id);
                $bindObject = $this->_getBinder()->create($id);
                $this->_popFromIDStack($id);
            } else {
                $bindObject = new $reference();
            }

            $property = $reflection->getProperty($name);
            if (!$property->isPublic()) {
                $property->setAccessible(true);
            }

            $property->setValue($actualObject, $bindObject);
        }

        return $actualObject;
    }
}