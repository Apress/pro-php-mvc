<?php

namespace Framework\Template
{
    use Framework\Base as Base;
    use Framework\StringMethods as StringMethods;
    use Framework\Template\Exception as Exception;
    
    class Implementation extends Base
    {
        protected function _handler($node)
        {
            if (empty($node["delimiter"]))
            {
                return null;
            }
            
            if (!empty($node["tag"]))
            {
                return $this->_map[$node["delimiter"]]["tags"][$node["tag"]]["handler"];
            }
            
            return $this->_map[$node["delimiter"]]["handler"];
        }
        
        public function handle($node, $content)
        {
            try
            {
                $handler = $this->_handler($node);
                return call_user_func_array(array($this, $handler), array($node, $content));
            }
            catch (\Exception $e)
            {
                throw new Exception\Implementation();
            }
        }
        
        public function match($source)
        {
            $type = null;
            $delimiter = null;
            
            foreach ($this->_map as $_delimiter => $_type)
            {
                if (!$delimiter || StringMethods::indexOf($source, $type["opener"]) == -1)
                {
                    $delimiter = $_delimiter;
                    $type = $_type;
                }
                
                $indexOf = StringMethods::indexOf($source, $_type["opener"]);
                
                if ($indexOf > -1)
                {
                    if (StringMethods::indexOf($source, $type["opener"]) > $indexOf)
                    {
                        $delimiter = $_delimiter;
                        $type = $_type;
                    }
                }
            }
            
            if ($type == null)
            {
                return null;
            }
            
            return array(
                "type" => $type,
                "delimiter" => $delimiter
            );
        }
    }    
}

namespace Framework\Template\Implementation
{
    use Framework\Template as Template;
    use Framework\StringMethods as StringMethods;
    
    class Standard extends Template\Implementation
    {
        protected $_map = array(
            "echo" => array(
                "opener" => "{echo",
                "closer" => "}",
                "handler" => "_echo"
            ),
            "script" => array(
                "opener" => "{script",
                "closer" => "}",
                "handler" => "_script"
            ),
            "statement" => array(
                "opener" => "{",
                "closer" => "}",
                "tags" => array(
                    "foreach" => array(
                        "isolated" => false,
                        "arguments" => "{element} in {object}",
                        "handler" => "_each"
                    ),
                    "for" => array(
                        "isolated" => false,
                        "arguments" => "{element} in {object}",
                        "handler" => "_for"
                    ),
                    "if" => array(
                        "isolated" => false,
                        "arguments" => null,
                        "handler" => "_if"
                    ),
                    "elseif" => array(
                        "isolated" => true,
                        "arguments" => null,
                        "handler" => "_elif"
                    ),
                    "else" => array(
                        "isolated" => true,
                        "arguments" => null,
                        "handler" => "_else"
                    ),
                    "macro" => array(
                        "isolated" => false,
                        "arguments" => "{name}({args})",
                        "handler" => "_macro"
                    ),
                    "literal" => array(
                        "isolated" => false,
                        "arguments" => null,
                        "handler" => "_literal"
                    )
                )
            )
        );
        
        protected function _echo($tree, $content)
        {
            $raw = $this->_script($tree, $content);
            return "\$_text[] = {$raw}";
        }
        
        protected function _script($tree, $content)
        {
            $raw = !empty($tree["raw"]) ? $tree["raw"] : "";
            return "{$raw};";
        }
        
        protected function _each($tree, $content)
        {
            $object = $tree["arguments"]["object"];
            $element = $tree["arguments"]["element"];
            
            return $this->_loop(
                $tree,
                "foreach ({$object} as {$element}_i => {$element}) {
                    {$content}
                }"
            );
        }
        
