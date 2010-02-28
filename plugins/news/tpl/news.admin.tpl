<!-- BEGIN: ADMIN -->
<script type="text/javascript">
	var num = {CATNUM};
</script>
<script type="text/javascript" src="{PHP.cfg.plugins_dir}/news/js/news.admin.js"></script>

<div id="catgenerator" style="display:none">
	<table class="cells">
		<tr>
			<td class="coltop" style="width:200px;">{PHP.L.Category}</td>
			<td class="coltop" style="width:50px;">{PHP.L.NewsCount}</td>
			<td class="coltop" style="width:50px;">{PHP.L.Newsautocut} *</td>
			<td class="coltop">{PHP.L.Tag}</td>
			<td class="coltop" style="width:150px;">{PHP.L.Template} **</td>
			<td class="coltop" style="width:20px;"></td>
		</tr>
		<tr>
			<td  class="coltop" colspan="6">{PHP.L.Maincat}</td>
		</tr>
		<tr>
			<td>{MAINCATEGORY}</td>
			<td><span id="main_cat">&nbsp;</span></td>
			<td><input type="text" class="text" name="cam" id="cam_main" value="{MAINCUT}" size="4" maxlength="4" /></td>
			<td>&#123;INDEX_NEWS}</td>
			<td>news.tpl</td>
			<td></td>
		</tr>
		<tr>
			<td  class="coltop" colspan="6">{PHP.L.Addcat}</td>
		</tr>

		<!-- BEGIN: ADDITIONAL -->
		<tr id="cat_{ADDNUM}">
			<td>
				<input type="text" class="text" name="cay" id="cay_{ADDNUM}" value="{ADDCATEGORY}" size="32" maxlength="255" /><div class="cat_exists" style="color:red; display:none;"> &nbsp;  {PHP.L.Newscat_exists}</div>
			</td>
			<td><input type="text" class="text" name="cac" id="cac_{ADDNUM}" value="{ADDCOUNT}" size="3" maxlength="255" /></td>
			<td><input type="text" class="text" name="cam" id="cam_{ADDNUM}" value="{ADDCUT}" size="4" maxlength="4" /></td>
			<td class="cat_desc"><span id="cag_{ADDNUM}">&nbsp;</span></td>
			<td class="cat_desc"><span id="caf_{ADDNUM}">&nbsp;</span></td>
			<td colspan="2" class="cat_exists" style="color:red; display:none;">{PHP.L.Newscat_exists}</td>
			<td><a href="#" class="deloption">{PHP.R.admin_icon_delete}</a></td>
		</tr>
		<!-- END: ADDITIONAL -->
		<tr id="addtr">
			<td  style="text-align:right;" colspan="6"><input  name="addoption" value="{PHP.L.Add}" id="addoption" type="button" /></td>
		</tr>
	</table>

	<label><input type="checkbox" value="1" name="newsmaincac" {UNSETADD} />&nbsp; {PHP.L.Unsetadd}</label>
	<hr />
	{PHP.L.cfg_syncpagination.0}: &nbsp;  <span id="syncpag">&nbsp; </span>

	<div class="centerall" style="padding:10px;"><input type="submit" class="submit" value="{PHP.L.Update}" /></div>
	* {PHP.L.Newsautocutdesc} <br/>
	** {PHP.L.Template_help}
</div>
<!-- END: ADMIN -->
