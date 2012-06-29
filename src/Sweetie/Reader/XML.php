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
    public function _parse($content)
    {
        $xml = new \SimpleXMLElement($content);

        foreach ($xml->xpath('//blueprint') as $set) {
            $attributes = $set->attributes();

            // ID always has to be unique
            $id = (string) $attributes['id'];
            $class = (string) $attributes['class'];

            $blueprint = $this->newBlueprint($id, $class);
            foreach ($set->xpath(sprintf('//blueprint[@id="%s"]//property', $id)) as $row) {
                $blueprint->addProperty((string) $row['name'], (string) $row['ref']);
            }
        }

        foreach ($xml->xpath('//sweetie//option') as $option) {
            $attr = $option->attributes();
            $this->setOption((string) $attr['key'], (string) $attr['value']);
        }
    }
}