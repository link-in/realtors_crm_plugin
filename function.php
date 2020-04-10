<?php

/** Users_by_district **/
add_action( 'rest_api_init', function () {
    register_rest_route(
        'captaincore/v1', '/customers/', array(
            'methods'       => 'GET',
            'callback'      => 'district_users_func',
            'show_in_index' => false
        )
    );
} );

function district_users_func( $request ) {
    $district = $request->get_param('district');
    $sub_district = $request->get_param('sub_district');
    if($district == 'כולם'){
        $q="SELECT DISTINCT user_id FROM `wp_usermeta` WHERE 1";
    }
    elseif(!empty($sub_district) && $sub_district != 'כל המחוז'){
        $meta_key =  get_subDistrict_by_district($district);
        $q="SELECT DISTINCT user_id FROM `wp_usermeta` WHERE meta_key='{$meta_key}' AND meta_value='{$sub_district}'";
    }else{
        $q="SELECT DISTINCT user_id FROM `wp_usermeta` WHERE meta_key='district' AND meta_value='{$district}'";
    }
    global $wpdb;
    $result = $wpdb->get_results($q);
    $allUsers = array();
    $counter = 0;
    $rowNumber=1;
    foreach($result as $user_id){
        $user = get_user_by ( 'ID',$user_id->user_id);
        // echo $user_id->user_id ." - " .$user->roles[0];
        // exit;
        if($user->roles[0] == 'administrator' || empty($user->roles[0])){
            continue;
        }

        $userMeta = get_user_meta ( $user_id->user_id);
        // Kint::dump($userMeta);
        $allUsers[$counter]['rowNumber'] = $rowNumber; 
        $allUsers[$counter]['first_name'] = $userMeta['first_name'][0]; 
        $allUsers[$counter]['last_name'] = $userMeta['last_name'][0]; 
        $allUsers[$counter]['phone'] = $userMeta['phone'][0]; 
        $allUsers[$counter]['district'] = $userMeta['district'][0]; 
        $allUsers[$counter]['member_purchase_date'] = $userMeta['member_purchase_date'][0]; 
        $allUsers[$counter]['member_expiry_date'] = $userMeta['member_expiry_date'][0]; 
        $allUsers[$counter]['district'] = $userMeta['district'][0]; 
        
        $allUsers[$counter]['user_email'] = $user->data->user_email; 
        $allUsers[$counter]['ID'] = $user_id->user_id; 
        if($user->roles[0] == 'author'){
            $allUsers[$counter]['roles'] = 'חבר לשכה שנתי'; 
        }elseif( $user->roles[0] == 'customer'){
            $allUsers[$counter]['roles'] = 'רשום באתר -לא חבר'; 
        }elseif( $user->roles[0] == 'monthly_subscriptionnot_approve'){
            $allUsers[$counter]['roles'] = 'חבר לשכה חדש - לא מאושר'; 
        }elseif( $user->roles[0] == 'new_monthly_subscriptionnot'){
            $allUsers[$counter]['roles'] = 'חבר לשכה חתום חודשי'; 
        }elseif( $user->roles[0] == 'expired_membership'){
            $allUsers[$counter]['roles'] = 'מנוי לא בתוקף'; 
        }

        $counter++;
        $rowNumber++;
    }
    header('Content-type: application/json');
    echo json_encode($allUsers);
    // Kint::dump($allUsers);
    exit;

}

function get_orders_by_user($userID) {
    
    // $userID = $request->get_param('userID');
    global $wpdb;
    $q="SELECT post_id FROM `wp_postmeta` WHERE `meta_key` = '_customer_user' AND `meta_value` = '{$userID}'";
    $result = $wpdb->get_results($q);
    $allUsers = array();
    $counter = 0;
    $rowNumber=1;
    $orders_arr = array();
    foreach($result as $item){
        $order = wc_get_order( $item->post_id );

        // $order_data = $order->get_data();
        $items = $order->get_items();

        foreach ( $items as $item ) {
            $orders_arr[$counter]['product_name'] = $item->get_name();
            $orders_arr[$counter]['product_id'] = $item->get_product_id();
        }
        // var_dump($order_items);
        $counter++;
        $rowNumber++;
    }
    return $orders_arr;
    // header('Content-type: application/json');
    // echo json_encode($orders_arr);
    // exit;
}

add_action( 'rest_api_init', function () {
    register_rest_route(
        'captaincore/v1', '/user_data/', array(
            'methods'       => 'GET',
            'callback'      => 'get_user_data_func',
            'show_in_index' => false
        )
    );
});

