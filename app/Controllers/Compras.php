<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ComprasModel;
use App\Models\TemporalCompraModel;
use App\Models\DetalleCompraModel;
use App\Models\ProductosModel;
use App\Models\ConfiguracionModel;
use FPDF;

class Compras extends BaseController
{
    protected $compras, $temporal_compra, $detalle_compra, $productos, $configuracion;
    protected $reglas, $session;


    public function __construct()
    {
        $this->session = session();
        $this->compras = new ComprasModel();
        $this->detalle_compra = new DetalleCompraModel();
        $this->configuracion = new ConfiguracionModel();
        helper(['form']);
    }

    public function index($activo = 1)
    {
        //Si no existe una variable de sesión - Valida las sesiones
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }
        $compras = $this->compras->where('activo', $activo)->findAll();
        $data = ['titulo' => 'Compras', 'compras' => $compras];

        echo view('header');
        echo view('compras/comprasView', $data);
        echo view('footer');
    }
    public function eliminados($activo = 0)
    {
        $unidades = $this->unidades->where('activo', $activo)->findAll();
        $data = ['titulo' => 'Unidades Eliminadas', 'datos' => $unidades];

        echo view('header');
        echo view('unidades/eliminados', $data);
        echo view('footer');
    }
    public function nuevo()
    {
        echo view('header');
        echo view('compras/nuevo');
        echo view('footer');
    }

    public function guardar()
    {

        $id_compra = $this->request->getPost('id_compra');
        $total = preg_replace('/[\$,]/', '', $this->request->getPost('total'));

        $session = session();
        //        echo "HOLA";
        $resultadoId = $this->compras->insertarCompra($id_compra, $total, $session->id_usuario);
        $this->temporal_compra = new TemporalCompraModel();

        if ($resultadoId) {

            $resultadoCompra = $this->temporal_compra->porCompra($id_compra);

            foreach ($resultadoCompra as $row) {
                $this->detalle_compra->save([
                    'id_compra' => $resultadoId,
                    'id_producto' => $row['id_producto'],
                    'nombre' => $row['nombre'],
                    'cantidad' => $row['cantidad'],
                    'precio' => $row['precio']
                ]);
                $this->productos = new ProductosModel();
                $this->productos->actualizaStock($row['id_producto'], $row['cantidad']);
            }
            $this->temporal_compra->eliminarCompra($id_compra);
        }
        //return redirect()->to(base_url() . "/productos");
        return redirect()->to(base_url() . "/compras/muestraCompraPdf/" . $resultadoId);
    }
    function muestraCompraPdf($id_compra)
    {
        $data['id_compra'] = $id_compra;
        echo view('header');
        echo view('compras/ver_compra_pdf', $data);
        echo view('footer');
    }
    function generaCompraPdf($id_compra)
    {
        //compras ya está asingado en el consultor
        //vamos a realizar una consulta a la BDD del ID enviado ($id_compra) y nos devuelva lo primero registro de la consulta.
        $datosCompra = $this->compras->where('id', $id_compra)->first();
        //Consulta el detalle de esta compra con una consulta más avanzada.
        $detalleCompra = $this->detalle_compra->select('*')
            ->where('id_compra', $id_compra)
            //Se declara la variable para que se guarden los resultados de la consulta.
            ->findAll();
        $nombreTienda = $this->configuracion->select('valor')
            ->where('nombre', 'tienda_nombre')
            /*vamos a utilizar GET->GETROW() y de esta manera los valores de la consulta nos trae como objetos 
            y podemos solicitar el valor de la columna*/
            ->get()->getRow()->valor; //este nombre se llama la columna que estamos consultando.

        /*Ahora consultamos la dirección del establecimiento con el código anterior */
        $direccionTienda = $this->configuracion->select('valor')->where('nombre', 'tienda_direccion')->get()->getRow()->valor; //este nombre se llama 

        /* CREAMOS NUESTRO PDF */
        $pdf = new FPDF('P', 'mm', 'letter'); //Datos del pdf, ORIENTACIÓN, Unidad de Medidad y tamaño de la hoja
        $pdf->AddPage(); //Añadimos una hoja
        $pdf->SetMargins(10, 10, 10); //Establecemos un margen
        $pdf->SetTitle("Compra"); //Establecemo el título

        $pdf->SetFont('Arial', 'B', 10); //Declaramos los valores de la fuente
        $pdf->Cell(195, 5, "Entrada de Productos", 0, 1, 'C'); //Creación de título en el PDF
        $pdf->SetFont('Arial', 'B', 9);

        $pdf->Image(base_url() . '/images/logo.png', 185, 5, 20, 20, 'PNG'); //Inserta imagenes
        $pdf->Cell(50, 5, $nombreTienda, 0, 1, 'L');
        $pdf->Cell(20, 5, utf8_decode('Dirección: '), 0, 0, 'L');
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(50, 5, $direccionTienda, 0, 1, 'L');

        //Combinación de negritas y normal para mostrar titulos del reporte en PDF
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(25, 5, utf8_decode('Fecha y hora: '), 0, 0, 'L');
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(50, 5, $datosCompra['fecha_ingresar'], 0, 1, 'L');

        $pdf->Ln(); // Salto de línea

        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(196, 5, utf8_decode('Detalle de Producto'), 1, 1, 'C', 1);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(14, 5, 'Nro', 1, 0, 'L');
        $pdf->Cell(25, 5, utf8_decode('Código'), 1, 0, 'L');
        $pdf->Cell(77, 5, 'Nombre', 1, 0, 'L');
        $pdf->Cell(25, 5, 'Precio', 1, 0, 'L');
        $pdf->Cell(25, 5, 'Cantidad', 1, 0, 'L');
        $pdf->Cell(30, 5, 'Importe', 1, 1, 'L');

        $pdf->SetFont('Arial', '', 8);

        $contador = 1;

        foreach ($detalleCompra as $row) {
            $pdf->Cell(14, 5, $contador, 1, 0, 'L');
            $pdf->Cell(25, 5, $row['id_producto'], 1, 0, 'L');
            $pdf->Cell(77, 5, utf8_decode($row['nombre']), 1, 0, 'L');
            $pdf->Cell(25, 5, '$' . number_format($row['precio'], 2, '.', ','), 1, 0, 'L');
            $pdf->Cell(25, 5, $row['cantidad'], 1, 0, 'L');
            $importe = number_format($row['precio'] * $row['cantidad'], 2, '.', ',');
            $pdf->Cell(30, 5, '$' . $importe, 1, 1, 'R');
            $contador++;
        }
        $pdf->Ln();
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(195, 5, 'Total: ' . number_format($datosCompra['total'], 2, '.', ','), 0, 1, 'R');

        $this->response->setHeader('Content-Type', 'application/pdf'); //Con codeigneiter debemos agregar el tipo de contenido. En este caso, pdf.
        $pdf->Output("compra_pdf.pdf", "I"); //Visualizamos el PDF con Output con nombre compra_pdf.pdf y con I que visualiza el pdf en el navegador


    }
}
