<?php

$template = new Framework\Template(array(
    "implementation" => new Framework\Template\Implementation\Standard()
));

Framework\Test::add(
    function() use ($template)
    {
        return ($template instanceof Framework\Template);
    },
    "Template instantiates",
    "Template"
);

Framework\Test::add(
    function() use ($template)
    {
        $template->parse("{echo 'hello world'}");
        $processed = $template->process();
        
        return ($processed == "hello world");
    },
    "Template parses echo tag",
    "Template"
);

Framework\Test::add(
    function() use ($template)
    {
        $template->parse("{script \$_text[] = 'foo bar' }");
        $processed = $template->process();
        
        return ($processed == "foo bar");
    },
    "Template parses script tag",
    "Template"
);

Framework\Test::add(
    function() use ($template)
    {
        $template->parse("
            {foreach \$number in \$numbers}{echo \$number_i},{echo \$number},{/foreach}"
        );
        $processed = $template->process(array(
            "numbers" => array(1, 2, 3)
        ));
        
        return (trim($processed) == "0,1,1,2,2,3,");
    },
    "Template parses foreach tag",
    "Template"
);

Framework\Test::add(
    function() use ($template)
    {
        $template->parse("
            {for \$number in \$numbers}{echo \$number_i},{echo \$number},{/for}
        ");
        $processed = $template->process(array(
            "numbers" => array(1, 2, 3)
        ));
        
        return (trim($processed) == "0,1,1,2,2,3,");
    },
    "Template parses for tag",
    "Template"
);

Framework\Test::add(
    function() use ($template)
    {
        $template->parse("
            {if \$check == \"yes\"}yes{/if}
            {elseif \$check == \"maybe\"}yes{/elseif}
            {else}yes{/else}
        ");
        
        $yes = $template->process(array(
            "check" => "yes"
        ));
        
        $maybe = $template->process(array(
            "check" => "maybe"
        ));
        
        $no = $template->process(array(
            "check" => null
        ));
        
        return ($yes == $maybe && $maybe == $no);
    },
    "Template parses if, else and elseif tags",
    "Template"
);

Framework\Test::add(
    function() use ($template)
    {
        $template->parse("
            {macro foo(\$number)}
                {echo \$number + 2}
            {/macro}
            
            {echo foo(2)}
        ");
        $processed = $template->process();
        
        return ($processed == 4);
    },
    "Template parses macro tag",
    "Template"
);

Framework\Test::add(
    function() use ($template)
    {
        $template->parse("
            {literal}
                {echo \"hello world\"}
            {/literal}
        ");
        $processed = $template->process();
        
        return (trim($processed) == "{echo \"hello world\"}");
    },
    "Template parses literal tag",
    "Template"
); 
