<?php
/**
* Shortcode for displaying manufacturers of products
* Handy because you usually have this data as taxonomy-fied attriutes and no way to group it via theme
* Using graphql woocommerce addon , and inefficiently at that because you can call it via internal function as well.
* Fastest way to do this is via database Search
*/


function listattr(){

	//GRAPHQL request
	$query =
<<<'JSON'
{
  paManufacturers(first: 1000) {
    nodes {
      name
      link
      count
      products(first: 1) {
        nodes {
          image {
            sourceUrl(size: WOOCOMMERCE_THUMBNAIL)
          }
        }
      }
    }
  }
}

JSON;

	$variables = '';
  $graphqlurl = "siteurl.com/graphql"

	$json = json_encode(['query' => $query, 'variables' => $variables]);

	$chObj = curl_init();
	curl_setopt($chObj, CURLOPT_URL, $graphqlurl);
	curl_setopt($chObj, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($chObj, CURLOPT_CUSTOMREQUEST, 'POST');
	curl_setopt($chObj, CURLOPT_POSTFIELDS, $json);
	curl_setopt($chObj, CURLOPT_HTTPHEADER,
		 array(
				'User-Agent: PHP Script',
				'Content-Type: application/json;charset=utf-8',

			)
		);
	$html = '';
	$i = 0;


// This uses shoptimizer(wp theme) css classes , please adjust to your themes / your own css
// I did this with 2 views (image box and dictionary)


	if($_GET['catalog'] == 1){
			$html .= '<div style="
    padding: 25px 0;
"><a href="/?catalog=0" style="
 padding: 2px;
"><span class="textwidget custom-html-widget" style="
 position: relative;
 /* top: -24px; */
"> <span class="ri ri-list"></span></span><span class="gamma widget-title" style="
 padding-left: 20px;
 text-decoration: underline;
">Προβολή Με Εικόνες</span></a></div>';
	}
	else{
			$html .= '<div style="
    padding: 25px 0;
"><a href="/?catalog=1" style="
 padding: 2px;
"><span class="textwidget custom-html-widget" style="
 position: relative;
 /* top: -24px; */
"> <span class="ri ri-list"></span></span><span class="gamma widget-title" style="
 padding-left: 20px;
 text-decoration: underline;
">Προβολή Λίστας</span></a></div>';

	}
$response= json_decode(  curl_exec($chObj));
	if($_GET['catalog'] == 1){
		$arrayof4 = array_chunk($response->data->paManufacturers->nodes ,(count($response->data->paManufacturers->nodes)+1)/4);
		$html .= '<div class="columns-4"><ul class="products columns-4">';
		$gramma = "";
		foreach($arrayof4 as $chunk){

			if ($i == 0) $html.= '<li class="product-category product first">';
			elseif ($i == 4) $html.= '<li class="product-category product last">';
			else $html .= '<li class="product-category product">';

			foreach($chunk as $key){
				$namels = ucfirst(strtolower($key->name));
				$firstCharacter = $namels[0];
				if ($firstCharacter == $gramma) $html .= '<a class="notbig" href="'.$key->link.'">';
				else {
					$html .= '<a class="firstbig" href="'.$key->link.'">';
					$gramma = $firstCharacter;
				}
				$html .= '<h2 class="woocommerce-loop-category__title">'.$namels.'<mark class="count">('.$key->count.')</mark></h2>';
				$html .= '</a>';
			}
			$html.= '</li>';
			$i += 1;
		}
		$html .='</ul></div>';
	}
	else{
		$html .= '<div class="columns-5"><ul class="products columns-5">';
		foreach($response->data->paManufacturers->nodes as $key){
			if($key->count > 0 && !empty($key->products->nodes[0])){

				if ($i == 0) $html.= '<li class="product-category product first">';
				elseif ($i == 4) $html.= '<li class="product-category product last">';
				else $html .= '<li class="product-category product">';
				$html .= '<a href="'.$key->link.'">';
				$imagethumb = $key->products->nodes[0]->image->sourceUrl;
				$html .= '<img width="300" height="300" src="'.$imagethumb.'" class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail" srcset="'.$imagethumb.'" sizes="(max-width: 300px) 100vw, 300px">';
				$html .= '<h2 class="woocommerce-loop-category__title">'.$key->name.'<mark class="count">('.$key->count.')</mark></h2>';
				$html.= '</a>';
				$html.= '</li>';
				if ($i == 4) $i = 0;
				else $i += 1;
			}
		}
		$html .='</ul></div>';
	}
	return $html;
}
