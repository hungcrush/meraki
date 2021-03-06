<?php
class TINY_Model extends CI_Model
{

    /* --------------------------------------------------------------
     * VARIABLES
     * ------------------------------------------------------------ */
     
     /**
      * Pagination default
      */
      
    public $current_page = 1;

    /**
     * This model's default database table. Automatically
     * guessed by pluralising the model name.
     */
    public $_table;
    protected $_attdb = 'tiny';
    
    public $join = '';

    /**
     * The database connection object. Will be set to the default
     * connection. This allows individual models to use different DBs
     * without overwriting CI's global $this->db connection.
     */
    public $_database;

    /**
     * This model's default primary key or unique identifier.
     * Used by the get(), update() and delete() functions.
     */
    protected $primary_key;
    protected $primary_key_before = array();

    /**
     * Support for soft deletes and this model's 'deleted' key
     */
    protected $soft_delete = FALSE;
    protected $soft_delete_key = 'deleted';
    protected $_temporary_with_deleted = FALSE;
    protected $_temporary_only_deleted = FALSE;

    /**
     * The various callbacks available to the model. Each are
     * simple lists of method names (methods will be run on $this).
     */
    protected $before_create = array();
    protected $after_create = array();
    protected $before_update = array();
    protected $after_update = array();
    protected $before_get = array();
    protected $after_get = array();
    protected $before_delete = array();
    protected $after_delete = array();

    protected $callback_parameters = array();

    /**
     * Protected, non-modifiable attributes
     */
    protected $protected_attributes = array();

    /**
     * Relationship arrays. Use flat strings for defaults or string
     * => array to customise the class name and primary key
     */
    protected $belongs_to = array();
    protected $has_many = array();

    protected $_with = array();

    /**
     * An array of validation rules. This needs to be the same format
     * as validation rules passed to the Form_validation library.
     */
    protected $validate = array();

    /**
     * Optionally skip the validation. Used in conjunction with
     * skip_validation() to skip data validation for any future calls.
     */
    protected $skip_validation = FALSE;

    /**
     * By default we return our results as objects. If we need to override
     * this, we can, or, we could use the `as_array()` and `as_object()` scopes.
     */
    protected $return_type = 'array';
    protected $_temporary_return_type = NULL;

    //-- Get all fileds name of current table
    protected $list_fields = array();
    
    //-- Controller method
    public $_ctrl;

    /* --------------------------------------------------------------
     * GENERIC METHODS
     * ------------------------------------------------------------ */

    /**
     * Initialise the model, tie into the CodeIgniter superobject and
     * try our best to guess the table name.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->helper('inflector');

        $this->_database = $this->db;
        $this->_ctrl     = & get_instance();
        
        $this->_fetch_table();

        array_unshift($this->before_create, 'protect_attributes');
        array_unshift($this->before_update, 'protect_attributes');

        $this->_temporary_return_type = $this->return_type;
    }

    /* --------------------------------------------------------------
     * CRUD INTERFACE
     * ------------------------------------------------------------ */

    /**
     * Fetch a single record based on the primary key. Returns an object.
     */
    public function get($primary_value)
    {
		return $this->get_by($this->primary_key, $primary_value);
    }

    /**
     * Fetch a single record based on an arbitrary WHERE call. Can be
     * any valid value to $this->_database->where().
     */
    public function get_by()
    {
        $where = func_get_args();

        if ($this->soft_delete && $this->_temporary_with_deleted !== TRUE)
        {
            $this->_database->where($this->soft_delete_key, (bool)$this->_temporary_only_deleted);
        }

		    $this->_set_where($where);

        $this->trigger('before_get');

        $row = $this->_database->get($this->_table)
                        ->{$this->_return_type()}();
        $this->_temporary_return_type = $this->return_type;

        $row = $this->trigger('after_get', $row);

        $this->_with = array();
        return $row;
    }

    /**
     * Fetch an array of records based on an array of primary values.
     */
    public function get_many($values)
    {
        $this->_database->where_in($this->primary_key, $values);

        return $this->get_all();
    }

    /**
     * Fetch an array of records based on an arbitrary WHERE call.
     */
    public function get_many_by()
    {
        $where = func_get_args();
        
        $this->_set_where($where);

        return $this->get_all();
    }

