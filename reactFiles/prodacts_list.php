<?php
echo "<h2>רשימת מוצרים/כנסים</h2>";
$orderby = 'name';
$order = 'asc';
$hide_empty = false ;
$cat_args = array(
    'orderby'    => $orderby,
    'order'      => $order,
    'hide_empty' => $hide_empty,
);
 
$product_categories = get_terms( 'product_cat', $cat_args );
 
if( !empty($product_categories) ){
    echo '
 
<ul>';
    foreach ($product_categories as $key => $category) {
        echo '<li>';
        echo $category->name;
        echo '</li>';
        $products = wc_get_products(array(
            'category' => array($category->slug),
        ));
        echo "<ul>";
        foreach ($products as $cat) {
            echo "<li><a href='?catid={$cat->get_id()}&catname={$cat->name}'>{$cat->name}</li>";
        }
        echo "</ul>";
       


    }
    echo '</ul>
 
 
';
}

?>