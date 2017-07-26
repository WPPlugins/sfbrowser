<?php
/*
	Plugin Name:	SFBrowser for Wordpress
	Plugin URI:		http://code.google.com/p/sfbrowser/
	Version:		1.3.0
	SFBrowser Version: 3.2.1
	WordPress Version: 3.0.1
	Author:			Ron Valstar
	Author URI:		http://sjeiti.com/
	Author email:	sfb@sjeiti.com
	Description:	Incorporation of the SFBrowser into Wordpress. See http://sfbrowser.sjeiti.com/ for more info.
	It is the basic SFBrowser installation with two additional files:
		- wp_sfbrowser.php				wp plugin file
		- wp_jquery.wpadminsfb.js		hack into the admin interface
	Plus of course a config file to suit Wordpress.
*/

function sfbrowser_version(){return "1.2.4";}

add_action('admin_head', 'sfbrowser_adminheader');
function sfbrowser_adminheader() {

	// check uploads folder
	$sUp = sfb_val('sfbrowser_uploadDirectory')."/";
	$sFld = "../".$sUp;
	if (!is_dir($sFld)) mkdir($sFld);

	// check for subfolders for image, video, audio or media
	$oTmce = sfb_val('sfbrowser_overrideTinyMCE');
	$i = 0;
	foreach (array("image","video","audio","media") as $sFolder) {
		if ($oTmce[$i]=="on") {
			$sSub = $sFld."/".sfb_val('sfbrowser_'.$sFolder.'Directory');
			if (!is_dir($sSub)) mkdir($sSub);
		}
		$i++;
	}

	//echo "<!--\n\n".WP_SFB_LANG."\n\n-->";

	// create plugins array
	$oPlug = sfb_val('sfbrowser_plugins');
	$aPlug = sfb_o('sfbrowser_plugins');
	$aPlugins = array();
	foreach ($oPlug as $i=>$s) $aPlugins[] = $aPlug['values'][$i];

	$oResize = sfb_val('sfbrowser_resizeImages');

	$T = WP_SFB_DEBUG?"\t":"";
	$N = WP_SFB_DEBUG?"\n":"";

	echo $N.$T.$T."<!-- wp-SFBrowser init -->".$N;
	echo $T.$T.'<script type="text/javascript" src="../wp-content/plugins/sfbrowser/wp_jquery.sfbrowser.js"></script>'.$N;
	echo $T.$T.'<script type="text/javascript">'.$N;
	echo $T.$T.$T.'jQuery(function() {'.$N;
	echo $T.$T.$T.$T.'jQuery.fn.wpadminsfb({'.$N;
	echo $T.$T.$T.$T.$T.' version: "'.sfbrowser_version().'"'.$N;
	echo $T.$T.$T.$T.$T.',siteUri: "'.site_url().'"'.$N;
	echo $T.$T.$T.$T.$T.',override:	{'.$N;
	echo $T.$T.$T.$T.$T.$T.'media:'.(sfb_val('sfbrowser_mediaMainMenu')?'true':'false').$N;
	echo $T.$T.$T.$T.$T.$T.',tinymce_image:'.($oTmce[0]=='on'?'true':'false').$N;
	echo $T.$T.$T.$T.$T.$T.',tinymce_video:'.($oTmce[1]=='on'?'true':'false').$N;
	echo $T.$T.$T.$T.$T.$T.',tinymce_audio:'.($oTmce[2]=='on'?'true':'false').$N;
	echo $T.$T.$T.$T.$T.$T.',tinymce_media:'.($oTmce[3]=='on'?'true':'false').$N;
	echo $T.$T.$T.$T.$T.$T.',feature:'.(sfb_val('sfbrowser_featureImage')?'true':'false').$N;
	echo $T.$T.$T.$T.$T.'}'.$N;
	echo $T.$T.$T.$T.$T.',saveFullPath: '.(sfb_val('sfbrowser_saveFullPath')?'true':'false').$N;
	echo $T.$T.$T.$T.$T.',resize: '.(($oResize[0]&&$oResize[1])?'['.$oResize[0].','.$oResize[1].']':'null').$N;
	echo $T.$T.$T.$T.$T.',imageFolder: \''.sfb_val('sfbrowser_imageDirectory').'\''.$N;
	echo $T.$T.$T.$T.$T.',videoFolder: \''.sfb_val('sfbrowser_videoDirectory').'\''.$N;
	echo $T.$T.$T.$T.$T.',audioFolder: \''.sfb_val('sfbrowser_audioDirectory').'\''.$N;
	echo $T.$T.$T.$T.$T.',mediaFolder: \''.sfb_val('sfbrowser_mediaDirectory').'\''.$N;
	echo $T.$T.$T.$T.$T.',sfbObject: {'.$N;
	echo $T.$T.$T.$T.$T.$T.'cookie:'.(sfb_val('sfbrowser_cookie')?'true':'false').$N;
	echo $T.$T.$T.$T.$T.$T.',dirs:'.(sfb_val('sfbrowser_dirs')?'true':'false').$N;
	echo $T.$T.$T.$T.$T.$T.',swfupload:'.(sfb_val('sfbrowser_swfUpload')?'true':'false').$N;
	echo $T.$T.$T.$T.$T.$T.',plugins:[\''.implode('\',\'',$aPlugins).'\']'.$N;
	echo $T.$T.$T.$T.$T.$T.',debug:'.(WP_SFB_DEBUG?'true':'false').$N;
	echo $T.$T.$T.$T.$T.'}'.$N;
	echo $T.$T.$T.$T.'});'.$N;
	echo $T.$T.$T.'});'.$N;
	echo $T.$T.'</script>'.$N;
	include_once("../wp-content/plugins/sfbrowser/connectors/php/init.php");
	echo $N.$T.$T."<!-- wp-SFBrowser end -->".$N;
}

