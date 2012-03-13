<?php
// +----------------------------------------------------------+
// | Sweetie Dependency Injection 2012                        |
// +----------------------------------------------------------+

namespace Sweetie\Reader;
use Sweetie\ClassBindings;
use Sweetie\Reader;

/**
 * XML based Reader
 *
 * @see Sweetie.Reader
 * @author Christopher Marchfelder <marchfelder@googlemail.com>
 * @license MIT
 */
class XML extends Reader
{

    /**
     * Holds the options from the xml
     *
     * @var string[]
     */
    protected $_options = array();

	/**
     * @see Sweetie.Reader::getOption()
     */
    public function getOption($key, $default)
    {
        return isset($this->_options[$key]) ? $this->_options[$key] : $default;
    }

	/**
     * @see Sweetie.Reader::load()
     */
    public function load($file)
    {
        if (!is_readable($file)) {
            $message = sprintf('File "%s" not found or not readable', $file);
            throw new \InvalidArgumentException($message);
        }

        $xml = new \SimpleXMLElement(file_get_contents($file));

        //
        $blueprints = array();

        foreach ($xml->xpath('//sweetie//bindings//blueprint') as $set) {
            $blueprint = $set->attributes();

            $id = (string) $blueprint['id'];
            if (isset($this->_bindings[$id])) {
                $message = sprintf('Cannot redeclare ID "%s"!', $id);
                throw new \InvalidArgumentException($message);
            }

            $binding = new ClassBindings($id, (string) $blueprint['class']);

            $path = sprintf('//blueprint[@id="%s"]//property', $id);
            foreach ($set->xpath($path) as $row) {
                $binding->addProperty((string) $row['name'], (string) $row['ref']);
            }

            $this->_bindings[$id] = $binding;
        }

        foreach ($xml->xpath('//sweetie//option') as $option) {
            $attr = $option->attributes();
            $this->_options[(string) $attr['key']] = (string) $attr['value'];
        }
    }
}