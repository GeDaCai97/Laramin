<?php

namespace Core;

class QueryBuilder
{
    protected string $tabla;
    protected string $modelo;
    protected array $joins = [];
    protected array $wheres = [];
    protected ?string $orderBy = null;
    protected array $selects = ['*'];

    public function __construct(string $tabla, string $modelo)
    {
        $this->tabla = $tabla;
        $this->modelo = $modelo;
    }

    public function select(array $columns): self
    {
        $this->selects = $columns;
        return $this;
    }

    public function join(string $tabla, string $localCol, string $foreignCol): self
    {
        $this->joins[] = "INNER JOIN $tabla ON $localCol = $foreignCol";
        return $this;
    }

    public function leftJoin(string $tabla, string $localCol, string $foreignCol): self
    {
        $this->joins[] = "LEFT JOIN $tabla ON $localCol = $foreignCol";
        return $this;
    }

    public function rightJoin(string $tabla, string $localCol, string $foreignCol): self
    {
        $this->joins[] = "RIGHT JOIN $tabla ON $localCol = $foreignCol";
        return $this;
    }

    public function where(string $columna, string $valor): self
    {
        $db = $this->modelo::getDB();
        $this->wheres[] = "$columna = '" . $db->escape_string($valor) . "'";
        return $this;
    }

    public function orderBy(string $columna, string $direccion = 'ASC'): self
    {
        $this->orderBy = "$columna $direccion";
        return $this;
    }

    public function get(): array
    {
        $sql = $this->buildSelectQuery();
        return $this->modelo::consultarSQL($sql);
    }

    public function first(): ?object
    {
        $sql = $this->buildSelectQuery() . " LIMIT 1";
        $result = $this->modelo::consultarSQL($sql);
        return $result[0] ?? null;
    }

    protected function buildSelectQuery(): string
    {
        $select = implode(', ', $this->selects);
        $sql = "SELECT $select FROM {$this->tabla}";

        if (!empty($this->joins)) {
            $sql .= ' ' . implode(' ', $this->joins);
        }

        if (!empty($this->wheres)) {
            $sql .= ' WHERE ' . implode(' AND ', $this->wheres);
        }

        return $sql;
    }
}