        protected function _for($tree, $content)
        {
            $object = $tree["arguments"]["object"];
            $element = $tree["arguments"]["element"];
            
            return $this->_loop(
                $tree,
                "for ({$element}_i = 0; {$element}_i < sizeof({$object}); {$element}_i++) {
                    {$element} = {$object}[{$element}_i];
                    {$content}
                }"
            );
        }
        
        protected function _if($tree, $content)
        {
            $raw = $tree["raw"];
            return "if ({$raw}) {{$content}}";
        }
        
        protected function _elif($tree, $content)
        {
            $raw = $tree["raw"];
            return "elseif ({$raw}) {{$content}}";
        }
        
        protected function _else($tree, $content)
        {
            return "else {{$content}}";
        }
        
        protected function _macro($tree, $content)
        {
            $arguments = $tree["arguments"];
            $name = $arguments["name"];
            $args = $arguments["args"];
            
            return "function {$name}({$args}) {
                \$_text = array();
                {$content}
                return implode(\$_text);
            }";
        }
        
        protected function _literal($tree, $content)
        {
            $source = addslashes($tree["source"]);
            return "\$_text[] = \"{$source}\";";
        }
        
        protected function _loop($tree, $inner)
        {
            $number = $tree["number"];
            $object = $tree["arguments"]["object"];
            $children = $tree["parent"]["children"];
            
            if (!empty($children[$number + 1]["tag"]) && $children[$number + 1]["tag"] == "else")
            {
                return "if (is_array({$object}) && sizeof({$object}) > 0) {{$inner}}";
            }
            return $inner;
        }
    }    
}

namespace Framework
{
    use Framework\Base as Base;
    use Framework\ArrayMethods as ArrayMethods;
    use Framework\StringMethods as StringMethods;
    use Framework\Template\Exception as Exception;
    
    class Template extends Base
    {
        /**
        * @readwrite
        */
        protected $_implementation;
        
        /**
        * @readwrite
        */
        protected $_header = "if (is_array(\$_data) && sizeof(\$_data)) extract(\$_data); \$_text = array();";
        
        /**
        * @readwrite
        */
        protected $_footer = "return implode(\$_text);";
        
        /**
        * @read
        */
        protected $_code;
        
        /**
        * @read
        */
        protected $_function;
        
        public function _getExceptionForImplementation($method)
        {
            return new Exception\Implementation("{$method} method not implemented");
        }
        
        protected function _arguments($source, $expression)
        {
            $args = $this->_array($expression, array(
                $expression => array(
                    "opener" => "{",
                    "closer" => "}"
                )
            ));
            
            $tags = $args["tags"];
            $arguments = array();
            $sanitized = StringMethods::sanitize($expression, "()[],.<>*$@");
            
            foreach ($tags as $i => $tag)
            {
                $sanitized = str_replace($tag, "(.*)", $sanitized);
                $tags[$i] = str_replace(array("{", "}"), "", $tag);
            }
            
            if (preg_match("#{$sanitized}#", $source, $matches))
            {
                foreach ($tags as $i => $tag)
                {
                    $arguments[$tag] = $matches[$i + 1];
                }
            }
            
            return $arguments;
        }
        
        protected function _tag($source)
        {
            $tag = null;
            $arguments = array();
            
            $match = $this->_implementation->match($source);
            if ($match == null)
            {
                return false;
            }
            
            $delimiter = $match["delimiter"];
            $type = $match["type"];
            
            $start = strlen($type["opener"]);
            $end = strpos($source, $type["closer"]);
            $extract = substr($source, $start, $end - $start);
            
            if (isset($type["tags"]))
            {
                $tags = implode("|", array_keys($type["tags"]));
                $regex = "#^(/){0,1}({$tags})\s*(.*)$#";
                
                if (!preg_match($regex, $extract, $matches))
                {
                    return false;
                }
                
                $tag = $matches[2];
                $extract = $matches[3];
                $closer = !!$matches[1];
            }
            
            if ($tag && $closer)
            {
                return array(
                    "tag" => $tag,
                    "delimiter" => $delimiter,
                    "closer" => true,
                    "source" => false,
                    "arguments" => false,
                    "isolated" => $type["tags"][$tag]["isolated"]
                );
            }
            
            if (isset($type["arguments"]))
            {
                $arguments = $this->_arguments($extract, $type["arguments"]);
            }
            else if ($tag && isset($type["tags"][$tag]["arguments"]))
            {
                $arguments = $this->_arguments($extract, $type["tags"][$tag]["arguments"]);
            }
            
            return array(
                "tag" => $tag,
                "delimiter" => $delimiter,
                "closer" => false,
                "source" => $extract,
                "arguments" => $arguments,
                "isolated" => (!empty($type["tags"]) ? $type["tags"][$tag]["isolated"] : false)
            );
        }
        
