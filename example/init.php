<?php

$f = function($name) {
    $parts = explode('\\', $name);

    $path = sprintf('../../src/%s.php', implode(DIRECTORY_SEPARATOR, $parts));
    include $path;
};

spl_autoload_register($f);

/* Dummy Classes */
class Foo
{

    /**
     * The Bar object
     *
     * @var Bar
     */
    protected $bar;

    /**
     * Constructor for funky stuff
     *
     * @param \Bar $bar
     */
    public function __construct(\Bar $bar = null)
    {
        if ($bar !== null) {
            $this->setBar($bar);
        }
    }

    /**
     * Returns the Bar Object
     *
     * @return Bar
     */
    public function getBar()
    {
        return $this->bar;
    }

    public function setBar(\Bar $bar)
    {
        $this->bar = $bar;
    }

}

interface hello
{

    /**
     * Returns a hello phrase
     *
     * @return string
     */
    public function sayHello();

}

class Bar implements hello
{

    /**
     * @see hello::sayHello();
     */
    public function sayHello()
    {
        return "Hello, I am Sweetie\n";
    }

}

class Baz implements hello
{
    /**
     * @see hello::sayHello();
     */
    public function sayHello()
    {
        return "Hello, I am Sweetie - but I was delivered by another class :) \n";
    }
}