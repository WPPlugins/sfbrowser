//var $ = jQuery;
;(function($) {
	// private variables
	var oSettings = { debug: true };
	var ss = oSettings;
	var oSfb = {
		 select:	function(aFiles){insertSFBContent(aFiles,"a")}
		,debug:		false
		,swfupload:	false
		,dirs:		true
		,plugins:	["imageresize"]
		,w:			700
		,h:			500
		,bgcolor:	"#F9F9F9"
		,bgalpha:	.7
	}
	var oData = {
		 image: {
			 folder:"img/"
			,allow:['jpg','jpeg','gif','png']
			,select:function(aFiles){insertSFBContent(aFiles,"img")}
			,bgcolor: "#F9F9F9"
			,resize:	null
		 },video: {
			 folder:"vid/"
		 },audio: {
			 folder:"aud/"
		 },media: {
			 folder:""
		 }
	};
	// default settings
	$.wpadminsfb = ww = {
		 id: "wpadminsfb"
		,defaults: {
			 debug:		false
			,version: "1.2.3"
			,siteUri:	''
			,override:	{}
			,sfbObject: {}
		}
	};
	// init
//	$(function() {
//		try {tinyMCE;$.fn.wpadminsfb()} catch (err) {}
//	});
	// call
	$.fn.extend({
		wpadminsfb: function(_settings) {
			$.extend(oSettings, ww.defaults, _settings);
			trace($.wpadminsfb.id+" "+ss.version,true);
			//
			$.extend(oSfb,ss.sfbObject);
//			alert(ss.sfbObject.dirs+" "+oSfb.dirs+" "+oSfb.dirs);
			//
			if (ss.resize) oData.image.resize = ss.resize;
			oData.image.folder = ss.imageFolder+"/";
			oData.video.folder = ss.videoFolder+"/";
			oData.audio.folder = ss.audioFolder+"/";
			oData.media.folder = ss.mediaFolder+"/";
			//
			// overrides
			if (ss.override.media) {
				var $Li = $("li#menu-media");
				$Li.removeClass(".wp-has-submenu");
				$Li.find(".wp-menu-toggle").remove();
				$Li.find(">a").removeAttr('href').click(function(e){$.sfb(oSfb);});
				$Li.find(".wp-submenu").remove();
				$("#menu-settings a[href=options-media.php]").parents("li:first").remove();
			}
			// tinymce
			$.each(oData,function(s,o){
				if (ss.override['tinymce_'+s]) {
					$("#media-buttons>a#add_"+s).removeAttr("href").removeClass("thickbox");
					$("#media-buttons>a#add_"+s).bind("click",function(e){$.sfb($.extend({},oSfb,o))})
				}
			}); 
/*			// featured image
			if (ss.override.feature) {
				$("a#set-post-thumbnail"); // href = media-upload.php?post_id=358&amp;type=image&amp;TB_iframe=1&amp;width=640&amp;height=404
				$("a#remove-post-thumbnail"); // onclick = WPRemoveThumbnail('1116368bb4');
			}

THIS SUCKS:

wp_postmeta
post_id		meta_key			meta_value
361			_thumbnail_id		359
358			_thumbnail_id		359

wp_posts
ID			post_title			guid
359			asdf				http/(..)/asdf.png

// UNSET

<div id="postimagediv" class="postbox ">
	<div class="handlediv" title="Click to toggle"><br></div><h3 class="hndle"><span>Featured Image</span></h3>
	<div class="inside">
		<input name="sfbrowser_version" id="sfbrowser_version" type="hidden" value="1.2.0">
		<p class="hide-if-no-js">
			<a title="Set featured image" href="media-upload.php?post_id=358&amp;type=image&amp;TB_iframe=1&amp;width=640&amp;height=404" id="set-post-thumbnail" class="thickbox">Set featured image</a>
		</p>
	</div>
</div>


// SET

<div id="postimagediv" class="postbox ">
	<div class="handlediv" title="Click to toggle"><br></div>
	<h3 class="hndle"><span>Featured Image</span></h3>
	<div class="inside">
		<input name="sfbrowser_version" id="sfbrowser_version" type="hidden" value="1.2.0">
		<p class="hide-if-no-js">
			<a title="Set featured image" href="media-upload.php?post_id=358&amp;type=image&amp;TB_iframe=1&amp;width=640&amp;height=404" id="set-post-thumbnail" class="thickbox">
				<img width="250" height="198" src="http://localhost/gmg/greentoday/web/wp-content/uploads/2010/11/cormiami2-250x198.png" class="attachment-post-thumbnail" alt="cormiami2" title="cormiami2">
			</a>
		</p>
		<p class="hide-if-no-js">
			<a href="#" id="remove-post-thumbnail" onclick="WPRemoveThumbnail('1116368bb4');return false;">Remove featured image</a>
		</p>
	</div>
</div>
*/
			//
			$(window).load(function () { // todo: test
				replaceSrc();
//				if(typeof tinyMCE == "undefined") return;
//				if (!tinyMCE) return;
				try {
					tinyMCE&&tinyMCE.activeEditor&&tinyMCE.activeEditor.onChange.add(replaceSrc);
					$("#edButtonPreview").click(replaceSrc);
				} catch (err) {
					trace("tinyMCE not found"); // TRACE ### tinyMCE
				}
			});
			//
			// admin form options
			$.each(["image","video","audio","media"],function(i,s){
				var $Input = $("#sfbrowser_overrideTinyMCE"+i);
				if (!$Input.is(":checked")) $("#sfbrowser_"+s+"Directory").parents("tr:first").hide();
				$Input.change(function(){
					var $Tr = $("#sfbrowser_"+s+"Directory").parents("tr:first");
					if (!$(this).is(":checked")) $Tr.hide();
					else $Tr.show();
				});
			});
		}
	});
	// insertSFBContent
	function insertSFBContent(aFiles,sType) {
		var sFile = aFiles[0].file.replace("..data/",ss.siteUri+"/data/").replace("../","").replace("//","/");
		var sHTML = "<img src=\""+sFile+"\" />";
		switch (sType) {
			case "a":
				trace("aFiles.length: "+aFiles.length); // TRACE ### aFiles.length
				if (aFiles.length>1) {
					sHTML = "<ul>";
					for (var i=0;i<aFiles.length;i++) {
						var sFile = aFiles[i].file.replace("..data/",ss.siteUri+"/data/").replace("../","").replace("//","/");
						sHTML += "<li><a href=\""+sFile+"\">"+sFile.split("/").pop()+"</a></li>";
					}
					sHTML += "</ul>";
				} else {
					sHTML = "<a href=\""+sFile+"\">"+sFile.split("/").pop()+"</a>";
				}
			break;
		}
		try { // bloody editor
			// looks like this is in visual mode
			tinyMCE.activeEditor.selection.setContent(sHTML);
			replaceSrc();
		} catch (e) {
			// looks like this is in html mode
			edInsertContent(edCanvas,sHTML);
		}
	}
	// replaceSrc
	function replaceSrc() {
		$("iframe").contents().find("body img").each(function(){
			var sSrc = $(this).attr("src");
			if (sSrc.substr(0,3)!="../") $(this).attr("src","../"+sSrc);
		});
	}
	// trace
	function trace(o,v) {
		if ((v||ss.debug)&&window.console&&window.console.log) {
			if (typeof(o)=="string")	window.console.log(o);
			else						for (var prop in o) window.console.log(prop+":\t"+String(o[prop]).split("\n")[0]);
		}
	}
})(jQuery);