<?php

namespace App\Models;

class Usuario extends Model
{
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'nombre', 'apellido', 'email', 'password', 'telefono', 'userLevel', 'confirmado', 'token', 'bloqueado'];

    public $id;
    public $nombre;
    public $apellido;
    public $email;
    public $password;
    public $telefono;
    public $userLevel;
    public $confirmado;
    public $token;
    public $bloqueado;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->apellido = $args['apellido'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->telefono = $args['telefono'] ?? '';
        $this->userLevel = $args['userLevel'] ?? 0;
        $this->confirmado = $args['confirmado'] ?? 0;
        $this->token = $args['token'] ?? '';
        $this->bloqueado = $args['bloqueado'] ?? 0;
    }

    public function domicilio()
    {
        return $this->hasOne(Domicilio::class, 'usuario_id', 'id');
    }
}