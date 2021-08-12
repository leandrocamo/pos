<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProductosModel;
use App\Models\UnidadesModel;
use App\Models\CategoriasModel;
use App\Models\DetalleRolesPermisosModel;

class Productos extends BaseController
{
    protected $productos, $session, $detalleRoles;
    protected $reglas;

    public function __construct()
    {
        $this->session = session();
        $this->productos = new ProductosModel();
        $this->unidades = new UnidadesModel();
        $this->categorias = new CategoriasModel();
        $this->detalleRoles = new DetalleRolesPermisosModel();

        helper(['form']);

        $this->reglas = [
            'codigo' => [
                'rules' => 'required|is_unique[producto.codigo]',
                'errors' => [
                    'required' => 'El campo {field} es obligatorio.',
                    'is_unique' => 'El campo {field} debe ser único.'
                ]
            ], 'nombre' => [
                'rules' => 'required',
                'errors' => ['required' => 'El campo {field} es obligatorio.']
            ], 'precio_venta' => [
                'rules' => 'required',
                'errors' => ['required' => 'El campo {field} es obligatorio.']
            ], 'precio_compra' => [
                'rules' => 'required',
                'errors' => ['required' => 'El campo {field} es obligatorio.']
            ], 'stock_minimo' => [
                'rules' => 'required',
                'errors' => ['required' => 'El campo {field} es obligatorio.']
            ]
        ];
    }

    public function index($activo = 1)
    {
        //Valido el acceso a los roles
        $permiso = $this->detalleRoles->verificaPermisos($this->session->id_rol, 'ProductosCatalogo');

        if (!$permiso) {
            echo 'no tiene permiso';
            exit;
        }

        //Si no existe una variable de sesión - Valida las sesiones
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }
        //Consulta que trae todos los productos de la BDD
        $productos = $this->productos->where('activo', $activo)->findAll();
        $data = ['titulo' => 'Productos', 'datos' => $productos];

