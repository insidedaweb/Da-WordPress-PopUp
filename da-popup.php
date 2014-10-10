<?php
/*
Plugin Name: Da Popup
Description: Create a custom pop-up for you WordPress site in order to gain Like, Follow, +1 and subscibers to your MailChimp list.
Version: 1.0
Author: Inside da Web
Author URI: http://insidedaweb.com
License: GPLv2 or later
*/

include('iwb-mailchimp.php');

  /* *****************************/
 /*  REGISTER CPT FOR PLUGIN    */
/* *****************************/

function dp_cpt_register() {
    $args = array(
      'public' => true,
      'label'  => 'Da Popup',
      'show_in_nav_menus' => false,
      'menu_position' => 80,
      'menu_icon' => 'dashicons-welcome-comments',
      'supports' => array('title', 'thumbnail'),
      'rewrite' => array('slug' => 'da-popup', 'with_front' => false),
    );
    register_post_type( 'da-popup', $args );
}
add_action( 'init', 'dp_cpt_register' );
  
  /* *****************************/
 /* REGISTER SCRIPTS AND STYLES */
/* *****************************/

function dp_register_styles() {
	wp_register_style( 'da-popup-style', plugins_url( 'da-wp-popup/css/style.css' ) );
	wp_enqueue_style( 'da-popup-style' );
}
// function dp_register_scripts() {
// 	wp_register_script( 'da-popup-js', plugins_url( 'da-wp-popup/js/da-popup-js.js' ), array("jquery"), "1.0" );
// 	wp_enqueue_script( 'da-popup-js' );
// }
add_action( 'wp_enqueue_scripts', 'dp_register_styles' );
// add_action( 'wp_enqueue_scripts', 'dp_register_scripts' );

function dp_register_admin_styles() {
        wp_register_style( 'dp_admin_css', plugins_url( 'da-wp-popup/css/style.css' ), false, '1.0.0' );
        wp_enqueue_style( 'dp_admin_css' );
}
add_action( 'admin_enqueue_scripts', 'dp_register_admin_styles' );

function dp_register_admin_scripts() {
        // wp_register_script( 'dp_admin_js', plugins_url( 'da-wp-popup/js/da-popup.js' ), false, '1.0.0' );
        // wp_enqueue_script( 'dp_admin_js' );
        wp_register_script( 'dp_color_js', plugins_url( 'da-wp-popup/js/jscolor/jscolor.js' ), false, '1.0.0' );
        wp_enqueue_script( 'dp_color_js' );
}
add_action( 'admin_enqueue_scripts', 'dp_register_admin_scripts' );
  
  /* ********************** */
 /*      META BOXES        */          
/* ********************** */

function dp_add_meta_boxes(){
  add_meta_box( 'dp-choose-theme-box', __( "Choose Theme" ), 'dp_choose_theme_options', 'da-popup', 'advanced', 'high' );
  add_meta_box( 'dp-preview-theme-box', __( "Theme Preview" ), 'dp_preview_theme_options', 'da-popup', 'advanced', 'high' );
  add_meta_box( 'dp-theme-settings-box', __( "Theme Settings" ), 'dp_theme_settings_options', 'da-popup', 'advanced', 'high' );
  add_meta_box( 'dp-general-settings-box', __( "General Settings" ), 'dp_general_settings_options', 'da-popup', 'advanced', 'high' );
  add_meta_box( 'dp-conditions-box', __( "Conditions" ), 'dp_conditions_options', 'da-popup', 'advanced', 'high' );
}

add_action( 'add_meta_boxes', 'dp_add_meta_boxes' );

  /* ********************** */
 /*        COOKIE          */          
/* ********************** */

function set_newuser_cookie() {
     if ( !is_admin() && !isset($_COOKIE['sitename_newvisitor'])) {
         setcookie('sitename_newvisitor', 1, time()+3600*24*100, COOKIEPATH, COOKIE_DOMAIN, false);
         return false;
     } else {
         return true;
     }

 }
add_action( 'init', 'set_newuser_cookie');

  /* ********************** */
 /*    CHOSE THEME BOX     */          
/* ********************** */

function dp_choose_theme_options( $post, $post_id){
	global $post;

	$dp_choose_theme = get_post_meta( $post->ID, 'dp-choose-theme', true);

  echo '<select class="styled" name="dp-choose-theme" value="'.$dp_choose_theme.'">
          <option value="one"'.( ( $dp_choose_theme == "one" ) ? " selected" : "" ).'>Classic Theme</option>
        </select>'; 
}

  /* ********************** */
 /*   PREVIEW THEME BOX    */          
/* ********************** */

