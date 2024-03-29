<?php 	

/*
 * Envia o quadro pro carrinho
 */
function iap_order() {
 	
	if (isset($_POST['preco'])) {
		$data['moldura'] = $_POST['moldura'];
		$data['acabamento'] = $_POST['acabamento'];
		$data['x'] = $_POST['largura'];
		$data['y'] = $_POST['altura'];
		$data['preco'] = $_POST['preco'];
		$data['imagem'] = $_POST["imagem"];
		$data['imagemEditada'] = $_POST['imagemAdobe'];

		global $woocommerce;
		
		$produto = get_page_by_title('Quadro Personalizado IAP', OBJECT, 'product');
		$woocommerce->cart->add_to_cart($produto->ID, 1, '', '', $data);
		
		echo "1";

	} else {

		echo "0";

	}

	die();

}
add_action('wp_ajax_iap_order', 'iap_order');
add_action('wp_ajax_nopriv_iap_order', 'iap_order');


/*
 * Faz o envio da imagem
 */
function iap_imageUpload(){
	
	//Verify nonce
	if ( ! wp_verify_nonce( $_POST['wp-img-nonce'], 'wp-img-nonce-iap' ) )
		die('0');

	$file = $_FILES['file'];
	if (!is_array($file) || ( $file['type'] != 'image/jpeg' && $file['type'] != 'image/jpg' && $file['type'] != 'image/png' ) )
		die('1');

	if ( $file['size'] > 32000000)
		die('2');

	//Prepare for Upload
	if (!function_exists('wp_handle_upload'))
		require_once(ABSPATH . 'wp-admin/includes/file.php');

	$id = media_handle_upload( 'file', 0 );
	if (is_wp_error( $id ))
		die('0');

	$url = wp_get_attachment_image_url( $id, 'full' );
	echo $id .'|'. $url;
	die();
}

add_action('wp_ajax_iap_imageUpload', 'iap_imageUpload');
add_action('wp_ajax_nopriv_iap_imageUpload', 'iap_imageUpload');



/*
 * Faz o envio da imagem da adobe
 */
function iap_imageTransfer(){

	//Prepare for Upload
	if (!function_exists('wp_handle_upload')) {
		require_once(ABSPATH . 'wp-admin/includes/media.php');
		require_once(ABSPATH . 'wp-admin/includes/file.php');
		require_once(ABSPATH . 'wp-admin/includes/image.php');
	}

	$url = $_POST['file'];
	$tmp = download_url( $url );
	if( is_wp_error( $tmp ) )
		die("1");

	$file = array();
	preg_match('/[^\?]+\.(jpg|jpeg|png)/i', $url, $matches);
	$file['name'] = basename($matches[0]);
	$file['tmp_name'] = $tmp;
	
	$id = media_handle_sideload( $file, 0 );
	if (is_wp_error( $id ))
		die('2');

	$url = wp_get_attachment_image_url( $id, 'full' );
	echo $id .'|'. $url;
	die();
}

add_action('wp_ajax_iap_imageTransfer', 'iap_imageTransfer');
add_action('wp_ajax_nopriv_iap_imageTransfer', 'iap_imageTransfer');