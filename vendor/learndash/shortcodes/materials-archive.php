<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
* Shortcode to diplay materials for steps and frameworks
*
*/
function rtp_materials_archive_shortcode( $atts ) {
  if ( isset( $_GET['fl_builder'] ) ) {
    $notification =  "<h3>This is the materials section</h3>";
    return $notification;
  } 
  /**
  *
  * pagination can be achived by using this plugin: https://listjs.com/
  * 
  *
  *
  */
  // get the materials
  $args = [
    'post_type' => 'rtp_material',
    'numberposts' => -1,
    'tax_query' => [
      [
        'taxonomy' => 'material_category',
        'field'    => 'slug',                 
        'terms'    => [$atts['slug']],
      ],
    ],
    'order' => 'ASC',
    'orderby' => 'title',
    'fields' => 'ids',
  ];
  $material_ids = get_posts( $args );
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
  <ul class="rtp-materials-list">

    <?php foreach( $material_ids as $material_id ) {
      
      $material = get_post( $material_id );
      $label = get_the_title( $material );  
      $class = '';
      $target = '_self';
      $description = $material->material_description;

     if( $material->material_type == 'video' ){
        $class = 'popup-vimeo';
        $link = $material->material_video_link;
        $icon = 'far fa-file-video';

      } elseif( $material->material_type == 'link' ){
        $link = $material->material_link;
        $icon = 'fas fa-link';
        $target = '_blank';

      } elseif( $material->material_type == 'pdf' ){
        $link = $material->material_document_id;
        $icon = 'far fa-file-pdf';
        $target = '_blank';

      } elseif( $material->material_type == 'spreadsheet' ){
        $link = 'https://docs.google.com/spreadsheets/d/'.$material->material_sheet_id.'/template/preview';
        $icon ='far fa-file-excel';
        $target = '_blank';
      }
    ?>
    <li class="rtp-materials-list-item">
      <a class="rtp-material <?php echo $class; ?>" href="<?php echo $link; ?>" target="<?php echo $target; ?>">
          <i class="<?php echo $icon; ?>"></i>
          <h3 class="rtp-materials-item-heading"><?php echo $label; ?></h3>
      </a>
      <div class="rtp-materials-item">
        <div class="rtp-materials-item-body">
          <p><?php echo $description; ?></p>
        </div>
      </div>
    </li>		
    <?php } ?>
  </ul>
</div>
<?php
   return ob_get_clean();
}

add_shortcode( 'rtp_materials_archive', 'rtp_materials_archive_shortcode' );