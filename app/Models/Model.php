<?php

namespace App\Models;

use Core\QueryBuilder;

class Model {

    // Base DE DATOS
    protected static $db;
    protected static $tabla = '';
    protected static $columnasDB = [];

    // Alertas y Mensajes
    protected static $alertas = [];
    
    // Definir la conexión a la BD - includes/database.php
    public static function setDB($database) {
        self::$db = $database;
    }

    public static function setAlerta($tipo, $mensaje) {
        static::$alertas[$tipo][] = $mensaje;
    }

    // Validación
    public static function getAlertas() {
        return static::$alertas;
    }

    public function validar() {
        static::$alertas = [];
        return static::$alertas;
    }

    // Consulta SQL para crear un objeto en Memoria
    public static function consultarSQL($query) {
        // Consultar la base de datos
        $resultado = self::$db->query($query);
        
        // Iterar los resultados
        $array = [];
        while($registro = $resultado->fetch_assoc()) {
            $array[] = static::crearObjeto($registro);
        }

        // liberar la memoria
        $resultado->free();
        // retornar los resultados
        return $array;
    }

    // Crea el objeto en memoria que es igual al de la BD
    // protected static function crearObjeto($registro) {
    //     $objeto = new static;

    //     foreach($registro as $key => $value ) {
    //         if(property_exists( $objeto, $key  )) {
    //             $objeto->$key = $value;
    //         }
    //     }

    //     return $objeto;
    // }

    // Crea el objeto en memoria que es igual al de la BD pero permite asignar propiedades dinámicamente
    // y no solo las que existen en la clase
    protected static function crearObjeto($registro) {
        $objeto = new static;

        foreach($registro as $key => $value ) {
            $objeto->$key = $value;
        }

        return $objeto;
    }

    // Identificar y unir los atributos de la BD
    public function atributos() {
        $atributos = [];
        foreach(static::$columnasDB as $columna) {
            if($columna === 'id') continue;
            $atributos[$columna] = $this->$columna;
        }
        return $atributos;
    }

    // Sanitizar los datos antes de guardarlos en la BD
    public function sanitizarAtributos() {
        $atributos = $this->atributos();
        $sanitizado = [];
        foreach($atributos as $key => $value ) {
            $sanitizado[$key] = self::$db->escape_string($value);
        }
        return $sanitizado;
    }

    // Sincroniza BD con Objetos en memoria
    public function sincronizar($args=[]) { 
        foreach($args as $key => $value) {
          if(property_exists($this, $key) && !is_null($value)) {
            $this->$key = $value;
          }
        }
    }

    // Registros - CRUD
    public function save() {
        $resultado = '';
        if(!is_null($this->id)) {
            // actualizar
            $resultado = $this->update();
        } else {
            // Creando un nuevo registro
            $resultado = $this->create();
        }
        return $resultado;
    }

    // Obtener todos los Registros
    public static function all($orden = 'DESC') {
        $query = "SELECT * FROM " . static::$tabla . " ORDER BY id $orden";
        $resultado = self::consultarSQL($query);
        return $resultado;
    }

    // Busca un registro por su id
    public static function find($id) {
        $query = "SELECT * FROM " . static::$tabla  ." WHERE id = $id";
        $resultado = self::consultarSQL($query);
        return array_shift( $resultado ) ;
    }

    // Obtener Registros con cierta cantidad
    public static function get($limite) {
        $query = "SELECT * FROM " . static::$tabla . " ORDER BY id DESC LIMIT $limite" ;
        $resultado = self::consultarSQL($query);
        return $resultado;
    }

    // Paginar los registros
    public static function paginar($por_pagina, $offset) {
        $query = "SELECT * FROM " . static::$tabla . " ORDER BY id DESC LIMIT $por_pagina OFFSET $offset" ;
        $resultado = self::consultarSQL($query);
        return $resultado;
    }

    // Busqueda Where con Columna 
    public static function where($columna, $valor) {
        $query = "SELECT * FROM " . static::$tabla . " WHERE $columna = '$valor'";
        $resultado = self::consultarSQL($query);
        return array_shift( $resultado ) ;
    }

    //Retornar los registros por un orden
    public static function orderBy($columna, $orden) {
        $query = "SELECT * FROM " . static::$tabla . " ORDER BY $columna $orden";
        $resultado = self::consultarSQL($query);
        return $resultado;
    }

    //Retornar por orden y con un limite
    public static function orderLimit($columna, $orden, $limite) {
        $query = "SELECT * FROM " . static::$tabla . " ORDER BY $columna $orden LIMIT $limite";
        $resultado = self::consultarSQL($query);
        return $resultado;
    }

    // Busqueda Where con Multiples
    public static function whereArray($array = []) {
        $query = "SELECT * FROM " . static::$tabla . " WHERE ";
        foreach($array as $key => $value) {
            if($key == array_key_last($array)) {
                $query .= "$key = '$value'";
            } else {
                $query .= "$key = '$value' AND ";
            }
        }
        $resultado = self::consultarSQL($query);
        return $resultado;
    }

