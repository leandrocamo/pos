<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\VentasModel;
use App\Models\TemporalCompraModel;
use App\Models\DetalleVentaModel;
use App\Models\ProductosModel;
use App\Models\ConfiguracionModel;
use App\Models\CajaModel;




class Venta extends BaseController
{
    protected $ventas, $temporal_compra, $detalle_venta, $productos, $configuracion, $cajas, $session;

    public function __construct()
    {
        $this->session = session();
        $this->ventas = new VentasModel();
        $this->detalle_venta = new DetalleVentaModel();
        $this->configuracion = new ConfiguracionModel();
        $this->productos = new ProductosModel();
        $this->cajas = new CajaModel();
        helper(['form']);
    }

    public function venta()
    {
        //Si no existe una variable de sesión - Valida las sesiones
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }
        echo view('header');
        echo view('venta/caja');
        echo view('footer');
    }


    public function index()
    {
        //Si no existe una variable de sesión - Valida las sesiones
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }
        $datos = $this->ventas->obtener(1);
        $data = ['titulo' => 'Ventas', 'datos' => $datos];

        echo view('header');
        echo view('venta/ventaView', $data);
        echo view('footer');
    }

    public function eliminados()
    {
        $datos = $this->ventas->obtener(0);
        $data = ['titulo' => 'Ventas Eliminadas', 'datos' => $datos];

        echo view('header');
        echo view('venta/eliminados', $data);
        echo view('footer');
    }

    public function guardar()
    {
        $id_venta = $this->request->getPost('id_venta');
        $total = preg_replace('/[\$,]/', '', $this->request->getPost('total'));
        $forma_pago = $this->request->getPost('forma_pago');
        $id_cliente = $this->request->getPost('id_cliente');

        //Invoco session
        //$session = session();
        $id_caja = $this->session->id_caja;
        $caja = $this->cajas->where('id', $id_caja)->first();
        $folio = $caja['folio'];

        $resultadoId = $this->ventas->insertaVenta($folio, $total, $this->session->id_usuario, $this->session->id_caja, $id_cliente, $forma_pago);
        $this->temporal_compra = new TemporalCompraModel();

        if ($resultadoId) {
            //aumento 1 al folio
            $folio++;
            //actualizo el folio en caja
            $this->cajas->update($this->session->id_caja, ['folio' => $folio]);

            $resultadoCompra = $this->temporal_compra->porCompra($id_venta);

            foreach ($resultadoCompra as $row) {
                $this->detalle_venta->save([
                    'id_venta' => $resultadoId,
                    'id_producto' => $row['id_producto'],
                    'nombre' => $row['nombre'],
                    'cantidad' => $row['cantidad'],
                    'precio' => $row['precio']
                ]);
                $this->productos = new ProductosModel();
                $this->productos->actualizaStock($row['id_producto'], $row['cantidad'], '-');
            }
            $this->temporal_compra->eliminarCompra($id_venta);
        }
        return redirect()->to(base_url() . "/venta/muestraTicket/" . $resultadoId);
    }
    function muestraTicket($id_venta)
    {
        $data['id_venta'] = $id_venta;
        echo view('header');
        echo view('venta/ver_ticket', $data);
        echo view('footer');
    }

    function generaTicket($id_venta)
    {
        $datosVenta = $this->ventas->where('id', $id_venta)->first();
        $detalleVenta = $this->detalle_venta->select('*')
            ->where('id_venta', $id_venta)
            ->findAll();
        $nombreTienda = $this->configuracion->select('valor')
            ->where('nombre', 'tienda_nombre')
            ->get()->getRow()->valor;

        $direccionTienda = $this->configuracion->select('valor')->where('nombre', 'tienda_direccion')->get()->getRow()->valor;
        $leyendaTicket = $this->configuracion->select('valor')->where('nombre', 'tienda_leyenda')->get()->getRow()->valor;
        $pdf = new \FPDF('P', 'mm', array(80, 200)); //Datos del pdf, ORIENTACIÓN, Unidad de Medidad y tamaño de la hoja (arreglo para poner el ancho y largo)
        $pdf->AddPage(); //Añadimos una hoja
        $pdf->SetMargins(5, 5, 5); //Establecemos un margen
        $pdf->SetTitle("Venta"); //Establecemo el título

        //$pdf->Image(base_url() . '/images/logo.png', 0, 0, 15, 15, 'PNG');
        //Declaramos los valores de la fuente
        $BordeCelda = 0;
        $pdf->SetFont('Courier', 'B', 8);
        $pdf->Cell(60, 3, "TICKET VENTA", $BordeCelda, 1, 'C'); //Creación de título en el PDF
        $pdf->MultiCell(70, 3, $nombreTienda, $BordeCelda, 'C', 0); //Nombre de la tienda
        $pdf->MultiCell(60, 3, $direccionTienda, $BordeCelda, 'L', 0);

        //$pdf->Cell(20, 5, utf8_decode('Dirección: '), 0, 0, 'L');
        //$pdf->SetFont('Arial', '', 9);
        //$pdf->Cell(0, 10, $direccionTienda, 0, 1, 'C');


        //$texto = utf8_decode('Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum');
        //$pdf->MultiCell(65, 3, $texto);

        //Combinación de negritas y normal para mostrar titulos del reporte en PDF
        //$pdf->SetFont('Arial', 'B', 9);

        $pdf->Cell(35, 3, utf8_decode('Fecha: '), $BordeCelda, 0, 'R');
        $pdf->SetFont('Courier', '', 8);
        $pdf->Cell(35, 3, $datosVenta['fecha_ingresar'], $BordeCelda, 1, 'L');

        $pdf->Cell(70, 3, 'Folio: ' . $datosVenta['folio'], $BordeCelda, 1, 'R');

        $pdf->SetFont('Courier', 'B', 7);
        $pdf->Cell(70, 3, '---------------------------------------------------', $BordeCelda, 1, 'C');
        //$pdf->Ln();
        $pdf->Cell(7, 3, 'CANT.', $BordeCelda, 0, 'R');
        $pdf->Cell(35, 3, utf8_decode('DESCRIPCIÓN'), $BordeCelda, 0, 'L');
        $pdf->Cell(15, 3, 'PRECIO', $BordeCelda, 0, 'L');
        $pdf->Cell(13, 3, 'IMPORTE', $BordeCelda, 1, 'L');
        $pdf->Cell(70, 3, '---------------------------------------------------', $BordeCelda, 1, 'C');
        //$pdf->Ln();
        $pdf->SetFont('Courier', '', 7);
        $contador = 1;

        foreach ($detalleVenta as $row) {
            $pdf->Cell(7, 3,  $row['cantidad'], $BordeCelda, 0, 'L');
            //CONTAR 23 CARACTERES PARA NOMBRE DEL PRODUCTO
            $pdf->Cell(35, 3, utf8_decode($row['nombre']), $BordeCelda, 0, 'L');
            $pdf->Cell(15, 3, '$' . number_format($row['precio'], 2, '.', ','), $BordeCelda, 0, 'L');
            $importe = number_format($row['precio'] * $row['cantidad'], 2, '.', ',');
            $pdf->Cell(13, 3, '$' . $importe, $BordeCelda, 1, 'R');
            $contador++;
        }
        $pdf->SetFont('Courier', 'B', 7);
        $pdf->Cell(70, 3, '---------------------------------------------------', $BordeCelda, 1, 'C');
        $pdf->Cell(70, 5, 'Total: ' . number_format($datosVenta['total'], 2, '.', ','), $BordeCelda, 1, 'R');
        $pdf->Cell(70, 3, '---------------------------------------------------', $BordeCelda, 1, 'C');


        $pdf->Ln();
        $pdf->MultiCell(70, 4, $leyendaTicket, $BordeCelda, 'C', 0);


        $this->response->setHeader('Content-Type', 'application/pdf');
        $pdf->Output("ticket.pdf", "I");
    }
    public function eliminar($id)
    {
        $productos = $this->detalle_venta->where('id_venta', $id)->findAll();
        foreach ($productos as $producto) {
            $this->productos->actualizaStock($producto['id_producto'], $producto['cantidad'], '+');
        }
        $this->ventas->update($id, ['activo' => 0]);
        return redirect()->to(base_url() . '\venta');
    }
}
