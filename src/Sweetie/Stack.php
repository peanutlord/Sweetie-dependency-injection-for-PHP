<?php
// +----------------------------------------------------------+
// | Sweetie Dependency Injection 2012                        |
// +----------------------------------------------------------+

namespace Sweetie;

/**
 * Stack class
 *
 * @author Christopher Marchfelder <marchfelder@googlemail.com>
 * @license MIT
 */
class Stack extends \SplStack
{

    /**
     * Clears the stack
     *
     * @return void
     */
    public function clear()
    {
        // Ignore empty stack
        if ($this->isEmpty()) {
            return;
        }

        // count() is eval'ed in every for-loop iteration, causing the stack
        // not to be cleaned completely
        $itemCount = $this->count();
        for ($i = 0; $i < $itemCount; $i++) {
            $this->pop();
        }
    }

    /**
     * Returns if the given element is already in the stack
     *
     * @param mixed $element
     *
     * @return bool
     */
    public function contains($element)
    {
        if ($this->isEmpty()) {
            return false;
        }

        foreach ($this as $row) {
            if ($row === $element) {
                return true;
            }
        }

        return false;
    }

    /**
     * Convertes the stack into a string
     *
     * @return string
     */
    public function __toString()
    {
        $elements = array();
        foreach ($this as $element) {
            $elements[] = $element;
        }

        return implode(' -> ', $elements);
    }

}