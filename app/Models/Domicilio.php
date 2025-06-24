<?php

namespace App\Models;

class Domicilio extends Model
{
    protected static $tabla = 'domicilios';
    protected static $columnasDB = ['id', 'calle', 'colonia', 'numexterior', 'numinterior', 'codigoPostal', 'referencias', 'usuario_id'];

    public $id;
    public $calle;
    public $colonia;
    public $numexterior;
    public $numinterior;
    public $codigoPostal;
    public $referencias;
    public $usuario_id;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->calle = $args['calle'] ?? '';
        $this->colonia = $args['colonia'] ?? '';
        $this->numexterior = $args['numexterior'] ?? '';
        $this->numinterior = $args['numinterior'] ?? '';
        $this->codigoPostal = $args['codigoPostal'] ?? '';
        $this->referencias = $args['referencias'] ?? '';
        $this->usuario_id = $args['usuario_id'] ?? null;
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}