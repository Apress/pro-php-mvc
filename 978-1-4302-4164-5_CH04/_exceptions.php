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

namespace Framework\Configuration
{
    class Exception extends \Framework\Core\Exception {}
}

namespace Framework\Configuration\Exception
{
    class Implementation extends \Framework\Configuration\Exception {}
}