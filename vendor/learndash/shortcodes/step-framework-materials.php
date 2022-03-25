<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
* Shortcode to diplay materials for steps and frameworks
*
*/
function rtp_materials_shortcode( $atts ) {
  if ( isset( $_GET['fl_builder'] ) ) {
    $notification =  "<h3>This is the materials section</h3>";
    return $notification;
  } 
  global $post;
  // get the materials
  if( $post->post_type == 'rtp_webinar') {
    $material_ids = rwmb_get_value( 'rtp_webinar_materials' );
  } else {
    $material_ids = rwmb_get_value( 'rtp_lms_materials' );
  }
  // ensure we have materials
  if( !isset( $material_ids ) || empty( $material_ids ) ) {
    return;
  }
	ob_start();
?>
<script type="text/javascript">
 jQuery( function(){
    jQuery('.popup-youtube, .popup-vimeo, .popup-gmaps').magnificPopup({
      disableOn: 0,
      type: 'iframe',
      mainClass: 'mfp-fade',
      removalDelay: 160,
      preloader: false,
      fixedContentPos: false,
      iframe: {
        patterns: {
          vimeo: {
            index: 'vimeo.com/',
            id: function(url){
              let idStr = url.replace("https://vimeo.com/","");
              let newUrl = idStr.replace("/", "?h=");
              return newUrl;
            },
            src: 'https://player.vimeo.com/video/%id%&amp;app_id=58479'
          },
        },
      }
    });
  });
</script>
<div id="rtp_lms_materials">
  <div id="rtp-materials-heading" class="">
    <div class="">
      <h2 class="c-h2">
      <span class="">Materials</span>
    </h2>
    </div>
    <div class="ct-div-block oxel_horizontal_divider__line c-margin-bottom-xs"></div>
  </div>
  <ul class="rtp-materials-list">

    <?php foreach( $material_ids as $material_id ) {
      
      $material = get_post( $material_id );
      $label = implode(' ',array_slice( explode(' ',get_the_title($material)),1));  
      $class = '';
      $target = '_self';
      $description = $material->material_description;

     if( $material->material_type == 'video' ){
        $class = 'popup-vimeo';
        $link = $material->material_video_link;
        $icon = 'fas fa-file-video';

      } elseif( $material->material_type == 'link' ){
        $link = $material->material_link;
        $icon = 'fas fa-link';
        $target = '_blank';

      } elseif( $material->material_type == 'pdf' ){
        $link = $material->material_document_id;
        $icon = 'fas fa-file-pdf';
        $target = '_blank';

      } elseif( $material->material_type == 'spreadsheet' ){
        $link = 'https://docs.google.com/spreadsheets/d/'.$material->material_sheet_id.'/template/preview';
        $icon ='fas fa-file-excel';
        $target = '_blank';
      }
    ?>
    <li class="rtp-materials-list-item">
      <a class="rtp-material <?php echo $class; ?>" href="<?php echo $link; ?>" target="<?php echo $target; ?>">
          <i class="<?php echo $icon; ?>"></i>
          <h6 class="rtp-materials-item-heading c-h6"><?php echo $label; ?></h6>
      </a>
      <div class="rtp-materials-item-body">
        <p><?php echo $description; ?></p>
      </div>
    </li>		
    <?php } ?>
  </ul>
</div>
<?php
   return ob_get_clean();
}

add_shortcode( 'rtp_materials', 'rtp_materials_shortcode' );