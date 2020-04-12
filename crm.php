<?php
/**
 * Plugin Name: crm 
 * Plugin URI: https://www.link-in.co.il
 * Description: מערכת מניהול מתווכים ללשכה
 * Version: 1.0
 * Author: צור ברכה
 * Author URI: https://www.link-in.co.il
 */

include 'function.php';

function crm_fun() { 

    if (!session_id()) {
        session_start();
        if(isset($_GET['district'])){
          $_SESSION['district'] = $_GET['district'];
          unset($_SESSION['sub_district']); 
        }
      
        if(isset($_GET['view'])){  
          $_SESSION['view'] = $_GET['view'];
        }
        
        if(isset($_GET['sub_district'])){  
          
          $_SESSION['sub_district'] = $_GET['sub_district'];
        }

        if(isset($_GET['view'])){  
          $_SESSION['view'] = $_GET['view'];
        }
    }
    
    wp_register_style( 'style-crm-css', plugins_url( '/css/style-crm.css', __FILE__ ));
    wp_enqueue_style( 'style-crm-css' );

      /*react login*/
      wp_register_script( 'babel-js', 'https://cdnjs.cloudflare.com/ajax/libs/babel-core/5.8.24/browser.min.js', array(), false, true );
      wp_enqueue_script( 'react-js', plugins_url( '/reactFiles/react.min.js', __FILE__ ), array(), false, true );
      wp_enqueue_script( 'reactdom-js',  plugins_url( '/reactFiles/react-dom.min.js', __FILE__ ), array(), false, true );
      wp_enqueue_script( 'axios-js', 'https://unpkg.com/axios/dist/axios.min.js' , array(), false, true );    
      wp_localize_script( 'babel-js', 'wpReactLogin', array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'nonce' => wp_create_nonce( 'wp_react_login' ) , 'nonceApi' => wp_create_nonce( 'wp_rest' )) );
      wp_enqueue_script( 'babel-js' );

      // bootstrap-table
      wp_enqueue_style( 'bootstrap-table-css', 'https://unpkg.com/bootstrap-table@1.16.0/dist/bootstrap-table.min.css');
      wp_enqueue_script( 'bootstrap-table-min-js', 'https://unpkg.com/bootstrap-table@1.16.0/dist/bootstrap-table.min.js', array('jquery'), '1.0' );
      wp_enqueue_script( 'bootstrap-table-filter-control-min', 'https://unpkg.com/bootstrap-table@1.16.0/dist/extensions/filter-control/bootstrap-table-filter-control.min.js', array('jquery'), '1.0' );
      wp_enqueue_script( 'tableExport-min', 'https://unpkg.com/tableexport.jquery.plugin/tableExport.min.js', array('jquery'), '1.0' );

      wp_enqueue_script( 'jspdf-js', 'https://unpkg.com/tableexport.jquery.plugin/libs/jsPDF/jspdf.min.js', array('jquery'), '1.0' );
      wp_enqueue_script( 'jspdf-plugin-autotable', 'https://unpkg.com/tableexport.jquery.plugin/libs/jsPDF-AutoTable/jspdf.plugin.autotable.js', array('jquery'), '1.0' );
      wp_enqueue_script( 'bootstrap-table-export', 'https://unpkg.com/bootstrap-table@1.16.0/dist/extensions/export/bootstrap-table-export.min.js', array('jquery'), '1.0' );
      wp_enqueue_script( 'script', plugins_url( '/js/script.js', __FILE__ ), array(), false, true );
    ?>

    <h1>מערכת ניהול מתווכים</h1>
    <div class="form-wrapper" style="position: relative;">
        <?php if(!is_user_logged_in()){ ?>
          <div class="react_login"></div>
          <script src="<?=plugins_url( '/reactFiles/ReactLoginForm.js', __FILE__ )?>" type="text/babel"></script>
        <?php }else{ 
          //במידה והמשתמש מנהל אז מאפשר לו גישה לכל המחוזות
          if(get_field('crm', 'user_'.get_current_user_id())){
            ?>
            <div class="row">
              <div class="col-md-12">
                <?php
                  $user_meta=get_userdata(get_current_user_id());
                  if(in_array( 'administrator', (array) $user_meta->roles )){
                    //עמוד ניהול לאדמיניסטרטור
                    if(!isset($_GET['district'])){
                      $_SESSION['district'] = 'כולם';
                    }
                    include('reactFiles/admin_dashboard.php');
                  }else{
                    include('reactFiles/district_manager_dashboard.php');
                  }

                  if(isset($_SESSION['view']) && $_SESSION['view'] != '1'){
                    //הצגת מתווכים שרכשו את המוצר/כנס
                     include('reactFiles/catagory_pro_list.php');
                  }else{
                    //הצגת המתווכים של המחוז
                    include('reactFiles/UsersByDistrict.php');
                  } 
                  
                ?>
              </div>
            </div>
            <?php
    
          }else{
            echo "<h1>אין לך הרשאות גישה</h1>";
          }
          
          
          ?>
    
    
        <?php }?>
     </div>
     
    <?php

} 

add_shortcode('crm', 'crm_fun'); 