function dp_preview_theme_options( $post, $post_id){
	global $post;

	$dp_choose_theme = get_post_meta( $post->ID, 'dp-choose-theme', true);
  if ( $dp_choose_theme == "" ){
      $dp_choose_theme = "one";
    }
    $dp_select_font = get_post_meta( $post->ID, 'dp-select-font', true);
    $left_title_text = get_post_meta( $post->ID, 'title-left-text', true);
    if ( $left_title_text == "" ){
      $left_title_text = "SHARE";
    }
    $right_title_text = get_post_meta( $post->ID, 'title-right-text', true);
    if ( $right_title_text == "" ){
      $right_title_text = "SUBSCRIBE";
    }
    $text_text = get_post_meta( $post->ID, 'text-text', true);
    if ( $text_text == "" ){
      $text_text = "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.";
    }
    $name_placeholder = get_post_meta( $post->ID, 'name-placeholder', true);
    if ( $name_placeholder == "" ){
      $name_placeholder = "Place your name";
    }
    $email_placeholder = get_post_meta( $post->ID, 'email-placeholder', true);
    if ( $email_placeholder == "" ){
      $email_placeholder = "Place your email";
    }
    $button_text = get_post_meta( $post->ID, 'button-text', true);
    if ( $button_text == "" ){
      $button_text = "Subscribe";
    }
    $or_text = get_post_meta( $post->ID, 'or-text', true);
    if ( $or_text == "" ){
      $or_text = "OR";
    }


    $left_color = get_post_meta( $post->ID, 'left-color', true);
    if ( $left_color == "" ){
      $left_color = "87D01E";
    }
    $right_color = get_post_meta( $post->ID, 'right-color', true);
    $button_color = get_post_meta( $post->ID, 'button-color', true);
    if ( $button_color == "" ){
      $button_color = "87D01E";
    }
    $left_title_color = get_post_meta( $post->ID, 'left-title-color', true);
    if ( $left_title_color == "" ){
      $left_title_color = "000000";
    }
    $right_title_color = get_post_meta( $post->ID, 'right-title-color', true);
    if ( $right_title_color == "" ){
      $right_title_color = "000000";
    }
    $text_text_color = get_post_meta( $post->ID, 'text-text-color', true);
    if ( $text_text_color == "" ){
      $text_text_color = "000000";
    }
    $button_text_color = get_post_meta( $post->ID, 'button-text-color', true);
    if ( $button_text_color == "" ){
      $button_text_color = "FFFFFF";
    }

    $facebook_cbx = get_post_meta( $post->ID, 'facebook-cbx', true);
    $facebook_link = get_post_meta( $post->ID, 'facebook-link', true);

    $twitter_cbx = get_post_meta( $post->ID, 'twitter-cbx', true);
    $twitter_link = get_post_meta( $post->ID, 'twitter-link', true);

    $google_cbx = get_post_meta( $post->ID, 'google-cbx', true);
    $google_link = get_post_meta( $post->ID, 'google-link', true);

    $linkedin_cbx = get_post_meta( $post->ID, 'linkedin-cbx', true);
    $linkedin_linkedin = get_post_meta( $post->ID, 'linkedin-linkedin', true);

    $pinetrest_cbx = get_post_meta( $post->ID, 'pinetrest-cbx', true);
    $pinetrest_link = get_post_meta( $post->ID, 'pinetrest-link', true);


	echo '<div class="theme-preview-box"> 
          <div class="classic-theme-wrap"'.( ( $dp_choose_theme == "one" ) ? "style='display:block'" : "style='display:none'" ).'>
            <h2>Classic Theme 1.0</h2>
            <div class="theme-preview-live">
              <div class="popup-wraper">
                <div class="popup-left" style="background-color:#'.$left_color.'">
                  <div class="share-title">
                    <p style="color:#'.$left_title_color.'">'.$left_title_text.'</p>
                  </div>
                  <div class="facebook" '.( ( $facebook_cbx !== "on" ) ? "style='display:none'" : "style='display:block'" ).'>
                    <iframe src="//www.facebook.com/plugins/like.php?href=https%3A%2F%2Fwww.facebook.com%2F'.$facebook_link.'&amp;width&amp;layout=box_count&amp;action=like&amp;show_faces=false&amp;share=true&amp;height=65" scrolling="no" frameborder="0" style="border:none; overflow:hidden; height:65px;" allowTransparency="true"></iframe>
                  </div>
                  <div class="twitter" '.( ( $twitter_cbx !== "on" ) ? "style='display:none'" : "style='display:block'" ).'>
                    <a class="twitter-follow-button" href="https://twitter.com/'.$twitter_link.'" data-show-screen-name="false" data-show-count="false" data-lang="en" >Follow </a>
                    <script type="text/javascript"> window.twttr = (function (d, s, id) { var t, js, fjs = d.getElementsByTagName(s)[0]; if (d.getElementById(id)) return;
                      js = d.createElement(s); js.id = id;
                      js.src= "https://platform.twitter.com/widgets.js";
                      fjs.parentNode.insertBefore(js, fjs);
                      return window.twttr || (t = { _e: [], ready: function (f) { t._e.push(f) } });
                      }(document, "script", "twitter-wjs"));
                  </script>
                  </div>
                  <div class="google" '.( ( $google_cbx !== "on" ) ? "style='display:none'" : "style='display:block'" ).'>
                    <script src="https://apis.google.com/js/platform.js" async defer></script>
                    <div class="g-follow" data-annotation="vertical-bubble" data-height="20" data-href="https://plus.google.com/116267312819938197604" data-rel="publisher"></div>
                  </div>
                  <div class="linkedin" '.( ( $linkedin_cbx !== "on" ) ? "style='display:none'" : "style='display:block'" ).'>
                    <script src="http://platform.linkedin.com/in.js" type="text/javascript">
                      lang: en_US
                    </script>
                    <script type="IN/FollowCompany" data-id="3182" data-width="70px" data-counter="top"></script>
                  </div>
                  <div class="pinetrest" '.( ( $pinetrest_cbx !== "on" ) ? "style='display:none'" : "style='display:block'" ).'>
                    <div class="pinetrest-inner">
                      <a data-pin-do="buttonFollow" href="http://www.pinterest.com/'.$pinetrest_link.'">Pinterest</a>
                      <!-- Please call pinit.js only once per page -->
                      <script type="text/javascript" async src="//assets.pinterest.com/js/pinit.js"></script>
                    </div>
                  </div>
                </div>
                <div class="or">
                  '.$or_text.'
                </div>
                <div class="popup-right" style="background-color:#'.$right_color.'">
                  <div class="subscribe-title">
                    <p style="color:#'.$right_title_color.'">'.$right_title_text.'</p>
                  </div>
                  <div class="subscribe-text">
                  <p style="color:#'.$text_text_color.'">'.$text_text.'</p>
                  </div>
                  <div class="subscribe-form">
                    <div class="subscribe-input-name">
                      <input class="sub-input name" name="name" type="text" placeholder="'.$name_placeholder.'">
                      <div class="input-icon">
                        <div class="input-icon-inner">
                          <i class="fa fa-user"></i>
                        </div>
                      </div>
                    </div>
                    <div class="subscribe-input-email">
                      <input class="sub-input email" name="email" type="text" placeholder="'.$email_placeholder.'">
                      <div class="input-icon">
                        <div class="input-icon-inner">
                          <i class="fa martop fa-envelope"></i>
                        </div>
                      </div>
                    </div>
                    <div class="subscribe-button">
                      <a class="subscribe-submit" data-loading="Loading..." data-label="Subscribe" data-icon="fa-pencil-square-o" style="background-color:#'.$button_color.';color:#'.$button_text_color.'">
                        <i class="fa fa-pencil-square-o"></i>
                        '.$button_text.'
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>'; ?> 
        <script type="text/javascript">
                      jQuery(window).load(function(){

                        jQuery(".styled").change(function() {
                          var val = jQuery(this).val();
                          if(val === "one") {
                              jQuery(".classic-theme-wrap").fadeIn(1000);
                              jQuery(".theme-settings-box").fadeIn(1000);
                          }
                          else if(val === "") {
                              jQuery(".classic-theme-wrap").fadeOut(500);
                              jQuery(".theme-settings-box").fadeOut(500);
                          }
                        });
        

                        jQuery('#left-input').change(function(){
                        var leftBackground = jQuery('#left-input').css('background-color');

                        jQuery('.popup-left').css('background-color', leftBackground);

                        });

                        jQuery('#right-input').change(function(){
                        var rightBackground = jQuery('#right-input').css('background-color');
                        
                        jQuery('.popup-right').css('background-color', rightBackground);
                        });

                        jQuery('#button-input').change(function(){
                        var buttonBackground = jQuery('#button-input').css('background-color');
                        
                        jQuery('.subscribe-submit').css('background-color', buttonBackground);
                        });

                        jQuery('#left-title-input').change(function(){
                        var leftTitleBackground = jQuery('#left-title-input').css('background-color');
                        
                        jQuery('.share-title p').css('color', leftTitleBackground);
                        });

                        jQuery('#right-title-input').change(function(){
                        var rightTitleBackground = jQuery('#right-title-input').css('background-color');
                        
                        jQuery('.subscribe-title p').css('color', rightTitleBackground);
                        });

                        jQuery('#text-input').change(function(){
                        var textBackground = jQuery('#text-input').css('background-color');
                        
                        jQuery('.subscribe-text p').css('color', textBackground);
                        });

                        jQuery('#button-text-input').change(function(){
                        var buttonTextColor = jQuery('#button-text-input').css('background-color');
                        
                        jQuery('.subscribe-submit').css('color', buttonTextColor);
                        });

                        jQuery('#title-left-text').keyup(function(){
                          var leftTitleText = jQuery('#title-left-text').val();
                        
                        jQuery('.share-title').html(leftTitleText);
                        });

                        jQuery('#title-right-text').keyup(function(){
                          var rightTitleText = jQuery('#title-right-text').val();
                        
                        jQuery('.subscribe-title').html(rightTitleText);
                        });

                        jQuery('#text-text').keyup(function(){
                          var textText = jQuery('#text-text').val();
              
                        jQuery('.subscribe-text').html(textText);
                        });

                        jQuery('#or-text').keyup(function(){
                          var orText = jQuery('#or-text').val();
                        
                        jQuery('.or').html(orText);
                        });

                            jQuery('.cbx').click(function(){
                              var id = jQuery(this).attr('data-id');
                                if( jQuery(this).is(':checked') ){
                              
                                jQuery('.'+id).show();
                              
                              } else {

                                jQuery('.'+id).hide();
                              };
                            });

                      })
                    </script> <?php
}

  /* ********************** */
 /*   THEME SETTING BOX    */          
