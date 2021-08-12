<?php

namespace App\Controllers;

use App\Models\ProductosModel;
use App\Models\VentasModel;

class Inicio extends BaseController
{
	protected $productosModel, $session;
	protected $ventasModel;


	public function __construct()
	{
		$this->productosModel = new ProductosModel();
		$this->ventasModel = new VentasModel();
		$this->session = session();
	}

	public function index()
	{
		//Si no existe una variable de sesión - Valida las sesiones
		if (!isset($this->session->id_usuario)) {
			return redirect()->to(base_url());
		}
		$total = $this->productosModel->totalProductos();
		$minimos = $this->productosModel->productoMinimo();
		$totalVentas = $this->ventasModel->totalDia(date('Y-m-d')); //2020-11-05

		$datos = ['total' => $total, 'totalVentas' => $totalVentas, 'min' => $minimos];

		echo view('header');
		echo view('inicio', $datos);
		echo view('footer');
	}

	//--------------------------------------------------------------------

}