function sfb_val($s){
	if (!isset($GLOBAL['sfbdata'])) $GLOBAL['sfbdata'] = sfbrowser_getFormdata();
	$o = $GLOBAL['sfbdata'][$s];
	$value = $o['value'];
	if ($o['type']=='checkbox'&&!isset($o['values'])) $value = $value=='on';
	return $value;
}
function sfb_o($s){
	if (!isset($GLOBAL['sfbdata'])) $GLOBAL['sfbdata'] = sfbrowser_getFormdata();
	return $GLOBAL['sfbdata'][$s];
}


//////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////

// sfbrowser_admin_init
add_action('admin_init', 'sfbrowser_admin_init');
function sfbrowser_admin_init(){
	//
	define("WP_SFB_DEBUG", sfb_val('sfbrowser_debug'));
	//
	//$sLang = qtrans_getLanguage();
	$aLang = preg_split('/-/',get_bloginfo('language'));
	define("WP_SFB_LANG", $aLang[0]);
	//
	define('SFB_SETTINGS', 'sfbrowser_settings');
	define('SFB_PAGE', 'sfbrowser_page');
	//
	define('SFB_PRFX', 'sfb_field');
	//
	$sSection = 'default';
	$aForm = sfbrowser_getFormdata();
	foreach ($aForm as $sId=>$aField) {
		$sLabel = $aField['label'];
		if ($aField['type']=='label') {
			$sSection = $sId;
			add_settings_section($sSection, $sLabel, 'sfbrowser_section_text', SFB_PAGE);
		} else if ($aField['type']=='hidden') {
			sfbrowser_drawFormField($aField);
		} else {
			register_setting( SFB_SETTINGS, $sId, 'sfbrowser_options_sanatize' ); //TODO:validation
			add_settings_field( $sId, $sLabel, 'sfbrowser_drawFormField', SFB_PAGE, $sSection, $aField);
		}
	}
}
function sfbrowser_section_text($for){
	$aForm = sfbrowser_getFormdata();
	if (isset($aForm[$for['id']]['text'])) echo '<p>'.$aForm[$for['id']]['text'].'</p>';
}
function sfbrowser_options_sanatize($a){
	return $a;
}

// admin menu
add_action('admin_menu', 'sfbrowser_adminMenu',4);
function sfbrowser_adminMenu() {
	add_options_page(__('SFBrowser Management', 'sfbrowser'), __('SFBrowser', 'sfbrowser'), 'manage_options', 'sfbrowser', 'sfbrowser_settings_page');
}
function sfbrowser_settings_page() {
	echo '<div class="wrap">';
	echo '<div id="icon-options-general" class="icon32"><br></div>';
	echo '<h2>'.__('SFBrowser options','sfbrowser').'</h2>';
	// todo: add alert for get_option('permalink_structure')!='' when tinymce is overridden
	echo '<p style="max-width:700px;">'.__('_SFBrowser explanation','sfbrowser').'</p>';
	// start form
	echo '<form method="post" action="options.php">';
	settings_fields(SFB_SETTINGS);
	do_settings_sections(SFB_PAGE);
	echo '<p><br/><input type="submit" name="submit" class="button-primary" value="'.__('Save changes','sfbrowser').'" /></p>';
	echo '</form>';
}


