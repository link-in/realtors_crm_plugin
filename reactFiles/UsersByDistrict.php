<div class="h2-box">
  <h2 style=" margin-bottom: 10px;">חברי מחוז : <?=$_SESSION['district']?></h2>
  <?php 
  if(!empty($_SESSION['sub_district']) && $_SESSION['district'] != 'כולם'){
    echo "<h3 style='font-size: 22px;line-height: 22px;color: #2c3e50;margin-top: 5px!important;font-weight: 100!important;'>תת מחוז : ".$_SESSION['sub_district']."</h3>";
  }
  ?>

  
</div>
<div class="result-number"></div>
<table
  id="table"
  data-ajax="ajaxRequestUsersCall"
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
      <!-- <th data-field="ID">מזהה</th> -->
      <th data-field="first_name" data-filter-control="input">שם פרטי</th>
      <th data-field="last_name" data-filter-control="input">שם משפחה</th>
      <th data-field="phone" data-filter-control="input">טלפון</th>
      <th data-field="user_email" data-filter-control="input">אימייל</th> 
      <th data-field="district" data-filter-control="select">מחוז</th> 
      <th data-field="roles" data-filter-control="select">סוג המינוי</th>
    </tr>
  </thead>
</table>

<script>
  jQuery(function() {
      // jQuery('#table').bootstrapTable()
      var jquerytable = jQuery('#table')
      jquerytable.bootstrapTable({
          formatNoMatches: function () {
              return 'לא נמצאו תוצאות';
          },
          formatLoadingMessage: function() {
              return '<b>בטעינה נא להמתין...</b>';
          }
      }).bootstrapTable({
          exportDataType: 'all',
          exportTypes: ['csv', 'excel', 'pdf']
      }).on('page-change.bs.table', function (data) {
          var rowTotal = 0;
          var counter = 0;
          jQuery('table tr').each(function () {
              var row = jQuery(this);                   
              jQuery(this).find('td').each(function () {
                  var td = jQuery(this);
                
              });
              counter++;
          });
          jQuery('.result-number').text('מספר התוצאות: '+counter);
      });
  });

function ajaxRequestUsersCall(params){
  ajaxRequestUsers(params,'/wp-json/captaincore/v1/customers','<?=$_SESSION['district']?>','<?=$_SESSION['sub_district']?>','NULL');
}

</script>