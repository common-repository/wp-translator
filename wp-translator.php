<?php
/*
Plugin Name: WP Translator
Plugin URI: http://wordpress.org/plugins/wp-translator/
Version: 1.0.1
Description: WP Translator plugin. This plugin adds Google Translator to your website by using a single [wp-translator] shortcode OR by using a simple widget. Settings include: layout style, hide/show specific languages, hide/show Google toolbar, and hide/show Google branding. Add the shortcode to pages, posts, and widgets.
Author: looksof
*/

include( plugin_dir_path( __FILE__ ) . 'widget.php');

class google_language_translator {
  
  public $languages_array = array(
	  'af' => 'Afrikaans',
	  'sq' => 'Albanian',
	  'ar' => 'Arabic',
	  'hy' => 'Armenian',
	  'az' => 'Azerbaijani',
	  'eu' => 'Basque',
	  'be' => 'Belarusian',
	  'bn' => 'Bengali',
	  'bs' => 'Bosnian',
	  'bg' => 'Bulgarian',
	  'ca' => 'Catalan',
	  'ceb' => 'Cebuano',
	  'zh-CN' => 'Chinese(Simplified)',
	  'zh-TW' => 'Chinese(Traditional)',
	  'hr' => 'Croatian',
	  'cs' => 'Czech',
	  'da' => 'Danish',
	  'nl' => 'Dutch',
	  'en' => 'English',
	  'eo' => 'Esperanto',
	  'et' => 'Estonian',
	  'tl' => 'Filipino',
	  'fi' => 'Finnish',
	  'fr' => 'French',
	  'gl' => 'Galician',
	  'ka' => 'Georgian',
	  'de' => 'German',
	  'el' => 'Greek',
	  'gu' => 'Gujarati',
	  'ht' => 'Haitian',
	  'ha' => 'Hausa',
	  'iw' => 'Hebrew',
	  'hi' => 'Hindi',
	  'hmn' => 'Hmong',
	  'hu' => 'Hungarian',
	  'is' => 'Icelandic',
	  'ig' => 'Igbo',
	  'id' => 'Indonesian',
	  'ga' => 'Irish',
	  'it' => 'Italian',
	  'ja' => 'Japanese',
	  'jw' => 'Javanese',
	  'kn' => 'Kannada',
	  'km' => 'Khmer',
	  'ko' => 'Korean',
	  'lo' => 'Lao',
	  'la' => 'Latin',
	  'lv' => 'Latvian',
	  'lt' => 'Lithuanian',
	  'mk' => 'Macedonian',
	  'ms' => 'Malay',
	  'mt' => 'Maltese',
	  'mi' => 'Maori',
	  'mr' => 'Marathi',
	  'mn' => 'Mongolian',
	  'ne' => 'Nepali',
	  'no' => 'Norwegian',
	  'fa' => 'Persian',
	  'pl' => 'Polish',
	  'pt' => 'Portuguese',
	  'pa' => 'Punjabi',
	  'ro' => 'Romanian',
	  'ru' => 'Russian',
	  'sr' => 'Serbian',
	  'sk' => 'Slovak',
	  'sl' => 'Slovenian',
	  'so' => 'Somali',
	  'es' => 'Spanish',
	  'sw' => 'Swahili',
	  'sv' => 'Swedish',
	  'ta' => 'Tamil',
	  'te' => 'Telugu',
	  'th' => 'Thai',
	  'tr' => 'Turkish',
	  'uk' => 'Ukranian',
	  'ur' => 'Urdu',
	  'vi' => 'Vietnamese',
	  'cy' => 'Welsh',
	  'yi' => 'Yiddish',
	  'yo' => 'Yoruba',
	  'zu' => 'Zulu'
	);
  
  public function __construct(){

    register_activation_hook( __FILE__, array( $this, 'glt_activate' ) );

    add_action('admin_menu',array(&$this, 'page_layout'));
    add_action('admin_init',array(&$this, 'initialize_settings')); 
    add_action('wp_head',array(&$this, 'load_css'));
    add_action('admin_head',array(&$this, 'load_css'));
    add_shortcode( 'wp-translator',array(&$this, 'google_translator_shortcode'));
    add_filter('widget_text','do_shortcode');

    if (!is_admin()) {
      add_action('init',array(&$this, 'flags'));
    }
  }
  
  public function glt_activate() {
    add_option('googlelanguagetranslator_active', 1); 
    add_option('googlelanguagetranslator_language','en'); 
    add_option('googlelanguagetranslator_language_option','all'); 
    add_option('language_display_settings',array ('en' => 1));
    add_option('googlelanguagetranslator_flags','show_flags');
    add_option('flag_display_settings',array ('flag-en' => 1)); 
    add_option('googlelanguagetranslator_translatebox','yes'); 
    add_option('googlelanguagetranslator_display','Vertical'); 
    add_option('googlelanguagetranslator_toolbar','Yes'); 
    add_option('googlelanguagetranslator_showbranding','Yes'); 
    add_option('googlelanguagetranslator_flags_alignment','flags_left');   
    add_option('googlelanguagetranslator_analytics',1);
    add_option('googlelanguagetranslator_analytics_id','');  
    add_option('googlelanguagetranslator_css','');
    add_option('googlelanguagetranslator_manage_translations',0);
    add_option('googlelanguagetranslator_multilanguage',0);
    add_option('googlelanguagetranslator_floating_widget','yes');
  } 
  
  public function load_css() {
	include( plugin_dir_path( __FILE__ ) . 'css/style.php');
  }

  public function scripts($hook_suffix) {
    global $p;
      if ($p == $hook_suffix) {
		wp_enqueue_script( 'cookie.js', plugins_url('js/cookie.js',__FILE__), array('jquery'));
        wp_enqueue_script( 'my-admin-script', plugins_url('js/admin.js',__FILE__), array('jquery'));
	    wp_enqueue_script( 'my-flag-script', plugins_url('js/flags.js',__FILE__), array('jquery'));
		
		if (get_option ('googlelanguagetranslator_floating_widget') == 'yes') {
          wp_enqueue_script( 'my-toolbar-script', plugins_url('js/toolbar.js',__FILE__), array('jquery'));
		  wp_enqueue_script( 'my-load-toolbar-script', plugins_url('js/load-toolbar.js',__FILE__), array('jquery'));
		  wp_register_style( 'toolbar.css', plugins_url('css/toolbar.css', __FILE__) );
		  wp_enqueue_style( 'toolbar.css' );
		}
		
		wp_enqueue_script( 'jquery-ui.js', plugins_url('js/jquery-ui.js',__FILE__), array('jquery'));
		wp_enqueue_script( 'jquery-ui-sortable.js', plugins_url('js/jquery-ui-sortable.js',__FILE__), array('jquery'));
		wp_enqueue_script( 'jquery-ui-widget.js', plugins_url('js/jquery-ui-widget.js',__FILE__), array('jquery'));
		wp_enqueue_script( 'jquery-ui-mouse.js', plugins_url('js/jquery-ui-mouse.js',__FILE__), array('jquery'));
		wp_enqueue_script( 'load_sortable_flags', plugins_url('js/load-sortable-flags.js',__FILE__), array('jquery'));
		wp_register_style( 'jquery-ui.css', plugins_url('css/jquery-ui.css',__FILE__) );
	    wp_register_style( 'style.css', plugins_url('css/style.css', __FILE__) );
        wp_enqueue_style( 'style.css' );	
		wp_enqueue_style( 'jquery-ui.css' );
      }
   }

   public function flags() {
        wp_enqueue_script( 'flags', plugins_url('js/flags.js',__FILE__), array('jquery'));
	 
	    if (get_option ('googlelanguagetranslator_floating_widget') == 'yes') {
          wp_enqueue_script( 'my-toolbar-script', plugins_url('js/toolbar.js',__FILE__), array('jquery'));
	      wp_enqueue_script( 'my-load-toolbar-script', plugins_url('js/load-toolbar.js',__FILE__), array('jquery'));
	      wp_register_style( 'toolbar.css', plugins_url('css/toolbar.css', __FILE__) );
		  wp_enqueue_style( 'toolbar.css' );
		}
	 
        wp_register_style( 'style.css', plugins_url('css/style.css', __FILE__) );
        wp_enqueue_style( 'style.css' );	
   }
    
   public function page_layout () {
      global $p;
   
      add_action( 'admin_enqueue_scripts',array(&$this, 'scripts'));
      add_action('admin_head',array(&$this, 'load_css'));
  
      $p = add_options_page('WP Translator', 'WP Translator', 'manage_options', 'google_language_translator', array(&$this, 'page_layout_cb'));
  }	
    

  public function google_translator_shortcode() {
    if (get_option('googlelanguagetranslator_display')=='Vertical'){
      return $this->googlelanguagetranslator_vertical();
    }

    elseif(get_option('googlelanguagetranslator_display')=='Horizontal'){
      return $this->googlelanguagetranslator_horizontal();
    }
  }

  public function googlelanguagetranslator_included_languages() {
    if ( get_option('googlelanguagetranslator_language_option')=='specific') { 
	  $get_language_choices = get_option ('language_display_settings');
	  
	  foreach ($get_language_choices as $key=>$value) {
	    if($value == 1) {
		  $items[] = $key;
	    }
	  }
	  
	  $comma_separated = implode(",",array_values($items));
	  
	  if ( get_option('googlelanguagetranslator_display') == 'Vertical') {
	     $lang = ', includedLanguages:\''.$comma_separated.'\'';
	       return $lang;
	  } elseif ( get_option('googlelanguagetranslator_display') == 'Horizontal') {
	     $lang = ', includedLanguages:\''.$comma_separated.'\'';
	       return $lang;
      } 
    }
  }

  public function analytics() {
    if ( get_option('googlelanguagetranslator_analytics') == 1 ) {
	  $analytics_id = get_option('googlelanguagetranslator_analytics_id');
	  $analytics = 'gaTrack: true, gaId: \''.$analytics_id.'\'';
	    return ', '.$analytics;
    }
  }
  