// admin options
function sfbrowser_getFormdata() {
	load_plugin_textdomain('sfbrowser','/wp-content/plugins/sfbrowser/wp_lang');//##TODO
	$aForm = array(
//		 'sfbrowser_version'=>array('type'=>'hidden', 'default'=>'1.2.0')

		 'label1'=>array('label'=>__('Basic settings','sfbrowser'),'type'=>'label')
		,'sfbrowser_uploadDirectory'=>array(	'default'=>'wp-content/uploads',	'label'=>__('Upload directory','sfbrowser'),	'w'=>'30')
		,'sfbrowser_imageDirectory'=>array(		'default'=>'',	'label'=>__('Image directory','sfbrowser'), 'text'=>__('(relative to upload directory)','sfbrowser'),	'w'=>'10')
		,'sfbrowser_videoDirectory'=>array(		'default'=>'',	'label'=>__('Video directory','sfbrowser'), 'text'=>__('(relative to upload directory)','sfbrowser'),	'w'=>'10')
		,'sfbrowser_audioDirectory'=>array(		'default'=>'',	'label'=>__('Audio directory','sfbrowser'), 'text'=>__('(relative to upload directory)','sfbrowser'),	'w'=>'10')
		,'sfbrowser_mediaDirectory'=>array(		'default'=>'',	'label'=>__('Media directory','sfbrowser'), 'text'=>__('(relative to upload directory)','sfbrowser'),	'w'=>'10')
//		,'sfbrowser_saveFullPath'=>array(		'default'=>'',						'label'=>__('Save full path','sfbrowser'),			'type'=>'checkbox')

		,'label2'=>array('label'=>__('Override Wordpress elements','sfbrowser'),'type'=>'label',	'text'=>__('_sfbrowser override explanation.','sfbrowser'))
		,'sfbrowser_mediaMainMenu'=>array(		'default'=>'on',					'label'=>__('Media menu item','sfbrowser'),			'type'=>'checkbox')
		,'sfbrowser_overrideTinyMCE'=>array(	'default'=>'a:4:{i:0;s:2:"on";i:1;s:2:"on";i:2;s:2:"on";i:3;s:2:"on";}',						'label'=>__('TinyMCE Upload/Insert','sfbrowser'),	'type'=>'checkbox', values=>array(
			 __("image",'sfbrowser')
			,__("video",'sfbrowser')
			,__("audio",'sfbrowser')
			,__("media",'sfbrowser')
		))
//		,'sfbrowser_featureImage'=>array(		'default'=>'on',					'label'=>__('Feature image','sfbrowser'),			'type'=>'checkbox')

		,'label3'=>array('label'=>__('SFBrowser settings','sfbrowser'),	'type'=>'label',	'text'=>__('_sfbrowser settings explanation.','sfbrowser'))
		,'sfbrowser_plugins'=>array(			'default'=>'a:1:{i:2;s:2:"on";}',	'label'=>__('Plugins','sfbrowser'),					'type'=>'checkbox', values=>array(
			 "imageresize"
			,"filetree"
			,"createascii"
		))
		,'sfbrowser_resizeImages'=>array(		'default'=>'',						'label'=>__('Resize images','sfbrowser'),			'values'=>array("w","h"),	'text'=>__('_resizeExplain','sfbrowser'),	'w'=>'4')
		,'sfbrowser_cookie'=>array(				'default'=>'',						'label'=>__('Save cookie','sfbrowser'),				'type'=>'checkbox',			'text'=>__('_cookieExplain','sfbrowser'))
		,'sfbrowser_dirs'=>array(				'default'=>'on',					'label'=>__('Directory creation','sfbrowser'),		'type'=>'checkbox',			'text'=>__('_dirsExplain','sfbrowser'))
		,'sfbrowser_swfUpload'=>array(			'default'=>'',						'label'=>__('Swf upload','sfbrowser'),				'type'=>'checkbox',			'text'=>__('_swfUploadExplain','sfbrowser'))
		,'sfbrowser_debug'=>array(				'default'=>'',						'label'=>__('Debug mode','sfbrowser'),				'type'=>'checkbox',			'text'=>__('_debugExplain','sfbrowser'))
	);
//	// search for installed sfbrowser plugins
//	$i = 0;
//	$sDir = "../wp-content/plugins/sfbrowser/plugins/";
//	if ($handle = opendir($sDir)) while (false!==($file=readdir($handle))) {
//		if ($file!='.'&&$file!='..') $aForm['sfbrowser_plugins']['values'][] = $file;
//	}
	// find or init existing values
	foreach ($aForm as $sId=>$aField) {
		if ($aField['type']!='label') {
			$sDefault = $aField['default'];
			$sVal = get_option($sId);
			if ($sVal===false) update_option($sId, $sDefault);
			$aForm[$sId]['value'] = $sVal!==false?$sVal:$sDefault;
			$aForm[$sId]['id'] = $sId;
		}
	}
	return $aForm;
}

