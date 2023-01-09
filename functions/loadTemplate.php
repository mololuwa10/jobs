<?php
function loadTemplate($fileName, $templateVars): false|string
{
    extract($templateVars);
    ob_start();
    require $fileName;
    return ob_get_clean();
}

class databaseTable
{
    private $table;
    private $primaryKey;

    public function __construct($table, $primaryKey)
    {
        $this->pdo = new PDO('mysql:dbname=job;host=mysql', 'student', 'student');
        $this->table = $table;
        $this->primaryKey = $primaryKey;
    }

    public function customFind($where, $criteria): false|array
    {
        $stmt = 'SELECT * FROM ' . $this->table;
        if ($where != "") {
            $stmt .= " WHERE $where";
        }

        $stmt = $this->pdo->prepare($stmt);
        $stmt->execute($criteria);
        return $stmt->fetchAll();
    }

    public function findAll(): false|array
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
}

function adminValidation(): void
{
    if (!isset($_SESSION)) {
        session_start();
    }
    if ($_SESSION['userDetails']['userType'] != 'admin') {
        header("Location: clientIndex");
        exit();
    }

}
