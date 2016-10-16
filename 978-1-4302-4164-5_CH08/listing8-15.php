<?php

include("_base.php");
include("_exceptions.php");
include("_inspector.php");
include("_methods.php");
include("template.php");

$structure = '
    {if $name && $address}
        name: {echo $name}<br />
        address: {echo $address}<br />
    {/if}
    {elseif $name}
        name: {echo $name}<br />
    {/elseif}
    
    {foreach $value in $stack}
        {foreach $item in $value}
            item ({echo $item_i}): {echo $item}<br />
        {/foreach}
    {/foreach}
    
    {foreach $value in $empty}
        <!--never printed-->
    {/foreach}
    {else}
        nothing in that stack!<br />
    {/else}
    
    {macro test($args)}
        this item\'s value is: {echo $args}<br />
    {/macro}
    
    {foreach $value in $stack["one"]}
        {echo test($value)}
    {/foreach}
    
    {if true}
        in first<br />
        {if true}
            in second<br />
            {if true}
                in third<br />
            {/if}
        {/if}
    {/if}
    
    {literal}
        {echo "hello world"}
    {/literal}
';

$data = array(
    "name" => "Chris",
    "address" => "Planet Earth!",
    "stack" => array(
        "one" => array(1, 2, 3),
        "two" => array(4, 5, 6)
    ),
    "empty" => array()
);

$template = new Framework\Template(array(
    "implementation" => new Framework\Template\Implementation\Standard()
));
$template->parse($structure);
echo $template->process($data);