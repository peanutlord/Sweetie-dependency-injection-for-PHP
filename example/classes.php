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