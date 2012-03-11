<?php
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
     * Returns the Bar Object
     *
     * @return Bar
     */
    public function getBar()
    {
        return $this->bar;
    }

}

class Bar
{

    /**
     * Returns a hello phrase
     *
     * @return string
     */
    public function sayHello()
    {
        return "Hello, I am Sweetie\n";
    }

}