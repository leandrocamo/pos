<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UsuarioModel;
use App\Models\CajaModel;
use App\Models\RolModel;
use App\Models\LogsModel;

class Usuario extends BaseController
{
    protected $usuario, $session, $logs;
    protected $reglas, $reglaslogin, $reglasCambia;

    public function __construct()
    {
        $this->session = session();
        $this->usuario = new UsuarioModel();
        $this->caja = new CajaModel();
        $this->rol = new RolModel();
        $this->logs = new LogsModel();
        helper(['form']);

        $this->reglas = [
            'usuario' => [
                'rules' => 'required|is_unique[usuario.usuario]',
                'errors' => [
                    'required' => 'El campo {field} es obligatorio.',
                    'is_unique' => 'El campo {field} debe ser único.'
                ]
            ], 'password' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'El campo {field} es obligatorio.'
                ]
            ], 'repassword' => [
                'rules' => 'required|matches[password]',
                'errors' => [
                    'required' => 'El campo {field} es obligatorio.',
                    'matches' => 'Las contraseñas no coinciden.'
                ]
            ], 'nombre' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'El campo {field} es obligatorio.'
                ]
            ], 'id_caja' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'El campo {field} es obligatorio.'
                ]
            ], 'id_rol' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'El campo {field} es obligatorio.'
                ]
            ]
        ];

        $this->reglaslogin = [
            'usuario' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'El campo {field} es obligatorio.'
                ]
            ],
            'password' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'El campo {field} es obligatorio.'
                ]
            ]
        ];
        $this->reglasCambia = [
            'password' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'El campo {field} es obligatorio.'
                ]
            ], 'repassword' => [
                'rules' => 'required|matches[password]',
                'errors' => [
                    'required' => 'El campo {field} es obligatorio.',
                    'matches' => 'Las contraseñas no coinciden.'
                ]
            ]
        ];
    }

    public function index($activo = 1)
    {
        //Si no existe una variable de sesión - Valida las sesiones
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }
        $usuario = $this->usuario->where('activo', $activo)->findAll();
        $data = ['titulo' => 'Usuarios', 'datos' => $usuario];

        echo view('header');
        echo view('usuario/usuarioView', $data);
        echo view('footer');
    }
    public function eliminados($activo = 0)
    {
        $usuario = $this->usuario->where('activo', $activo)->findAll();
        $data = ['titulo' => 'Usuario Eliminadas', 'datos' => $usuario];

        echo view('header');
        echo view('usuario/eliminados', $data);
        echo view('footer');
    }
    public function nuevo()
    {
        $cajas = $this->caja->where('activo', 1)->findAll();
        $roles = $this->rol->where('activo', 1)->findAll();
        $data = ['titulo' => 'Agregar usuario', 'cajas' => $cajas, 'roles' => $roles];

        echo view('header');
        echo view('usuario/nuevo', $data);
        echo view('footer');
    }

    public function insertar()
    {
        if ($this->request->getMethod() == "post" && $this->validate($this->reglas)) {
            $hash = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);

            $this->usuario->save([
                'usuario' => $this->request->getPost('usuario'),
                'password' => $hash,
                'nombre' => $this->request->getPost('nombre'),
                'id_caja' => $this->request->getPost('id_caja'),
                'id_rol' => $this->request->getPost('id_rol')
            ]);
            return redirect()->to(base_url() . '/usuario');
        } else {
            $cajas = $this->caja->where('activo', 1)->findAll();
            $roles = $this->rol->where('activo', 1)->findAll();
            $data = ['titulo' => 'Agregar usuario', 'cajas' => $cajas, 'roles' => $roles, 'validation' => $this->validator];
            echo view('header');
            echo view('usuario/nuevo', $data);
            echo view('footer');
        }
    }
    public function editar($id, $valid = null)
    {
        $usuario = $this->usuario->where('id', $id)->first();

        if ($valid != null) {
            $data = ['titulo' => 'Editar usuario', 'datos' => $usuario, 'validation' => $valid];
        } else {
            $data = ['titulo' => 'Editar usuario', 'datos' => $usuario];
        }

        echo view('header');
        echo view('usuario/editar', $data);
        echo view('footer');
    }

    public function actualizar()
    {
        if ($this->request->getMethod() == "post" && $this->validate($this->reglas)) {
            $this->usuario->update(
                $this->request->getPost('id'),
                [
                    'nombre' => $this->request->getPost('nombre'),
                    'nombre_corto' => $this->request->getPost('nombre_corto')
                ]
            );
            return redirect()->to(base_url() . '/usuario');
        } else {
            return $this->editar($this->request->getPost('id'), $this->validator);
        }
    }
    public function eliminar($id)
    {
        $this->usuario->update($id, ['activo' => 0]);
        return redirect()->to(base_url() . '/usuario');
    }
    public function reingresar($id)
    {
        $this->usuario->update($id, ['activo' => 1]);
        return redirect()->to(base_url() . '/usuario');
    }
    public function login()
    {
        echo view('login');
    }
    public function valida()
    {
        if ($this->request->getMethod() == "post" && $this->validate($this->reglaslogin)) {
            $usuario = $this->request->getPost('usuario');
            $password = $this->request->getPost('password');
            $datosUsuario = $this->usuario->where('usuario', $usuario)->first();

            if ($datosUsuario != null) {
                if (password_verify($password, $datosUsuario['password'])) {
                    $datosSesion = [
                        'id_usuario' => $datosUsuario['id'],
                        'nombre' => $datosUsuario['nombre'],
                        'id_caja' => $datosUsuario['id_caja'],
                        'id_rol' => $datosUsuario['id_rol']
                    ];

                    //Registro de acceso del usuario a logs
                    $ip = $_SERVER['REMOTE_ADDR'];//Guardad la IP
                    $detalle = $_SERVER['HTTP_USER_AGENT'];//Guarda el navegador y SO
                    //Guarda en log los datos de la tabla log
                    $this->logs->save([
                        'id_usuario' => $datosUsuario['id'],
                        'evento' => 'Inicio de sesión',
                        'ip' => $ip,
                        'detalle' => $detalle,
                    ]);

                    $session = session();
                    $session->set($datosSesion);
                    return redirect()->to(base_url() . '/inicio');
                } else {
                    $data['error'] = "Las contraseñas no coincide";
                    echo view('login', $data);
                }
            } else {
                $data['error'] = "El usuario no existe";
                echo view('login', $data);
            }
        } else {
            $data = ['validation' => $this->validator];
            echo view('login', $data);
        }
    }
    public function logout()
    {
        $session = session();
        //Registro de acceso del usuario a logs
        $ip = $_SERVER['REMOTE_ADDR'];//Guardad la IP
        $detalle = $_SERVER['HTTP_USER_AGENT'];//Guarda el navegador y SO
        //Guarda en log los datos de la tabla log
        $this->logs->save([
            'id_usuario' => $session->id_usuario,
            'evento' => 'Cierre de sesión',
            'ip' => $ip,
            'detalle' => $detalle,
        ]);
        $session->destroy();
        return redirect()->to(base_url());
    }
    public function cambia_password()
    {
        $session = session();
        $usuario = $this->usuario->where('id', $session->id_usuario)->first();
        $data = ['titulo' => 'Cambiar contraseña', 'usuario' => $usuario];

        echo view('header');
        echo view('usuario/cambia_password', $data);
        echo view('footer');
    }
    public function actualizar_password()
    {
        if ($this->request->getMethod() == "post" && $this->validate($this->reglasCambia)) {
            $session = session();
            $idUsuario = $session->id_usuario;
            $hash = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);

            $this->usuario->update($idUsuario, ['password' => $hash]);

            $usuario = $this->usuario->where('id', $session->id_usuario)->first();
            $data = ['titulo' => 'Cambiar contraseña', 'usuario' => $usuario, 'mensaje' => 'Contraseña actualizada'];

            echo view('header');
            echo view('usuario/cambia_password', $data);
            echo view('footer');
        } else {
            $session = session();
            $usuario = $this->usuario->where('id', $session->id_usuario)->first();
            $data = ['titulo' => 'Cambiar contraseña', 'usuario' => $usuario, 'validation' => $this->validator];

            echo view('header');
            echo view('usuario/cambia_password', $data);
            echo view('footer');
        }
    }
}
