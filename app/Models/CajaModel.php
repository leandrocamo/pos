<?php

namespace App\Models;

use CodeIgniter\Model;

class CajaModel extends Model
{
    protected $table      = 'caja';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['numero_caja','nombre','folio','activo'];

    protected $useTimestamps = true;
    protected $createdField  = 'fecha_ingresar';
    protected $updatedField  = 'fecha_editar';
    protected $deletedField  = 'deleted_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;
}
