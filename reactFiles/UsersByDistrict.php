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
  // jQuery(function() {
  //   jQuery('#table').bootstrapTable()
  // });
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

  function detailFormatter(index, row) {
    jQuery("#user_data_form").remove();
    var userData = getUserData(row.ID);
    if(userData.length == 0){
      return false;
    }
    var html = []
    console.log(userData);
    html.push('<div id="user_data_form" class="row"><div class="col-md-6"><form id="user_data">');
    html.push('<h3>פרטים אישים:</h3>');
    html.push('<input type="hidden" name="user_id" value="' + userData.user_id + '" />');
    html.push('<div class="form-group col-md-6 col-xs-12"><label for="first_name">שם פרטי</label><input type="text" class="form-control" name="first_name" id="first_name" value="' + userData.first_name + '"></div>');
    html.push('<div class="form-group col-md-6 col-xs-12"><label for="last_name">שם משפחה</label><input type="text" class="form-control" name="last_name" id="last_name" value="' + userData.last_name + '"></div>');
    html.push('<div class="form-group col-md-6 col-xs-12"><label for="shipping_address_1">כתובת</label><input type="text" class="form-control" name="shipping_address_1" id="shipping_address_1" value="' + userData.shipping_address_1 + '"></div>');
    html.push('<div class="form-group col-md-6 col-xs-12"><label for="shipping_postcode">מיקוד</label><input type="text" class="form-control" name="shipping_postcode" id="shipping_postcode" value="' + userData.shipping_postcode + '"></div>');
    html.push('<div class="form-group col-md-6 col-xs-12"><label for="phone">טלפון</label><input type="text" class="form-control" name="phone" id="phone" value="' + userData.phone + '"></div>');
    html.push('<div class="form-group col-md-6 col-xs-12"><label for="district">מחוז</label><input type="text" disabled="disabled" class="form-control" name="district" id="district" value="' + userData.district + '"></div>');
    html.push('<div class="form-group col-md-6 col-xs-12"><label for="sub_district">תת מחוז</label><input type="text" disabled="disabled" class="form-control" name="sub_district" id="sub_district" value="' + userData.sub_district + '"></div>');
    html.push('<div class="form-group col-md-6 col-xs-12"><label for="license_number">מספר מתווך</label><input type="text" class="form-control" name="license_number" id="license_number" value="' + userData.license_number + '"></div>');
    html.push('<div class="form-group col-md-6 col-xs-12"><label for="office_name">משרד</label><input type="text" class="form-control" name="office_name" id="office_name" value="' + userData.office_name + '"></div>');
    html.push('<div class="form-group col-md-6 col-xs-12"><label for="license_number">מספר מתווך</label><input type="text" disabled="disabled" class="form-control" name="license_number" id="license_number" value="' + userData.license_number + '"></div>');
    html.push('<div class="form-group col-md-6 col-xs-12"><label for="user_id">מס לקוח</label><input type="text" disabled="disabled" class="form-control" name="user_id" id="user_id" value="' + userData.user_id + '"></div>');
    html.push('<div id="wait" style="width: 289px;position: absolute;top: 20%;left: calc(50% - 90px);text-align: center;visibility: hidden;background: rgba(255, 255, 255, 0.38);padding: 40px;border-radius: 5px;"> <img id="ajax-img" src="https://realtors.org.il/wp-content/themes/buzz/assets/images/demo_wait.gif" width="64" height="64" style="display: none;margin: auto;"> <br> <div id="ajax-text" style="color: #000000;font-size: 16px;">מעדכן נתונים</div> </div>');
    html.push('<div class="form-group col-md-6 col-xs-12"><button onclick="update_user_click()" type="submit" class="btn btn-primary" style=" margin-top: 23px;">עדכן</button></div>');
    html.push('</form></div>');
    html.push('<div class="col-md-6">');
    html.push('<h3>הזמנות:</h3>');
    jQuery.each(userData.orders, function (key, line) {
      console.log(line);
      jQuery.each(line, function (key, value) {
        html.push('<p><b>'+(key+1)+' :</b> ' + value.product_name + '</p>');
      });
    });
    html.push('</div>');
    html.push('</div>');
    return html.join('');
  }

  function ajaxRequestUsers(params) {
        jQuery.ajax({
          url: "/wp-json/captaincore/v1/customers",
          type: 'GET',
          dataType: 'json',
          headers: {
              'X-WP-Nonce': wpReactLogin.nonceApi
          },
          data: {'district': '<?=$_SESSION['district']?>' , 'sub_district': '<?=$_SESSION['sub_district']?>'},
          contentType: 'application/json; charset=utf-8',
          success: function (result) {
          params.success(result)
          jQuery('.result-number').text('מספר התוצאות: '+result.length)
          // CallBack(result);
          },
          error: function (error) {
              
          }
      });
  }  

  function getUserData(userID){
    var result="";
    jQuery.ajax({
          url: "/wp-json/captaincore/v1/user_data",
          async: false, 
          type: 'GET',
          dataType: 'json',
          headers: {
              'X-WP-Nonce': wpReactLogin.nonceApi
          },
          data: {'userID': userID},
          contentType: 'application/json; charset=utf-8',
          success: function (data) {
            result = data; 
          },
          error: function (error) {
              
          }
      });
      return result;
  }

  function runningFormatter(value, row, index) {
      return index;
  }



  function update_user_click(){
    jQuery( "#user_data" ).submit(function(event) {
      // להוסיף הודעת שליחה
      event.preventDefault();
      jQuery("#wait").css('visibility', 'visible');
      jQuery('#ajax-img').show();
      var data = jQuery('#user_data').serializeArray();
      console.log(data);
      jQuery.ajax({
        url: wpReactLogin.ajax_url,
        type : "post",
        dataType: 'json',
        data : {action: "update_user_data", data : jQuery('#user_data').serialize(), _wpnonce: wpReactLogin.nonce},
        cache: false,
        success: function(data) {

            if(data.success == 'update'){
              jQuery('#ajax-img').attr("src","<?=get_template_directory_uri()?>/assets/images/v.jpg");
              jQuery('#ajax-text').text('עודכן בהצלחה');
              setTimeout(function(){
                jQuery("#wait").css('visibility', 'hidden');
                jQuery('#ajax-img').hide();
              },3000);
            }
    
        }.bind(this),
        error: function(xhr, status, err) {
          console.log(err.toString());
        }.bind(this)
      });
    });
  }



</script>