<?php

namespace Framework\Database\Query
{
    use Framework\Database as Database;
    use Framework\Database\Exception as Exception;
    
    class Mysql extends Database\Query
    {
        public function all()
        {
            $sql = $this->_buildSelect();
            $result = $this->connector->execute($sql);
            
            if ($result === false)
            {
                $error = $this->connector->lastError;
                throw new Exception\Sql("There was an error with your SQL query: {$error}");
            }
            
            $rows = array();
            
            for ($i = 0; $i < $result->num_rows; $i++)
            {
                $rows[] = $result->fetch_array(MYSQLI_ASSOC);
            }
            
            return $rows;
        }
    }
}