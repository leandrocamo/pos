<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductosModel extends Model
{
    protected $table      = 'producto';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['codigo', 'nombre', 'precio_venta', 'precio_compra', 'existencias', 'stock_minimo', 'inventariable', 'id_unidad', 'id_categoria', 'activo'];

    protected $useTimestamps = true;
    protected $createdField  = 'fecha_ingreso';
    protected $updatedField  = '';
    protected $deletedField  = 'deleted_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function actualizaStock($id_producto, $cantidad, $operador = '+')
    {
        $this->set('existencias', "existencias $operador $cantidad", FALSE);
        $this->where('id', $id_producto);
        $this->update();
    }

    public function totalProductos()
    {
        //Cuenta todos los resultados de una consulta.
        return $this->where('activo', 1)->countAllResults();
    }
    public function productoMinimo()
    {
        $where = "stock_minimo >= existencias AND inventariable = 1 AND activo = 1";
        $this->where($where);
        $sql = $this->countAllResults();
        return $sql;
    }

    public function getProductoMinimo()
    {
        $where = "stock_minimo >= existencias AND inventariable = 1 AND activo = 1";
        return $this->where($where)->findAll();
    }
}
