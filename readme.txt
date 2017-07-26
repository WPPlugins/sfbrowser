=== SFBrowser ===
Contributors: Sjeiti
Tags: filebrowser media, upload, uploader, file, sfbrowser, media, image, resize, ascii, preview, directory, folder, rename, move
Requires at least: 3.0.1
Tested up to: 3.0.1
Stable tag: 1.3.0

SFBrowser is a file browser and uploader. It can replace the existing media library.


== Description ==

SFBrowser is a file browser and uploader. It can be used as an alternative for the existing media library. But it can also quite easily be used to support other plugins and/or extensions.
Unlike the existing media library SFBrowser does not store references to files in the database. So files uploaded through FTP are inmediately visible.
For the moment, SFBrowser will return relative paths to the files. This has numerous upsides but one downside is that it will not work properly with url rewrites. This will be fixed in future releases.

= how it works =
Upon file selection SFBrowser returns a list of file objects.
A file object contains:

* file(String):	The file including its path
* mime(String):	The filetype
* rsize(int):	The size in bytes
* size(String):	The size formatted to B, kB, MB, GB etc..
* time(int):	The time in seconds from Unix Epoch
* date(String):	The time formatted in "j-n-Y H:i"
* width(int):	If image, the width in px
* height(int):	If image, the height in px

= SFBrowser features =
* ajax file upload
* optional as3 swf upload (queued multiple uploads, upload progress, upload canceling, selection filtering, size filtering)
* localisation (English, Dutch or Spanish)
* server side script connector
* plugin environment (with imageresize plugin, filetree and create/edit ascii)
* data caching (minimal server communication)
* sortable file table
* file filtering
* file renameing
* file duplication
* file movement
* file download
* file/folder context menu
* file preview (image, audio, video, zip, text/ascii, pdf and swf)
* folder creation
* multiple files selection (not in IE for now)
* inline or overlay window
* window dragging and resizing
* cookie for size, position and path
* keyboard shortcuts
* key file selection

For more Information visit the [SFBrowser homepage](http://sfbrowser.sjeiti.com/), or the [googlecode repository](http://code.google.com/p/sfbrowser/).


== Installation ==

1. After download place the files in the Wordpress plugin directory (wp-content/plugins/sfbrowser/)
1. Activate the plugin through the 'Plugins' menu in WordPress
1. In the admin settings menu you can adjust SFBrowser according to you needs.

== Screenshots ==

1. The right-click context menu
2. Simultaneous uploads
3. File renaming
4. File movement by drag and drop
5. The image resize plugin