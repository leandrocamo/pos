<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ConfiguracionModel;

class Configuracion extends BaseController
{
    protected $configuracion, $session;
    protected $reglas;

    public function __construct()
    {
        $this->session = session();
        $this->configuracion = new ConfiguracionModel();
        helper(['form', 'upload']);

        $this->reglas = [
            'tienda_nombre' => [
                'rules' => 'required',
                'errors' => ['required' => 'El campo {field} es obligatorio.']
            ],
            'tienda_ruc' => [
                'rules' => 'required',
                'errors' => ['required' => 'El campo {field} es obligatorio.']
            ],
            'tienda_telefono' => [
                'rules' => 'required',
                'errors' => ['required' => 'El campo {field} es obligatorio.']
            ],
            'tienda_email' => [
                'rules' => 'required',
                'errors' => ['required' => 'El campo {field} es obligatorio.']
            ],
            'tienda_direccion' => [
                'rules' => 'required',
                'errors' => ['required' => 'El campo {field} es obligatorio.']
            ],
            'tienda_leyenda' => [
                'rules' => 'required',
                'errors' => ['required' => 'El campo {field} es obligatorio.']
            ]
        ];
    }

    public function index($activo = 1)
    {
        //Si no existe una variable de sesión - Valida las sesiones
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }
        $nombre = $this->configuracion->where('nombre', 'tienda_nombre')->first();
        $ruc = $this->configuracion->where('nombre', 'tienda_ruc')->first();
        $telefono = $this->configuracion->where('nombre', 'tienda_telefono')->first();
        $correo = $this->configuracion->where('nombre', 'tienda_email')->first();
        $direccion = $this->configuracion->where('nombre', 'tienda_direccion')->first();
        $leyenda = $this->configuracion->where('nombre', 'tienda_leyenda')->first();


        $data = [
            'titulo' => 'Configuración',
            'nombre' => $nombre,
            'ruc' => $ruc,
            'telefono' => $telefono,
            'correo' => $correo,
            'direccion' => $direccion,
            'leyenda' => $leyenda
        ];

        echo view('header');
        echo view('configuracion/configuracionView', $data);
        echo view('footer');
    }

    public function actualizar()
    {
        if ($this->request->getMethod() == "post" && $this->validate($this->reglas)) {
            $this->configuracion->whereIn('nombre', ['tienda_nombre'])->set(['valor' => $this->request->getPost('tienda_nombre')])->update();
            $this->configuracion->whereIn('nombre', ['tienda_ruc'])->set(['valor' => $this->request->getPost('tienda_ruc')])->update();
            $this->configuracion->whereIn('nombre', ['tienda_telefono'])->set(['valor' => $this->request->getPost('tienda_telefono')])->update();
            $this->configuracion->whereIn('nombre', ['tienda_email'])->set(['valor' => $this->request->getPost('tienda_email')])->update();
            $this->configuracion->whereIn('nombre', ['tienda_direccion'])->set(['valor' => $this->request->getPost('tienda_direccion')])->update();
            $this->configuracion->whereIn('nombre', ['tienda_leyenda'])->set(['valor' => $this->request->getPost('tienda_leyenda')])->update();

            $img = $this->request->getFile('tienda_logo');

            $validacion = $this->validate([
                'tienda_logo' => [
                    'uploaded[tienda_logo]',
                    'mime_in[tienda_logo,image/png]',
                    'max_size[tienda_logo, 4096]'
                ]
            ]);
            // GUARDA IMAGEN AUTOMATICAMENTE EN \writable\uploads
            //$img->move(WRITEPATH . '/uploads');
            if ($validacion) {
                $ruta_logo = "images/logotipo.png";
                if (file_exists($ruta_logo)) {
                    unlink($ruta_logo);
                }
                $img = $this->request->getFile('tienda_logo');
                $img->move('./images', 'logotipo.png');
            } else {
                echo 'ERROR en la validación';
                exit;
            }


            return redirect()->to(base_url() . '/configuracion');
        } else {
            //return $this->editar($this->request->getPost('id'), $this->validator);
        }
    }
}
