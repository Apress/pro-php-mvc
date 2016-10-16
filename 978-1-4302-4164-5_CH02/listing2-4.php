<?php

try
{
    throw new Exception();
}
catch (Exception $e)
{
    echo "An exception was raised.";
}