  public function googlelanguagetranslator_vertical(){   
	$new_languages_array_string = get_option('googlelanguagetranslator_flags_order');
	$new_languages_array = explode(",",$new_languages_array_string);
	$new_languages_array_codes = array_values($new_languages_array);
	$get_language_choices = get_option ('language_display_settings');
	$flag_width = get_option('googlelanguagetranslator_flag_size');
	$default_language_code = get_option('googlelanguagetranslator_language');	
    $is_active = get_option ( 'googlelanguagetranslator_active' );
    $get_flag_choices = get_option ('flag_display_settings');
	$get_language_option = get_option('googlelanguagetranslator_language_option');
    $language_choices = $this->googlelanguagetranslator_included_languages();
    $floating_widget = get_option ('googlelanguagetranslator_floating_widget');
	$str = '';
  
	if( $is_active == 1){
	  
	  foreach ($get_flag_choices as $flag_choice_key) {}
	  
	  if ($floating_widget=='yes' && $get_language_option != 'specific') {
	    $str.='<div id="glt-translate-trigger"><span class="notranslate">Translate &raquo;</strong></div>';
        $str.='<div id="glt-toolbar">';
		$str.='<ul id="sortable" class="ui-sortable">';
	    
	    if (empty($new_languages_array_string)) {
		    foreach ($this->languages_array as $key=>$value) {
		      $language_code = $key;
		      $language_name = $value;
			  $language_name_flag = $language_name;
			  
			  if ($flag_choice_key == '1') {
                if ( isset ( $get_flag_choices['flag-'.$language_code.''] ) ) {
				  $str.='<a id="'.$language_name.'" href="#" onclick="doGoogleLanguageTranslator(\''.$default_language_code.'|'.$language_code.'\'); return false;" title="'.$language_name.'" class="notranslate flag '.$language_code.'"><img class="flagimg" src="'.plugins_url().'/google-language-translator/images/flags24/'.$language_name_flag.'.png" height="24" width="24" alt="'.$language_name.'"/></a>';
				}//isset
		      } //$key
			}//foreach
	    } else {
			foreach ($new_languages_array_codes as $value) {
		      $language_name = $value;
			  $language_code = array_search ($language_name,$this->languages_array);
			  $language_name_flag = $language_name;
			  
			  if ($flag_choice_key == '1') {
			    if (in_array($language_name,$this->languages_array)) {
                  if ( isset ( $get_flag_choices['flag-'.$language_code.''] ) ) {
					$str.='<a id="'.$language_name.'" href="#" onclick="doGoogleLanguageTranslator(\''.$default_language_code.'|'.$language_code.'\'); return false;" title="'.$language_name.'" class="notranslate flag '.$language_code.'"><img class="flagimg" src="'.plugins_url().'/google-language-translator/images/flags24/'.$language_name_flag.'.png" height="24" width="24" alt="'.$language_name.'"/></a>';
		          } //isset
			    } //in_array
		      }//if
	        }//foreach
		}
		$str.='</ul>';
        $str.='</div>';
	  } //endif $floating_widget
	  
	  $str.='<div id="flags">';
	  $str.='<ul id="sortable" class="ui-sortable" style="float:left">';
	  
	    if (empty($new_languages_array_string)) {
		    foreach ($this->languages_array as $key=>$value) {
		      $language_code = $key;
		      $language_name = $value;
			  $language_name_flag = $language_name;
			  
			  if ($flag_choice_key == '1') {
                if ( isset ( $get_flag_choices['flag-'.$language_code.''] ) ) {				  
				  $str.='<a id="'.$language_name.'" onclick="doGoogleLanguageTranslator(\''.$default_language_code.'|'.$language_code.'\'); return false;" title="'.$language_name.'" class="notranslate flag '.$language_code.'"><img class="flagimg" src="'.plugins_url().'/google-language-translator/images/flags24/'.$language_name_flag.'.png" height="'.$flag_width.'" width="'.$flag_width.'" alt="'.$language_name.'"/></a>';
			    }
		      } //$key
			}//foreach
	    } else {
			foreach ($new_languages_array_codes as $value) {
		      $language_name = $value;
			  $language_code = array_search ($language_name,$this->languages_array);
			  $language_name_flag = $language_name;
			  
			  if ($flag_choice_key == '1') {
			    if (in_array($language_name,$this->languages_array)) {
                  if ( isset ( $get_flag_choices['flag-'.$language_code.''] ) ) {
					$str.='<a id="'.$language_name.'" onclick="doGoogleLanguageTranslator(\''.$default_language_code.'|'.$language_code.'\'); return false;" title="'.$language_name.'" class="notranslate flag '.$language_code.'"><img class="flagimg" src="'.plugins_url().'/google-language-translator/images/flags24/'.$language_name_flag.'.png" height="'.$flag_width.'" width="'.$flag_width.'" alt="'.$flag_width.'"/></a>';
		          } //isset
			    } //in_array
		      }//flag_choice_key
	        }//foreach
		}//else
	  
      $str.='</ul>';
	  $str.='</div>';
		  
		$is_multilanguage = get_option('googlelanguagetranslator_multilanguage');
		$auto_display = ', autoDisplay: false';

        if ($is_multilanguage == 1) {
          $multilanguagePage = ', multilanguagePage:true';
		  
		  $str.='<script type="text/javascript">     
         function GoogleLanguageTranslatorInit() { 
         new google.translate.TranslateElement({pageLanguage: \''.get_option('googlelanguagetranslator_language').'\''.$language_choices . $auto_display . $multilanguagePage . $this->analytics().'}, \'google_language_translator\');}
              </script><script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=GoogleLanguageTranslatorInit"></script>
<div id="google_language_translator"></div>';
		  return $str;
		} elseif ($is_multilanguage == 0) {		  
		
        $str.='<script type="text/javascript">     
         function GoogleLanguageTranslatorInit() { 
         new google.translate.TranslateElement({pageLanguage: \''.get_option('googlelanguagetranslator_language').'\''.$language_choices . $auto_display . $this->analytics().'}, \'google_language_translator\');}
              </script><script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=GoogleLanguageTranslatorInit"></script>
<div id="google_language_translator"></div>';
		  return $str;
		}
	} //End is_active
  } // End glt_vertical
  
  public function googlelanguagetranslator_horizontal(){
    $new_languages_array_string = get_option('googlelanguagetranslator_flags_order');
	$new_languages_array = explode(",",$new_languages_array_string);
	$new_languages_array_codes = array_values($new_languages_array);
	$get_language_choices = get_option ('language_display_settings');
	$flag_width = get_option('googlelanguagetranslator_flag_size');
	$default_language_code = get_option('googlelanguagetranslator_language');	
    $is_active = get_option ( 'googlelanguagetranslator_active' );
    $get_flag_choices = get_option ('flag_display_settings');
	$get_language_option = get_option('googlelanguagetranslator_language_option');
    $language_choices = $this->googlelanguagetranslator_included_languages();
    $floating_widget = get_option ('googlelanguagetranslator_floating_widget');
	$str = '';
  
    if( $is_active == 1) {
	  foreach ($get_flag_choices as $flag_choice_key) {}
	  
	  if ($floating_widget=='yes' && $get_language_option != 'specific') {
	    $str.='<div id="glt-translate-trigger"><span class="notranslate">Translate &raquo;</strong></div>';
        $str.='<div id="glt-toolbar">';
		$str.='<ul id="sortable" class="ui-sortable" style="float:left">';
	    
	    if (empty($new_languages_array_string)) {
	    
		    foreach ($this->languages_array as $key=>$value) {
		      $language_code = $key;
		      $language_name = $value;
			  $language_name_flag = $language_name;
			  
			  if ($flag_choice_key == '1') {
                if ( isset ( $get_flag_choices['flag-'.$language_code.''] ) ) {
				  $str.='<a id="'.$language_name.'" href="#" onclick="doGoogleLanguageTranslator(\''.$default_language_code.'|'.$language_code.'\'); return false;" title="'.$language_name.'" class="notranslate flag '.$languge_code.'"><img class="flagimg" src="'.plugins_url().'/google-language-translator/images/flags24/'.$language_name_flag.'.png" height="24" width="24" alt="'.$language_name.'"/></a>';
			    }
		      } //$key
			}//foreach
			  
			 
	    } elseif (!empty($new_languages_array_string)) {
		
			foreach ($new_languages_array_codes as $value) {
		      $language_name = $value;
			  $language_code = array_search ($language_name,$this->languages_array);
			  $language_name_flag = $language_name;
			  
			  if ($flag_choice_key == '1') {
			    if (in_array($language_name,$this->languages_array)) {
                  if ( isset ( $get_flag_choices['flag-'.$language_code.''] ) ) {
				    $str.='<a id="'.$language_name.'" href="#" onclick="doGoogleLanguageTranslator(\''.$default_language_code.'|'.$language_code.'\'); return false;" title="'.$language_name.'" class="notranslate flag '.$languge_code.'"><img class="flagimg" src="'.plugins_url().'/google-language-translator/images/flags24/'.$language_name_flag.'.png" height="24" width="24" alt="'.$language_name.'"/></a>';
			      }
		        } //$in_array
			  } //$key
		    }//foreach
	    }//empty
	  
		$str.='</ul>';
        $str.='</div>';
	  }

	  $str.='<div id="flags">';
	  $str.='<ul id="sortable" class="ui-sortable">';
	    
	    if (empty($new_languages_array_string)) {
	    
		    foreach ($this->languages_array as $key=>$value) {
		      $language_code = $key;
		      $language_name = $value;
			  $language_name_flag = $language_name;
			  
			  if ($flag_choice_key == '1') {
                if ( isset ( $get_flag_choices['flag-'.$language_code.''] ) ) {
				  $str.='<a id="'.$language_name.'" onclick="doGoogleLanguageTranslator(\''.$default_language_code.'|'.$language_code.'\'); return false;" title="'.$language_name.'" class="notranslate flag '.$language_code.'"><img class="flagimg" src="'.plugins_url().'/google-language-translator/images/flags24/'.$language_name_flag.'.png" height="'.$flag_width.'" width="'.$flag_width.'" alt="'.$language_name.'"/></a>';
			    }
		      } //$key
			}//foreach
	    } elseif (!empty($new_languages_array_string)) {
		
			foreach ($new_languages_array_codes as $value) {
		      $language_name = $value;
			  $language_code = array_search ($language_name,$this->languages_array);
			  $language_name_flag = $language_name;
			  
			  if ($flag_choice_key == '1') {
                if ( isset ( $get_flag_choices['flag-'.$language_code.''] ) ) {
				  $str.='<a id="'.$language_name.'" onclick="doGoogleLanguageTranslator(\''.$default_language_code.'|'.$language_code.'\'); return false;" title="'.$language_name.'" class="notranslate flag '.$language_code.'"><img class="flagimg" src="'.plugins_url().'/google-language-translator/images/flags24/'.$language_name_flag.'.png" height="'.$flag_width.'" width="'.$flag_width.'" alt="'.$language_name.'"/></a>';
		       } //isset
		    }//$flag_choice_key
	      }//foreach
		}//elseif
	  
      $str.='</ul>';
	  $str.='</div>';
		
		$is_multilanguage = get_option('googlelanguagetranslator_multilanguage');
		$horizontal_layout = ', layout: google.translate.TranslateElement.InlineLayout.HORIZONTAL';
		$auto_display = ', autoDisplay: false';

        if ($is_multilanguage == 1) {
          $multilanguagePage = ', multilanguagePage:true';
		  
		  $str.='<script type="text/javascript">     
         function GoogleLanguageTranslatorInit() { 
         new google.translate.TranslateElement({pageLanguage: \''.get_option('googlelanguagetranslator_language').'\''. $language_choices . $horizontal_layout . $auto_display . $multilanguagePage . $this->analytics().'}, \'google_language_translator\');}
              </script><script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=GoogleLanguageTranslatorInit"></script>
<div id="google_language_translator"></div>';
		return $str;
		} elseif ($is_multilanguage == 0) {		  
		
        $str.='<script type="text/javascript">     
         function GoogleLanguageTranslatorInit() { 
         new google.translate.TranslateElement({pageLanguage: \''.get_option('googlelanguagetranslator_language').'\''. $language_choices . $horizontal_layout . $auto_display . $this->analytics().'}, \'google_language_translator\');}
              </script><script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=GoogleLanguageTranslatorInit"></script>
<div id="google_language_translator"></div>';
		return $str;
		}
    }
  } // End glt_horizontal
 
  public function initialize_settings() {  
  
    // First, we register a section. This is necessary since all future options must belong to one.  
    add_settings_section(  
        'glt_settings',         // ID used to identify this section and with which to register options  
        'Settings',                  // Title to be displayed on the administration page  
        '', // Callback used to render the description of the section  
        'google_language_translator'                           // Page on which to add this section of options  
    ); 
  
    //Fieldset 1
    add_settings_field( 'googlelanguagetranslator_active','Plugin status:','googlelanguagetranslator_active_cb','google_language_translator','glt_settings');
    add_settings_field( 'googlelanguagetranslator_language','Choose the original language of your website','','googlelanguagetranslator_language_cb','glt_settings');
    add_settings_field( 'googlelanguagetranslator_language_option','What translation languages will you display to website visitors?','googlelanguagetranslator_language_option_cb','google_language_translator','glt_settings');	
	add_settings_field( 'language_display_settings','Your language choices','language_display_settings_cb','google_language_translator','glt_settings');
    add_settings_field( 'googlelanguagetranslator_flags','Show Flag Images?','googlelanguagetranslator_flags_cb','google_language_translator','glt_settings');
    add_settings_field( 'flag_display_settings','Flag Options','flag_display_settings_cb','google_language_translator','glt_settings');
    add_settings_field( 'googlelanguagetranslator_translatebox','Show Translate Box?','googlelanguagetranslator_translatebox_cb','google_language_translator','glt_settings');
    add_settings_field( 'googlelanguagetranslator_display', 'Layout Options','googlelanguagetranslator_display_cb','google_language_translator','glt_settings');
    add_settings_field( 'googlelanguagetranslator_toolbar', 'Show Toolbar','googlelanguagetranslator_toolbar_cb','google_language_translator','glt_settings');
    add_settings_field( 'googlelanguagetranslator_showbranding', 'Show Google Branding','googlelanguagetranslator_showbranding_cb','google_language_translator','glt_settings');
    add_settings_field( 'googlelanguagetranslator_flags_alignment', 'Align Flags Right or Left', 'googlelanguagetranslator_flags_alignment_cb','google_language_translator','glt_settings');
    add_settings_field( 'googlelanguagetranslator_analytics','Activate Google Analytics tracking?','googlelanguagetranslator_analytics_cb','google_language_translator','glt_settings');
    add_settings_field( 'googlelanguagetranslator_analytics_id','Enter your Google Analytics ID','googlelanguagetranslator_analytics_id_cb','google_language_translator','glt_settings');
    add_settings_field( 'googlelanguagetranslator_css','Custom CSS Overrides','googlelanguagetranslator_css_cb','google_language_translator','glt_settings');
    add_settings_field( 'googlelanguagetranslator_manage_translations','Turn on translation management?','googlelanguagetranslator_manage_translations_cb','google_language_translator','glt_settings');
    add_settings_field( 'googlelanguagetranslator_multilanguage','Multilanguage webpages?','googlelanguagetranslator_multilanguage_cb','google_language_translator','glt_settings');
    add_settings_field( 'googlelanguagetranslator_floating_widget','Show floating translation widget?','googlelanguagetranslator_floating_widget_cb','google_language_translator','glt_settings');
    add_settings_field( 'googlelanguagetranslator_flag_size','Flag sizes:','googlelanguagetranslator_flag_size_cb','google_language_translator','glt_settings');
    add_settings_field( 'googlelanguagetranslator_flags_order','The order of flag positions','googlelanguagetranslator_flags_order_cb','google_language_translator','glt_settings');
  
    //Register Fieldset 1
    register_setting( 'google_language_translator','googlelanguagetranslator_active'); 
    register_setting( 'google_language_translator','googlelanguagetranslator_language');
    register_setting( 'google_language_translator','googlelanguagetranslator_language_option');
    register_setting( 'google_language_translator','language_display_settings');
    register_setting( 'google_language_translator','googlelanguagetranslator_flags');
    register_setting( 'google_language_translator','flag_display_settings');
    register_setting( 'google_language_translator','googlelanguagetranslator_translatebox');
    register_setting( 'google_language_translator','googlelanguagetranslator_display');
    register_setting( 'google_language_translator','googlelanguagetranslator_toolbar');
    register_setting( 'google_language_translator','googlelanguagetranslator_showbranding');
    register_setting( 'google_language_translator','googlelanguagetranslator_flags_alignment');
    register_setting( 'google_language_translator','googlelanguagetranslator_disable_mootools');
    register_setting( 'google_language_translator','googlelanguagetranslator_disable_modal');
    register_setting( 'google_language_translator','googlelanguagetranslator_analytics');
    register_setting( 'google_language_translator','googlelanguagetranslator_analytics_id');
    register_setting( 'google_language_translator','googlelanguagetranslator_css');
    register_setting( 'google_language_translator','googlelanguagetranslator_manage_translations');
    register_setting( 'google_language_translator','googlelanguagetranslator_multilanguage');
    register_setting( 'google_language_translator','googlelanguagetranslator_floating_widget');
	register_setting( 'google_language_translator','googlelanguagetranslator_flag_size');
	register_setting( 'google_language_translator','googlelanguagetranslator_flags_order');
  }
  
  public function googlelanguagetranslator_active_cb() {
	  
	$option_name = 'googlelanguagetranslator_active' ;
    $new_value = 1;

      if ( get_option( $option_name ) === false ) {

      // The option does not exist, so we update it.
      update_option( $option_name, $new_value );
	  } 
	  
	  $options = get_option (''.$option_name.'');
	  		
	  $html = '<input type="checkbox" name="googlelanguagetranslator_active" id="googlelanguagetranslator_active" value="1" '.checked(1,$options,false).'/> &nbsp; Activate WP Translator?';
	  echo $html;
	}
  
  public function googlelanguagetranslator_language_cb() {
	  
	$option_name = 'googlelanguagetranslator_language';
    $new_value = 'en';

      if ( get_option( $option_name ) === false ) {

      // The option does not exist, so we update it.
      update_option( $option_name, $new_value );
	  } 
	  
	  $options = get_option (''.$option_name.''); ?>
	   <select name="googlelanguagetranslator_language" id="googlelanguagetranslator_language">
				  <option value="af" <?php if($options=='af'){echo "selected";}?>>Afrikaans</option>
				  <option value="sq" <?php if($options=='sq'){echo "selected";}?>>Albanian</option>
				  <option value="ar" <?php if($options=='ar'){echo "selected";}?>>Arabic</option>
				  <option value="hy" <?php if($options=='hy'){echo "selected";}?>>Armenian</option>
				  <option value="az" <?php if($options=='az'){echo "selected";}?>>Azerbaijani</option>
				  <option value="eu" <?php if($options=='eu'){echo "selected";}?>>Basque</option>
				  <option value="be" <?php if($options=='be'){echo "selected";}?>>Belarusian</option>
				  <option value="bn" <?php if($options=='bn'){echo "selected";}?>>Bengali</option>
				  <option value="bs" <?php if($options=='bs'){echo "selected";}?>>Bosnian</option>
				  <option value="bg" <?php if($options=='bg'){echo "selected";}?>>Bulgarian</option>
				  <option value="ca" <?php if($options=='ca'){echo "selected";}?>>Catalan</option>
				  <option value="ceb" <?php if($options=='ceb'){echo "selected";}?>>Cebuano</option>
				  <option value="zh-CN" <?php if($options=='zh-CN'){echo "selected";}?>>Chinese</option>
				  <option value="zh-TW" <?php if($options=='zh-TW'){echo "selected";}?>>Chinese (Han)</option>
				  <option value="hr" <?php if($options=='hr'){echo "selected";}?>>Croatian</option>
				  <option value="cs" <?php if($options=='cs'){echo "selected";}?>>Czech</option>
				  <option value="da" <?php if($options=='da'){echo "selected";}?>>Danish</option>
				  <option value="nl" <?php if($options=='nl'){echo "selected";}?>>Dutch</option>
				  <option value="en" <?php if($options=='en'){echo "selected";}?>>English</option>
				  <option value="eo" <?php if($options=='eo'){echo "selected";}?>>Esperanto</option>
				  <option value="et" <?php if($options=='et'){echo "selected";}?>>Estonian</option>
				  <option value="tl" <?php if($options=='tl'){echo "selected";}?>>Filipino</option>
				  <option value="fi" <?php if($options=='fi'){echo "selected";}?>>Finnish</option>
				  <option value="fr" <?php if($options=='fr'){echo "selected";}?>>French</option>
				  <option value="gl" <?php if($options=='gl'){echo "selected";}?>>Galician</option>
				  <option value="ka" <?php if($options=='ka'){echo "selected";}?>>Georgian</option>
				  <option value="de" <?php if($options=='de'){echo "selected";}?>>German</option>
				  <option value="el" <?php if($options=='el'){echo "selected";}?>>Greek</option>
				  <option value="gu" <?php if($options=='gu'){echo "selected";}?>>Gujarati</option>
				  <option value="ht" <?php if($options=='ht'){echo "selected";}?>>Haitian Creole</option>
		          <option value="ha" <?php if($options=='ha'){echo "selected";}?>>Hausa</option>
				  <option value="iw" <?php if($options=='iw'){echo "selected";}?>>Hebrew</option>
				  <option value="hi" <?php if($options=='hi'){echo "selected";}?>>Hindi</option>
				  <option value="hmn" <?php if($options=='hmn'){echo "selected";}?>>Hmong</option>
				  <option value="hu" <?php if($options=='hu'){echo "selected";}?>>Hungarian</option>
				  <option value="is" <?php if($options=='is'){echo "selected";}?>>Icelandic</option>
		          <option value="ig" <?php if($options=='ig'){echo "selected";}?>>Igbo</option>
				  <option value="id" <?php if($options=='id'){echo "selected";}?>>Indonesian</option>
				  <option value="ga" <?php if($options=='ga'){echo "selected";}?>>Irish</option>
				  <option value="it" <?php if($options=='it'){echo "selected";}?>>Italian</option>
				  <option value="ja" <?php if($options=='ja'){echo "selected";}?>>Japanese</option>
				  <option value="jw" <?php if($options=='jw'){echo "selected";}?>>Javanese</option>
				  <option value="kn" <?php if($options=='kn'){echo "selected";}?>>Kannada</option>
				  <option value="km" <?php if($options=='km'){echo "selected";}?>>Khmer</option>
				  <option value="ko" <?php if($options=='ko'){echo "selected";}?>>Korean</option>
				  <option value="lo" <?php if($options=='lo'){echo "selected";}?>>Lao</option>
				  <option value="la" <?php if($options=='la'){echo "selected";}?>>Latin</option>
				  <option value="lv" <?php if($options=='lv'){echo "selected";}?>>Latvian</option>
				  <option value="lt" <?php if($options=='lt'){echo "selected";}?>>Lithuanian</option>
				  <option value="mk" <?php if($options=='mk'){echo "selected";}?>>Macedonian</option>
				  <option value="ms" <?php if($options=='ms'){echo "selected";}?>>Malay</option>
				  <option value="mt" <?php if($options=='mt'){echo "selected";}?>>Maltese</option>
		          <option value="mi" <?php if($options=='mi'){echo "selected";}?>>Maori</option>
				  <option value="mr" <?php if($options=='mr'){echo "selected";}?>>Marathi</option>
		          <option value="mn" <?php if($options=='mn'){echo "selected";}?>>Mongolian</option>
		          <option value="ne" <?php if($options=='ne'){echo "selected";}?>>Nepali</option>
				  <option value="no" <?php if($options=='no'){echo "selected";}?>>Norwegian</option>
				  <option value="fa" <?php if($options=='fa'){echo "selected";}?>>Persian</option>
				  <option value="pl" <?php if($options=='pl'){echo "selected";}?>>Polish</option>
				  <option value="pt" <?php if($options=='pt'){echo "selected";}?>>Portuguese</option>
		          <option value="pa" <?php if($options=='pa'){echo "selected";}?>>Punjabi</option>
				  <option value="ro" <?php if($options=='ro'){echo "selected";}?>>Romanian</option>
				  <option value="ru" <?php if($options=='ru'){echo "selected";}?>>Russian</option>
				  <option value="sr" <?php if($options=='sr'){echo "selected";}?>>Serbian</option>
				  <option value="sk" <?php if($options=='sk'){echo "selected";}?>>Slovak</option>
				  <option value="sl" <?php if($options=='sl'){echo "selected";}?>>Slovenian</option>
		          <option value="so" <?php if($options=='so'){echo "selected";}?>>Somali</option>
				  <option value="es" <?php if($options=='es'){echo "selected";}?>>Spanish</option>
				  <option value="sw" <?php if($options=='sw'){echo "selected";}?>>Swahili</option>
				  <option value="sv" <?php if($options=='sv'){echo "selected";}?>>Swedish</option>
				  <option value="ta" <?php if($options=='ta'){echo "selected";}?>>Tamil</option>
				  <option value="te" <?php if($options=='te'){echo "selected";}?>>Telugu</option>
				  <option value="th" <?php if($options=='th'){echo "selected";}?>>Thai</option>
				  <option value="tr" <?php if($options=='tr'){echo "selected";}?>>Turkish</option>
				  <option value="uk" <?php if($options=='uk'){echo "selected";}?>>Ukranian</option>
				  <option value="ur" <?php if($options=='ur'){echo "selected";}?>>Urdu</option>
				  <option value="vi" <?php if($options=='vi'){echo "selected";}?>>Vietnamese</option>
				  <option value="cy" <?php if($options=='cy'){echo "selected";}?>>Welsh</option>
				  <option value="yi" <?php if($options=='yi'){echo "selected";}?>>Yiddish</option>
		          <option value="cy" <?php if($options=='cy'){echo "selected";}?>>Yoruba</option>
				  <option value="yi" <?php if($options=='yi'){echo "selected";}?>>Zulu</option>		
         
         </select>
    <?php     
    } 
  
    public function googlelanguagetranslator_language_option_cb() {
	
	$option_name = 'googlelanguagetranslator_language_option' ;
    $new_value = 'all';

      if ( get_option( $option_name ) === false ) {

      // The option does not exist, so we update it.
      update_option( $option_name, $new_value );
	  } 
	  
	  $options = get_option (''.$option_name.''); ?>

    <input type="radio" name="googlelanguagetranslator_language_option" id="googlelanguagetranslator_language_option" value="all" <?php if($options=='all'){echo "checked";}?>/> All Languages<br/>
	<input type="radio" name="googlelanguagetranslator_language_option" id="googlelanguagetranslator_language_option" value="specific" <?php if($options=='specific'){echo "checked";}?>/> Specific Languages
    <?php 
	}
  
    public function language_display_settings_cb() {
	$defaults = array (
	  'en' => 1
	);
	
	$option_name = 'language_display_settings' ;
    $new_value = $defaults;

      if ( get_option( $option_name ) === false ) {

      // The option does not exist, so we update it.
      update_option( $option_name, $new_value );
	  } 
	  
	  $get_language_choices = get_option (''.$option_name.''); 

      if (!isset ( $get_language_choices ['af'] ) ) {
	    $get_language_choices['af'] = 0;
	  }
	  
	   if (!isset ( $get_language_choices ['sq'] ) ) {
	    $get_language_choices['sq'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['ar'] ) ) {
	    $get_language_choices['ar'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['hy'] ) ) {
	    $get_language_choices['hy'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['az'] ) ) {
	    $get_language_choices['az'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['eu'] ) ) {	
	    $get_language_choices['eu'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['be'] ) ) {
	    $get_language_choices['be'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['bn'] ) ) {
		$get_language_choices['bn'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['bs'] ) ) {
		 $get_language_choices['bs'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['bg'] ) ) { 
		 $get_language_choices['bg'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['ca'] ) ) { 
	     $get_language_choices['ca'] = 0;	 
	   }
	  
	   if (!isset ( $get_language_choices ['ceb'] ) ) {
		 $get_language_choices['ceb'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['zh-CN'] ) ) {
		 $get_language_choices['zh-CN'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['zh-TW'] ) ) {
	     $get_language_choices['zh-TW'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['hr'] ) ) {
	     $get_language_choices['hr'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['cs'] ) ) {
	     $get_language_choices['cs'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['da'] ) ) {
	     $get_language_choices['da'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['nl'] ) ) {
	     $get_language_choices['nl'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['en'] ) ) {
	   $get_language_choices['en'] = 1; 
	   }
	  
	   if (!isset ( $get_language_choices ['eo'] ) ) {
	     $get_language_choices['eo'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['et'] ) ) {
	     $get_language_choices['et'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['tl'] ) ) {
	     $get_language_choices['tl'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['fi'] ) ) {
	     $get_language_choices['fi'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['fr'] ) ) {
	     $get_language_choices['fr'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['gl'] ) ) {
	     $get_language_choices['gl'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['ka'] ) ) {
	     $get_language_choices['ka'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['de'] ) ) {
	     $get_language_choices['de'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['el'] ) ) {
	     $get_language_choices['el'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['gu'] ) ) {
	     $get_language_choices['gu'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['ht'] ) ) {
	     $get_language_choices['ht'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['ha'] ) ) {
	     $get_language_choices['ha'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['iw'] ) ) {
	     $get_language_choices['iw'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['hi'] ) ) {
	     $get_language_choices['hi'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['hmn'] ) ) {
	     $get_language_choices['hmn'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['hu'] ) ) {
	     $get_language_choices['hu'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['is'] ) ) {
	     $get_language_choices['is'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['ig'] ) ) {
	     $get_language_choices['ig'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['id'] ) ) {
	     $get_language_choices['id'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['ga'] ) ) {
	     $get_language_choices['ga'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['it'] ) ) {
	     $get_language_choices['it'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['ja'] ) ) {
	     $get_language_choices['ja'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['jw'] ) ) {
	     $get_language_choices['jw'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['kn'] ) ) {
	     $get_language_choices['kn'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['km'] ) ) {
	     $get_language_choices['km'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['ko'] ) ) {
	     $get_language_choices['ko'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['lo'] ) ) {
	     $get_language_choices['lo'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['la'] ) ) {
	     $get_language_choices['la'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['lv'] ) ) {
	     $get_language_choices['lv'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['lt'] ) ) {
	     $get_language_choices['lt'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['mk'] ) ) {
	     $get_language_choices['mk'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['ms'] ) ) {
	     $get_language_choices['ms'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['mt'] ) ) {
	     $get_language_choices['mt'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['mi'] ) ) {
	     $get_language_choices['mi'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['mr'] ) ) {
	     $get_language_choices['mr'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['mn'] ) ) {
	     $get_language_choices['mn'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['ne'] ) ) {
	     $get_language_choices['ne'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['no'] ) ) {
	     $get_language_choices['no'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['fa'] ) ) {
	     $get_language_choices['fa'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['pl'] ) ) {
	     $get_language_choices['pl'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['pt'] ) ) {
	     $get_language_choices['pt'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['pa'] ) ) {
	     $get_language_choices['pa'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['ro'] ) ) {
	     $get_language_choices['ro'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['ru'] ) ) {
	     $get_language_choices['ru'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['sr'] ) ) {
	     $get_language_choices['sr'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['sk'] ) ) {
	     $get_language_choices['sk'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['sl'] ) ) {
	     $get_language_choices['sl'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['so'] ) ) {
	     $get_language_choices['so'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['es'] ) ) {
	     $get_language_choices['es'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['sw'] ) ) {
	     $get_language_choices['sw'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['sv'] ) ) {
	     $get_language_choices['sv'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['ta'] ) ) {
	     $get_language_choices['ta'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['te'] ) ) {
	     $get_language_choices['te'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['th'] ) ) {
	     $get_language_choices['th'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['tr'] ) ) {
	     $get_language_choices['tr'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['uk'] ) ) {
	     $get_language_choices['uk'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['ur'] ) ) {
	     $get_language_choices['ur'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['vi'] ) ) {
	     $get_language_choices['vi'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['cy'] ) ) {
	     $get_language_choices['cy'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['yi'] ) ) {
	     $get_language_choices['yi'] = 0;
	   } 

       if (!isset ( $get_language_choices ['yo'] ) ) {
	     $get_language_choices['yo'] = 0;
	   }
	  
	   if (!isset ( $get_language_choices ['zu'] ) ) {
	     $get_language_choices['zu'] = 0;
	   } ?>
	
	            <div class="languages" style="width:25%; float:left">
					<div><input type="checkbox" name="language_display_settings[af]" value="1" <?php if ( 1 == $get_language_choices['af'] ) echo 'checked="checked"'; ?>/> Afrikaans</div>
				    <div><input type="checkbox" name="language_display_settings[sq]" value="1" <?php if ( 1 == $get_language_choices['sq'] ) echo 'checked="checked"'; ?>/> Albanian</div>
					<div><input type="checkbox" name="language_display_settings[ar]" value="1" <?php if ( 1 == $get_language_choices['ar'] ) echo 'checked="checked"'; ?>/> Arabic</div>
                    <div><input type="checkbox" name="language_display_settings[hy]" value="1" <?php if ( 1 == $get_language_choices['hy'] ) echo 'checked="checked"'; ?>/> Armenian</div>
                    <div><input type="checkbox" name="language_display_settings[az]" value="1" <?php if ( 1 == $get_language_choices['az'] ) echo 'checked="checked"'; ?>/> Azerbaijani</div>                  
                    <div><input type="checkbox" name="language_display_settings[eu]" value="1" <?php if ( 1 == $get_language_choices['eu'] ) echo 'checked="checked"'; ?>/> Basque</div>                    
                    <div><input type="checkbox" name="language_display_settings[be]" value="1" <?php if ( 1 == $get_language_choices['be'] ) echo 'checked="checked"'; ?>/> Belarusian</div>                    
                    <div><input type="checkbox" name="language_display_settings[bn]" value="1" <?php if ( 1 == $get_language_choices['bn'] ) echo 'checked="checked"'; ?>/> Bengali</div> 
					<div><input type="checkbox" name="language_display_settings[bs]" value="1" <?php if ( 1 == $get_language_choices['bs'] ) echo 'checked="checked"'; ?>/> Bosnian</div> 
                    <div><input type="checkbox" name="language_display_settings[bg]" value="1" <?php if ( 1 == $get_language_choices['bg'] ) echo 'checked="checked"'; ?>/> Bulgarian</div>                    
                    <div><input type="checkbox" name="language_display_settings[ca]" value="1" <?php if ( 1 == $get_language_choices['ca'] ) echo 'checked="checked"'; ?>/> Catalan</div> 
					<div><input type="checkbox" name="language_display_settings[ceb]" value="1" <?php if ( 1 == $get_language_choices['ceb'] ) echo 'checked="checked"'; ?>/> Cebuano</div>
                    <div><input type="checkbox" name="language_display_settings[zh-CN]" value="1" <?php if ( 1 == $get_language_choices['zh-CN'] ) echo 'checked="checked"'; ?>/> Chinese</div>                    
                    <div><input type="checkbox" name="language_display_settings[zh-TW]" value="1" <?php if ( 1 == $get_language_choices['zh-TW'] ) echo 'checked="checked"'; ?>/> Chinese (Han)</div>                    
                    <div><input type="checkbox" name="language_display_settings[hr]" value="1" <?php if ( 1 == $get_language_choices['hr'] ) echo 'checked="checked"'; ?>/> Croatian</div>                    
                    <div><input type="checkbox" name="language_display_settings[cs]" value="1" <?php if ( 1 == $get_language_choices['cs'] ) echo 'checked="checked"'; ?>/> Czech</div>                    			
                    <div><input type="checkbox" name="language_display_settings[da]" value="1" <?php if ( 1 == $get_language_choices['da'] ) echo 'checked="checked"'; ?>/> Danish</div>                    
                    <div><input type="checkbox" name="language_display_settings[nl]" value="1" <?php if ( 1 == $get_language_choices['nl'] ) echo 'checked="checked"'; ?>/> Dutch</div>                    				
                    <div><input type="checkbox" name="language_display_settings[en]" value="1" <?php if ( 1 == $get_language_choices['en'] ) echo 'checked="checked"'; ?>/> English</div>
				    <div><input type="checkbox" name="language_display_settings[eo]" value="1" <?php if ( 1 == $get_language_choices['eo'] ) echo 'checked="checked"'; ?>/> Esperanto</div>
				    <div><input type="checkbox" name="language_display_settings[et]" value="1" <?php if ( 1 == $get_language_choices['et'] ) echo 'checked="checked"'; ?>/> Estonian</div>                   
				</div>
				  
				  <div class="languages" style="width:25%; float:left">
                    <div><input type="checkbox" name="language_display_settings[tl]" value="1" <?php if ( 1 == $get_language_choices['tl'] ) echo 'checked="checked"'; ?>/> Filipino</div>                     
                    <div><input type="checkbox" name="language_display_settings[fi]" value="1" <?php if ( 1 == $get_language_choices['fi'] ) echo 'checked="checked"'; ?>/> Finnish</div>                    
                    <div><input type="checkbox" name="language_display_settings[fr]" value="1" <?php if ( 1 == $get_language_choices['fr'] ) echo 'checked="checked"'; ?>/> French</div>                     
                    <div><input type="checkbox" name="language_display_settings[gl]" value="1" <?php if ( 1 == $get_language_choices['gl'] ) echo 'checked="checked"'; ?>/> Galician</div>                    
                    <div><input type="checkbox" name="language_display_settings[ka]" value="1" <?php if ( 1 == $get_language_choices['ka'] ) echo 'checked="checked"'; ?>/> Georgian</div>                    
                    <div><input type="checkbox" name="language_display_settings[de]" value="1" <?php if ( 1 == $get_language_choices['de'] ) echo 'checked="checked"'; ?>/> German</div>                  
                    <div><input type="checkbox" name="language_display_settings[el]" value="1" <?php if ( 1 == $get_language_choices['el'] ) echo 'checked="checked"'; ?>/> Greek</div>                    
                    <div><input type="checkbox" name="language_display_settings[gu]" value="1" <?php if ( 1 == $get_language_choices['gu'] ) echo 'checked="checked"'; ?>/> Gujarati</div>       
                    <div><input type="checkbox" name="language_display_settings[ht]" value="1" <?php if ( 1 == $get_language_choices['ht'] ) echo 'checked="checked"'; ?>/> Haitian Creole</div>                     			
                    <div><input type="checkbox" name="language_display_settings[ha]" value="1" <?php if ( 1 == $get_language_choices['ha'] ) echo 'checked="checked"'; ?>/> Hausa</div>                     			
					<div><input type="checkbox" name="language_display_settings[iw]" value="1" <?php if ( 1 == $get_language_choices['iw'] ) echo 'checked="checked"'; ?>/> Hebrew</div>                  
                    <div><input type="checkbox" name="language_display_settings[hi]" value="1" <?php if ( 1 == $get_language_choices['hi'] ) echo 'checked="checked"'; ?>/> Hindi</div>    
					<div><input type="checkbox" name="language_display_settings[hmn]" value="1" <?php if ( 1 == $get_language_choices['hmn'] ) echo 'checked="checked"'; ?>/> Hmong</div>
                    <div><input type="checkbox" name="language_display_settings[hu]" value="1" <?php if ( 1 == $get_language_choices['hu'] ) echo 'checked="checked"'; ?>/> Hungarian</div>               
                    <div><input type="checkbox" name="language_display_settings[is]" value="1" <?php if ( 1 == $get_language_choices['is'] ) echo 'checked="checked"'; ?>/> Icelandic</div>
					<div><input type="checkbox" name="language_display_settings[ig]" value="1" <?php if ( 1 == $get_language_choices['ig'] ) echo 'checked="checked"'; ?>/> Igbo</div>                 
                    <div><input type="checkbox" name="language_display_settings[id]" value="1" <?php if ( 1 == $get_language_choices['id'] ) echo 'checked="checked"'; ?>/> Indonesian</div>                   
                    <div><input type="checkbox" name="language_display_settings[ga]" value="1" <?php if ( 1 == $get_language_choices['ga'] ) echo 'checked="checked"'; ?>/> Irish</div>  
					<div><input type="checkbox" name="language_display_settings[it]" value="1" <?php if ( 1 == $get_language_choices['it'] ) echo 'checked="checked"'; ?>/> Italian</div>
					<div><input type="checkbox" name="language_display_settings[ja]" value="1" <?php if ( 1 == $get_language_choices['ja'] ) echo 'checked="checked"'; ?>/> Japanese</div>
					<div><input type="checkbox" name="language_display_settings[jw]" value="1" <?php if ( 1 == $get_language_choices['jw'] ) echo 'checked="checked"'; ?>/> Javanese</div>
				</div>
				  
				  <div class="languages" style="width:25%; float:left">   
                    <div><input type="checkbox" name="language_display_settings[kn]" value="1" <?php if ( 1 == $get_language_choices['kn'] ) echo 'checked="checked"'; ?>/> Kannada</div> 
					<div><input type="checkbox" name="language_display_settings[km]" value="1" <?php if ( 1 == $get_language_choices['km'] ) echo 'checked="checked"'; ?>/> Khmer</div>
                    <div><input type="checkbox" name="language_display_settings[ko]" value="1" <?php if ( 1 == $get_language_choices['ko'] ) echo 'checked="checked"'; ?>/> Korean</div>                    
                    <div><input type="checkbox" name="language_display_settings[lo]" value="1" <?php if ( 1 == $get_language_choices['lo'] ) echo 'checked="checked"'; ?>/> Lao</div>                     
                    <div><input type="checkbox" name="language_display_settings[la]" value="1" <?php if ( 1 == $get_language_choices['la'] ) echo 'checked="checked"'; ?>/> Latin</div>                    
                    <div><input type="checkbox" name="language_display_settings[lv]" value="1" <?php if ( 1 == $get_language_choices['lv'] ) echo 'checked="checked"'; ?>/> Latvian</div>                    
                    <div><input type="checkbox" name="language_display_settings[lt]" value="1" <?php if ( 1 == $get_language_choices['lt'] ) echo 'checked="checked"'; ?>/> Lithuanian</div>                  
                    <div><input type="checkbox" name="language_display_settings[mk]" value="1" <?php if ( 1 == $get_language_choices['mk'] ) echo 'checked="checked"'; ?>/> Macedonian</div>                    
                    <div><input type="checkbox" name="language_display_settings[ms]" value="1" <?php if ( 1 == $get_language_choices['ms'] ) echo 'checked="checked"'; ?>/> Malay</div>       
                    <div><input type="checkbox" name="language_display_settings[mt]" value="1" <?php if ( 1 == $get_language_choices['mt'] ) echo 'checked="checked"'; ?>/> Maltese</div>
					<div><input type="checkbox" name="language_display_settings[mi]" value="1" <?php if ( 1 == $get_language_choices['mi'] ) echo 'checked="checked"'; ?>/> Maori</div>
					<div><input type="checkbox" name="language_display_settings[mr]" value="1" <?php if ( 1 == $get_language_choices['mr'] ) echo 'checked="checked"'; ?>/> Marathi</div>
					<div><input type="checkbox" name="language_display_settings[mn]" value="1" <?php if ( 1 == $get_language_choices['mn'] ) echo 'checked="checked"'; ?>/> Mongolian</div>
					<div><input type="checkbox" name="language_display_settings[ne]" value="1" <?php if ( 1 == $get_language_choices['ne'] ) echo 'checked="checked"'; ?>/> Nepali</div>   
                    <div><input type="checkbox" name="language_display_settings[no]" value="1" <?php if ( 1 == $get_language_choices['no'] ) echo 'checked="checked"'; ?>/> Norwegian</div>                  
                    <div><input type="checkbox" name="language_display_settings[fa]" value="1" <?php if ( 1 == $get_language_choices['fa'] ) echo 'checked="checked"'; ?>/> Persian</div>          
                    <div><input type="checkbox" name="language_display_settings[pl]" value="1" <?php if ( 1 == $get_language_choices['pl'] ) echo 'checked="checked"'; ?>/> Polish</div>               
                    <div><input type="checkbox" name="language_display_settings[pt]" value="1" <?php if ( 1 == $get_language_choices['pt'] ) echo 'checked="checked"'; ?>/> Portuguese</div> 
					<div><input type="checkbox" name="language_display_settings[pa]" value="1" <?php if ( 1 == $get_language_choices['pa'] ) echo 'checked="checked"'; ?>/> Punjabi</div>                  
                    <div><input type="checkbox" name="language_display_settings[ro]" value="1" <?php if ( 1 == $get_language_choices['ro'] ) echo 'checked="checked"'; ?>/> Romanian</div>                   
                    <div><input type="checkbox" name="language_display_settings[ru]" value="1" <?php if ( 1 == $get_language_choices['ru'] ) echo 'checked="checked"'; ?>/> Russian</div>  
				</div>
				  
				  <div class="languages" style="width:25%; float:left">
 					<div><input type="checkbox" name="language_display_settings[sr]" value="1" <?php if ( 1 == $get_language_choices['sr'] ) echo 'checked="checked"'; ?>/> Serbian</div>                      
                    <div><input type="checkbox" name="language_display_settings[sk]" value="1" <?php if ( 1 == $get_language_choices['sk'] ) echo 'checked="checked"'; ?>/> Slovak</div>                   
                    <div><input type="checkbox" name="language_display_settings[sl]" value="1" <?php if ( 1 == $get_language_choices['sl'] ) echo 'checked="checked"'; ?>/> Slovenian</div> 
					<div><input type="checkbox" name="language_display_settings[so]" value="1" <?php if ( 1 == $get_language_choices['so'] ) echo 'checked="checked"'; ?>/> Somali</div>                     
                    <div><input type="checkbox" name="language_display_settings[es]" value="1" <?php if ( 1 == $get_language_choices['es'] ) echo 'checked="checked"'; ?>/> Spanish</div>                    
                    <div><input type="checkbox" name="language_display_settings[sw]" value="1" <?php if ( 1 == $get_language_choices['sw'] ) echo 'checked="checked"'; ?>/> Swahili</div>                     
                    <div><input type="checkbox" name="language_display_settings[sv]" value="1" <?php if ( 1 == $get_language_choices['sv'] ) echo 'checked="checked"'; ?>/> Swedish</div>                    
                    <div><input type="checkbox" name="language_display_settings[ta]" value="1" <?php if ( 1 == $get_language_choices['ta'] ) echo 'checked="checked"'; ?>/> Tamil</div>                    
                    <div><input type="checkbox" name="language_display_settings[te]" value="1" <?php if ( 1 == $get_language_choices['te'] ) echo 'checked="checked"'; ?>/> Telugu</div>                  
                    <div><input type="checkbox" name="language_display_settings[th]" value="1" <?php if ( 1 == $get_language_choices['th'] ) echo 'checked="checked"'; ?>/> Thai</div>                    
                    <div><input type="checkbox" name="language_display_settings[tr]" value="1" <?php if ( 1 == $get_language_choices['tr'] ) echo 'checked="checked"'; ?>/> Turkish</div>       
                    <div><input type="checkbox" name="language_display_settings[uk]" value="1" <?php if ( 1 == $get_language_choices['uk'] ) echo 'checked="checked"'; ?>/> Ukranian</div>                     			
                    <div><input type="checkbox" name="language_display_settings[ur]" value="1" <?php if ( 1 == $get_language_choices['ur'] ) echo 'checked="checked"'; ?>/> Urdu</div>                  
                    <div><input type="checkbox" name="language_display_settings[vi]" value="1" <?php if ( 1 == $get_language_choices['vi'] ) echo 'checked="checked"'; ?>/> Vietnamese</div>          
                    <div><input type="checkbox" name="language_display_settings[cy]" value="1" <?php if ( 1 == $get_language_choices['cy'] ) echo 'checked="checked"'; ?>/> Welsh</div>               
                    <div><input type="checkbox" name="language_display_settings[yi]" value="1" <?php if ( 1 == $get_language_choices['yi'] ) echo 'checked="checked"'; ?>/> Yiddish</div> 
					<div><input type="checkbox" name="language_display_settings[yo]" value="1" <?php if ( 1 == $get_language_choices['yo'] ) echo 'checked="checked"'; ?>/> Yoruba</div>               
                    <div><input type="checkbox" name="language_display_settings[zu]" value="1" <?php if ( 1 == $get_language_choices['zu'] ) echo 'checked="checked"'; ?>/> Zulu</div>                 
				  
				  </div>
			
			<div style="clear:both"></div>
    <?php } 
  
    public function googlelanguagetranslator_flags_cb() { 
	
	  $option_name = 'googlelanguagetranslator_flags' ;
      $new_value = 'show_flags';

      if ( get_option( $option_name ) === false ) {

      // The option does not exist, so we update it.
      update_option( $option_name, $new_value );
	  } 
	  
	  $options = get_option (''.$option_name.''); ?>

      <input type="radio" name="googlelanguagetranslator_flags" id="googlelanguagetranslator_flags" value="show_flags" <?php if($options=='show_flags'){echo "checked";}?>/> Yes, show flag images<br/>
	  <input type="radio" name="googlelanguagetranslator_flags" id="googlelanguagetranslator_flags" value="hide_flags" <?php if($options=='hide_flags'){echo "checked";}?>/> No, hide flag images
    <?php 
	}  

    public function flag_display_settings_cb() {
	  $defaults = array(
        'flag-en' => 1
      );
	  
	  $option_name = 'flag_display_settings' ;
      $new_value = $defaults;

      if ( get_option( $option_name ) === false ) {

      // The option does not exist, so we update it.
      update_option( $option_name, $new_value );
	  } 
	  
	  $get_flag_choices = get_option (''.$option_name.'');
	  
	  if (!isset ( $get_flag_choices ['flag-af'] ) ) {
	    $get_flag_choices['flag-af'] = 0;
	  }
	  
	   if (!isset ( $get_flag_choices ['flag-sq'] ) ) {
	    $get_flag_choices['flag-sq'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-ar'] ) ) {
	    $get_flag_choices['flag-ar'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-hy'] ) ) {
	    $get_flag_choices['flag-hy'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-az'] ) ) {
	    $get_flag_choices['flag-az'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-eu'] ) ) {	
	    $get_flag_choices['flag-eu'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-be'] ) ) {
	    $get_flag_choices['flag-be'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-bn'] ) ) {
		$get_flag_choices['flag-bn'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-bs'] ) ) {
		 $get_flag_choices['flag-bs'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-bg'] ) ) { 
		 $get_flag_choices['flag-bg'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-ca'] ) ) { 
	     $get_flag_choices['flag-ca'] = 0;	 
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-ceb'] ) ) {
		 $get_flag_choices['flag-ceb'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-zh-CN'] ) ) {
		 $get_flag_choices['flag-zh-CN'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-zh-TW'] ) ) {
	     $get_flag_choices['flag-zh-TW'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-hr'] ) ) {
	     $get_flag_choices['flag-hr'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-cs'] ) ) {
	     $get_flag_choices['flag-cs'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-da'] ) ) {
	     $get_flag_choices['flag-da'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-nl'] ) ) {
	     $get_flag_choices['flag-nl'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-en'] ) ) {
	   $get_flag_choices['flag-en'] = 1; 
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-eo'] ) ) {
	     $get_flag_choices['flag-eo'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-et'] ) ) {
	     $get_flag_choices['flag-et'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-tl'] ) ) {
	     $get_flag_choices['flag-tl'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-fi'] ) ) {
	     $get_flag_choices['flag-fi'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-fr'] ) ) {
	     $get_flag_choices['flag-fr'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-gl'] ) ) {
	     $get_flag_choices['flag-gl'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-ka'] ) ) {
	     $get_flag_choices['flag-ka'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-de'] ) ) {
	     $get_flag_choices['flag-de'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-el'] ) ) {
	     $get_flag_choices['flag-el'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-gu'] ) ) {
	     $get_flag_choices['flag-gu'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-ht'] ) ) {
	     $get_flag_choices['flag-ht'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-ha'] ) ) {
	     $get_flag_choices['flag-ha'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-iw'] ) ) {
	     $get_flag_choices['flag-iw'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-hi'] ) ) {
	     $get_flag_choices['flag-hi'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-hmn'] ) ) {
	     $get_flag_choices['flag-hmn'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-hu'] ) ) {
	     $get_flag_choices['flag-hu'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-is'] ) ) {
	     $get_flag_choices['flag-is'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-ig'] ) ) {
	     $get_flag_choices['flag-ig'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-id'] ) ) {
	     $get_flag_choices['flag-id'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-ga'] ) ) {
	     $get_flag_choices['flag-ga'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-it'] ) ) {
	     $get_flag_choices['flag-it'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-ja'] ) ) {
	     $get_flag_choices['flag-ja'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-jw'] ) ) {
	     $get_flag_choices['flag-jw'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-kn'] ) ) {
	     $get_flag_choices['flag-kn'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-km'] ) ) {
	     $get_flag_choices['flag-km'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-ko'] ) ) {
	     $get_flag_choices['flag-ko'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-lo'] ) ) {
	     $get_flag_choices['flag-lo'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-la'] ) ) {
	     $get_flag_choices['flag-la'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-lv'] ) ) {
	     $get_flag_choices['flag-lv'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-lt'] ) ) {
	     $get_flag_choices['flag-lt'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-mk'] ) ) {
	     $get_flag_choices['flag-mk'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-ms'] ) ) {
	     $get_flag_choices['flag-ms'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-mt'] ) ) {
	     $get_flag_choices['flag-mt'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-mi'] ) ) {
	     $get_flag_choices['flag-mi'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-mr'] ) ) {
	     $get_flag_choices['flag-mr'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-mn'] ) ) {
	     $get_flag_choices['flag-mn'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-ne'] ) ) {
	     $get_flag_choices['flag-ne'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-no'] ) ) {
	     $get_flag_choices['flag-no'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-fa'] ) ) {
	     $get_flag_choices['flag-fa'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-pl'] ) ) {
	     $get_flag_choices['flag-pl'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-pt'] ) ) {
	     $get_flag_choices['flag-pt'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-pa'] ) ) {
	     $get_flag_choices['flag-pa'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-ro'] ) ) {
	     $get_flag_choices['flag-ro'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-ru'] ) ) {
	     $get_flag_choices['flag-ru'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-sr'] ) ) {
	     $get_flag_choices['flag-sr'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-sk'] ) ) {
	     $get_flag_choices['flag-sk'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-sl'] ) ) {
	     $get_flag_choices['flag-sl'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-so'] ) ) {
	     $get_flag_choices['flag-so'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-so'] ) ) {
	     $get_flag_choices['flag-so'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-es'] ) ) {
	     $get_flag_choices['flag-es'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-sw'] ) ) {
	     $get_flag_choices['flag-sw'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-sv'] ) ) {
	     $get_flag_choices['flag-sv'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-ta'] ) ) {
	     $get_flag_choices['flag-ta'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-te'] ) ) {
	     $get_flag_choices['flag-te'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-th'] ) ) {
	     $get_flag_choices['flag-th'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-tr'] ) ) {
	     $get_flag_choices['flag-tr'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-uk'] ) ) {
	     $get_flag_choices['flag-uk'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-ur'] ) ) {
	     $get_flag_choices['flag-ur'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-vi'] ) ) {
	     $get_flag_choices['flag-vi'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-cy'] ) ) {
	     $get_flag_choices['flag-cy'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-yi'] ) ) {
	     $get_flag_choices['flag-yi'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-yo'] ) ) {
	     $get_flag_choices['flag-yo'] = 0;
	   }
	  
	   if (!isset ( $get_flag_choices ['flag-zu'] ) ) {
	     $get_flag_choices['flag-zu'] = 0;
	   }
	  
	 ?>
                  <div class="flagdisplay" style="width:25%; float:left">
					<div><input type="checkbox" name="flag_display_settings[flag-af]" value="1" <?php checked( 1,$get_flag_choices['flag-af']); ?>/> Afrikaans</div>
					<div><input type="checkbox" name="flag_display_settings[flag-sq]" value="1" <?php checked( 1,$get_flag_choices['flag-sq']); ?>/> Albanian</div>
					<div><input type="checkbox" name="flag_display_settings[flag-ar]" value="1" <?php checked( 1,$get_flag_choices['flag-ar']); ?>/> Arabic</div>
                    <div><input type="checkbox" name="flag_display_settings[flag-hy]" value="1" <?php checked( 1,$get_flag_choices['flag-hy']); ?>/> Armenian</div>
                    <div><input type="checkbox" name="flag_display_settings[flag-az]" value="1" <?php checked( 1,$get_flag_choices['flag-az']); ?>/> Azerbaijani</div>                  
                    <div><input type="checkbox" name="flag_display_settings[flag-eu]" value="1" <?php checked( 1,$get_flag_choices['flag-eu']); ?>/> Basque</div>                    
                    <div><input type="checkbox" name="flag_display_settings[flag-be]" value="1" <?php checked( 1,$get_flag_choices['flag-be']); ?>/> Belarusian</div>                    
                    <div><input type="checkbox" name="flag_display_settings[flag-bn]" value="1" <?php checked( 1,$get_flag_choices['flag-bn']); ?>/> Bengali</div> 
					<div><input type="checkbox" name="flag_display_settings[flag-bs]" value="1" <?php checked( 1,$get_flag_choices['flag-bs']); ?>/> Bosnian</div> 
                    <div><input type="checkbox" name="flag_display_settings[flag-bg]" value="1" <?php checked( 1,$get_flag_choices['flag-bg']); ?>/> Bulgarian</div>                    
                    <div><input type="checkbox" name="flag_display_settings[flag-ca]" value="1" <?php checked( 1,$get_flag_choices['flag-ca']); ?>/> Catalan</div> 
					<div><input type="checkbox" name="flag_display_settings[flag-ceb]" value="1" <?php checked( 1,$get_flag_choices['flag-ceb']); ?>/> Cebuano</div>
                    <div><input type="checkbox" name="flag_display_settings[flag-zh-CN]" value="1" <?php checked( 1,$get_flag_choices['flag-zh-CN']); ?>/> Chinese</div>                    
                    <div><input type="checkbox" name="flag_display_settings[flag-zh-TW]" value="1" <?php checked( 1,$get_flag_choices['flag-zh-TW']); ?>/> Chinese (Han)</div>                    
                    <div><input type="checkbox" name="flag_display_settings[flag-hr]" value="1" <?php checked( 1,$get_flag_choices['flag-hr']); ?>/> Croatian</div>                    
                    <div><input type="checkbox" name="flag_display_settings[flag-cs]" value="1" <?php checked( 1,$get_flag_choices['flag-cs']); ?>/> Czech</div>                    			
                    <div><input type="checkbox" name="flag_display_settings[flag-da]" value="1" <?php checked( 1,$get_flag_choices['flag-da']); ?>/> Danish</div>                    
                    <div><input type="checkbox" name="flag_display_settings[flag-nl]" value="1" <?php checked( 1,$get_flag_choices['flag-nl']); ?>/> Dutch</div>                    				
                    <div><input type="checkbox" name="flag_display_settings[flag-en]" value="1" <?php checked(1,$get_flag_choices['flag-en']); ?>/> English</div>
					<div><input type="checkbox" name="flag_display_settings[flag-eo]" value="1" <?php checked( 1,$get_flag_choices['flag-eo']); ?>/> Esperanto</div>                      
                    <div><input type="checkbox" name="flag_display_settings[flag-et]" value="1" <?php checked( 1,$get_flag_choices['flag-et']); ?>/> Estonian</div>                   
				</div>
				  
				  <div class="flagdisplay" style="width:25%; float:left">
                    <div><input type="checkbox" name="flag_display_settings[flag-tl]" value="1" <?php checked( 1,$get_flag_choices['flag-tl']); ?>/> Filipino</div>                     
                    <div><input type="checkbox" name="flag_display_settings[flag-fi]" value="1" <?php checked( 1,$get_flag_choices['flag-fi']); ?>/> Finnish</div>                    
                    <div><input type="checkbox" name="flag_display_settings[flag-fr]" value="1" <?php checked( 1,$get_flag_choices['flag-fr']); ?>/> French</div>                     
                    <div><input type="checkbox" name="flag_display_settings[flag-gl]" value="1" <?php checked( 1,$get_flag_choices['flag-gl']); ?>/> Galician</div>                    
                    <div><input type="checkbox" name="flag_display_settings[flag-ka]" value="1" <?php checked( 1,$get_flag_choices['flag-ka']); ?>/> Georgian</div>                    
                    <div><input type="checkbox" name="flag_display_settings[flag-de]" value="1" <?php checked( 1,$get_flag_choices['flag-de']); ?>/> German</div>                  
                    <div><input type="checkbox" name="flag_display_settings[flag-el]" value="1" <?php checked( 1,$get_flag_choices['flag-el']); ?>/> Greek</div>                    
                    <div><input type="checkbox" name="flag_display_settings[flag-gu]" value="1" <?php checked( 1,$get_flag_choices['flag-gu']); ?>/> Gujarati</div>       
                    <div><input type="checkbox" name="flag_display_settings[flag-ht]" value="1" <?php checked( 1,$get_flag_choices['flag-ht']); ?>/> Haitian Creole</div>
					<div><input type="checkbox" name="flag_display_settings[flag-ha]" value="1" <?php checked( 1,$get_flag_choices['flag-ha']); ?>/> Hausa</div>
                    <div><input type="checkbox" name="flag_display_settings[flag-iw]" value="1" <?php checked( 1,$get_flag_choices['flag-iw']); ?>/> Hebrew</div>                  
                    <div><input type="checkbox" name="flag_display_settings[flag-hi]" value="1" <?php checked( 1,$get_flag_choices['flag-hi']); ?>/> Hindi</div>    
					<div><input type="checkbox" name="flag_display_settings[flag-hmn]" value="1" <?php checked( 1,$get_flag_choices['flag-hmn']); ?>/> Hmong</div>
                    <div><input type="checkbox" name="flag_display_settings[flag-hu]" value="1" <?php checked( 1,$get_flag_choices['flag-hu']); ?>/> Hungarian</div>               
                    <div><input type="checkbox" name="flag_display_settings[flag-is]" value="1" <?php checked( 1,$get_flag_choices['flag-is']); ?>/> Icelandic</div> 
					<div><input type="checkbox" name="flag_display_settings[flag-ig]" value="1" <?php checked( 1,$get_flag_choices['flag-ig']); ?>/> Igbo</div>                 
                    <div><input type="checkbox" name="flag_display_settings[flag-id]" value="1" <?php checked( 1,$get_flag_choices['flag-id']); ?>/> Indonesian</div>                   
                    <div><input type="checkbox" name="flag_display_settings[flag-ga]" value="1" <?php checked( 1,$get_flag_choices['flag-ga']); ?>/> Irish</div>  
					<div><input type="checkbox" name="flag_display_settings[flag-it]" value="1" <?php checked( 1,$get_flag_choices['flag-it']); ?>/> Italian</div>
					<div><input type="checkbox" name="flag_display_settings[flag-ja]" value="1" <?php checked( 1,$get_flag_choices['flag-ja']); ?>/> Japanese</div>   
					<div><input type="checkbox" name="flag_display_settings[flag-jw]" value="1" <?php checked( 1,$get_flag_choices['flag-jw']); ?>/> Javanese</div>
				</div>
				  
				  <div class="flagdisplay" style="width:25%; float:left">   
                    <div><input type="checkbox" name="flag_display_settings[flag-kn]" value="1" <?php checked( 1,$get_flag_choices['flag-kn']); ?>/> Kannada</div> 
					<div><input type="checkbox" name="flag_display_settings[flag-km]" value="1" <?php checked( 1,$get_flag_choices['flag-km']); ?>/> Khmer</div>
                    <div><input type="checkbox" name="flag_display_settings[flag-ko]" value="1" <?php checked( 1,$get_flag_choices['flag-ko']); ?>/> Korean</div>                    
                    <div><input type="checkbox" name="flag_display_settings[flag-lo]" value="1" <?php checked( 1,$get_flag_choices['flag-lo']); ?>/> Lao</div>                     
                    <div><input type="checkbox" name="flag_display_settings[flag-la]" value="1" <?php checked( 1,$get_flag_choices['flag-la']); ?>/> Latin</div>                    
                    <div><input type="checkbox" name="flag_display_settings[flag-lv]" value="1" <?php checked( 1,$get_flag_choices['flag-lv']); ?>/> Latvian</div>                    
                    <div><input type="checkbox" name="flag_display_settings[flag-lt]" value="1" <?php checked( 1,$get_flag_choices['flag-lt']); ?>/> Lithuanian</div>                  
                    <div><input type="checkbox" name="flag_display_settings[flag-mk]" value="1" <?php checked( 1,$get_flag_choices['flag-mk']); ?>/> Macedonian</div>                    
                    <div><input type="checkbox" name="flag_display_settings[flag-ms]" value="1" <?php checked( 1,$get_flag_choices['flag-ms']); ?>/> Malay</div>       
                    <div><input type="checkbox" name="flag_display_settings[flag-mt]" value="1" <?php checked( 1,$get_flag_choices['flag-mt']); ?>/> Maltese</div>
					<div><input type="checkbox" name="flag_display_settings[flag-mi]" value="1" <?php checked( 1,$get_flag_choices['flag-mi']); ?>/> Maori</div>
					<div><input type="checkbox" name="flag_display_settings[flag-mr]" value="1" <?php checked( 1,$get_flag_choices['flag-mr']); ?>/> Marathi</div>
					<div><input type="checkbox" name="flag_display_settings[flag-mn]" value="1" <?php checked( 1,$get_flag_choices['flag-mn']); ?>/> Mongolian</div>   
                    <div><input type="checkbox" name="flag_display_settings[flag-ne]" value="1" <?php checked( 1,$get_flag_choices['flag-ne']); ?>/> Nepali</div>                  
                    <div><input type="checkbox" name="flag_display_settings[flag-no]" value="1" <?php checked( 1,$get_flag_choices['flag-no']); ?>/> Norwegian</div>                  
                    <div><input type="checkbox" name="flag_display_settings[flag-fa]" value="1" <?php checked( 1,$get_flag_choices['flag-fa']); ?>/> Persian</div>          
                    <div><input type="checkbox" name="flag_display_settings[flag-pl]" value="1" <?php checked( 1,$get_flag_choices['flag-pl']); ?>/> Polish</div>               
                    <div><input type="checkbox" name="flag_display_settings[flag-pt]" value="1" <?php checked( 1,$get_flag_choices['flag-pt']); ?>/> Portuguese</div>
					<div><input type="checkbox" name="flag_display_settings[flag-pa]" value="1" <?php checked( 1,$get_flag_choices['flag-pa']); ?>/> Punjabi</div> 
                    <div><input type="checkbox" name="flag_display_settings[flag-ro]" value="1" <?php checked( 1,$get_flag_choices['flag-ro']); ?>/> Romanian</div>                   
                    <div><input type="checkbox" name="flag_display_settings[flag-ru]" value="1" <?php checked( 1,$get_flag_choices['flag-ru']); ?>/> Russian</div>  
				</div>
				  
				  <div class="flagdisplay" style="width:25%; float:left">
 					<div><input type="checkbox" name="flag_display_settings[flag-sr]" value="1" <?php checked( 1,$get_flag_choices['flag-sr']); ?>/> Serbian</div>                      
                    <div><input type="checkbox" name="flag_display_settings[flag-sk]" value="1" <?php checked( 1,$get_flag_choices['flag-sk']); ?>/> Slovak</div>                   
                    <div><input type="checkbox" name="flag_display_settings[flag-sl]" value="1" <?php checked( 1,$get_flag_choices['flag-sl']); ?>/> Slovenian</div>
					<div><input type="checkbox" name="flag_display_settings[flag-so]" value="1" <?php checked( 1,$get_flag_choices['flag-so']); ?>/> Somali</div>                     
                    <div><input type="checkbox" name="flag_display_settings[flag-es]" value="1" <?php checked( 1,$get_flag_choices['flag-es']); ?>/> Spanish</div>                    
                    <div><input type="checkbox" name="flag_display_settings[flag-sw]" value="1" <?php checked( 1,$get_flag_choices['flag-sw']); ?>/> Swahili</div>                     
                    <div><input type="checkbox" name="flag_display_settings[flag-sv]" value="1" <?php checked( 1,$get_flag_choices['flag-sv']); ?>/> Swedish</div>                    
                    <div><input type="checkbox" name="flag_display_settings[flag-ta]" value="1" <?php checked( 1,$get_flag_choices['flag-ta']); ?>/> Tamil</div>                    
                    <div><input type="checkbox" name="flag_display_settings[flag-te]" value="1" <?php checked( 1,$get_flag_choices['flag-te']); ?>/> Telugu</div>                  
                    <div><input type="checkbox" name="flag_display_settings[flag-th]" value="1" <?php checked( 1,$get_flag_choices['flag-th']); ?>/> Thai</div>                    
                    <div><input type="checkbox" name="flag_display_settings[flag-tr]" value="1" <?php checked( 1,$get_flag_choices['flag-tr']); ?>/> Turkish</div>       
                    <div><input type="checkbox" name="flag_display_settings[flag-uk]" value="1" <?php checked( 1,$get_flag_choices['flag-uk']); ?>/> Ukranian</div>                     			
                    <div><input type="checkbox" name="flag_display_settings[flag-ur]" value="1" <?php checked( 1,$get_flag_choices['flag-ur']); ?>/> Urdu</div>                  
                    <div><input type="checkbox" name="flag_display_settings[flag-vi]" value="1" <?php checked( 1,$get_flag_choices['flag-vi']); ?>/> Vietnamese</div>          
                    <div><input type="checkbox" name="flag_display_settings[flag-cy]" value="1" <?php checked( 1,$get_flag_choices['flag-cy']); ?>/> Welsh</div>               
                    <div><input type="checkbox" name="flag_display_settings[flag-yi]" value="1" <?php checked( 1,$get_flag_choices['flag-yi']); ?>/> Yiddish</div> 
					<div><input type="checkbox" name="flag_display_settings[flag-yo]" value="1" <?php checked( 1,$get_flag_choices['flag-yo']); ?>/> Yoruba</div>               
                    <div><input type="checkbox" name="flag_display_settings[flag-zu]" value="1" <?php checked( 1,$get_flag_choices['flag-zu']); ?>/> Zulu</div>                 
                 
                 </div>
			<div style="clear:both"></div>
  <?php }
  
  public function googlelanguagetranslator_floating_widget_cb() {
	
	$option_name = 'googlelanguagetranslator_floating_widget' ;
    $new_value = 'yes';

      if ( get_option( $option_name ) === false ) {

      // The option does not exist, so we update it.
      update_option( $option_name, $new_value );
	  } 
	  
	  $options = get_option (''.$option_name.''); ?>

          <select name="googlelanguagetranslator_floating_widget" id="googlelanguagetranslator_floating_widget" style="width:170px">
		      <option value="yes" <?php if($options=='yes'){echo "selected";}?>>Yes, show widget</option>
			  <option value="no" <?php if($options=='no'){echo "selected";}?>>No, hide widget</option>
		  </select>
  <?php }
  
  public function googlelanguagetranslator_translatebox_cb() {
	
	$option_name = 'googlelanguagetranslator_translatebox' ;
    $new_value = 'yes';

      if ( get_option( $option_name ) === false ) {

      // The option does not exist, so we update it.
      update_option( $option_name, $new_value );
	  } 
	  
	  $options = get_option (''.$option_name.''); ?>

          <select name="googlelanguagetranslator_translatebox" id="googlelanguagetranslator_translatebox" style="width:190px">
		      <option value="yes" <?php if($options=='yes'){echo "selected";}?>>Yes, show language box</option>
			  <option value="no" <?php if($options=='no'){echo "selected";}?>>No, hide language box</option>
		  </select>
  <?php }
  
  public function googlelanguagetranslator_display_cb() {
	
	$option_name = 'googlelanguagetranslator_display' ;
    $new_value = 'Vertical';

      if ( get_option( $option_name ) === false ) {

      // The option does not exist, so we update it.
      update_option( $option_name, $new_value );
	  } 
	  
	  $options = get_option (''.$option_name.''); ?>

          <select name="googlelanguagetranslator_display" id="googlelanguagetranslator_display" style="width:170px;">
             <option value="Vertical" <?php if(get_option('googlelanguagetranslator_display')=='Vertical'){echo "selected";}?>>Vertical</option>
             <option value="Horizontal" <?php if(get_option('googlelanguagetranslator_display')=='Horizontal'){echo "selected";}?>>Horizontal</option>
          </select>  
  <?php }
  
  public function googlelanguagetranslator_toolbar_cb() {
	
	$option_name = 'googlelanguagetranslator_toolbar' ;
    $new_value = 'Yes';

      if ( get_option( $option_name ) === false ) {

      // The option does not exist, so we update it.
      update_option( $option_name, $new_value );
	  } 
	  
	  $options = get_option (''.$option_name.''); ?>

          <select name="googlelanguagetranslator_toolbar" id="googlelanguagetranslator_toolbar" style="width:170px;">
             <option value="Yes" <?php if(get_option('googlelanguagetranslator_toolbar')=='Yes'){echo "selected";}?>>Yes</option>
             <option value="No" <?php if(get_option('googlelanguagetranslator_toolbar')=='No'){echo "selected";}?>>No</option>
          </select>
  <?php }
  
  public function googlelanguagetranslator_showbranding_cb() {
	
	$option_name = 'googlelanguagetranslator_showbranding' ;
    $new_value = 'Yes';

      if ( get_option( $option_name ) === false ) {

      // The option does not exist, so we update it.
      update_option( $option_name, $new_value );
	  } 
	  
	  $options = get_option (''.$option_name.''); ?>

          <select name="googlelanguagetranslator_showbranding" id="googlelanguagetranslator_showbranding" style="width:170px;">
             <option value="Yes" <?php if(get_option('googlelanguagetranslator_showbranding')=='Yes'){echo "selected";}?>>Yes</option>
             <option value="No" <?php if(get_option('googlelanguagetranslator_showbranding')=='No'){echo "selected";}?>>No</option>
          </select> 
  <?php }
  
  public function googlelanguagetranslator_flags_alignment_cb() {
	
	$option_name = 'googlelanguagetranslator_flags_alignment' ;
    $new_value = 'flags_left';

      if ( get_option( $option_name ) === false ) {

      // The option does not exist, so we update it.
      update_option( $option_name, 'flags_left' );
	  } 
	  
	  $options = get_option (''.$option_name.''); ?>

      <input type="radio" name="googlelanguagetranslator_flags_alignment" id="flags_left" value="flags_left" <?php if($options=='flags_left'){echo "checked";}?>/> Align Left<br/>
      <input type="radio" name="googlelanguagetranslator_flags_alignment" id="flags_right" value="flags_right" <?php if($options=='flags_right'){echo "checked";}?>/> Align Right
  <?php }
  
  public function googlelanguagetranslator_analytics_cb() {
	
	$option_name = 'googlelanguagetranslator_analytics' ;
    $new_value = 0;

      if ( get_option( $option_name ) === false ) {

      // The option does not exist, so we update it.
      update_option( $option_name, $new_value );
	  } 
	  
	  $options = get_option (''.$option_name.'');

    $html = '<input type="checkbox" name="googlelanguagetranslator_analytics" id="googlelanguagetranslator_analytics" value="1" '.checked(1,$options,false).'/> &nbsp; Activate Google Analytics tracking?';
    echo $html;
  }
  
  public function googlelanguagetranslator_analytics_id_cb() {
	
	$option_name = 'googlelanguagetranslator_analytics_id' ;
    $new_value = '';

      if ( get_option( $option_name ) === false ) {

      // The option does not exist, so we update it.
      update_option( $option_name, $new_value );
	  } 
	  
	  $options = get_option (''.$option_name.'');

    $html = '<input type="text" name="googlelanguagetranslator_analytics_id" id="googlelanguagetranslator_analytics_id" value="'.$options.'" />';
    echo $html;
  }
  
  public function googlelanguagetranslator_flag_size_cb() {
	
	$option_name = 'googlelanguagetranslator_flag_size' ;
    $new_value = '16px';

      if ( get_option( $option_name ) === false ) {

      // The option does not exist, so we update it.
      update_option( $option_name, $new_value );
	  } 
	  
	  $options = get_option (''.$option_name.''); ?>

          <select name="googlelanguagetranslator_flag_size" id="googlelanguagetranslator_flag_size" style="width:110px;">
             <option value="16px" <?php if($options=='16px'){echo "selected";}?>>16px</option>
			 <option value="18px" <?php if($options=='18px'){echo "selected";}?>>18px</option>
             <option value="20px" <?php if($options=='20px'){echo "selected";}?>>20px</option>
			 <option value="22px" <?php if($options=='22px'){echo "selected";}?>>22px</option>
             <option value="24px" <?php if($options=='24px'){echo "selected";}?>>24px</option>
          </select> 
  <?php }
  
  public function googlelanguagetranslator_flags_order_cb() {
	$option_name = 'googlelanguagetranslator_flags_order';
	$new_value = '';
	
	if ( get_option ( $option_name ) === false ) {
	  
	  // The option does not exist, so we update it.
	  update_option( $option_name, $new_value );
	}
	
	$options = get_option ( ''.$option_name.'' ); ?>

    <input type="hidden" id="order" name="googlelanguagetranslator_flags_order" value="<?php print_r(get_option('googlelanguagetranslator_flags_order')); ?>" />
   <?php
  }
  
  public function googlelanguagetranslator_css_cb() {
	 
    $option_name = 'googlelanguagetranslator_css' ;
    $new_value = '';

      if ( get_option( $option_name ) === false ) {

      // The option does not exist, so we update it.
      update_option( $option_name, $new_value );
	  } 
	  
	  $options = get_option (''.$option_name.'');
    
	  $html = '<textarea style="width:100%; height:200px" name="googlelanguagetranslator_css" id="googlelanguagetranslator_css">'.$options.'</textarea>';
    echo $html;
 }
  
  public function googlelanguagetranslator_manage_translations_cb() { 
     $option_name = 'googlelanguagetranslator_manage_translations' ;
     $new_value = 0;

      if ( get_option( $option_name ) === false ) {

      // The option does not exist, so we update it.
      update_option( $option_name, $new_value );
	  } 
	  
	  $options = get_option (''.$option_name.'');

    $html = '<input type="checkbox" name="googlelanguagetranslator_manage_translations" id="googlelanguagetranslator_manage_translations" value="1" '.checked(1,$options,false).'/> &nbsp; Turn on translation management?';
    echo $html;
  }
  
  public function googlelanguagetranslator_multilanguage_cb() {
	
	$option_name = 'googlelanguagetranslator_multilanguage' ;
    $new_value = 0;

      if ( get_option( $option_name ) === false ) {

      // The option does not exist, so we update it.
      update_option( $option_name, $new_value );
	  } 
	  
	  $options = get_option (''.$option_name.''); 

      $html = '<input type="checkbox" name="googlelanguagetranslator_multilanguage" id="googlelanguagetranslator_multilanguage" value="1" '.checked(1,$options,false).'/> &nbsp; Turn on multilanguage mode?';
      echo $html; 
  }

  public function page_layout_cb() { ?>
        <div class="wrap">
	      <div id="icon-options-general" class="icon32"></div>
	        <h2><span class="notranslate">Google Language Translator</span></h2>
		      <form action="<?php echo admin_url('options.php'); ?>" method="post">
	          <div class="metabox-holder has-right-sidebar" style="float:left; width:65%">
                <div class="postbox" style="width: 100%">
                  <h3 class="notranslate">Settings</h3>
                  
			      <?php settings_fields('google_language_translator'); ?>
                    <table style="border-collapse:separate" width="100%" border="0" cellspacing="8" cellpadding="0" class="form-table">
                      <tr>
						<td style="width:60%" class="notranslate">Plugin Status:</td>
				        <td class="notranslate"><?php $this->googlelanguagetranslator_active_cb(); ?></td>
                      </tr>
					  
					  <tr class="notranslate">
				        <td>Choose the original language of your website</td>
						<td><?php $this->googlelanguagetranslator_language_cb(); ?></td>
					  </tr>
					  
					  <tr class="notranslate">
						<td>What translation languages will display in the language box?<br/>("All Languages" option <strong><u>must</u></strong> be chosen to show flags.)</td>
						<td><?php $this->googlelanguagetranslator_language_option_cb(); ?></td>
					  </tr>
					  
					  <tr class="notranslate">
						<td colspan="2"><?php $this->language_display_settings_cb(); ?></td>
					  </tr>
					  
					  <tr class="notranslate">
				        <td class="choose_flags_intro">Show flag images?<br/>(Display up to 81 flags above the translator)</td>
						<td class="choose_flags_intro"><?php $this->googlelanguagetranslator_flags_cb(); ?></td>
					  </tr>
					  
					  <tr class="notranslate">
				        <td class="choose_flags">Choose the flags you want to display:</td>
				        <td></td>
			          </tr>
					  
					  <tr class="notranslate">
						<td colspan="2" class="choose_flags"><?php $this->flag_display_settings_cb(); ?></td>
					  </tr>
					  
					  <tr class="notranslate">
						<td>Show floating translation widget? <strong style="color:red">(New!)</strong><br/>
						  <span>("All Languages" option <strong><u>must</u></strong> be chosen to show widget.)</span>
						</td>
						<td><?php $this->googlelanguagetranslator_floating_widget_cb(); ?></td>
					  </tr>
					  
					  <tr class="notranslate">
				        <td>Show translate box?</td>
						<td><?php $this->googlelanguagetranslator_translatebox_cb(); ?></td>
					  </tr>
					  
					  <tr class="notranslate">
                        <td>Layout options:</td>
						<td><?php $this->googlelanguagetranslator_display_cb(); ?></td>
					  </tr>
					  
					  <tr class="notranslate">
                        <td>Show Google Toolbar?</td>
						<td><?php $this->googlelanguagetranslator_toolbar_cb(); ?></td>
					  </tr>
					  
					  <tr class="notranslate">
				        <td>Show Google Branding?<br/>
				          <span>(<a href="https://developers.google.com/translate/v2/attribution" target="_blank">Learn more</a> about Google's Attribution Requirements.)</span>
                        </td>
						<td><?php $this->googlelanguagetranslator_showbranding_cb(); ?></td>
					  </tr>
					  
					  <tr class="alignment notranslate">
				        <td class="flagdisplay">Align the translator left or right?</td>
						<td class="flagdisplay"><?php $this->googlelanguagetranslator_flags_alignment_cb(); ?></td>
					  </tr>
					  
					  <tr class="manage_translations notranslate">
						<td>Turn on translation management?<br/>(Managed directly through your Google account.  Requires <a href="http://translate.google.com/manager/website/settings" target="_blank">Google Translate</a> meta tag installed in header.)</td>
						<td><?php $this->googlelanguagetranslator_manage_translations_cb(); ?></td>
					  </tr>

                      <tr class="multilanguage notranslate">
						<td>Multilanguage Page Option?: (<em>not recommended)</em><br/>(If checked, a "forced" translation of your webpage will be served when returned to the default language, instead of delivering original content.)</td>
						<td><?php $this->googlelanguagetranslator_multilanguage_cb(); ?></td>
					  </tr>
					  
					  <tr class="notranslate">
						<td>Google Analytics:</td>
						<td><?php $this->googlelanguagetranslator_analytics_cb(); ?></td>
					  </tr>
					  
					  <tr class="analytics notranslate">
						<td>Google Analytics ID (Ex. 'UA-11117410-2')</td>
						<td><?php $this->googlelanguagetranslator_analytics_id_cb(); ?></td>
					  </tr>
					  
					  <tr class="notranslate">
						<td>Copy/Paste this shortcode if adding to pages/posts:</td>
						<td><code>[wp-translator]</code></td>
                      </tr>
				  </table>
					  
				  <table style="border-collapse:separate" width="100%" border="0" cellspacing="8" cellpadding="0" class="form-table">
					  <tr class="notranslate">
						<td style="width:40%">For usage in header/footer:</td>
						<td style="width:60%"><code>&lt;?php echo do_shortcode('[wp-translator]'); ?&gt;</code></td>
					  </tr>
					  
					  <tr class="notranslate">
						<td><?php submit_button(); ?></td>
						<td></td>
					  </tr>
			      </table>	  
            
		  </div> <!-- .postbox -->
		  </div> <!-- .metbox-holder -->
		  
		  <div class="metabox-holder" style="float:right; clear:right; width:33%">
		    <div class="postbox">
		      <h3 class="notranslate">Preview</h3>
			    <table style="width:100%">
		          <tr>
					<td style="box-sizing:border-box; -webkit-box-sizing:border-box; -moz-box-sizing:border-box; padding:15px 15px; margin:0px"><span style="color:red; font-weight:bold" class="notranslate">(New!)</span><span class="notranslate"> Drag &amp; drop flags to change their position.</span><br/><br/><?php echo do_shortcode('[wp-translator]'); ?><p class="hello"><span class="notranslate">Translated text:</span> &nbsp; <span>Hello</span></p></td>
				  </tr>
				  
				  <tr>
					<td><?php if (isset ($_POST['googlelanguagetranslator_flags_order']) ) { echo $_POST['googlelanguagetranslator_flags_order']; } ?></td>
				  </tr>
	
	
		        </table>
		    </div> <!-- .postbox -->
	      </div> <!-- .metabox-holder -->
				
		  <div id="glt_advanced_settings" class="metabox-holder notranslate" style="float: right; width: 33%;">
          <div class="postbox">
            <h3>Advanced Settings</h3>
			<div class="inside">
			    <table style="border-collapse:separate" width="100%" border="0" cellspacing="8" cellpadding="0" class="form-table">
				      <tr class="notranslate">
				        <td class="advanced">Select flag size:</td>
						<td class="advanced"><?php $this->googlelanguagetranslator_flag_size_cb(); ?></td>
                      </tr>
			    </table>
			 </div>
          </div>
	   </div>
		  
				
	   <div class="metabox-holder notranslate" style="float: right; width: 33%;">
          <div class="postbox">
            <h3>Add CSS Styles</h3>
			<div class="inside">
			  <p>You can apply any necessary CSS styles below:</p>
			      <?php $this->googlelanguagetranslator_css_cb(); ?>
			 </div>
          </div>
	   </div>
	  <?php $this->googlelanguagetranslator_flags_order_cb(); ?>
	</form>
	
	   
</div> <!-- .wrap -->
<?php 
  }
}

$google_language_translator = new google_language_translator();