/* ********************** */


function dp_theme_settings_options( $post, $post_id){
	global $post;


	  $dp_choose_theme = get_post_meta( $post->ID, 'dp-choose-theme', true);
    if ( $dp_choose_theme == "" ){
      $dp_choose_theme = "one";
    }
    $dp_select_font = get_post_meta( $post->ID, 'dp-select-font', true);
    $left_title_text = get_post_meta( $post->ID, 'title-left-text', true);
    if ( $left_title_text == "" ){
      $left_title_text = "SHARE";
    }
    $right_title_text = get_post_meta( $post->ID, 'title-right-text', true);
    if ( $right_title_text == "" ){
      $right_title_text = "SUBSCRIBE";
    }
    $text_text = get_post_meta( $post->ID, 'text-text', true);
    if ( $text_text == "" ){
      $text_text = "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.";
    }
    $name_placeholder = get_post_meta( $post->ID, 'name-placeholder', true);
    if ( $name_placeholder == "" ){
      $name_placeholder = "Place your name";
    }
    $email_placeholder = get_post_meta( $post->ID, 'email-placeholder', true);
    if ( $email_placeholder == "" ){
      $email_placeholder = "Place your email";
    }
    $button_text = get_post_meta( $post->ID, 'button-text', true);
    if ( $button_text == "" ){
      $button_text = "Subscribe";
    }
    $or_text = get_post_meta( $post->ID, 'or-text', true);
    if ( $or_text == "" ){
      $or_text = "OR";
    }


    $left_color = get_post_meta( $post->ID, 'left-color', true);
    if ( $left_color == "" ){
      $left_color = "87D01E";
    }
    $right_color = get_post_meta( $post->ID, 'right-color', true);
    $button_color = get_post_meta( $post->ID, 'button-color', true);
    if ( $button_color == "" ){
      $button_color = "87D01E";
    }
    $left_title_color = get_post_meta( $post->ID, 'left-title-color', true);
    if ( $left_title_color == "" ){
      $left_title_color = "000000";
    }
    $right_title_color = get_post_meta( $post->ID, 'right-title-color', true);
    if ( $right_title_color == "" ){
      $right_title_color = "000000";
    }
    $text_text_color = get_post_meta( $post->ID, 'text-text-color', true);
    if ( $text_text_color == "" ){
      $text_text_color = "000000";
    }
    $button_text_color = get_post_meta( $post->ID, 'button-text-color', true);
    if ( $button_text_color == "" ){
      $button_text_color = "FFFFFF";
    }

    $facebook_cbx = get_post_meta( $post->ID, 'facebook-cbx', true);
    $facebook_link = get_post_meta( $post->ID, 'facebook-link', true);

    $twitter_cbx = get_post_meta( $post->ID, 'twitter-cbx', true);
    $twitter_link = get_post_meta( $post->ID, 'twitter-link', true);

    $google_cbx = get_post_meta( $post->ID, 'google-cbx', true);
    $google_link = get_post_meta( $post->ID, 'google-link', true);

    $linkedin_cbx = get_post_meta( $post->ID, 'linkedin-cbx', true);
    $linkedin_linkedin = get_post_meta( $post->ID, 'linkedin-linkedin', true);

    $pinetrest_cbx = get_post_meta( $post->ID, 'pinetrest-cbx', true);
    $pinetrest_link = get_post_meta( $post->ID, 'pinetrest-link', true);


	echo '<div class="theme-settings-box"'.( ( $dp_choose_theme == "one" ) ? "style='display:block'" : "style='display:none'" ).'> 
          
          <div class="theme-settings-box-left">

            <div class="theme-settings-box-column">

              <div class="theme-settings-box-column-left">
                 Left Title
              </div>

              <div class="theme-settings-box-column-right">
                <input id="title-left-text" type="text" name="title-left-text" value="'.$left_title_text.'">
              </div><br>

            </div>

            <div class="theme-settings-box-column">

              <div class="theme-settings-box-column-left">
                 Right Title
              </div>

              <div class="theme-settings-box-column-right">
                <input id="title-right-text" type="text" name="title-right-text" value="'.$right_title_text.'">
              </div><br>

            </div>

            <div class="theme-settings-box-column fortextarea">

              <div class="theme-settings-box-column-left">
                 Text
              </div>

              <div class="theme-settings-box-column-right">
                <textarea id="text-text" name="text-text">'.$text_text.'</textarea>
              </div><br>

            </div>

            <div class="theme-settings-box-column">

              <div class="theme-settings-box-column-left">
                 Name Placeholder
              </div>

              <div class="theme-settings-box-column-right">
                <input id="title-right-text" type="text" name="name-placeholder" value="'.$name_placeholder.'">
              </div><br>

            </div>

            <div class="theme-settings-box-column">

              <div class="theme-settings-box-column-left">
                 Email Placeholder
              </div>

              <div class="theme-settings-box-column-right">
                <input id="title-right-text" type="text" name="email-placeholder" value="'.$email_placeholder.'">
              </div><br>

            </div>

            <div class="theme-settings-box-column">

              <div class="theme-settings-box-column-left">
                 Button Text
              </div>

              <div class="theme-settings-box-column-right">
                <input id="title-right-text" type="text" name="button-text" value="'.$button_text.'">
              </div><br>

            </div>

            <div class="theme-settings-box-column">

              <div class="theme-settings-box-column-left">
                 Middle Text
              </div>

              <div class="theme-settings-box-column-right">
                <input id="or-text" type="text" name="or-text" value="'.$or_text.'">
              </div><br>

            </div>
              
              <div class="clear"></div>

          </div>

          <div class="theme-settings-box-right">

            <div class="theme-settings-box-column">

              <div class="theme-settings-box-column-left">
                 Left Area Color
              </div>

              <div class="theme-settings-box-column-right">
                <input id="left-input" style="width:80px;" class="color" name="left-color" value="'.$left_color.'"> 
              </div><br>

            </div>

            <div class="theme-settings-box-column">

              <div class="theme-settings-box-column-left">
                 Right Area Color
              </div>

              <div class="theme-settings-box-column-right">
                <input id="right-input" style="width:80px;" class="color" name="right-color" value="'.$right_color.'">
              </div><br>

            </div>

            <div class="theme-settings-box-column">

              <div class="theme-settings-box-column-left">
                 Left Title Color
              </div>

              <div class="theme-settings-box-column-right">
                <input id="left-title-input" style="width:80px;" class="color" name="left-title-color" value="'.$left_title_color.'">
              </div><br>

            </div>

            <div class="theme-settings-box-column">

              <div class="theme-settings-box-column-left">
                 Right Title Color
              </div>

              <div class="theme-settings-box-column-right">
                <input id="right-title-input" style="width:80px;" class="color" name="right-title-color" value="'.$right_title_color.'">
              </div><br>

            </div>

            <div class="theme-settings-box-column">

              <div class="theme-settings-box-column-left">
                 Text Color
              </div>

              <div class="theme-settings-box-column-right">
                <input id="text-input" style="width:80px;" class="color" name="text-text-color" value="'.$text_text_color.'">
              </div><br>

            </div>

            <div class="theme-settings-box-column">

              <div class="theme-settings-box-column-left">
                 Button Color
              </div>

              <div class="theme-settings-box-column-right">
                <input id="button-input" style="width:80px;" class="color" name="button-color" value="'.$button_color.'">
              </div><br>

            </div>

            <div class="theme-settings-box-column">

              <div class="theme-settings-box-column-left">
                 Button Text Color
              </div>

              <div class="theme-settings-box-column-right">
                <input id="button-text-input" style="width:80px;" class="color" name="button-text-color" value="'.$button_text_color.'">
              </div><br>

            </div>

            <div class="theme-settings-box-column">

              <div class="theme-settings-box-column-left">
                 <input type="checkbox" data-id="facebook" class="cbx" name="facebook-cbx" '.( ( $facebook_cbx == "on" ) ? "checked" : "" ).'><br> www.facebook.com/
              </div>

              <div class="theme-settings-box-column-right">
               <input class="shorty" type="text" name="facebook-link" value="'.$facebook_link.'">
              </div><br>

            </div>

            <div class="theme-settings-box-column">

              <div class="theme-settings-box-column-left">
                 <input type="checkbox" data-id="twitter" class="cbx" name="twitter-cbx" '.( ( $twitter_cbx == "on" ) ? "checked" : "" ).'><br> www.twitter.com/
              </div>

              <div class="theme-settings-box-column-right">
               <input class="shorty" type="text" name="twitter-link" value="'.$twitter_link.'">
              </div><br>

            </div>

            <div class="theme-settings-box-column">

              <div class="theme-settings-box-column-left">
                 <input type="checkbox" data-id="google" class="cbx" name="google-cbx" '.( ( $google_cbx == "on" ) ? "checked" : "" ).'><br> www.plus.google.com/
              </div>

              <div class="theme-settings-box-column-right">
               <input class="shorty" type="text" name="google-link" value="'.$google_link.'">
              </div><br>

            </div>

            <div class="theme-settings-box-column">

              <div class="theme-settings-box-column-left">
                 <input type="checkbox" data-id="linkedin" class="cbx" name="linkedin-cbx" '.( ( $linkedin_cbx == "on" ) ? "checked" : "" ).'><br>  www.linkedin.com/
              </div>

              <div class="theme-settings-box-column-right">
                <input class="shorty" type="text" name="linkedin-link" value="'.$linkedin_link.'">
              </div><br>

            </div>

            <div class="theme-settings-box-column">

              <div class="theme-settings-box-column-left">
                 <input type="checkbox" data-id="pinetrest" class="cbx" name="pinetrest-cbx" '.( ( $pinetrest_cbx == "on" ) ? "checked" : "" ).'><br> www.pinetrest.com/
              </div>

              <div class="theme-settings-box-column-right">
                <input class="shorty" type="text" name="pinetrest-link" value="'.$pinetrest_link.'">
              </div><br>

            </div>



            <div class="clear"></div>

          </div>

          <div class="clear"></div>

       </div>'; 

}
function dp_save_metaboxes_settings(){
  global $post;
  
  if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ){
    return $post_id;
  }else{
    update_post_meta( $post->ID, 'facebook-cbx', $_POST['facebook-cbx'] );
    update_post_meta( $post->ID, 'facebook-link', $_POST['facebook-link'] );
    update_post_meta( $post->ID, 'twitter-cbx', $_POST['twitter-cbx'] );
    update_post_meta( $post->ID, 'twitter-link', $_POST['twitter-link'] );
    update_post_meta( $post->ID, 'google-cbx', $_POST['google-cbx'] );
    update_post_meta( $post->ID, 'google-link', $_POST['google-link'] );
    update_post_meta( $post->ID, 'linkedin-cbx', $_POST['linkedin-cbx'] );
    update_post_meta( $post->ID, 'linkedin-link', $_POST['linkedin-link'] );
    update_post_meta( $post->ID, 'pinetrest-cbx', $_POST['pinetrest-cbx'] );
    update_post_meta( $post->ID, 'pinetrest-link', $_POST['pinetrest-link'] );
  }
}
add_action( 'save_post', 'dp_save_metaboxes_settings' );
  /* ********************** */
 /*  GENERAL SETTINGS BOX  */          
