<?php
namespace Database;
use PDO;
use PDOException;

class DatabaseTable
{
    private $table;
    private $primaryKey;

    private $pdo;
    private $dbName;
    public function __construct($table, $primaryKey, $dbName = 'job')
    {
        $this->pdo = new PDO('mysql:dbname='.$dbName.';host=mysql', 'student', 'student');
        $this->table = $table;
        $this->primaryKey = $primaryKey;
    }

    public function customFind($where, $criteria)
    {
        $stmt = 'SELECT * FROM ' . $this->table;
        if ($where != "") {
            $stmt .= " WHERE $where";
        }

        $stmt = $this->pdo->prepare($stmt);
        $stmt->execute($criteria);
        return $stmt->fetchAll();
    }

    public function findAll()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM " . $this->table);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function find($field, $value)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM ' . $this->table . ' WHERE ' . $field . ' = :value LIMIT 1');
        $criteria = [
            'value' => $value
        ];
        $stmt->execute($criteria);

        return $stmt->fetch();
    }

    public function save($record): void
    {
        if (empty($record[$this->primaryKey])) {
            unset($record[$this->primaryKey]);
        }
        try {
            $this->insert($record);
        } catch (Exception $e) {
            $this->update($record);
        }
    }

    public function insert($record): void
    {
        $keys = array_keys($record);
        $values = implode(', ', $keys);
        $valuesWithColons = implode(', :', $keys);

        $query = 'INSERT INTO ' . $this->table . ' (' . $values . ') VALUES (:' . $valuesWithColons . ')';
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($record);
    }

    public function update($record): void
    {
        $query = 'UPDATE ' . $this->table . ' SET ';
        $tableArray = [];
        foreach ($record as $key => $value) {
            $tableArray[] = $key . '= :' . $key;
        }
        $query .= implode(', ', $tableArray); // Create the where statement usingthe $primaryKey variableand a placeholder for the value
        $query .= ' WHERE ' . $this->primaryKey . ' =:primaryKey'; // Now write to the key 'primaryKey'which is used as the placeholder byreading the current primary Key

        $record['primaryKey'] = $record[$this->primaryKey]; // reads the primary key from the array

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($record);
    }

    public function custom($stmt, $criteria = [], $checkFetch = true)
    {
        try {
            $stmt = $this->pdo->prepare($stmt);
            $stmt->execute($criteria);
            if ($checkFetch) {
                return $stmt->fetch();
            } else {
                return $stmt->fetchAll();
            }
        } catch (PDOException $e) {
            echo $e; 
        }
    }

    public function delete($conditions = []) {
        $sql = "DELETE FROM " . $this->table;

        if(!empty($conditions)) {
            $whereClause = [];
            foreach ($conditions as $column => $value) {
                $whereClause[] = $column ." = :" . $column;
            }
            $whereClause = implode(' AND ', $whereClause);
            $sql .= " WHERE " . $whereClause;
        }

        $stmt = $this->pdo->prepare($sql);

        foreach ($conditions as $column => $value) {
            $stmt->bindValue(":". $column, $value);
        }

        return $stmt->execute();
    }
}