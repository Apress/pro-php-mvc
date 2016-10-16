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

namespace Framework\Cache
{
    class Exception extends \Framework\Core\Exception {}
}

namespace Framework\Cache\Exception
{
    class Implementation extends \Framework\Cache\Exception {}
}