/* ********************** */

function dp_general_settings_options( $post, $post_id){
  global $post;

  $popup_delay = get_post_meta( $post->ID, 'popup-delay', true);

  echo '<div class="general-settings">
           Start popup after 
           <input type="text" class="popup_delay" name="popup-delay" value="'.$popup_delay.'"> seconds
        </div>';
}
function dp_save_metaboxes_general_settings(){
  global $post;
  
  if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ){
    return $post_id;
  }else{
    update_post_meta( $post->ID, 'popup-delay', $_POST['popup-delay'] );
  }
}
add_action( 'save_post', 'dp_save_metaboxes_general_settings' );
  /* ********************** */
 /*    CONDITIONS BOX      */          
/* ********************** */

function dp_conditions_options( $post, $post_id){
	global $post;

	$dp_conditions = get_post_meta( $post->ID, 'dp_conditions', true);
  $display_home = get_post_meta( $post->ID, 'home-show', true);
  $display_page = get_post_meta( $post->ID, 'page-show', true);
  $display_post = get_post_meta( $post->ID, 'post-show', true);
  $cat_value = get_post_meta( get_the_ID(), 'post_category', true);


	echo '<div class="conditions-box">
          <div class="theme-settings-box-left yes-border-left">
              
            <div class="theme-settings-box-column">

              <div class="theme-settings-box-column-left">
                 Display on Home page :
              </div>

              <div class="theme-settings-box-column-right">
                <input data-id="home" type="checkbox" name="home-show" '.( ( $display_home == "on" ) ? "checked" : "" ).'><br>
              </div><br>

            </div>

            <div class="theme-settings-box-column">

              <div class="theme-settings-box-column-left">
                 Display on Pages :
              </div>

              <div class="theme-settings-box-column-right">
                <input data-id="pages" type="checkbox" name="page-show"'.( ( $display_page == "on" ) ? "checked" : "" ).'><br>
              </div><br>

            </div>

            <div class="theme-settings-box-column">

              <div class="theme-settings-box-column-left">
                 Display on the Posts :
              </div>

              <div class="theme-settings-box-column-right">
                <input data-id="posts" type="checkbox" name="post-show"'.( ( $display_post == "on" ) ? "checked" : "" ).'><br>
              </div><br>

            </div>

              <div class="clear"></div>

          </div>

          <div class="theme-settings-box-right no-border-right">

            <div class="theme-settings-box-column listing-categories">

              <div class="theme-settings-box-column-left">
                 Display on Categories :
              </div>

              <div class="theme-settings-box-column-right">';
                wp_category_checklist(null,null,$cat_value); 
                echo '</div><br>

            </div>

            <div class="clear"></div>

          </div>

          <div class="clear"></div>
       </div>';
}
function dp_save_metaboxes_condition(){
  global $post;
  
  if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ){
    return $post_id;
  }else{
    update_post_meta( $post->ID, 'home-show', $_POST['home-show'] );
    update_post_meta( $post->ID, 'page-show', $_POST['page-show'] );
    update_post_meta( $post->ID, 'post-show', $_POST['post-show'] );
    update_post_meta( $post->ID, 'post_category', $_POST['post_category'] );
  }
}
add_action( 'save_post', 'dp_save_metaboxes_condition' );

  /* ********************** */
 /*   PREVIEW BUTTON BOX   */          
