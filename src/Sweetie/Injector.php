<?php
// +----------------------------------------------------------+
// | Sweetie Dependency Injection 2012                        |
// +----------------------------------------------------------+

namespace Sweetie;
use Sweetie\Blueprint\Property;

use Sweetie\Binder;

/**
 * Responsible to inject the according classes into the properties of the class
 *
 * @author Christopher Marchfelder <marchfelder@googlemail.com>
 * @license MIT
 */
abstract class Injector
{

    /**
     * The Binder of Sweetie
     *
     * @var Sweetie\Binder
     */
    protected $_binder = null;

    /**
     * The blueprint
     *
     * @var \Sweetie\Blueprint
     */
    protected $_blueprint = null;

    /**
     * Generates the injector
     *
     * @param Sweetie\Binder $binder
     *
     * @return void
     */
    public function __construct(Binder $binder)
    {
        $this->_binder = $binder;
    }

    /**
     * Returns the binder of Sweetie
     *
     * @return Binder
     */
    protected function _getBinder()
    {
        return $this->_binder;
    }

    /**
     * Returns a dependency defined the blueprint
     *
     * @param Sweetie\Blueprint\Property
     *
     * @return object
     */
    protected function _getDependency(Property $property)
    {
        if ($property->isClassReference()) {
            $reference = $property->getReference();
            return new $reference();
        }

        switch ($property->getReferenceType()) {
            case 'id':
                return $this->_getIdDependency($property);
                break;
            case 'invoke':
                return $this->_getInvokeDependency($property);
                break;
        }
    }

    /**
     * Returns a object created from a id reference
     *
     * @param Property $property
     *
     * @return object
     */
    protected function _getIdDependency(Property $property)
    {
        $id = $property->getIdFromReference();
        return $this->_getBinder()->create($id);
    }

    /**
     * Returns a object created from invoking a class with method
     *
     * @param Property $property
     *
     * @return object
     */
    protected function _getInvokeDependency(Property $property)
    {
        $invokeParams = $property->getInvokeParams();

        // First element of the array is the class, the second the method, any other
        // are parameters which will be passed, along with the name of the property,
        // to the factory
        $class = array_shift($invokeParams);
        $method = array_shift($invokeParams);

        // We want to invoke the methode, no matter if its static or not
        $reflection = new \ReflectionClass($class);
        if (!$reflection->hasMethod($method)) {
            throw new \BadMethodCallException(sprintf('Unknown Method "%s"', $method));
        }

        /* @var $concreteMethod ReflectionMethod */
        $concreteMethod = $reflection->getMethod($method);
        if (!$concreteMethod->isPublic()) {
            throw new \BadMethodCallException(sprintf('Method "%s" is not public', $method));
        }

        array_unshift($invokeParams, $property->getName());
        return $concreteMethod->invokeArgs($reflection->newInstanceArgs(), $invokeParams);
    }

    /**
     * Takes a ClassBindings object and injects the references
     * into all properties
     *
     * @param Blueprint $blueprint
     *
     * @return object
     */
    public abstract function inject(Blueprint $blueprint);

}