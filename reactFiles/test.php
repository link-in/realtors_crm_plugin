<link href="https://unpkg.com/bootstrap-table@1.16.0/dist/bootstrap-table.min.css" rel="stylesheet">

<script src="https://unpkg.com/tableexport.jquery.plugin/tableExport.min.js"></script>
<script src="https://unpkg.com/tableexport.jquery.plugin/libs/jsPDF/jspdf.min.js"></script>
<script src="https://unpkg.com/tableexport.jquery.plugin/libs/jsPDF-AutoTable/jspdf.plugin.autotable.js"></script>
<script src="https://unpkg.com/bootstrap-table@1.16.0/dist/bootstrap-table.min.js"></script>
<script src="https://unpkg.com/bootstrap-table@1.16.0/dist/extensions/export/bootstrap-table-export.min.js"></script>

<style>
#toolbar {
  margin: 0;
}
</style>

<!-- <div id="toolbar" class="select">
  <select class="form-control">
    <option value="">Export Basic</option>
    <option value="all">Export All</option>
  </select>
</div>
 -->

<table
  id="table"
  data-ajax="ajaxRequestUsers"
  data-filter-control="true"
  data-detail-view="true"
  data-detail-view-icon="false"
  data-detail-view-by-click="true"
  data-detail-formatter="detailFormatter"
  data-show-export="true"
>
  <thead>
    <tr>
      <th data-formatter="runningFormatter">*</th>
      <th data-field="ID">מזהה</th>
      <th data-field="first_name" data-filter-control="input">שם פרטי</th>
      <th data-field="last_name" data-filter-control="input">שם משפחה</th>
      <th data-field="phone" data-filter-control="input">טלפון</th>
      <th data-field="roles" data-filter-control="select">סוג המינוי</th>
    </tr>
  </thead>
</table>
<script>

  var jquerytable = jQuery('#table')
      jquerytable.bootstrapTable('destroy').bootstrapTable({
        exportDataType: 'all',
        exportTypes: ['json', 'xml', 'csv', 'txt', 'sql', 'excel', 'pdf'],
      });
    


  // jQuery(function() {
  //   jQuery('#toolbar').find('select').change(function () {
  //     jquerytable.bootstrapTable('destroy').bootstrapTable({
  //       exportDataType: jQuery(this).val(),
  //       exportTypes: ['json', 'xml', 'csv', 'txt', 'sql', 'excel', 'pdf'],
  //       columns: []
  //     })
  //   }).trigger('change')
  // });

  function ajaxRequestUsers(params) {
        jQuery.ajax({
          url: "/wp-json/captaincore/v1/customers",
          type: 'GET',
          dataType: 'json',
          headers: {
              'X-WP-Nonce': wpReactLogin.nonceApi
          },
          data: {'district': '<?=$district?>'},
          contentType: 'application/json; charset=utf-8',
          success: function (result) {
            params.success(result)
          
          // CallBack(result);
          },
          error: function (error) {
              
          }
      });
  }  

</script>