/* ********************** */

function dp_preview_button_options( $post, $post_id){
	global $post;

	$dp_preview_button = get_post_meta( $post->ID, 'dp_preview_button', true);

	echo '<div class="preview_button"> </div>';
}



function dp_save_metaboxes(){
  global $post;
  
  if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ){
    return $post_id;
  }else{
    update_post_meta( $post->ID, 'dp-choose-theme', $_POST['dp-choose-theme'] );
    update_post_meta( $post->ID, 'dp-select-font', $_POST['dp-select-font'] );
    update_post_meta( $post->ID, 'title-left-text', $_POST['title-left-text'] );
    update_post_meta( $post->ID, 'title-right-text', $_POST['title-right-text'] );
    update_post_meta( $post->ID, 'text-text', $_POST['text-text'] );
    update_post_meta( $post->ID, 'name-placeholder', $_POST['name-placeholder'] );
    update_post_meta( $post->ID, 'email-placeholder', $_POST['email-placeholder'] );
    update_post_meta( $post->ID, 'button-text', $_POST['button-text'] );
    update_post_meta( $post->ID, 'or-text', $_POST['or-text'] );
    update_post_meta( $post->ID, 'left-color', $_POST['left-color'] );
    update_post_meta( $post->ID, 'right-color', $_POST['right-color'] );
    update_post_meta( $post->ID, 'button-color', $_POST['button-color'] );
    update_post_meta( $post->ID, 'left-title-color', $_POST['left-title-color'] );
    update_post_meta( $post->ID, 'right-title-color', $_POST['right-title-color'] );
    update_post_meta( $post->ID, 'text-text-color', $_POST['text-text-color'] );
    update_post_meta( $post->ID, 'button-text-color', $_POST['button-text-color'] );
  }
}
add_action( 'save_post', 'dp_save_metaboxes' );

