<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientesModel extends Model
{
    protected $table      = 'cliente';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['cedula','nombre', 'direccion', 'telefono', 'correo', 'activo'];

    protected $useTimestamps = true;
    protected $createdField  = 'fecha_ingresar';
    protected $updatedField  = 'fecha_editar';
    protected $deletedField  = 'deleted_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;
}
