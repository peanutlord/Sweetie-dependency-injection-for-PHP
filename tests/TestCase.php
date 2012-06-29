<?php
// +----------------------------------------------------------+
// | Sweetie Dependency Injection 2012                        |
// +----------------------------------------------------------+

use  \PHPUnit_Framework_TestCase;

/**
 * Extends the default test case class from phpunit
 *
 * @author Christopher Marchfelder <marchfelder@googlemail.com>
 * @license MIT
 */
class TestCase extends PHPUnit_Framework_TestCase
{

    /**
     *
     *
     * @var string[]
     */
    private $_writtenFiles = array();

    /**
     * Writes a file with given name and content
     *
     * @param string $path fill path with name
     * @param string $data
     *
     * @return void
     */
    protected function _writeFile($path, $data)
    {
        if (file_put_contents($path, $data) !== false) {
            $this->_writtenFiles[] = $path;
        }
    }

    /**
     * Removes all files which have been written via TestCase::_writeFile()
     *
     * @return void
     */
    protected function _removeWrittenFiles()
    {
        foreach ($this->_writtenFiles as $file) {
            @unlink($this->_writtenFiles);
        }
    }

    /**
     * @see PHPUnit_Framework_TestCase::tearDown()
     */
    public function tearDown()
    {
        $this->_removeWrittenFiles();
    }

}