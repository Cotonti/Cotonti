<?php
/**
 * English Language File for the PFS Module (pfs.en.lang.php)
 *
 * @package PFS
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL.');

/**
 * Module Config
 */

$L['cfg_maxpfsperpage'] = 'Max. elements on page';
$L['cfg_maxpfsperpage_hint'] = ' ';
$L['cfg_pfsfilecheck'] = 'File Check';
$L['cfg_pfsfilecheck_hint'] = 'If Enabled will check any uploaded files through the '.$L['PFS'].', or images through the profile. To insure they are valid files. &quot;Yes&quot; recommended, for security reasons.';
$L['cfg_pfsmaxuploads'] = 'Max. concurrent uploads at a time';
$L['cfg_pfsmaxuploads_hint'] = '';
$L['cfg_pfsnomimepass'] = 'No Mimetype Pass';
$L['cfg_pfsnomimepass_hint'] = 'If Enabled it will allow uploaded files to pass even if there is no mimetype in the config file.';
$L['cfg_pfstimename'] = 'Time-based filenames';
$L['cfg_pfstimename_hint'] = 'Generate filenames based on current time stamp. By default the original file name is used with some necessary character conversions.';
$L['cfg_pfsuserfolder'] = 'Folder storage mode';
$L['cfg_pfsuserfolder_hint'] = 'If enabled, will store the user files in subfolders /datas/users/USERID/FOLDERNAME/... Must be set at the FIRST setup of the site ONLY. As soon as a file is uploaded, it\'s too late to change this.';
$L['cfg_flashupload'] = 'Use flash uploader';
$L['cfg_flashupload_hint'] = 'Allows uploading many files at once.';
$L['cfg_pfs_winclose'] = 'Close popup window after bbcode insertion';
$L['cfg_th_amode'] = 'Thumbnails generation';
$L['cfg_th_amode_hint'] = '';
$L['cfg_th_border'] = 'Thumbnails, border size';
$L['cfg_th_border_hint'] = 'Default: 4 pixels';
$L['cfg_th_colorbg'] = 'Thumbnails, border color';
$L['cfg_th_colorbg_hint'] = 'Default: 000000, hex color code';
$L['cfg_th_colortext'] = 'Thumbnails, text color';
$L['cfg_th_colortext_hint'] = 'Default: FFFFFF, hex color code';
$L['cfg_th_dimpriority'] = 'Thumbnails, rescaling priority dimension';
$L['cfg_th_dimpriority_hint'] = '';
$L['cfg_th_jpeg_quality'] = 'Thumbnails, Jpeg quality';
$L['cfg_th_jpeg_quality_hint'] = 'Default: 85';
$L['cfg_th_keepratio'] = 'Thumbnail, keep ratio?';
$L['cfg_th_keepratio_hint'] = '';
$L['cfg_th_separator'] = 'Thumbnail Options';
$L['cfg_th_textsize'] = 'Thumbnails, size of the text';
$L['cfg_th_textsize_hint'] = '';
$L['cfg_th_x'] = 'Thumbnails, width';
$L['cfg_th_x_hint'] = 'Default: 112 pixels';
$L['cfg_th_y'] = 'Thumbnails, height';
$L['cfg_th_y_hint'] = 'Default: 84 pixel, recommended: Width x 0.75';

/**
 * Other
 */

$L['adm_gd'] = 'GD graphical library';
$L['adm_allpfs'] = 'All PFS';
$L['adm_allfiles'] = 'All files';
$L['adm_thumbnails'] = 'Thumbnails';
$L['adm_orphandbentries'] = 'Orphan DB entries';
$L['adm_orphanfiles'] = 'Orphan files';
$L['adm_delallthumbs'] = 'Delete all thumbnails';
$L['adm_rebuildallthumbs']= 'Delete and rebuild all thumbnails';
$L['adm_help_allpfs'] = $L['PFS'].' of all registered users';
$L['adm_nogd'] = 'The GD graphical library is not supported by this host, Cotonti won\'t be able to create thumbnails for images. Go for '.$L['Configuration'].' &gt; '.$L['PFS'].' and set &quot;Thumbnails generation&quot; to &quot;'.$L['Disabled'].'&quot;.';
$L['adm_help_pfsfiles'] = 'Not available';
$L['adm_help_pfsthumbs'] = 'Not available';
$L['info_desc'] = 'Personalized (PFS) and common (SFS) file storage facility';

/**
 * Main
 */

$L['pfs_cancelall'] = 'Cancel All';
$L['pfs_direxists'] = 'Such a folder already exists.<br />Old path: %1$s<br />New path: %2$s';
$L['pfs_extallowed'] = 'Extensions allowed';
$L['pfs_filecheckfail'] = 'Warning: File Check Failed for Extension: %1$s Filename - %2$s';
$L['pfs_filechecknomime'] = 'Warning: No Mime Type data was found for the Extension: %1$s Filename - %2$s';
$L['pfs_fileexists'] = 'The upload failed, there\'s already a file with this name?';
$L['pfs_filelistempty'] = 'List is empty.';
$L['pfs_filemimemissing'] = 'The mime type for %1$s is missing. Upload Failed';
$L['pfs_filenotmoved'] = 'The upload failed, temporary file cannot be moved.';
$L['pfs_filenotvalid'] = 'This is not a valid %1$s file.';
$L['pfs_filesintheroot'] = 'File(s) in the root';
$L['pfs_filesinthisfolder'] = 'File(s) in this folder';
$L['pfs_filetoobigorext'] = 'The upload failed, this file is too big or the extension is not allowed?';
$L['pfs_folderistempty'] = 'This folder is empty.';
$L['pfs_foldertitlemissing'] = 'A folder title is required.';
$L['pfs_isgallery'] = 'Gallery?';
$L['pfs_ispublic'] = 'Public?';
$L['pfs_maxsize'] = 'Maximum size for a file';
$L['pfs_maxspace'] = 'Maximum space allowed';
$L['pfs_newfile'] = 'Upload a file:';
$L['pfs_newfolder'] = 'Create a new folder:';
$L['pfs_onpage'] = 'On this page';
$L['pfs_parentfolder'] = 'Parent folder';
$L['pfs_pastefile'] = 'Paste as file link';
$L['pfs_pasteimage'] = 'Paste as image';
$L['pfs_pastethumb'] = 'Paste as thumbnail';
$L['pfs_resizeimages'] = 'to scale the image?';
$L['pfs_title'] = 'My Personal File Space';
$L['pfs_totalsize'] = 'Total size';
$L['pfs_uploadfiles'] = 'Upload Files';

$L['pfs_insertasthumbnail'] = 'Insert as thumbnail';
$L['pfs_insertasimage'] = 'Insert as fullsize image';
$L['pfs_insertaslink'] = 'Insert as a link to the file';
$L['pfs_dimensions'] = 'Dimensions';

$L['pfs_confirm_delete_file'] = 'Are you sure want to delete this file?';
$L['pfs_confirm_delete_folder'] = 'Are you sure want to delete this folder and all of its contents?';