function get_user_data_func( $request ) {
    $userID = $request->get_param('userID');
    $userData = get_userdata($userID);
    $user_data = array();
    $user_data['user_id'] = $userID;
    $user_data['first_name'] = get_user_meta($userID,'first_name');
    $user_data['last_name'] = get_user_meta($userID,'last_name');
    $user_data['shipping_address_1'] = get_user_meta($userID,'shipping_address_1');
    $user_data['shipping_postcode'] = get_user_meta($userID,'shipping_postcode');
    $user_data['phone'] = get_user_meta($userID,'phone');
    $user_data['district'] = get_user_meta($userID,'district');
    $user_data['sub_district'] = get_user_meta($userID,get_subDistrict_by_district($user_data['district'][0]));
// var_dump($user_data['sub_district']);
    if(empty($user_data['sub_district'])){
        $user_data['sub_district'] = '';
    }

    // var_dump($user_data['sub_district']);
    $user_data['license_number'] = get_user_meta($userID,'license_number');
    $user_data['office_name'] = get_user_meta($userID,'office_name');
    $user_data['orders'][] = get_orders_by_user($userID);


    header('Content-type: application/json');
    echo json_encode($user_data);

    exit;

}
/** END Users_by_district **/

/** get_purchased_users_by_product **/

add_action( 'rest_api_init', function () {
    register_rest_route(
        'captaincore/v1', '/purchased_users/', array(
            'methods'       => 'GET',
            'callback'      => 'get_purchased_users_func',
            'show_in_index' => false
        )
    );
});

