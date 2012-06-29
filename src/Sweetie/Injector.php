<?php
// +----------------------------------------------------------+
// | Sweetie Dependency Injection 2012                        |
// +----------------------------------------------------------+

namespace Sweetie;
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
     * Returns a blueprint dependency by reference name
     *
     * @param string $ref blueprint reference, either class or id
     *
     * @return object
     */
    protected function _getDependencyFromReference($ref)
    {
        if (!$this->_blueprint->isIdReference($ref)) {
            return new $ref();
        }

        // We are safe here, Reader has already taken care of cyclic dependencies
        $id = $this->_blueprint->getIdFromReference($ref);
        $objectToBind = $this->_getBinder()->create($id);

        return $objectToBind;
    }

    /**
     * Injects the references into the objects
     *
     * @todo dislike, see todo from blueprint::isIdReference
     *
     * @param Blueprint $blueprint
     *
     * @return object
     */
    public function inject(Blueprint $blueprint)
    {
        $this->_blueprint = $blueprint;
        return $this->_doInject($blueprint);
    }

    /**
     * Takes a ClassBindings object and injects the references
     * into all properties
     *
     * @param Blueprint $blueprint
     *
     * @return object
     */
    protected abstract function _doInject(Blueprint $blueprint);

}