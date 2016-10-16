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

namespace Framework\Database
{
    class Exception extends \Framework\Core\Exception {}
}

namespace Framework\Database\Exception
{
    class Implementation extends \Framework\Database\Exception {}
    class Argument extends \Framework\Database\Exception {}
    class Service extends \Framework\Database\Exception {}
    class Sql extends \Framework\Database\Exception {}
}

namespace Framework\Model
{
    class Exception extends \Framework\Core\Exception {}
}

namespace Framework\Model\Exception
{
    class Implementation extends \Framework\Model\Exception {}
    class Primary extends \Framework\Model\Exception {}
    class Connector extends \Framework\Model\Exception {}
    class Type extends \Framework\Model\Exception {}
}