    /**
     * Fetch all the records in the table. Can be used as a generic call
     * to $this->_database->get() with scoped methods.
     */
    public function get_all()
    {
        $this->trigger('before_get');

        if ($this->soft_delete && $this->_temporary_with_deleted !== TRUE)
        {
            $this->_database->where($this->soft_delete_key, (bool)$this->_temporary_only_deleted);
        }

        $result = $this->_database->get($this->_table)
                           ->{$this->_return_type(1)}();
        $this->_temporary_return_type = $this->return_type;

        foreach ($result as $key => &$row)
        {
            $row = $this->trigger('after_get', $row, ($key == count($result) - 1));
        }

        $this->_with = array();
        return $result;
    }

    /**
     * Insert a new row into the table. $data should be an associative array
     * of data to be inserted. Returns newly created ID.
     */
    public function insert($data, $skip_validation = FALSE)
    {
        if ($skip_validation === FALSE)
        {
            $data = $this->validate($data);
        }

        $this->fields_exist($data);

        if ($data !== FALSE && !empty($data))
        {
            $data = $this->trigger('before_create', $data);

            $this->_database->insert($this->_table, $data);
            $insert_id = $this->_database->insert_id();

            $this->trigger('after_create', $insert_id);

            return $insert_id;
        }
        else
        {
            return FALSE;
        }
    }

    /**
     * Insert multiple rows into the table. Returns an array of multiple IDs.
     */
    public function insert_many($data, $skip_validation = FALSE)
    {
        $ids = array();

        foreach ($data as $key => $row)
        {
            $this->fields_exist($data);
            $ids[] = $this->insert($row, $skip_validation, ($key == count($data) - 1));
        }

        return $ids;
    }

    /**
     * Updated a record based on the primary value.
     */
    public function update($primary_value, $data, $skip_validation = FALSE)
    {
        $data = $this->trigger('before_update', $data);

        if ($skip_validation === FALSE)
        {
            $data = $this->validate($data);
        }
        $this->fields_exist($data);

        if ($data !== FALSE)
        {
            $result = $this->_database->where($this->primary_key, $primary_value)
                               ->set($data)
                               ->update($this->_table);

            $this->trigger('after_update', array($data, $result));

            return $result;
        }
        else
        {
            return FALSE;
        }
    }

    /*
      AUTHOR: TRAN VINH HUNG
      - INSERT AND UPDATE AUTO
      @DATA => PARAMS FROM REQUEST, GET IT AND PROCESS IT

      @param $files ARRAY
        key (from data)
    */