function dp_get_popust_for_cat( $cat_id ){
  $popups = array();
  $args = array(
      'post_type'  => 'da-popup',
      'post_status'=>'publish',
      'posts_per_page' => -1
    );
  $the_query = new WP_Query( $args );
  while ( $the_query->have_posts() ) {
        $the_query->the_post();
        $cat_value = get_post_meta( get_the_ID(), 'post_category', true);
        if( is_array($cat_value) && in_array( $cat_id, $cat_value ) ){
          $popups[] = get_the_ID();
        }
  }
  wp_reset_postdata();

  return ( count($popups) > 0 ) ? $popups : array(0);

}

function dp_printing_popup() {
  global $post;
    
    $args = array(
      'post_type'  => 'da-popup',
      'post_status'=>'publish',
      'meta_value' => 'on'
    );
      if( is_home() ){
      $args['meta_key'] = 'home-show';
     }else if( is_page() ){
      $args['meta_key'] = 'page-show';
     }else if( is_single($post) ){
      $args['meta_key'] = 'post-show';
     }else if( is_category() ){
      $cat_id = get_query_var('cat');
      $args['post__in'] =  dp_get_popust_for_cat( $cat_id );
      unset( $args['meta_value'] );
    }
      

    $the_query = new WP_Query( $args );

    if($the_query->have_posts()){
      while ( $the_query->have_posts() ) {
        $the_query->the_post();
        
        $dp_choose_theme = get_post_meta( get_the_ID(), 'dp-choose-theme', true);
        $dp_select_font = get_post_meta( $post->ID, 'dp-select-font', true);
        $left_title_text = get_post_meta( $post->ID, 'title-left-text', true);
        $right_title_text = get_post_meta( $post->ID, 'title-right-text', true);
        $text_text = get_post_meta( $post->ID, 'text-text', true);
        $name_placeholder = get_post_meta( $post->ID, 'name-placeholder', true);
        $email_placeholder = get_post_meta( $post->ID, 'email-placeholder', true);
        $button_text = get_post_meta( $post->ID, 'button-text', true);
        $or_text = get_post_meta( $post->ID, 'or-text', true);

        $left_color = get_post_meta( $post->ID, 'left-color', true);
        $right_color = get_post_meta( $post->ID, 'right-color', true);
        $button_color = get_post_meta( $post->ID, 'button-color', true);
        $left_title_color = get_post_meta( $post->ID, 'left-title-color', true);
        $right_title_color = get_post_meta( $post->ID, 'right-title-color', true);
        $text_text_color = get_post_meta( $post->ID, 'text-text-color', true);
        $button_text_color = get_post_meta( $post->ID, 'button-text-color', true);

        $facebook_cbx = get_post_meta( $post->ID, 'facebook-cbx', true);
        $facebook_link = get_post_meta( $post->ID, 'facebook-link', true);

        $twitter_cbx = get_post_meta( $post->ID, 'twitter-cbx', true);
        $twitter_link = get_post_meta( $post->ID, 'twitter-link', true);

        $google_cbx = get_post_meta( $post->ID, 'google-cbx', true);
        $google_link = get_post_meta( $post->ID, 'google-link', true);

        $linkedin_cbx = get_post_meta( $post->ID, 'linkedin-cbx', true);
        $linkedin_linkedin = get_post_meta( $post->ID, 'linkedin-linkedin', true);

        $pinetrest_cbx = get_post_meta( $post->ID, 'pinetrest-cbx', true);
        $pinetrest_link = get_post_meta( $post->ID, 'pinetrest-link', true);  

        $popup_delay = get_post_meta( $post->ID, 'popup-delay', true);
          
        echo '
        <script type="text/javascript">
          jQuery(window).load(function(){

            $timeout = jQuery("#popup_delay_hidden").val();

            setTimeout(function() {
              jQuery(".dark-bg-live").fadeIn();
              jQuery(".wraper-live").fadeIn();
            }, $timeout*1000);

            jQuery(".dark-bg").click(function(){
              jQuery(".dark-bg-live").fadeOut();
              jQuery(".wraper-live").fadeOut();
            });

            jQuery(".dp-close-button").click(function(){
              jQuery(".dark-bg").fadeOut(500);
              jQuery(".popup-wraper").fadeOut(500);
            });

            })
        </script>

        <div class="dark-bg dark-bg-live"></div>
              <div class="popup-wraper wraper-live"> <input type="hidden" id="popup_delay_hidden" value="'.get_post_meta( $post->ID, 'popup-delay', true).'">
              <div class="dp-close-button">X</div>
                <div class="popup-left" style="background-color:#'.$left_color.'">
                  <div class="share-title">
                    <p style="color:#'.$left_title_color.'">'.$left_title_text.'</p>
                  </div>
                  <div class="facebook" '.( ( $facebook_cbx !== "on" ) ? "style='display:none'" : "style='display:block'" ).'>
                    <iframe src="//www.facebook.com/plugins/like.php?href=https%3A%2F%2Fwww.facebook.com%2F'.$facebook_link.'&amp;width&amp;layout=box_count&amp;action=like&amp;show_faces=false&amp;share=true&amp;height=65" scrolling="no" frameborder="0" style="border:none; overflow:hidden; height:65px;" allowTransparency="true"></iframe>
                  </div>
                  <div class="twitter" '.( ( $twitter_cbx !== "on" ) ? "style='display:none'" : "style='display:block'" ).'>
                    <a class="twitter-follow-button" href="https://twitter.com/'.$twitter_link.'" data-show-screen-name="false" data-show-count="false" data-lang="en" >Follow </a>
                    <script type="text/javascript"> window.twttr = (function (d, s, id) { var t, js, fjs = d.getElementsByTagName(s)[0]; if (d.getElementById(id)) return;
                      js = d.createElement(s); js.id = id;
                      js.src= "https://platform.twitter.com/widgets.js";
                      fjs.parentNode.insertBefore(js, fjs);
                      return window.twttr || (t = { _e: [], ready: function (f) { t._e.push(f) } });
                      }(document, "script", "twitter-wjs"));
                  </script>
                  </div>
                  <div class="google" '.( ( $google_cbx !== "on" ) ? "style='display:none'" : "style='display:block'" ).'>
                    <script src="https://apis.google.com/js/platform.js" async defer></script>
                    <div class="g-follow" data-annotation="vertical-bubble" data-height="20" data-href="https://plus.google.com/116267312819938197604" data-rel="publisher"></div>
                  </div>
                  <div class="linkedin" '.( ( $linkedin_cbx !== "on" ) ? "style='display:none'" : "style='display:block'" ).'>
                    <script src="http://platform.linkedin.com/in.js" type="text/javascript">
                      lang: en_US
                    </script>
                    <script type="IN/FollowCompany" data-id="3182" data-width="70px" data-counter="top"></script>
                  </div>
                  <div class="pinetrest" '.( ( $pinetrest_cbx !== "on" ) ? "style='display:none'" : "style='display:block'" ).'>
                    <div class="pinetrest-inner">
                      <a data-pin-do="buttonFollow" href="http://www.pinterest.com/'.$pinetrest_link.'">Pinterest</a>
                      <!-- Please call pinit.js only once per page -->
                      <script type="text/javascript" async src="//assets.pinterest.com/js/pinit.js"></script>
                    </div>
                  </div>
                </div>
                <div class="or-wraper">
                <div class="or">
                  '.$or_text.'
                </div>
                </div>
                <div class="popup-right" style="background-color:#'.$right_color.'">
                  <div class="subscribe-title">
                    <p style="color:#'.$right_title_color.'">'.$right_title_text.'</p>
                  </div>
                  <div class="subscribe-text">
                  <p style="color:#'.$text_text_color.'">'.$text_text.'</p>
                  </div>
                  <div class="subscribe-form">
                  <form method="post" action="#" id="mcsubscribeform" name="mcsubscribeform">
                    <div class="subscribe-input-name">
                      <input class="sub-input name" name="dp_name" type="text" placeholder="'.$name_placeholder.'" id="dp_name_sub">
                      <div class="input-icon">
                        <div class="input-icon-inner">
                          <i class="fa fa-user"></i>
                        </div>
                      </div>
                    </div>
                    <div class="subscribe-input-email">
                      <input class="sub-input email" name="dp_email" type="text" placeholder="'.$email_placeholder.'" id="dp_email_sub">
                      <div class="input-icon">
                        <div class="input-icon-inner">
                          <i class="fa martop fa-envelope"></i>
                        </div>
                      </div>
                    </div>
                    <div class="subscribe-button">
                      <a href="#" class="subscribe-submit" data-loading="Loading..." data-label="Subscribe" data-icon="fa-pencil-square-o" style="background-color:#'.$button_color.';color:#'.$button_text_color.'">
                        <i class="fa fa-pencil-square-o"></i>
                        '.$button_text.'
                      </a>
                    </div>
                  </div>
                </div>
              </div>';
      }
      wp_reset_postdata();
      }else {
        echo 'no posts found';
      }
    
}

