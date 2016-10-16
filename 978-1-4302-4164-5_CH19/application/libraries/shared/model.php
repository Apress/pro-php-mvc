<?php

namespace Shared
{
    class Model extends \Framework\Model
    {
        /**
        * @column
        * @readwrite
        * @primary
        * @type autonumber
        */
        protected $_id;

        /**
        * @column
        * @readwrite
        * @type boolean
        * @index
        */
        protected $_live;
        
        /**
        * @column
        * @readwrite
        * @type boolean
        * @index
        */
        protected $_deleted;
        
        /**
        * @column
        * @readwrite
        * @type datetime
        */
        protected $_created;
        
        /**
        * @column
        * @readwrite
        * @type datetime
        */
        protected $_modified;

        public function save()
        {
            $primary = $this->getPrimaryColumn();
            $raw = $primary["raw"];
            
            if (empty($this->$raw))
            {
                $this->setCreated(date("Y-m-d H:i:s"));
                $this->setDeleted(false);
                $this->setLive(true);
            }
            $this->setModified(date("Y-m-d H:i:s"));
            
            parent::save();
        }
    }
}
