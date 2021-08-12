// Call the dataTables jQuery plugin
$(document).ready(function () {
  $('#dataTable').DataTable({
    "language": {
      "url": "//cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json"
    },
    //CAMBIAR EL ORDEN DE LAS COLUMNAS DE LA TABLA
    "ordering": false
    //order: [0, 'desc']
  });
});
