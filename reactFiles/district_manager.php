<?php
$orderby = 'name';
$order = 'asc';
$hide_empty = false ;
$cat_args = array(
    'orderby'    => $orderby,
    'order'      => $order,
    'hide_empty' => $hide_empty,
);
 
$product_categories = get_terms( 'product_cat', $cat_args );
exit('dsfsdds');
?>

<div class="row admin-box">
    <div class="col-md-6">
        <form id="district-form" >
            <div class="form-group">
                <label for="exampleFormControlSelect1">בחר מחוז: </label>
                <select class="form-control" id="district" name="district">
                <?php
                $values= get_field_object( 'field_5d4bf4ee5e5d0');
                if(isset($_SESSION['district'])){
                    $selected = $_SESSION['district'];
                }else{
                    $selected = '';
                }
               
                foreach ($values['choices'] as $key => $value) {
                    echo "<option ";
                    if( $value == $selected ){
                        echo 'selected';
                     }
                     if($value == '0'){
                        $value = 'כולם';
                     }
                    echo ">$value</option>";
                }
                ?>  
                </select>
            </div>
        </form>

        <div class="sub-district-box">
            <?php
            if( !empty($_SESSION['district']) ){

                if($_SESSION['district'] == 'מחוז גוש דן'){
                    $values= get_field_object( acf_get_field_key('sub_district_1',3940));
                }

                if($_SESSION['district'] == 'מחוז הדרום והנגב'){
                    $values= get_field_object( acf_get_field_key('sub_district_2',3940));
                }

                if($_SESSION['district'] == 'מחוז השפלה'){
                    $values= get_field_object( acf_get_field_key('sub_district_3',3940));
                }

                if($_SESSION['district'] == 'מחוז השרון'){
                    $values= get_field_object( acf_get_field_key('sub_district_5',3940));
                }

                if($_SESSION['district'] == 'מחוז חיפה והגליל המערבי'){
                    $values= get_field_object( acf_get_field_key('sub_district_6',3940));
                }

                if($_SESSION['district'] == 'פתח תקווה ראש העין והשומרון'){
                    $values= get_field_object( acf_get_field_key('sub_district_7',3940));
                }

                if($_SESSION['district'] == 'מחוז מישור החוף הצפוני'){
                    $values= get_field_object( acf_get_field_key('sub_district_8',3940));
                }


            ?>
                <form id="sub_district-form" >
                    <div class="form-group">
                        <label for="exampleFormControlSelect1">תת מחוז: </label>
                        <select class="form-control" id="sub_district" name="sub_district">
                        <?php
                        
                        if(isset($_SESSION['sub_district'])){
                            $selected = $_SESSION['sub_district'];
                        }else{
                            $selected = '';
                        }
                    
                        foreach ($values['choices'] as $key => $value) {
                            echo "<option ";
                            if( $value == $selected ){
                                echo 'selected';
                            }
                            if($value == '0'){
                                $value = 'כולם';
                            }
                            echo ">$value</option>";
                        }
                        ?>  
                        </select>
                    </div>
                </form>
            <?php } ?>       

        </div>


    </div>



    <div class="col-md-6">
        <label for="exampleFormControlSelect1">בחר אפשרות להצגה: </label>
        <?php
        if( !empty($product_categories) ){
            echo '<form id="to_view">';
            echo '<select class="form-control" id="product" name="view">';
            echo "<option>בחר</option>";
            echo "<option ";
            if( 1 == intval($_SESSION['view'] )){
                echo "selected ";
                // exit;
            }
            echo "value='1'>חברי המחוז</option>";
            foreach ($product_categories as $key => $category) {
                $products = wc_get_products(array(
                    'category' => array($category->slug),
                ));

                // if(isset($_SESSION['view'])){
                //     $selected = $_SESSION['view'];
                // }else{
                //     $selected = '';
                // }
                foreach ($products as $pro) {

                    echo "<option ";
                    if( $pro->get_id() == intval($_SESSION['view'] )){
                        echo "selected ";
                        // exit;
                    }
                    echo "value='{$pro->get_id()}'>{$pro->name}</option>";
                    // var_dump(intval($_SESSION['view'] ));
                    // var_dump($pro->get_id());

                }
            }
            echo '</select>';
            echo '</form>';
        }
    ?>
    </div>

</div>

<script>
    jQuery('#district').change(function () {
            jQuery( "#district-form" ).submit();
    });

    jQuery('#product').change(function () {
            jQuery( "#to_view" ).submit();
    });

    jQuery('#sub_district').change(function () {
            jQuery( "#sub_district-form" ).submit();
    });

    
</script>
    </div>


</div>