function get_purchased_users_func( $request ) {
    global $wpdb;
    $product_id = $request->get_param('prodcatid');
    $district = $request->get_param('district');
    $sub_district = $request->get_param('sub_district');

    $statuses = array_map( 'esc_sql', wc_get_is_paid_statuses() );

    $customer = $wpdb->get_col("
       SELECT DISTINCT pm.meta_value FROM {$wpdb->posts} AS p
       INNER JOIN {$wpdb->postmeta} AS pm ON p.ID = pm.post_id
       INNER JOIN {$wpdb->prefix}woocommerce_order_items AS i ON p.ID = i.order_id
       INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS im ON i.order_item_id = im.order_item_id
       WHERE p.post_status IN ( 'wc-" . implode( "','wc-", $statuses ) . "' )
       AND pm.meta_key IN ( '_customer_user' )
       AND im.meta_key IN ( '_product_id', '_variation_id' )
       AND im.meta_value = $product_id
    ");

    $allUsers = array();
    $counter = 0;
    $rowNumber=1;
    foreach($customer as $user_id){

        $userMeta = get_user_meta ( $user_id);
        
        //var_dump($userMeta['district'][0]);
        
        // var_dump($district);
        if($district != 'כולם'){
            if($userMeta['district'][0] != $district){
                continue;
            }

            if(!empty($sub_district) && $sub_district != 'כל המחוז'){
                if($userMeta[get_subDistrict_by_district($district)][0] != $sub_district){
                    continue;
                } 
            }

        }

      

        // Kint::dump($userMeta);
        $allUsers[$counter]['rowNumber'] = $rowNumber; 
        $allUsers[$counter]['first_name'] = $userMeta['first_name'][0]; 
        $allUsers[$counter]['last_name'] = $userMeta['last_name'][0]; 
        $allUsers[$counter]['phone'] = $userMeta['phone'][0]; 
        $allUsers[$counter]['district'] = $userMeta['district'][0]; 
        $allUsers[$counter]['member_purchase_date'] = $userMeta['member_purchase_date'][0]; 
        $allUsers[$counter]['member_expiry_date'] = $userMeta['member_expiry_date'][0]; 
        $allUsers[$counter]['district'] = $userMeta['district'][0]; 
        $user = get_user_by ( 'ID',$user_id);
        $allUsers[$counter]['user_email'] = $user->data->user_email; 
        $allUsers[$counter]['ID'] = $user_id; 
        if($user->roles[0] == 'author'){
            $allUsers[$counter]['roles'] = 'חבר לשכה שנתי'; 
        }elseif( $user->roles[0] == 'customer'){
            $allUsers[$counter]['roles'] = 'רשום באתר -לא חבר'; 
        }elseif( $user->roles[0] == 'monthly_subscriptionnot_approve'){
            $allUsers[$counter]['roles'] = 'חבר לשכה חדש - לא מאושר'; 
        }elseif( $user->roles[0] == 'new_monthly_subscriptionnot'){
            $allUsers[$counter]['roles'] = 'חבר לשכה חתום חודשי'; 
        }

        $q = "SELECT DISTINCT im.order_item_id
        FROM wp_posts as p  
        INNER JOIN wp_postmeta AS pm ON p.ID = pm.post_id
        INNER JOIN wp_woocommerce_order_items AS i ON p.ID = i.order_id
        INNER JOIN wp_woocommerce_order_itemmeta AS im ON i.order_item_id = im.order_item_id
        WHERE 
        p.post_type LIKE 'shop_order' AND
        (pm.meta_key='_customer_user' AND pm.meta_value = $user_id) AND 
        (im.meta_key = '_product_id' AND im.meta_value = '{$product_id}')";
        $result = $wpdb->get_results($q);

        $q = "SELECT  meta_value FROM `wp_woocommerce_order_itemmeta` WHERE 
        meta_key = '_line_total' AND order_item_id = '{$result[0]->order_item_id}'";
        $result = $wpdb->get_results($q);

        $allUsers[$counter]['price'] = $result[0]->meta_value; 
        $counter++;
        $rowNumber++;
        
    }
    header('Content-type: application/json');
    echo json_encode($allUsers);
    exit;

}
/** END get_purchased_users_by_product **/



add_action( 'wp_ajax_nopriv_react_check_if_logged', 'react_check_if_logged' );
add_action( 'wp_ajax_react_check_if_logged', 'react_check_if_logged' );
function react_check_if_logged(){
	$id = get_current_user_id();
	if( $id > 0 ){
		echo  json_encode(
			array(
				'success' => 1,
				'user' => new WP_User($id)
			));
	} else {
		echo  json_encode(
			array(
				'success' => 0
			));
	}

	wp_die();
}

add_action( 'wp_ajax_nopriv_react_login_user', 'react_login_user' );
add_action( 'wp_ajax_react_login_user', 'react_login_user' );
function react_login_user() {
	global $wpdb;
	check_ajax_referer( 'wp_react_login', '_wpnonce' );

	$username = $_POST['username'];
	$password = $_POST['password'];

	$auth = wp_authenticate( $username, $password );
	if( is_wp_error( $auth )) {
		echo  json_encode(
			array(
				'success' => 0,
				'message' => $auth->get_error_message()
			));
	} else {
		wp_set_auth_cookie( $auth->ID );
		echo  json_encode(
			array(
				'success' => 1,
				'user' => $auth
			));
	}
	

	wp_die();
}


add_action( 'wp_ajax_nopriv_update_user_data', 'update_user_data' );
add_action( 'wp_ajax_update_user_data', 'update_user_data' );
function update_user_data(){
    check_ajax_referer( 'wp_react_login', '_wpnonce' );
      
    $params = array();
    parse_str($_POST['data'], $params);
    //var_dump($params);

    $valid = false;
    if(!empty($params["first_name"])){
        $valid = update_user_meta( $params["user_id"], 'first_name', $params["first_name"] );
    }
    if(!empty($params["last_name"])){
        $valid = update_user_meta( $params["user_id"], 'last_name', $params["last_name"] );
    }
    if(!empty($params["shipping_address_1"])){
        $valid = update_user_meta( $params["user_id"], 'shipping_address_1', $params["shipping_address_1"] );
    }
    if(!empty($params["shipping_postcode"])){
        $valid = update_user_meta( $params["user_id"], 'shipping_postcode', $params["shipping_postcode"] );
    }
    if(!empty($params["phone"])){
        $valid = update_user_meta( $params["user_id"], 'phone', $params["phone"] );
    }
    if(!empty($params["district"])){
        $valid = update_user_meta( $params["user_id"], 'district', $params["district"] );
    }
    if(!empty($params["license_number"])){
        $valid = update_user_meta( $params["user_id"], 'license_number', $params["license_number"] );
    }
    if(!empty($params["office_name"])){
        $valid = update_user_meta( $params["user_id"], 'office_name', $params["office_name"] );
    }

	if( true ){
		echo  json_encode(
			array(
				'success' => 'update'
			));
	} 
	wp_die();
}


function get_subDistrict_by_district($district_name){
    if($district_name == 'מחוז גוש דן'){
        return 'sub_district_1';
    }
    if($district_name == 'מחוז הדרום והנגב'){
        return 'sub_district_2';
    }
    if($district_name == 'מחוז השפלה'){
        return 'sub_district_3';
    }
    if($district_name == 'מחוז השרון'){
        return 'sub_district_5';
    }
    if($district_name == 'מחוז חיפה והגליל המערבי'){
        return 'sub_district_6';
    }
    if($district_name == 'פתח תקווה ראש העין והשומרון'){
        return 'sub_district_7';
    }
    if($district_name == 'מחוז מישור החוף הצפוני'){
        return 'sub_district_8';
    }
}
