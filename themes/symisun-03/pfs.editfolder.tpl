<!-- BEGIN: MAIN -->

<!-- BEGIN: STANDALONE_HEADER -->
<html>
<head>
<title>{PHP.L.pfs_title} - {PHP.cfg.maintitle}</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<base href="{PHP.cfg.mainurl}/" />
{PFS_HEAD}
<script type="text/javascript">
//<![CDATA[
{PFS_HEADER_JAVASCRIPT}
//]]>
</script>
<link href="themes/{PHP.theme}/{PHP.theme}.css" type="text/css" rel="stylesheet" />
<style type="text/css">
	#content, #left { width: 100%; }
	#right, .breadcrumb { display: none; }
	</style>
</head>
<body>
<!-- END: STANDALONE_HEADER -->

	<div id="content">
    	<div class="padding20 popup whitee">
        	
            <h1>{PFS_TITLE}</h1>
            <div class="breadcrumb">{PHP.themelang.list.bread}: <a href="{PHP|cot_url('users')}">{PHP.L.Users}</a> <a href="{PHP.usr.name|cot_url('users','m=details&u=$this')}">{PHP.usr.name}</a> <a href="{PHP|cot_url('pfs')}">{PHP.L.PFS}</a></div>
            <!-- IF {PFS_SUBTITLE} == true -->
            <p class="details">{PFS_SUBTITLE}</p>
            <!-- ENDIF -->
			{FILE "./themes/symisun-03/warnings.tpl"}
<form id="editfolder" action="{PFS_ACTION}" method="post">
			<table class="cells">
				<!--<tr>
					<td class="width20">{PHP.L.pfs_parentfolder}:</td>
					<td class="width80">{PFF_FOLDER}</td>
				</tr>-->
				<tr>
					<td>{PHP.L.Folder}:</td>
					<td>{PFF_TITLE}</td>
				</tr>
				<tr>
					<td>{PHP.L.Description}:</td>
					<td>{PFF_DESC}</td>
				</tr>
				<tr>
					<td>{PHP.L.Date}:</td>
					<td>{PFF_DATE}</td>
				</tr>
				<tr>
					<td>{PHP.L.Updated}:</td>
					<td>{PFF_UPDATED}</td>
				</tr>
				<tr>
					<td>{PHP.L.pfs_ispublic}</td>
					<td>
						{PFF_ISPUBLIC}
					</td>
				</tr>
				<tr>
					<td>{PHP.L.pfs_isgallery}</td>
					<td>
						{PFF_ISGALLERY}
					</td>
				</tr>
				<tr>
					<td colspan="2" class="valid">
						<button type="submit">{PHP.L.Update}</button>
					</td>
				</tr>
			</table>
		</form>
		<br class="clear" />
		

            </div>


        </div>    
    </div>
    <br class="clear" />
<!-- BEGIN: STANDALONE_FOOTER -->
</body>
</html>
<!-- END: STANDALONE_FOOTER -->

<!-- END: MAIN -->