if ( !set_newuser_cookie() ){
  add_action('wp_footer', 'dp_printing_popup');
}




function pluginname_ajaxurl() {
    ?>
<script type="text/javascript">
    var ajaxurl = '<?php echo admin_url("admin-ajax.php"); ?>';
</script>
<?php
}

add_action('wp_head','pluginname_ajaxurl');

add_action( 'wp_footer', 'dp_subscribe_javascript_ajax' ); // Write our JS below here

function dp_subscribe_javascript_ajax() { ?>
  <script type="text/javascript" >
  jQuery('.subscribe-submit').click(function(e) {
    e.preventDefault();
    var dp_name = jQuery('#dp_name_sub').val();
    var dp_email = jQuery('#dp_email_sub').val();
    var data = {
      'action': 'dp_subscribe_ajax',
      'name': dp_name,
      'email': dp_email
    };
    console.log(dp_email);
    // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
    jQuery.post(ajaxurl, data, function(response) {
      if(response === 0){
        alert('You are not subscribed');
      } else {
        alert('You have subscribed');
      }
    });
  });
  </script> <?php
}


add_action( 'wp_ajax_dp_subscribe_ajax', 'dp_subscribe_ajax_function' );
add_action( 'wp_ajax_nopriv_dp_subscribe_ajax', 'dp_subscribe_ajax_function' );

function dp_subscribe_ajax_function() {
  global $wpdb; // this is how you get access to the database

 $name= $_POST['name'];
 $email= $_POST['email'];
 $list = get_option('iwbmc_api_list');
 echo iwbmc_subscribe( $email, $name, $list);

  die(); // this is required to terminate immediately and return a proper response
}