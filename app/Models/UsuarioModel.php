<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table      = 'usuario';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['usuario','password','nombre','id_caja','id_rol','activo'];

    protected $useTimestamps = true;
    protected $createdField  = 'fecha_ingresar';
    protected $updatedField  = 'fecha_editar';
    protected $deletedField  = 'deleted_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;
}
