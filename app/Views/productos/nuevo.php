<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <h4 class="mt-4"><?php echo $titulo ?></h4>

            <?php if (isset($validation)) { ?>
                <div class="alert alert-danger">
                    <?php echo $validation->listErrors(); ?>
                </div>
            <?php } ?>


            <form method="POST" enctype="multipart/form-data" action="<?php echo base_url(); ?>/productos/insertar" autocomplete="off">

                <div class="form-group">
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <label>Código</label>
                            <input class="form-control" id="codigo" name="codigo" type="text" value="<?php echo set_value('codigo'); ?>" autofocus required />
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
                            <label>Unidad</label>
                            <select class="form-control" id="id_unidad" name="id_unidad">
                                <option value="">Seleccionar unidad</option>
                                <?php foreach ($unidades as $unidad) {
                                    echo '<option value="' . $unidad['id'] . '">' . $unidad['nombre'] . '</option>';
                                } ?>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label>Categoria</label>
                            <select class="form-control" id="id_categoria" name="id_categoria">
                                <option value="">Seleccionar categoria</option>
                                <?php foreach ($categorias as $categoria) {
                                    echo '<option value="' . $categoria['id'] . '">' . $categoria['nombre'] . '</option>';
                                } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <label>Precio venta</label>
                            <input class="form-control" id="precio_venta" name="precio_venta" type="text" value="<?php echo set_value('precio_venta'); ?>" required />
                        </div>
                        <div class="col-12 col-sm-6">
                            <label>Precio compra</label>
                            <input class="form-control" id="precio_compra" name="precio_compra" type="text" value="<?php echo set_value('precio_compra'); ?>" required />
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <label>Stock mínimo</label>
                            <input class="form-control" id="stock_minimo" name="stock_minimo" type="text" value="<?php echo set_value('stock_minimo'); ?>" required />
                        </div>
                        <div class="col-12 col-sm-6">
                            <label>Es inventariable</label>
                            <select id="inventariable" name="inventariable" class="form-control">
                                <option value="1">Sí</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <label>Imagen del producto</label><br />
                            <input type="file" id="img_producto" name="img_producto" accept="image/*" />
                            <p class="text-danger">Cargar imagen en formato PNG de 150 x 150 px</p>
                        </div>
                    </div>
                </div>




                <a href="<?php echo base_url(); ?>/productos" class="btn btn-primary">Regresar</a>
                <button type="submit" class="btn btn-success">Guardar</button>
            </form>
        </div>
    </main>