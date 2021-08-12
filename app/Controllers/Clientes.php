<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ClientesModel;

class Clientes extends BaseController
{
    protected $clientes;
    protected $reglas;
    protected $session;

    public function __construct()
    {
        $this->session = session();
        $this->clientes = new ClientesModel();
        helper(['form']);

        $this->reglas = [
            'cedula' => [
                'rules' => 'required|is_unique[cliente.cedula]',
                'errors' => [
                    'required' => 'El campo {field} es obligatorio.',
                    'is_unique' => 'El campo {field} ya se encuentra registrado.'
                    //'is_unique' => 'El campo {field} debe ser único.'
                ]
            ],
            'nombre' => [
                'rules' => 'required',
                'errors' => ['required' => 'El campo {field} es obligatorio.']
            ],
            'direccion' => [
                'rules' => 'required',
                'errors' => ['required' => 'El campo {field} es obligatorio.']
            ],
            'telefono' => [
                'rules' => 'required',
                'errors' => ['required' => 'El campo {field} es obligatorio.']
            ],
            'correo' => [
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
        $clientes = $this->clientes->where('activo', $activo)->findAll();
        $data = ['titulo' => 'Clientes', 'datos' => $clientes];

        echo view('header');
        echo view('clientes/clientesView', $data);
        echo view('footer');
    }
    public function eliminados($activo = 0)
    {
        $clientes = $this->clientes->where('activo', $activo)->findAll();
        $data = ['titulo' => 'Clientes Eliminadas', 'datos' => $clientes];

        echo view('header');
        echo view('clientes/eliminados', $data);
        echo view('footer');
    }
    public function nuevo()
    {
        $data = ['titulo' => 'Agregar cliente'];

        echo view('header');
        echo view('clientes/nuevo', $data);
        echo view('footer');
    }

    public function insertar()
    {
        if ($this->request->getMethod() == "post" && $this->validate($this->reglas)) {
            $this->clientes->save([
                'cedula' => $this->request->getPost('cedula'),
                'nombre' => $this->request->getPost('nombre'),
                'direccion' => $this->request->getPost('direccion'),
                'telefono' => $this->request->getPost('telefono'),
                'correo' => $this->request->getPost('correo')
            ]);
            return redirect()->to(base_url() . '/clientes');
        } else {
            $data = ['titulo' => 'Agregar cliente', 'validation' => $this->validator];
            echo view('header');
            echo view('clientes/nuevo', $data);
            echo view('footer');
        }
    }
    public function editar($id, $valid = null)
    {
        $cliente = $this->clientes->where('id', $id)->first();

        if ($valid != null) {
            $data = ['titulo' => 'Editar cliente', 'datos' => $cliente, 'validation' => $valid];
        } else {
            $data = ['titulo' => 'Editar cliente', 'datos' => $cliente];
        }

        echo view('header');
        echo view('clientes/editar', $data);
        echo view('footer');
    }

    public function actualizar()
    {
        if ($this->request->getMethod() == "post" && $this->validate($this->reglas)) {
            $this->clientes->update(
                $this->request->getPost('id'),
                [
                    'cedula' => $this->request->getPost('cedula'),
                    'nombre' => $this->request->getPost('nombre'),
                    'direccion' => $this->request->getPost('direccion'),
                    'telefono' => $this->request->getPost('telefono'),
                    'correo' => $this->request->getPost('correo')
                ]
            );
            return redirect()->to(base_url() . '/clientes');
        } else {
            return $this->editar($this->request->getPost('id'), $this->validator);
        }
    }
    public function eliminar($id)
    {
        $this->clientes->update($id, ['activo' => 0]);
        return redirect()->to(base_url() . '/clientes');
    }
    public function reingresar($id)
    {
        $this->clientes->update($id, ['activo' => 1]);
        return redirect()->to(base_url() . '/clientes');
    }
    public function autocompleteData()
    {
        $returnData = array();
        $valor = $this->request->getGet('term');
        $clientes = $this->clientes->like('nombre', $valor)->where('activo', 1)->findAll();
        if (!empty($clientes)) {
            foreach ($clientes as $row) {
                $data['id'] = $row['id'];
                $data['value'] = $row['nombre'];
                array_push($returnData, $data);
            }
        }
        echo json_encode($returnData);
    }
}
