<?php
class QueryBuilder
{
    protected PDO $pdo;
    protected string $table;
    protected array $where = [];
    protected array $orWhere = [];
    protected array $join = [];
    protected array $not = [];
    protected string $groupBy = '';
    protected string $orderBy = '';
    protected string $select = '*';    
    private string $offset;
    private string $limit;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function read(string $table)
    {
        $this->table = $table;
        return $this;
    }

    public function where(array $conditions)
    {
        $this->where = $conditions;
        return $this;
    }
    public function not(array $conditions)
    {
        $this->not = $conditions;
        return $this;
    }

    public function orWhere(array $conditions)
    {
        $this->orWhere = $conditions;
        return $this;
    }

    public function groupBy(string $groupBy)
    {
        $this->groupBy = $groupBy;
        return $this;
    }

    public function orderBy(string $orderBy)
    {
        $this->orderBy = $orderBy;
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



    public function join(string $table, string $condition)
    {
        $this->join[] = "JOIN {$table} ON {$condition}";
        return $this;
    }

    public function get(string $select = '*', bool $fetchAll = true)
    {
        $this->select = $select;

        $query = "SELECT {$this->select} FROM {$this->table}";

        if (!empty($this->join)) {
            $query .= " " . implode(' ', $this->join);
        }

        if (!empty($this->where)) {
            $query .= " WHERE " . implode(' AND ', array_map(
                fn ($v, $k) => sprintf("%s = :%s", $k, $k),
                $this->where,
                array_keys($this->where)
            ));
        }

        if (!empty($this->not)) {
            $query .= " AND " . implode(' AND ', array_map(
                fn ($v, $k) => sprintf("%s != :%s", $k, $k),
                $this->not,
                array_keys($this->not)
            ));
        }

        

        if (!empty($this->orWhere)) {
            $query .= " OR " . implode(' OR ', array_map(
                fn ($v, $k) => sprintf("%s = :%s", $k, $k),
                $this->orWhere,
                array_keys($this->orWhere)
            ));
        }

        if (!empty($this->groupBy)) {
            $query .= " GROUP BY {$this->groupBy}";
        }

        if (!empty($this->orderBy)) {
            $query .= " ORDER BY {$this->orderBy}";
        }

        if (!empty($this->limit)) {
            $query .= " LIMIT";

            if (!empty($this->offset)) {
                $query .= " {$this->offset},";
            }

            $query .= " {$this->limit}";
        }

        $stmt = $this->pdo->prepare($query);

        foreach ($this->where as $key => &$val) {
            $stmt->bindParam(':' . $key, $val);
        }

        foreach ($this->not as $key => &$val) {
            $stmt->bindParam(':' . $key, $val);
        }

        foreach ($this->orWhere as $key => &$val) {
            $stmt->bindParam(':or' . $key, $val);
        }

        // print($query);
        // die;

        $stmt->execute();

        if ($fetchAll) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        $stmt->closeCursor();
    }
}



//TODO
/**
 *  $query = new QueryBuilder($pdo);
 *  $results = $query->read('users')
 *                  ->join('orders', 'users.id = orders.user_id')
 *                  ->where(['users.status' => 'active'])
 *                  ->orderBy('users.created_at DESC')
 *                  ->get();

 */
