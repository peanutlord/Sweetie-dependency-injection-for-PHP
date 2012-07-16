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
     * Holds the template definitions
     *
     * @var unknown_type
     */
    protected $_templates = array();

    /**
     * Holds the options from the xml
     *
     * @var string[]
     */
    protected $_options = array();

    /**
     * @see Sweetie.Reader::getOption()
     */
    public function getOption($key, $default = false)
    {
        return isset($this->_options[$key]) ? $this->_options[$key] : $default;
    }

    /**
     * @see Sweetie.Reader::load()
     */
    protected function _parse($content)
    {
        $xml = new \SimpleXMLElement($content);

        $this->_parseTemplates($xml);
        $this->_parseBlueprints($xml);
        $this->_parseOptions($xml);
    }

    /**
     * Parses the templates from the xml
     *
     * @param SimpleXMLElement $xml
     *
     * @return void
     */
    protected function _parseTemplates(\SimpleXMLElement $xml)
    {
        foreach ($xml->xpath('//template') as $template) {
            $attributes = $template->attributes();
            $id = (string) $attributes['id'];

            foreach ($xml->xpath(sprintf('//template[@id="%s"]//property', $id)) as $row) {
                $this->_addTemplate($id, (string) $row['name'], (string) $row['ref']);
            }

        }
    }

    /**
     * Parses the blueprints
     *
     * @param SimpleXMLElement $xml
     *
     * @return void
     */
    protected function _parseBlueprints(\SimpleXMLElement $xml)
    {
        foreach ($xml->xpath('//blueprint') as $set) {
            $attributes = $set->attributes();

            // ID always has to be unique
            $id = (string) $attributes['id'];
            $class = (string) $attributes['class'];

            $blueprint = $this->_newBlueprint($id, $class);

            // Templates first, the blueprint might override some stuff
            if (isset($attributes['template-id'])) {
                $templateId = (string) $attributes['template-id'];
                $this->_applyTemplate($blueprint, $templateId);
            }

            foreach ($set->xpath(sprintf('//blueprint[@id="%s"]//property', $id)) as $row) {
                $blueprint->addProperty((string) $row['name'], (string) $row['ref']);
            }
        }
    }

    /**
     * Parses the options from the xml
     *
     * @param SimpleXMLElement $xml
     *
     * @return void
     */
    protected function _parseOptions(\SimpleXMLElement $xml)
    {
        foreach ($xml->xpath('//sweetie//option') as $option) {
            $attr = $option->attributes();
            $this->setOption((string) $attr['key'], (string) $attr['value']);
        }
    }
}