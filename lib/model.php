<?php

/*

Prefetching related data sql example

SELECT
users.*,
GROUP_CONCAT(CAST(posts.id AS CHAR)) _related_posts,
profiles.id _related_profile

FROM
users

LEFT JOIN
posts ON users.id = posts.user_id

LEFT JOIN
profiles ON users.profile_id = profiles.id

GROUP BY users.id 
*/

class model //extends singleton
{
	
	public $tableName; //private
	public $modelName; //private

	public $columns; //private

//	public $lessQueriesMoreTime = true;
//	public $relations; //will be overridden on extend.

	public $recordInstances = array(); 
	public $recordStore; //private - holds all loaded records for the current table
	
	private $registry;
	private $db;
		
//	public static $_instances = array();
	
	public function __construct($registry)
	{
		$this->registry = $registry;
		$this->db = $registry->db;
				
		$this->tableName = strtolower( get_class($this) . 's' );
		$this->modelName = get_class($this);
		
		if(!isset($this->registry->models))
		{
			$this->registry->models = new stdClass();
		}
		
		$this->registry->models->{$this->modelName} = $this;
		
		$this->columns = new stdClass(); //anonymous class?
		$this->recordStore = new recordset($this);
		
		$columns = $this->db->getAll("DESCRIBE {$this->tableName};");
		foreach($columns as $column)
		{
			$this->columns->{$column['Field']} = new column($column);
		}
	}
	
	private function constructRecordset($data, $parent=null, $maxRecursions=null)
	{
		$recordset = new recordset($this);
		if($data)
		{
			foreach($data as $row)
			{
				$recordset->{$row['id']} = $this->constructRecord($row, $parent, $maxRecursions);
			}
		}

		return $recordset;
	}
	
	public function constructRecord($data, $parent=null, $maxRecursions=null)
	{
		if($data['id'])
		{
			if($this->recordStore->{$data['id']})
			{
				// maybe check if the data are actually the same?
				$record = $this->recordStore->{$data['id']};
			}
			else
			{
				$record = new record($data, $this);
				$this->recordStore->{$data['id']} = $record;
			}
		}
		else
		{
			// this means it's a new records with no data yet.
			$record = new record($data, $this);
		}
		
		$clonedRecord = clone $record;
		$clonedRecord->initialize($parent, $maxRecursions);
		return $clonedRecord;
	}
	
	public function findAllBy($key=null, $value=null, $parent=null, $maxRecursions=null)
	{		
		$where = ($key&&$value) ? "WHERE `{$key}`='{$value}'" : null;
		$query = "SELECT * FROM {$this->tableName} {$where}";
		$results = $this->db->getAll($query);
		$results = $this->constructRecordset($results, $parent, $maxRecursions);
		
		return $results;
	}
	
	public function findOneBy($key=null, $value=null, $parent=null, $maxRecursions=null)
	{
		foreach($this->recordStore as $record)
		{
			if($record->{$key}==$value)
			{
				return $record;
			}
		}
		
		$where = ($key&&$value) ? "WHERE `{$key}`='{$value}'" : null;

		$query = "SELECT * FROM {$this->tableName} {$where}";
		$results = $this->db->getRow($query);
		$results = $this->constructRecord($results, $parent, $maxRecursions);
		
		return $results;
	}
	
}

?>