<?php

try
{
    throw new LogicException();
}
catch (LogicException $e)
{
    // runs only if the Exception thrown
    // is of type "LogicException"
    echo "LogicException raised!";
}
catch (Exception $e)
{
    // runs only if an Exception was thrown which
    // was not caught by any previous catch blocks
    echo "Something went wrong, and we don't know what it was...";
}