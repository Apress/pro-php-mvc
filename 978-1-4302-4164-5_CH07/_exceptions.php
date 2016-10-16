<?php 

namespace Framework\Core
{
    class Exception extends \Exception {}
}

namespace Framework\Core\Exception
{
    class ReadOnly extends \Framework\Core\Exception {}
    class WriteOnly extends \Framework\Core\Exception {}
    class Property extends \Framework\Core\Exception {}
    class Argument extends \Framework\Core\Exception {}
}

namespace Framework\Router
{
    class Exception extends \Framework\Core\Exception {}
}

namespace Framework\Router\Exception
{
    class Implementation extends \Framework\Router\Exception {}
    class Controller extends \Framework\Router\Exception {}
    class Action extends \Framework\Router\Exception {}
}