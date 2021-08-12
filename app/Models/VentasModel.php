<?php

namespace App\Models;

use CodeIgniter\Model;

class VentasModel extends Model
{
    protected $table      = 'venta';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['folio', 'total', 'id_usuario', 'id_caja', 'id_cliente', 'forma_pago', 'activo'];

    protected $useTimestamps = true;
    protected $createdField  = 'fecha_ingresar';
    protected $updatedField  = '';
    protected $deletedField  = '';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function insertaVenta($id_venta, $total, $id_usuario, $id_caja, $id_cliente, $forma_pago)
    {
        $this->insert([
            'folio' => $id_venta,
            'total' => $total,
            'id_usuario' => $id_usuario,
            'id_caja' => $id_caja,
            'id_cliente' => $id_cliente,
            'forma_pago' => $forma_pago
        ]);
        return $this->insertID();
    }
    public function obtener($activo = 1)
    {
        $this->select('venta.*, u.usuario AS cajero, c.nombre');
        $this->join('usuario AS u', 'venta.id_usuario=u.id'); //INNER JOIN
        $this->join('cliente AS c', 'venta.id_cliente=c.id');
        $this->join('caja AS ca', 'venta.id_caja=ca.id');
        $this->where('venta.activo', $activo);
        $this->orderBy('venta.fecha_ingresar', 'DESC');
        $datos = $this->findAll();
        //PERMITE IMPRIMIR LA SENTENCIA SQL
        //print_r($this->getlastQuery());
        return $datos;
    }
    public function totalDia($fecha)
    {
        //sumar las ventas del día
        $this->select("sum(total) AS total");
        //Cuenta todos los resultados de una consulta.
        //Consulta 2 filtros
        $where = "activo = 1 AND DATE(fecha_ingresar) = '$fecha'";
        $datos = $this->where($where)->first();
        //return $this->where($where)->countAllResults();
        return $datos;
    }
}
