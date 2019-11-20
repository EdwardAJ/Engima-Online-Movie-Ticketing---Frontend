<?php
abstract class Model
{
    protected static function get_mysql_connection()
    {
        include_once root().'/wrapper/mysql_connection.php';
        return new MySQLConnection(
            getenv('MYSQL_SERVER_FOR_PHP'),
            getenv('MYSQL_USER'),
            getenv('MYSQL_PASS'),
            getenv('MYSQL_DATABASE')
        );
    }

    public static function get_by($column, $value)
    {
        $mysql_connection = static::get_mysql_connection();

        $query = 'SELECT * FROM '.strtolower(get_called_class()).'s'.' WHERE '.$column.' = "'.$value.'";';
        $query_result = $mysql_connection->query($query);

        return static::get_objects_from_query_result($query_result);
    }

    public static function get_all()
    {
        $mysql_connection = static::get_mysql_connection();

        $query = 'SELECT * FROM '.strtolower(get_called_class()).'s'.';';
        $query_result = $mysql_connection->query($query);

        return static::get_objects_from_query_result($query_result);
    }

    public static function like($column, $value)
    {
        $mysql_connection = static::get_mysql_connection();

        $query = 'SELECT * FROM '.strtolower(get_called_class()).'s'.' WHERE '.$column.' LIKE "'.$value.'";';
        $query_result = $mysql_connection->query($query);

        return static::get_objects_from_query_result($query_result);
    }

    public static function delete($column, $value)
    {
        $mysql_connection = static::get_mysql_connection();

        $query = 'DELETE FROM '.strtolower(get_called_class()).'s'.' WHERE '.$column.' = "'.$value.'";';
        return $mysql_connection->query($query);
    }

    protected static function get_objects_from_query_result($query_result)
    {
        $rows = array();

        if ($query_result->num_rows > 0) {
            while ($row = $query_result->fetch_assoc()) {
                array_push($rows, static::mysql_row_to_object($row));
            }
        }

        return $rows;
    }

    public static function mysql_row_to_object($row)
    {
        $class_name = get_called_class();
        $object = new $class_name();
        foreach (array_keys($row) as $attribute) {
            if (property_exists($object, $attribute)) {
                $object->$attribute = $row[$attribute];
            }
        }
        return $object;
    }

    public function save()
    {
        $mysql_connection = static::get_mysql_connection();

        $query = 'INSERT INTO ';
        $query .= strtolower(get_called_class()).'s ';
        $query .= '('.$this->object_to_attributes_string().') ';
        $query .= 'VALUES ('.$this->object_to_value_string().');';
        
        return $mysql_connection->query($query);
    }

    public function update()
    {
        $mysql_connection = static::get_mysql_connection();

        $query = 'UPDATE '.strtolower(get_called_class()).'s SET ';

        $attribute_to_set = explode(',', $this->object_to_attributes_string());
        $value_to_set = explode(',', $this->object_to_value_string());
        $set_query = '';

        for ($i = 0; $i < count($attribute_to_set); $i++) {
            if ($i > 0 && $i < count($attribute_to_set)) {
                $set_query .= ',';
            }
            $set_query .= $attribute_to_set[$i].'='.$value_to_set[$i];
        }

        $id = get_object_vars($this)['id'];
        $query .= $set_query.' WHERE id='.$id.';';

        return $mysql_connection->query($query);
    }

    public function object_to_value_string()
    {
        $values = get_object_vars($this);
        $value_string = '';

        $count = 0;
        foreach ($values as $key => $value) {
            if ($key == 'id') {
                continue;
            }
            $value_string .= $count > 0 && $count < count($values) - 1 ? ', ' : '';
            if ($value != null || $value === 0) {
                if (gettype($value) == 'string') {
                    $value_string .= '"'.$value.'"';
                } elseif ($value instanceof DateTime) {
                    $value_string .= '"'.$value->format('Y-m-d h:i:s').'"';
                } else {
                    $value_string .= $value;
                }
            } else {
                $value_string .= 'NULL';
            }
            $count++;
        }

        return $value_string;
    }

    public function object_to_attributes_string()
    {
        $values = get_object_vars($this);
        $attribute_string = '';

        $count = 0;
        foreach ($values as $key => $value) {
            if ($key == 'id') {
                continue;
            }
            $attribute_string .= $count > 0 && $count < count($values) - 1 ? ', ' : '';
            $attribute_string .= $key;
            $count++;
        }

        return $attribute_string;
    }

    public function jsonSerialize()
    {
        $json = get_class_vars($this);
        return $json;
    }
}
