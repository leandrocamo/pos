<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <h4 class="mt-4"><?php echo $titulo ?></h4>

            <?php if (isset($validation)) { ?>
                <div class="alert alert-danger">
                    <?php echo $validation->listErrors(); ?>
                </div>
            <?php } ?>

            <form method="POST" action="<?php echo base_url(); ?>/usuario/insertar" autocomplete="off">
                <div class="form-group">
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <label>Usuario</label>
                            <input class="form-control" id="usuario" name="usuario" type="text" value="<?php echo set_value('usuario'); ?>" autofocus required />
                        </div>
                        <div class="col-12 col-sm-6">
                            <label>Nombre</label>
                            <input class="form-control" id="nombre" name="nombre" type="text" value="<?php echo set_value('nombre'); ?>" required />
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <label>Contraseña</label>
                            <input class="form-control" id="password" name="password" type="password" value="<?php echo set_value('password'); ?>" required />
                        </div>
                        <div class="col-12 col-sm-6">
                            <label>Confirmar contraseña</label>
                            <input class="form-control" id="repassword" name="repassword" type="password" value="<?php echo set_value('password'); ?>" required />
                        </div>
                    </div>
                </div>


                <div class="form-group">
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <label>Caja</label>
                            <select class="form-control" id="id_caja" name="id_caja" required>
                                <option value="">Seleccionar caja</option>
                                <?php foreach ($cajas as $caja) {
                                    echo '<option value="' . $caja['id'] . '">' . $caja['nombre'] . '</option>';
                                } ?>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label>Rol</label>
                            <select class="form-control" id="id_rol" name="id_rol" required>
                                <option value="">Seleccionar rol</option>
                                <?php foreach ($roles as $rol) {
                                    echo '<option value="' . $rol['id'] . '">' . $rol['nombre'] . '</option>';
                                } ?>
                            </select>
                        </div>
                    </div>

                </div>
      



        <a href="<?php echo base_url(); ?>/usuario" class="btn btn-primary">Regresar</a>
        <button type="submit" class="btn btn-success">Guardar</button>
        </form>
</div>
</main>