// sfbrowser_drawFormField
function sfbrowser_drawFormField($data){
	$sId = $data['id'];
	$sLabel = $data['label'];
	$bRequired = isset($data['req'])?$data['req']:false;
	$sRequired = $bRequired?' required="required"':'';
	$sType = isset($data['type'])?$data['type']:'text';
	$sValue = $data['value'];
	$sValTr = ' value="'.$sValue.'"';
	$aValues = isset($data['values'])?$data['values']:array();//$sId=>$sLabel
	$sWidth = isset($data['w'])?' size="'.$data['w'].'" ':'';
	switch ($sType) {
		case 'text': // text
			if (count($aValues)==0) {
				echo '<input name="'.$sId.'" id="'.$sId.'" type="'.$sType.'" '.$sWidth.$sValTr.$sRequired.' size="50" /> ';
			} else {
				foreach ($aValues as $sValueId=>$sValueLabel) {
					$sSubName = $sId.'['.$sValueId.']';
					$sSubId = $sId.$sValueId;
					echo '<label for="'.$sSubId.'">'.$sValueLabel.'</label> <input name="'.$sSubName.'" id="'.$sSubId.'" type="'.$sType.'" value="'.$sValue[$sValueId].'" '.$sWidth.$sRequired.'/> ';
				}
			}
			if (isset($data['text'])) echo '<span class="description">'.$data['text'].'</span>';
		break;
		case 'checkbox': // todo: set checked status if true
			if (count($aValues)==0) {
				echo '<input name="'.$sId.'" id="'.$sId.'" type="'.$sType.'" '.($sValue=='on'?'checked="checked"':'').' '.$sRequired.'/> ';
			} else {
				foreach ($aValues as $sValueId=>$sValueLabel) {
					$sSubName = $sId.'['.$sValueId.']';
					$sSubId = $sId.$sValueId;
					echo '<input name="'.$sSubName.'" id="'.$sSubId.'" type="'.$sType.'" '.($sValue[$sValueId]=='on'?'checked="checked"':'').' '.$sRequired.'/> <label for="'.$sSubId.'">'.$sValueLabel.'</label> ';
				}
			}
			if (isset($data['text'])) echo '<span class="description">'.$data['text'].'</span>';
		break;
		case 'textarea':
			echo '<textarea name="'.$sId.'" id="'.$sId.'" class="form_'.$sType.'" type="'.$sType.'" '.$sRequired.'>'.$value.'</textarea>';
		break;
		case 'hidden':
			echo '<input name="'.$sId.'" id="'.$sId.'" type="'.$sType.'" value="'.$sValue.'" />';
		break;
		case 'test': // test
			$opt = get_option($sId);
			echo '<input name="'.$sId.'[a]" id="'.$sId.'" type="'.$sType.'"  value="'.$opt['a'].'" '.$sRequired.' />';
			echo '<input name="'.$sId.'[b]" id="'.$sId.'" type="'.$sType.'"  value="'.$opt['b'].'" '.$sRequired.' />';
		break;
		default: echo "<!-- field type ".$sType." does not exist -->";
	}
}


add_filter('plugin_action_links', 'sfbrowser_links', 10, 2);
function sfbrowser_links($links, $file){ // copied from qtranslate who copied from Sociable Plugin
	//Static so we don't call plugin_basename on every plugin row.
	static $this_plugin;
	if (!$this_plugin) $this_plugin = plugin_basename(dirname(__FILE__).'/wp_sfbrowser.php');
	if ($file == $this_plugin){
		$settings_link = '<a href="options-general.php?page=sfbrowser">' . __('Settings', 'sfbrowser') . '</a>';
		array_unshift( $links, $settings_link ); // before other links
	}
	return $links;
}
?>