        echo view('header');
        echo view('productos/productos', $data);
        echo view('footer');
    }
    public function eliminados($activo = 0)
    {
        $productos = $this->productos->where('activo', $activo)->findAll();
        $data = ['titulo' => 'Productos Eliminadas', 'datos' => $productos];

        echo view('header');
        echo view('productos/eliminados', $data);
        echo view('footer');
    }
    public function nuevo()
    {
        $unidades = $this->unidades->where('activo', 1)->findAll();
        $categorias = $this->categorias->where('activo', 1)->findAll();
        $data = ['titulo' => 'Agregar producto', 'unidades' => $unidades, 'categorias' => $categorias];

        echo view('header');
        echo view('productos/nuevo', $data);
        echo view('footer');
    }

    public function insertar()
    {
        if ($this->request->getMethod() == "post" && $this->validate($this->reglas)) {
            $this->productos->save([
                'codigo' => $this->request->getPost('codigo'),
                'nombre' => $this->request->getPost('nombre'),
                'precio_venta' => $this->request->getPost('precio_venta'),
                'precio_compra' => $this->request->getPost('precio_compra'),
                'stock_minimo' => $this->request->getPost('stock_minimo'),
                'inventariable' => $this->request->getPost('inventariable'),
                'id_unidad' => $this->request->getPost('id_unidad'),
                'id_categoria' => $this->request->getPost('id_categoria')
            ]);

            //Guardo el ID del producto ingresado
            $id = $this->productos->insertID();

            $validacion = $this->validate([
                'img_producto' => [
                    'uploaded[img_producto]',
                    'mime_in[img_producto,image/jpg,image/jpeg]',
                    'max_size[img_producto, 4096]'
                ]
            ]);
            if ($validacion) {
                $ruta_logo = "images/productos/" . $id . ".jpg";
                if (file_exists($ruta_logo)) {
                    unlink($ruta_logo);
                }
                $img = $this->request->getFile('img_producto');
                $img->move('./images/productos/', $id . '.jpg');
            } else {
                echo 'ERROR en la validación';
            }




            return redirect()->to(base_url() . '/productos');
        } else {
            $unidades = $this->unidades->where('activo', 1)->findAll();
            $categorias = $this->categorias->where('activo', 1)->findAll();
            $data = ['titulo' => 'Agregar producto', 'unidades' => $unidades, 'categorias' => $categorias, 'validation' => $this->validator];

            echo view('header');
            echo view('productos/nuevo', $data);
            echo view('footer');
        }
    }
    public function editar($id, $valid = null)
    {
        $unidades = $this->unidades->where('activo', 1)->findAll();
        $categorias = $this->categorias->where('activo', 1)->findAll();
        $producto = $this->productos->where('id', $id)->first();

        if ($valid != null) {
            $data = ['titulo' => 'Editar producto', 'unidades' => $unidades, 'categorias' => $categorias, 'producto' => $producto, 'validation' => $valid];
        } else {
            $data = ['titulo' => 'Editar producto', 'unidades' => $unidades, 'categorias' => $categorias, 'producto' => $producto];
        }

        echo view('header');
        echo view('productos/editar', $data);
        echo view('footer');
    }

    public function actualizar()
    {
        $this->productos->update(
            $this->request->getPost('id'),
            [
                'codigo' => $this->request->getPost('codigo'),
                'nombre' => $this->request->getPost('nombre'),
                'precio_venta' => $this->request->getPost('precio_venta'),
                'precio_compra' => $this->request->getPost('precio_compra'),
                'stock_minimo' => $this->request->getPost('stock_minimo'),
                'inventariable' => $this->request->getPost('inventariable'),
                'id_unidad' => $this->request->getPost('id_unidad'),
                'id_categoria' => $this->request->getPost('id_categoria')
            ]
        );
        //Guardo el ID del producto ingresado
        $id = $this->request->getPost('id');

        $validacion = $this->validate([
            'img_producto' => [
                'uploaded[img_producto]',
                'mime_in[img_producto,image/jpg,image/jpeg]',
                'max_size[img_producto, 4096]'
            ]
        ]);

        if ($validacion) {
            $ruta_logo = "images/productos/" . $id . ".jpg";
            echo 'ERROR: ' . $ruta_logo;
            //exit;
            if (file_exists($ruta_logo)) {
                unlink($ruta_logo);
            }
            $img = $this->request->getFile('img_producto');
            $img->move('./images/productos/', $id . '.jpg');
        } else {
            echo 'ERROR en la validación';
            // exit;
        }

        /*if ($validacion) {
            $ruta_logo = "images/logotipo.png";
            if (file_exists($ruta_logo)) {
                unlink($ruta_logo);
            }
            $img = $this->request->getFile('tienda_logo');
            $img->move('./images', 'logotipo.png');
        } else {
            echo 'ERROR en la validación';
        }*/

        return redirect()->to(base_url() . '/productos');
    }
    public function eliminar($id)
    {
        $this->productos->update($id, ['activo' => 0]);
        return redirect()->to(base_url() . '/productos');
    }
    public function reingresar($id)
    {
        $this->productos->update($id, ['activo' => 1]);
        return redirect()->to(base_url() . '/productos');
    }
    public function buscarPorCodigo($codigo)
    {
        $this->productos->select('*');
        $this->productos->where('codigo', $codigo);
        $this->productos->where('activo', 1);
        $datos = $this->productos->get()->getRow();


        $res['existe'] = false;
        $res['datos'] = '';
        $res['error'] = '';

        if ($datos) {
            $res['datos'] = $datos;
            $res['existe'] = true;
        } else {
            $res['error'] = 'No existe el producto';
            $res['existe'] = false;
        }
        echo json_encode($res);
    }

    public function autocompleteData()
    {
        $returnData = array();
        $valor = $this->request->getGet('term');
        $productos = $this->productos->like('codigo', $valor)->where('activo', 1)->findAll();
        if (!empty($productos)) {
            foreach ($productos as $row) {
                $data['id'] = $row['id'];
                $data['value'] = $row['codigo'];
                $data['label'] = $row['codigo'] . ' - ' . $row['nombre'];
                array_push($returnData, $data);
            }
        }
        echo json_encode($returnData);
    }
    public function generaBarras()
    {
        $pdf = new \FPDF('p', 'mm', 'letter');
        $pdf->AddPage();
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetTitle("Codigo de barras");

        $productos = $this->productos->where('activo', 1)->findAll();
        foreach ($productos as $producto) {
            $codigo = $producto['codigo'];
            $generaBarcode = new \barcode_genera();
            $generaBarcode->barcode("images/barcode/" . $codigo . ".png", $codigo, 20, "horizontal", "code39", true);
            $pdf->Image("images/barcode/" . $codigo . ".png");
            //Borra los códigos de barras generados y guardados!
            unlink("images/barcode/" . $codigo . ".png");
        }
        $this->response->setHeader('Content-Type', 'application/pdf');
        $pdf->Output('Codigo.pdf', 'I');
    }
    function muestraCodigo()
    {
        echo view('header');
        echo view('productos/ver_codigos');
        echo view('footer');
    }

    public function generaMinimosPdf()
    {
        $pdf = new \FPDF('p', 'mm', 'letter');
        $pdf->AddPage();
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetTitle("Productos con stock mínimo");

        $pdf->SetFont("Arial", "B", 10);
        $pdf->Image("images/logotipo.png", 10, 5, 20);

        $pdf->Cell(0, 5, utf8_decode("Reporte de producto con stock mínimo"), 0, 1, 'C');
        $pdf->Ln(15);

        $pdf->Cell(30, 5, utf8_decode("Código"), 1, 0, 'C');
        $pdf->Cell(100, 5, utf8_decode("Nombre"), 1, 0, 'C');
        $pdf->Cell(30, 5, utf8_decode("Existencias"), 1, 0, 'C');
        $pdf->Cell(30, 5, utf8_decode("Stock Mínimo"), 1, 1, 'C');

        $datosProductos = $this->productos->getProductoMinimo();

        $pdf->SetFont("Arial", "", 10);

        foreach ($datosProductos as $producto) {
            $pdf->Cell(30, 5, $producto['codigo'], 1, 0, 'C');
            $pdf->Cell(100, 5, utf8_decode($producto['nombre']), 1, 0, 'C');
            $pdf->Cell(30, 5, $producto['existencias'], 1, 0, 'C');
            $pdf->Cell(30, 5, $producto['stock_minimo'], 1, 1, 'C');
        }



        $this->response->setHeader('Content-Type', 'application/pdf');
        $pdf->Output('ProductoMinimo.pdf', 'I');
    }
    function mostrarMinimos()
    {
        echo view('header');
        echo view('productos/ver_minimos');
        echo view('footer');
    }
}
