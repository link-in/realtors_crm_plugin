<?php
$product = wc_get_product( $_SESSION['view'] );
?>
<div class="h2-box">
    <h2>
        רכישות:
        <?=$product->name ?>
    </h2>
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
    <th data-field="first_name" data-filter-control="input">שם פרטי</th>
    <th data-field="last_name" data-filter-control="input">שם משפחה</th>
    <th data-field="phone" data-filter-control="input">טלפון</th>
    <th data-field="roles" data-filter-control="select">סוג המינוי</th>
    <th data-field="price" data-filter-control="select" class="price"><span>מחיר</span></th>
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

            // jQuery('table > tbody  > tr').find('td').each(function(index, tr) { 
            //     console.log(tr);
            //     });

                var rowTotal = 0;
                var counter = 0;
                jQuery('table tr').each(function () {
                    var row = jQuery(this);                   
                    jQuery(this).find('td').each(function () {
                        var td = jQuery(this);
                        if(td.hasClass('price')){
                            rowTotal += parseFloat(td.text());
                            counter++;
                        }
                    });
                });
                jQuery('.totalprice').text(rowTotal);
                jQuery('.totalresult').text(counter);
        });

    });

    function ajaxRequestUsers(params) {
            jQuery.ajax({
            url: "/wp-json/captaincore/v1/purchased_users",
            type: 'GET',
            dataType: 'json',
            headers: {
                'X-WP-Nonce': wpReactLogin.nonceApi
            },
            data: {'prodcatid':'<?=$_SESSION['view']?>','district':'<?=$_SESSION['district']?>', 'sub_district': '<?=$_SESSION['sub_district']?>'},
            contentType: 'application/json; charset=utf-8',
            success: function (result) {
                jQuery('.result-number').html('מספר התוצאות: <span class="totalresult">'+result.length+'</span>')
                params.success(result)
                var totalPrice = 0;
                result.forEach(function(row) {
                    totalPrice +=parseInt(row.price);
                });
                jQuery('.result-number').append( '<div>סה"כ מכירות: <span class="totalprice">'+totalPrice+'</span></div>' );
                // console.log(totalPrice);
                // console.log(result);
            
            // CallBack(result);
            },
            error: function (error) {
                
            }
        });
    }  

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
    function runningFormatter(value, row, index) {
        return index;
    }

</script>