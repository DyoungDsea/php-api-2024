<?php
/**
 * The QueryBuilder class provides a fluent interface for building and executing SQL queries.
 *
 * @package QueryBuilder
  *
 * @method $this table(string $table) Set the table for the query.
 * @method $this where(string $condition, string $boolean = 'AND') Add a basic where clause to the query.
 * @method $this orWhere(string $condition) Add an "or where" clause to the query.
 * @method $this orderBy(string $column, string $direction = 'ASC') Add an "order by" clause to the query.
 * @method $this offset(int $offset) Set the "offset" value of the query.
 * @method $this limit(int $limit) Set the "limit" value of the query.
 * @method array get(['userid', 'dfname', 'demail', 'dphone']) Execute the query and get the results.
 */

class QueryBuilder extends Connection
{
    private $table;
    private $conditions = [];
    private $orderByColumn;
    private $orderByDirection;
    private $offset;
    private $limit;
    private $joins = [];

    public function __construct()
    {
        parent::__construct();
    }

     /**
     * Set the table for the query.
     *
     * @param  string  $table
     * @return $this
     */
    public function table($table)
    {
        $this->table = $table;
        return $this;
    }

     
    /**
     * Perform a JOIN operation with a raw SQL string.
     *
     * @param  string  $rawJoin
     * @return $this
     */
    public function join($rawJoin)
    {
        $this->joins[] = $rawJoin;
        return $this;
    }

    private function buildJoins()
    {
        return implode(" ", $this->joins);
    }

    public function where($condition, $boolean = 'AND')
    {
        $this->conditions[] = compact('condition', 'boolean');
        return $this;
    }

    public function orWhere($condition)
    {
        return $this->where($condition, 'OR');
    }

    public function orderBy($column, $direction = 'ASC')
    {
        $this->orderByColumn = $column;
        $this->orderByDirection = $direction;
        return $this;
    }

    public function offset($offset)
    {
        $this->offset = $offset;
        return $this;
    }

    public function limit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    
    /**
     * Get the results of the query.
     *
     * @return array
     */
    public function get($columns = ['*'], $single=false)
{
        $query = "SELECT " . implode(', ', $columns) . " FROM {$this->table}";

        if (!empty($this->conditions)) {
            $query .= " WHERE " . $this->buildConditions();
        }

        // Add JOIN clauses if there are any
        if (!empty($this->joins)) {
            $query .= " " . $this->buildJoins();
        }

        if (!empty($this->orderByColumn)) {
            $query .= " ORDER BY {$this->orderByColumn} {$this->orderByDirection}";
        }

        if (!empty($this->limit)) {
            $query .= " LIMIT";

            if (!empty($this->offset)) {
                $query .= " {$this->offset},";
            }

            $query .= " {$this->limit}";
        }

        return $this->executeQuery($query, $single);
    }

    private function buildConditions()
    {
        $conditions = [];
        foreach ($this->conditions as $condition) {
            if (isset($condition['column'])) {
                $conditions[] = "{$condition['column']} {$condition['operator']} :{$condition['column']}";
            } elseif (isset($condition['condition'])) {
                $conditions[] = "({$condition['condition']})";
            }
        }

        $glue = $this->conditions[0]['boolean'] ?? 'AND';

        return implode(" " . $glue . " ", $conditions);
    }

    private function executeQuery($query, $single)
    {
        // Implement the logic to execute the query and return results
        // Example using PDO:
        $stmt = $this->getPDO()->prepare($query);
        $stmt->execute();
        if ($single) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



  













}