    public static function whereArrayUnique($array = []) {
        $query = "SELECT * FROM " . static::$tabla . " WHERE ";
        foreach($array as $key => $value) {
            if($key == array_key_last($array)) {
                $query .= "$key = '$value'";
            } else {
                $query .= "$key = '$value' AND ";
            }
        }
        $resultado = self::consultarSQL($query);
        return array_shift( $resultado )  ;
    }
    
    // public static function belongsTo($columna, $valor) {
    //     $query = "SELECT * FROM " . static::$tabla . " WHERE $columna = '$valor'";
    //     $resultado = self::consultarSQL($query);
    //     return $resultado;
    // }

    public static function total($columna = '', $valor = '') {
        $query = "SELECT COUNT(*) FROM " . static::$tabla;
        if($columna) {
            $query .= " WHERE $columna = $valor";
        }
        $resultado = self::$db->query($query);
        $total = $resultado->fetch_array();
        return array_shift( $total );
    }

    //total de registros con un array where
    public static function totalArray($array = []) {
        $query = "SELECT COUNT(*) FROM " . static::$tabla . " WHERE ";
        foreach($array as $key => $value) {
            if($key == array_key_last($array)) {
                $query .= "$key = '$value'";
            } else {
                $query .= "$key = '$value' AND ";
            }
        }
        $resultado = self::$db->query($query);
        $total = $resultado->fetch_array();
        return array_shift( $total );
    }

    // crea un nuevo registro
    public function create() {
        // Sanitizar los datos
        $atributos = $this->sanitizarAtributos();

        // Insertar en la base de datos
        $query = " INSERT INTO " . static::$tabla . " ( ";
        $query .= join(', ', array_keys($atributos));
        $query .= " ) VALUES ('"; 
        $query .= join("', '", array_values($atributos));
        $query .= "') ";

        // Resultado de la consulta
        
        $resultado = self::$db->query($query);
        return [
           'resultado' =>  $resultado,
           'id' => self::$db->insert_id
        ];
    }

    // Actualizar el registro
    public function update() {
        // Sanitizar los datos
        $atributos = $this->sanitizarAtributos();

        // Iterar para ir agregando cada campo de la BD
        $valores = [];
        foreach($atributos as $key => $value) {
            $valores[] = "{$key}='{$value}'";
        }

        // Consulta SQL
        $query = "UPDATE " . static::$tabla ." SET ";
        $query .=  join(', ', $valores );
        $query .= " WHERE id = '" . self::$db->escape_string($this->id) . "' ";
        $query .= " LIMIT 1 "; 
        
        // Actualizar BD
        $resultado = self::$db->query($query);
        return $resultado;
    }

    // Eliminar un Registro por su ID
    public function delete() {
        $query = "DELETE FROM "  . static::$tabla . " WHERE id = " . self::$db->escape_string($this->id) . " LIMIT 1";
        $resultado = self::$db->query($query);
        return $resultado;
    }

    // Relaciones

    // hasOne: un registro relacionado (1 a 1)
    public function hasOne(string $related, string $foreignKey, string $localKey = 'id') {
        $foreignKey = $foreignKey ?? strtolower((new \ReflectionClass($this))->getShortName()) . '_id';
        $localValue = $this->$localKey;

        $query = "SELECT * FROM " . $related::$tabla . " WHERE $foreignKey = '$localValue' LIMIT 1";
        $resultado = $related::consultarSQL($query);
        return array_shift($resultado);
    }

    // hasMany: varios registros relacionados (1 a muchos)
    public function hasMany(string $related, string $foreignKey, string $localKey = 'id') {
        $foreignKey = $foreignKey ?? strtolower((new \ReflectionClass($this))->getShortName()) . '_id';
        $localValue = $this->$localKey;

        $query = "SELECT * FROM " . $related::$tabla . " WHERE $foreignKey = '$localValue'";
        return $related::consultarSQL($query);
    }

    // belongsTo: pertenencia (muchos a 1)
    public function belongsTo(string $related, string $foreignKey, string $ownerKey = 'id') {
        $foreignKeyValue = $this->$foreignKey;

        $query = "SELECT * FROM " . $related::$tabla . " WHERE $ownerKey = '$foreignKeyValue' LIMIT 1";
        $resultado = $related::consultarSQL($query);
        return array_shift($resultado);
    }

    // belongsToMany: relación muchos a muchos con tabla pivote
    public function belongsToMany(string $related, string $pivotTable, string $foreignPivotKey, string $relatedPivotKey, string $localKey = 'id', string $relatedKey = 'id') {
        $localId = $this->$localKey;

        $query = "SELECT r.* FROM " . $related::$tabla . " r 
                  JOIN $pivotTable p ON r.$relatedKey = p.$relatedPivotKey
                  WHERE p.$foreignPivotKey = '$localId'";

        return $related::consultarSQL($query);
    }

    public static function with(string $relacion)
    {
        $instancia = new static;
        $registros = static::all();
        
        foreach ($registros as $registro) {
            $registro->$relacion = $registro->$relacion(); // carga anticipada
        }

        return $registros;
    }

    public static function raw(string $query) {
        return self::consultarSQL($query);
    }

    public static function query()
    {
        return new QueryBuilder(static::$tabla, static::class);
    }

    public static function getDB()
    {
        return self::$db;
    }
}