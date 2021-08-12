<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <h4 class="mt-4"><?php echo $titulo ?></h4>

            <?php if (isset($validation)) { ?>
                <div class="alert alert-danger">
                    <?php echo $validation->listErrors(); ?>
                </div>
            <?php } ?>

            <form method="POST" enctype="multipart/form-data" action="<?php echo base_url(); ?>/configuracion/actualizar" autocomplete="off">
                <div class="form-group">
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <label>Nombre de la Tienda</label>
                            <input class="form-control" id="tienda_nombre" name="tienda_nombre" type="text" value="<?php echo $nombre['valor']; ?>" autofocus required />
                        </div>
                        <div class="col-12 col-sm-6">
                            <label>RUC</label>
                            <input class="form-control" id="tienda_ruc" name="tienda_ruc" type="text" value="<?php echo $ruc['valor']; ?>" required />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <label>Telefono</label>
                            <input class="form-control" id="tienda_telefono" name="tienda_telefono" type="text" value="<?php echo $telefono['valor']; ?>" required />
                        </div>
                        <div class="col-12 col-sm-6">
                            <label>Correo Electrónico</label>
                            <input class="form-control" id="tienda_email" name="tienda_email" type="text" value="<?php echo $correo['valor']; ?>" required />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <label>Direccion</label>
                            <textarea class="form-control" id="tienda_direccion" name="tienda_direccion" required> <?php echo $direccion['valor']; ?> </textarea>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label>Leyenda</label>
                            <textarea class="form-control" id="tienda_leyenda" name="tienda_leyenda" required><?php echo $leyenda['valor']; ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <label>Logotipo</label><br />
                            <img src="<?php echo base_url() . '/images/logotipo.png'; ?>" class="img-resposive" width="200">
                            <input type="file" id="tienda_logo" name="tienda_logo" accept="image/png" />
                            <p class="text-danger">Cargar imagen en formato PNG de 150 x 150 px</p>
                        </div>
                    </div>
                </div>

                <a href="<?php echo base_url(); ?>/clientes" class="btn btn-primary">Regresar</a>
                <button type="submit" class="btn btn-success">Guardar</button>
            </form>

        </div>
    </main>
    <!-- Modal -->
    <div class="modal fade" id="modal-confirma" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Eliminar registro</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>¿Desea eliminar este registro?</p>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-danger btn-ok">Aceptar</a>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>