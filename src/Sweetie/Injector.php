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
        if (!$property->isIdReference()) {
            $reference = $property->getReference();
            return new $reference();
        }

        // We are safe here, Reader has already taken care of cyclic dependencies
        $id = $property->getIdFromReference();
        return $this->_getBinder()->create($id);
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