        protected function _array($source)
        {
            $parts = array();
            $tags = array();
            $all = array();
            
            $type = null;
            $delimiter = null;
            
            while ($source)
            {
                $match = $this->_implementation->match($source);
                
                $type = $match["type"];
                $delimiter = $match["delimiter"];
                
                $opener = strpos($source, $type["opener"]);
                $closer = strpos($source, $type["closer"]) + strlen($type["closer"]);
                
                if ($opener !== false)
                {
                    $parts[] = substr($source, 0, $opener);
                    $tags[] = substr($source, $opener, $closer - $opener);
                    $source = substr($source, $closer);
                }
                else
                {
                    $parts[] = $source;
                    $source = "";
                }
            }
            
            foreach ($parts as $i => $part)
            {
                $all[] = $part;
                if (isset($tags[$i]))
                {
                    $all[] = $tags[$i];
                }
            }
            
            return array(
                "text" => ArrayMethods::clean($parts),
                "tags" => ArrayMethods::clean($tags),
                "all" => ArrayMethods::clean($all)
            );
        }
        
        protected function _tree($array)
        {
            $root = array(
                "children" => array()
            );
            $current =& $root;
            
            foreach ($array as $i => $node)
            {
                $result = $this->_tag($node);
                
                if ($result)
                {
                    $tag = isset($result["tag"]) ? $result["tag"] : "";
                    $arguments = isset($result["arguments"]) ? $result["arguments"] : "";
                    
                    if ($tag)
                    {
                        if (!$result["closer"])
                        {
                            $last = ArrayMethods::last($current["children"]);
        
                            if ($result["isolated"] && is_string($last))
                            {
                                array_pop($current["children"]);
                            }
                            
                            $current["children"][] = array(
                                "index" => $i,
                                "parent" => &$current,
                                "children" => array(),
                                "raw" => $result["source"],
                                "tag" => $tag,
                                "arguments" => $arguments,
                                "delimiter" => $result["delimiter"],
                                "number" => sizeof($current["children"])
                            );
                            $current =& $current["children"][sizeof($current["children"]) - 1];
                        }
                        else if (isset($current["tag"]) && $result["tag"] == $current["tag"])
                        {
                            $start = $current["index"] + 1;
                            $length = $i - $start;
                            $current["source"] = implode(array_slice($array, $start, $length));
                            $current =& $current["parent"];
                        }
                    }
                    else
                    {
                        $current["children"][] = array(
                            "index" => $i,
                            "parent" => &$current,
                            "children" => array(),
                            "raw" => $result["source"],
                            "tag" => $tag,
                            "arguments" => $arguments,
                            "delimiter" => $result["delimiter"],
                            "number" => sizeof($current["children"])
                        );
                    }
                }
                else
                {
                    $current["children"][] = $node;
                }
            }
            
            return $root;
        }
        
        protected function _script($tree)
        {
            $content = array();
            
            if (is_string($tree))
            {
                $tree = addslashes($tree);
                return "\$_text[] = \"{$tree}\";";
            }
            
            if (sizeof($tree["children"]) > 0)
            {
                foreach ($tree["children"] as $child)
                {
                    $content[] = $this->_script($child);
                }
            }
            
            if (isset($tree["parent"]))
            {
                return $this->_implementation->handle($tree, implode($content));
            }
            
            return implode($content);
        }
        
        public function parse($template)
        {
            if (!is_a($this->_implementation, "Framework\Template\Implementation"))
            {
                throw new Exception\Implementation();
            }
            
            $array = $this->_array($template);
            $tree = $this->_tree($array["all"]);
            
            $this->_code = $this->header.$this->_script($tree).$this->footer;
            $this->_function = create_function("\$_data", $this->code);
            
            return $this;
        }
        
        public function process($data = array())
        {
            if ($this->_function == null)
            {
                throw new Exception\Parser();
            }
            
            try
            {
                $function = $this->_function;
                return $function($data);
            }
            catch (\Exception $e)
            {
                throw new Exception\Parser($e);
            }
        }
    }    
}
