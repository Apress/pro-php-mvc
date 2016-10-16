<?php

namespace Framework
{
    class Hello
    {
        public function world()
        {
            echo "Hello world!";
        }
    }
}

namespace Foo
{
    // allows us to refer to the Hello class
    // without specifying its namespace each time
    use Framework\Hello as Hello; 

    class Bar
    {
        function __construct()
        {
            // here we can refer to Framework\Hello as simply Hello
            // due to the preceding "use" statement
            $hello = new Hello();
            $hello->world();
        }
    }
}

namespace
{
    $hello = new Framework\Hello();
    $hello->world(); //... prints "Hello world!"
    
    $foo = new Foo\Bar();
    $foo->bar(); //... prints "Hello world!"
}
