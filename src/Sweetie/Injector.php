<?php
// +----------------------------------------------------------+
// | Sweetie Dependency Injection 2012                        |
// +----------------------------------------------------------+

namespace Sweetie;

/**
 * Responsible to inject the according classes into the properties of the class
 *
 * @author Christopher Marchfelder <marchfelder@googlemail.com>
 * @license MIT
 */
abstract class Injector
{

    /**
     * Takes a ClassBindings object and injects the references
     * into all properties
     *
     * @param ClassBindings $bindings
     *
     * @return object
     */
    public abstract function inject(ClassBindings $bindings);

}