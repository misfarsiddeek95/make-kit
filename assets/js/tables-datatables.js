'use strict';

(function ($) {
  'use strict';

  //  Default Table

  $('#table-1').DataTable();

  // Exporting Table Data
  $('#table-2').DataTable({
    dom: 'Bfrtip',
    buttons: ['copy', 'excel', 'csv', 'pdf', 'print']
  });

  // Table with Column Filtering  
  var table3 = $('#table-3').DataTable({
    "aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
  });

  $('#table-3 tfoot th').each(function () {
    var title = $('#table-3 thead th').eq($(this).index()).text();
    $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');
  });

  table3.columns().every(function () {
    var that = this;
    $('input', this.footer()).on('keyup change', function () {
      if (that.search() !== this.value) {
        that.search(this.value).draw();
      }
    });
  });
})(jQuery);
