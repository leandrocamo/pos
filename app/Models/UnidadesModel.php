<?php

namespace App\Models;

use CodeIgniter\Model;

class UnidadesModel extends Model
{
    protected $table      = 'unidad';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['nombre', 'nombre_corto','activo'];

    protected $useTimestamps = true;
    protected $createdField  = 'fecha_ingreso';
    protected $updatedField  = 'fecha_editar';
    protected $deletedField  = 'deleted_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;
}