    public function insert_auto($data, $fileds = array(), $table = FALSE)
    {
      $this->_table = $table ? $table : $this->_table;


      //-- fetch all fields of this table
      $this->_get_fields_table();

      //-- apply primary key for other table
      if($table != FALSE)
        $this->_fetch_primary_key();

      $dataInsert = array();
      $dataInsertOther = array();
      foreach($data as $key => $value)
      {
          if(!is_array($value))
          {
              $dataInsert[$key] = $value;
          }
          else
          {
              if(isset($fileds[$key]))
              {
                //if($this->_checkIsEmpty($value))
                  $dataInsertOther[] = array($value, $fileds[$key], $key);
              }else
              {
                  if(in_array($key, $this->list_fields) && is_string($key) || $key == '_update_key'){
                      $dataInsert[$key] = json_encode($value, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
                  }else{
                      //$this->insert(array_merge($this->primary_key_before, $value));
                  }
              }
          }
      }

      if(empty($dataInsert)) return;
      
      if(!isset($dataInsert['_update_key']))
      {
        $id = $this->insert($dataInsert);
      }
      else
      {
        $id = $dataInsert['_update_key'];
        
        unset($dataInsert['_update_key']);
        $this->update($id, $dataInsert);

      }

      foreach($dataInsertOther as $insert)
      {
          $this->primary_key_before = array();
          $this->primary_key_before[$this->primary_key] = $id;

          $this->insert_auto($insert[0], $insert[1], $this->_table.'_'.$insert[2]);
      }

      return $id;

    }

    private function _checkIsEmpty(&$arr)
    {
        if(is_array($arr) && count($arr) > 0)
        {
          //$length = count($arr);
          foreach ($arr as $key => $values){
            foreach($values as $value){
              if(empty(trim($value))){
                unset($arr[$key]);
                break;
              }
            }
          }
          if(empty($arr)) return FALSE;
          return TRUE;
        }
        return FALSE;
    }

    private function fields_exist(&$data)
    {
      $regex = '/^(?:(?:31(\/|-|\.)(?:0?[13578]|1[02]|(?:Jan|Mar|May|Jul|Aug|Oct|Dec)))\1|(?:(?:29|30)(\/|-|\.)(?:0?[1,3-9]|1[0-2]|(?:Jan|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec))\2))(?:(?:1[6-9]|[2-9]\d)?\d{2})$|^(?:29(\/|-|\.)(?:0?2|(?:Feb))\3(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$|^(?:0?[1-9]|1\d|2[0-8])(\/|-|\.)(?:(?:0?[1-9]|(?:Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep))|(?:1[0-2]|(?:Oct|Nov|Dec)))\4(?:(?:1[6-9]|[2-9]\d)?\d{2})$/';
      if(!empty($this->list_fields)){

          foreach($data as $key => &$val)
          {
            if(!in_array($key, $this->list_fields)) unset($data[$key]);
            // Start to process data
            else
            {
                switch(true)
                {
                    case (preg_match('/password/', $key)):
                        $val = $this->__encode($val);
                        break;
                    case (preg_match('/(date|deadline|created)/', $key)):
                        if(preg_match($regex, $val))
                          $val = strtotime($val);
                        else
                          $val = time();
                        break;
                    case (preg_match('/user_id/', $key)):
                        if($val == 0)
                          $val = $this->tiny->userData['user_id'];
                        break;
                }
            }
          }
      }
    }

    public function fileds_output_process(&$data, $methods = array())
    {
      $extData = array();

      foreach ($data as $key => &$value) {
          if(is_array($value))
          {
              $this->fileds_output_process($value, $methods);
          }
          else
          {
              switch(true)
              {
                  case (preg_match('/(date|deadline|created)/', $key)):
                      $time_since = in_array('time_since', $methods) ? TRUE : FALSE;
                      $value = $this->formatTime($value, $time_since);
                      break;
                  case ($key == 'user_id'):
                      $extData['info_user'] = $this->tiny->loadUserInfo($value);
                      break;
              }
          }
      }
      $data = array_replace_recursive($data, $extData);
    }

    /**
     * Update many records, based on an array of primary values.
     */
    public function update_many($primary_values, $data, $skip_validation = FALSE)
    {
        $data = $this->trigger('before_update', $data);

        if ($skip_validation === FALSE)
        {
            $data = $this->validate($data);
        }

        if ($data !== FALSE)
        {
            $result = $this->_database->where_in($this->primary_key, $primary_values)
                               ->set($data)
                               ->update($this->_table);

            $this->trigger('after_update', array($data, $result));

            return $result;
        }
        else
        {
            return FALSE;
        }
    }

    /**
     * Updated a record based on an arbitrary WHERE clause.
     */
    public function update_by()
    {
        $args = func_get_args();
        $data = array_pop($args);

        $data = $this->trigger('before_update', $data);

        if ($this->validate($data) !== FALSE)
        {
            $this->_set_where($args);
            $result = $this->_database->set($data)
                               ->update($this->_table);
            $this->trigger('after_update', array($data, $result));

            return $result;
        }
        else
        {
            return FALSE;
        }
    }

    /**
     * Update all records
     */
    public function update_all($data)
    {
        $data = $this->trigger('before_update', $data);
        $result = $this->_database->set($data)
                           ->update($this->_table);
        $this->trigger('after_update', array($data, $result));

        return $result;
    }

    /**
     * Delete a row from the table by the primary value
     */
    public function delete($id)
    {
        $this->trigger('before_delete', $id);

        $this->_database->where($this->primary_key, $id);

        if ($this->soft_delete)
        {
            $result = $this->_database->update($this->_table, array( $this->soft_delete_key => TRUE ));
        }
        else
        {
            $result = $this->_database->delete($this->_table);
        }

        $this->trigger('after_delete', $result);

        return $result;
    }

    /**
     * Delete a row from the database table by an arbitrary WHERE clause
     */
    public function delete_by()
    {
        $where = func_get_args();

	    $where = $this->trigger('before_delete', $where);

        $this->_set_where($where);


        if ($this->soft_delete)
        {
            $result = $this->_database->update($this->_table, array( $this->soft_delete_key => TRUE ));
        }
        else
        {
            $result = $this->_database->delete($this->_table);
        }

        $this->trigger('after_delete', $result);

        return $result;
    }

    /**
     * Delete many rows from the database table by multiple primary values
     */
    public function delete_many($primary_values)
    {
        $primary_values = $this->trigger('before_delete', $primary_values);

        $this->_database->where_in($this->primary_key, $primary_values);

        if ($this->soft_delete)
        {
            $result = $this->_database->update($this->_table, array( $this->soft_delete_key => TRUE ));
        }
        else
        {
            $result = $this->_database->delete($this->_table);
        }

        $this->trigger('after_delete', $result);

        return $result;
    }


    /**
     * Truncates the table
     */
    public function truncate()
    {
        $result = $this->_database->truncate($this->_table);

        return $result;
    }

    /* --------------------------------------------------------------
     * RELATIONSHIPS
     * ------------------------------------------------------------ */
     
    public function join($table, $cond){
        $this->join = $this->_table.'.';
        $this->_database->join($table, $cond);
        return $this;
    }

    public function with($relationship)
    {
        $this->_with[] = $relationship;

        if (!in_array('relate', $this->after_get))
        {
            $this->after_get[] = 'relate';
        }

        return $this;
    }

    public function relate($row)
    {
		if (empty($row))
        {
		    return $row;
        }

        foreach ($this->belongs_to as $key => $value)
        {
            if (is_string($value))
            {
                $relationship = $value;
                $options = array( 'primary_key' => $value . '_id', 'model' => $value . '_model' );
            }
            else
            {
                $relationship = $key;
                $options = $value;
            }

            if (in_array($relationship, $this->_with))
            {
                $this->load->model($options['model'], $relationship . '_model');

                if (is_object($row))
                {
                    $row->{$relationship} = $this->{$relationship . '_model'}->get($row->{$options['primary_key']});
                }
                else
                {
                    $row[$relationship] = $this->{$relationship . '_model'}->get($row[$options['primary_key']]);
                }
            }
        }

        foreach ($this->has_many as $key => $value)
        {
            if (is_string($value))
            {
                $relationship = $value;
                $options = array( 'primary_key' => singular($this->_table) . '_id', 'model' => singular($value) . '_model' );
            }
            else
            {
                $relationship = $key;
                $options = $value;
            }

            if (in_array($relationship, $this->_with))
            {
                $this->load->model($options['model'], $relationship . '_model');

                if (is_object($row))
                {
                    $row->{$relationship} = $this->{$relationship . '_model'}->get_many_by($options['primary_key'], $row->{$this->primary_key});
                }
                else
                {
                    $row[$relationship] = $this->{$relationship . '_model'}->get_many_by($options['primary_key'], $row[$this->primary_key]);
                }
            }
        }

        return $row;
    }

    /* --------------------------------------------------------------
     * UTILITY METHODS
     * ------------------------------------------------------------ */

    /**
     * Retrieve and generate a form_dropdown friendly array
     */
    function dropdown()
    {
        $args = func_get_args();

        if(count($args) == 2)
        {
            list($key, $value) = $args;
        }
        else
        {
            $key = $this->primary_key;
            $value = $args[0];
        }

        $this->trigger('before_dropdown', array( $key, $value ));

        if ($this->soft_delete && $this->_temporary_with_deleted !== TRUE)
        {
            $this->_database->where($this->soft_delete_key, FALSE);
        }

        $result = $this->_database->select(array($key, $value))
                           ->get($this->_table)
                           ->result();

        $options = array();

        foreach ($result as $row)
        {
            $options[$row->{$key}] = $row->{$value};
        }

        $options = $this->trigger('after_dropdown', $options);

        return $options;
    }

    /**
     * Fetch a count of rows based on an arbitrary WHERE call.
     */
    public function count_by()
    {
        if ($this->soft_delete && $this->_temporary_with_deleted !== TRUE)
        {
            $this->_database->where($this->soft_delete_key, (bool)$this->_temporary_only_deleted);
        }

        $where = func_get_args();
        $this->_set_where($where);

        return $this->_database->count_all_results($this->_table);
    }

    /**
     * Fetch a total count of rows, disregarding any previous conditions
     */
    public function count_all()
    {
        if ($this->soft_delete && $this->_temporary_with_deleted !== TRUE)
        {
            $this->_database->where($this->soft_delete_key, (bool)$this->_temporary_only_deleted);
        }

        return $this->_database->count_all($this->_table);
    }

    /**
     * Tell the class to skip the insert validation
     */
    public function skip_validation()
    {
        $this->skip_validation = TRUE;
        return $this;
    }

    /**
     * Get the skip validation status
     */
    public function get_skip_validation()
    {
        return $this->skip_validation;
    }

    /**
     * Return the next auto increment of the table. Only tested on MySQL.
     */
    public function get_next_id()
    {
        return (int) $this->_database->select('AUTO_INCREMENT')
            ->from('information_schema.TABLES')
            ->where('TABLE_NAME', $this->_table)
            ->where('TABLE_SCHEMA', $this->_database->database)->get()->row()->AUTO_INCREMENT;
    }

    /**
     * Getter for the table name
     */
    public function table()
    {
        return $this->_table;
    }

    /* --------------------------------------------------------------
     * GLOBAL SCOPES
     * ------------------------------------------------------------ */

    /**
     * Return the next call as an array rather than an object
     */
    public function as_array()
    {
        $this->_temporary_return_type = 'array';
        return $this;
    }

    /**
     * Return the next call as an object rather than an array
     */
    public function as_object()
    {
        $this->_temporary_return_type = 'object';
        return $this;
    }

    /**
     * Don't care about soft deleted rows on the next call
     */
    public function with_deleted()
    {
        $this->_temporary_with_deleted = TRUE;
        return $this;
    }

    /**
     * Only get deleted rows on the next call
     */
    public function only_deleted()
    {
        $this->_temporary_only_deleted = TRUE;
        return $this;
    }

    /* --------------------------------------------------------------
     * OBSERVERS
     * ------------------------------------------------------------ */

    /**
     * MySQL DATETIME created_at and updated_at
     */
    public function created_at($row)
    {
        if (is_object($row))
        {
            $row->created_at = date('Y-m-d H:i:s');
        }
        else
        {
            $row['created_at'] = date('Y-m-d H:i:s');
        }

        return $row;
    }

    public function updated_at($row)
    {
        if (is_object($row))
        {
            $row->updated_at = date('Y-m-d H:i:s');
        }
        else
        {
            $row['updated_at'] = date('Y-m-d H:i:s');
        }

        return $row;
    }

    /**
     * Serialises data for you automatically, allowing you to pass
     * through objects and let it handle the serialisation in the background
     */
    public function serialize($row)
    {
        foreach ($this->callback_parameters as $column)
        {
            $row[$column] = serialize($row[$column]);
        }

        return $row;
    }

    public function unserialize($row)
    {
        foreach ($this->callback_parameters as $column)
        {
            if (is_array($row))
            {
                $row[$column] = unserialize($row[$column]);
            }
            else
            {
                $row->$column = unserialize($row->$column);
            }
        }

        return $row;
    }

    /**
     * Protect attributes by removing them from $row array
     */
    public function protect_attributes($row)
    {
        foreach ($this->protected_attributes as $attr)
        {
            if (is_object($row))
            {
                unset($row->$attr);
            }
            else
            {
                unset($row[$attr]);
            }
        }

        return $row;
    }

    /* --------------------------------------------------------------
     * QUERY BUILDER DIRECT ACCESS METHODS
     * ------------------------------------------------------------ */

    /**
     * A wrapper to $this->_database->order_by()
     */
    public function order_by($criteria, $order = 'ASC')
    {
        if ( is_array($criteria) )
        {
            foreach ($criteria as $key => $value)
            {
                $this->_database->order_by($key, $value);
            }
        }
        else
        {
            $this->_database->order_by($criteria, $order);
        }
        return $this;
    }

    /**
     * A wrapper to $this->_database->limit()
     */
    public function limit($limit, $offset = 0)
    {
        $this->_database->limit($limit, $offset);
        return $this;
    }

    /* --------------------------------------------------------------
     * INTERNAL METHODS
     * ------------------------------------------------------------ */

    /**
     * Trigger an event and call its observers. Pass through the event name
     * (which looks for an instance variable $this->event_name), an array of
     * parameters to pass through and an optional 'last in interation' boolean
     */
    public function trigger($event, $data = FALSE, $last = TRUE)
    {
        if (isset($this->$event) && is_array($this->$event))
        {
            foreach ($this->$event as $method)
            {
                if (strpos($method, '('))
                {
                    preg_match('/([a-zA-Z0-9\_\-]+)(\(([a-zA-Z0-9\_\-\., ]+)\))?/', $method, $matches);

                    $method = $matches[1];
                    $this->callback_parameters = explode(',', $matches[3]);
                }

                $data = call_user_func_array(array($this, $method), array($data, $last));
            }
        }

        return $data;
    }

    /**
     * Run validation on the passed data
     */
    public function validate($data)
    {
        if($this->skip_validation)
        {
            return $data;
        }

        if(!empty($this->validate))
        {
            foreach($data as $key => $val)
            {
                $_POST[$key] = $val;
            }

            $this->load->library('form_validation');

            if(is_array($this->validate))
            {
                $this->form_validation->set_rules($this->validate);

                if ($this->form_validation->run() === TRUE)
                {
                    return $data;
                }
                else
                {
                    return FALSE;
                }
            }
            else
            {
                if ($this->form_validation->run($this->validate) === TRUE)
                {
                    return $data;
                }
                else
                {
                    return FALSE;
                }
            }
        }
        else
        {
            return $data;
        }
    }

    public function _get_fields_table($table = FALSE)
    {
      $table = $table ?: $this->_table;
      $this->list_fields = $this->db->list_fields($table);
    }

    /**
     * Guess the table name by pluralising the model name
     */
    private function _fetch_table()
    {
        if ($this->_table == NULL)
        {
            $this->_table = plural(preg_replace('/(_m|_model)?$/', '', strtolower(get_class($this))));
        }
        if($this->primary_key == NULl)
        {
            $this->primary_key = preg_replace('/(_m|_model)?$/', '', strtolower(get_class($this))).'_id';
        }
        $this->_table = $this->_attdb.'_'.$this->_table;
    }

    /**
     * Guess the primary key for current table
     */
    private function _fetch_primary_key()
    {
        if($this->primary_key == NULl)
        {
            $this->primary_key = $this->_database->query("SHOW KEYS FROM `".$this->_table."` WHERE Key_name = 'PRIMARY'")->row()->Column_name;
        }
    }

    /**
     * Set WHERE parameters, cleverly
     */
    protected function _set_where($params)
    {
        if (count($params) == 1 && is_array($params[0]))
        {
            foreach ($params[0] as $field => $filter)
            {
                if (is_array($filter))
                {
                    $this->_database->where_in($field, $filter);
                }
                else
                {
                    if (is_int($field))
                    {
                      if(strpos($filter, 'OR ') !== FALSE)
                      {
                        $filter = str_replace('OR ', '', $filter);
                        $this->_database->or_where($filter);
                        
                      }
                      else
                      {
                        $this->_database->where($filter);
                      }
                    }
                    else
                    {
                        $this->_database->where($field, $filter);
                    }
                }
            }
        } 
        else if (count($params) == 1)
        {
            $this->_database->where($params[0]);
        }
    	else if(count($params) == 2)
		{
            if (is_array($params[1]))
            {
                $this->_database->where_in($this->join.$params[0], $params[1]);    
            }
            else
            {
                $this->_database->where($this->join.$params[0], $params[1]);
            }
		}
		else if(count($params) == 3)
		{
			$this->_database->where($params[0], $params[1], $params[2]);
		}
        else
        {
            if (is_array($params[1]))
            {
                $this->_database->where_in($params[0], $params[1]);    
            }
            else if($params[0] != '')
            {
                $this->_database->where($params[0], $params[1]);
            }
        }
    }
    
    protected function flush_cache(){
        $this->_database->flush_cache();
    }

    /**
     * Return the method name for the current return type
     */
    protected function _return_type($multi = FALSE)
    {
        $method = ($multi) ? 'result' : 'row';
        return $this->_temporary_return_type == 'array' ? $method . '_array' : $method;
    }

    public function __encode($password){
        $password = trim($password);
        return md5(sha1($password) . sha1($password)) . md5($password);
    }
    
    public function getImageLink($folder, $filename = FALSE){
        if(!$filename){
            $path = explode('|', $folder);
            $folder = $path[0];
            $filename = $path[1];
        }
        return (object)array(
            'src'       => $this->tiny->URL___.'uploads/'.$folder.'/thumbs/'.$filename,
            'origin'    => $this->tiny->URL___.'uploads/'.$folder.'/full-size/'.$filename,
        );
    }
    
    public function ___e($mess){
        return array(
            'error' => $mess
        );
    }
    
    public function __request($params = '', $type = ''){
        $value = '';
        switch($type){
            case 'post':
                if(isset($_POST[$params]) && !empty($_POST[$params]))
                    $value = $_POST[$params];
                break;
            case 'get':
                if(isset($_GET[$params]) && !empty($_GET[$params]))
                    $value = $_GET[$params];
                break;
            default:
                if(isset($_REQUEST[$params]) && !empty($_REQUEST[$params]))
                    $value = $_REQUEST[$params];
                break;
        }
        return $value;
    }
    
    public function _pagination($array = false){
        $page = $this->__request('page') == '' ? $this->current_page : $this->__request('page');
        $offset = ($page - 1) * $this->tiny->items_per_page;
        if($array){
            return array_splice($array, $offset, $this->tiny->items_per_page);
        }else
        $this->limit($this->tiny->items_per_page, $offset);
    }

    /*
      Init search apply for lazy code..
      @arr_search : keyword and search_other
        + keyword : @array => field name of table want search with keyword
        + search_other: @array => other fields, get value via $_POST<$fields_name>
    */
    
    public function _initSearch($arr_search = array()){
        if(!is_array($arr_search)) return FALSE;
        
        $this->_database->start_cache();
        if(isset($_POST['keyword']) &&  !empty($_POST['keyword']) && isset($arr_search['keyword'])){
            $str_where = '';
            foreach($arr_search['keyword'] as $value){
                $str_where .= $value.' LIKE "%{key}%" OR ';
            }
            $str_where = rtrim($str_where, ' OR ');
            
            $c = $this->input->post('keyword');
            $c = str_replace('  ', ' ', $c);
            $c = explode(' ', $c);
            
            foreach($c as $key){
                $this->_database->where('('.str_replace('{key}', $key, $str_where).')');
            }
        }
        
        if(isset($arr_search['search_other'])){
            foreach($arr_search['search_other'] as $value){
                if(isset($_POST[$value]) && trim($_POST[$value]) != '' && $_POST[$value] != 'all'){
                    $this->_database->where($value, $_POST[$value]);
                }
            }
        }
        $this->_database->stop_cache();
    }
    
    public function formatTime($time_int = '', $time_since = FALSE){
        if(!$time_since)
          return date('M d, Y', $time_int);
        else
          return $this->lib->time_since($time_int);
    }

    /*
      Since: 25/6/2016
      get rows via dataTables plugin
      ---------------------
      Array
      (
          [draw] => 1
          [columns] => Array()
          [order] => Array
              (
                  [0] => Array
                      (
                          [column] => 0
                          [dir] => asc
                      )
              )
          [start] => 0
          [length] => 10
          [search] => Array
              (
                  [value] => 
                  [regex] => false
              )
          [_] => 1466820097481
      )
    */
    public function getDataTable($dataTable)
    {
        $this->current_page = $dataTable['start'] / $dataTable['length'] + 1;
        $this->tiny->initialize(array(
            'items_per_page'  => $dataTable['length']
        ));

        //-- int Search with keyword if exist
        if(isset($dataTable['keyword']))
        {
          $this->_initSearch(array(
            'keyword' => array('name', 'address', 'phone', 'email')
          ));
        }

        //-- get total fields => use count_by because want to search with keyword
        $total = $this->count_by();
        //-- Pageination before db get datas
        $this->_pagination();
        //-- order only array first
        $indexOrder = $dataTable['order'][0];
        $this->order_by($dataTable['columns'][$indexOrder['column']]['data'], $indexOrder['dir']);

        if(isset($dataTable['where']))
        {
            foreach($dataTable['where'] as $key => $where)
            {
                $this->_database->where($key, $where);
            }
        }

        $data = $this->get_all();
        foreach($data as &$row)
        {
          $this->fileds_output_process($row);
        }
        return array(
            'draw'            => $dataTable['draw'],
            'recordsTotal'    => $total,
            'recordsFiltered' => $total,
            'data'            => $data
        );
    }
    
}