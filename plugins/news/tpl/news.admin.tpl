<!-- BEGIN: MAIN -->
<script src="{PHP.cfg.plugins_dir}/news/js/news.admin.js" type="text/javascript"></script>
<div id="catgenerator" style="display:none"> <div style="display:none">{MAINCATEGORY}</div>
	<table class="cells">
		<tr>
			<td class="coltop width30">{PHP.L.Category}</td>
			<td class="coltop width10">{PHP.L.NewsCount}</td>
			<td class="coltop width10">{PHP.L.Newsautocut} *</td>
			<td class="coltop width25">{PHP.L.Tag}</td>
			<td class="coltop width15">{PHP.L.Template} **</td>
			<td class="coltop width10">&nbsp;</td>
		</tr>
		<!-- BEGIN: ADDITIONAL -->
		<tr id="cat_{ADDNUM}" class="newscat">
			<td>
				<input type="text" class="text cay" name="cay" value="{ADDCATEGORY}" size="32" maxlength="255" />
				<div class="cat_exists" style="color:red; display:none;"> &nbsp; {PHP.L.Newscat_exists}</div>
			</td>
			<td><input type="text" class="text cac" name="cac" value="{ADDCOUNT}" size="3" maxlength="255" /></td>
			<td><input type="text" class="text cam" name="cam" value="{ADDCUT}" size="4" maxlength="4" /></td>
			<td class="cat_desc"><span class="cag">&nbsp;</span></td>
			<td class="cat_desc"><span class="caf">&nbsp;</span></td>
			<td colspan="2" class="cat_exists" style="color:red; display:none;">{PHP.L.Newscat_exists}</td>
			<td><a href="#" class="deloption" style="display:none">{PHP.R.admin_icon_delete}</a></td>
		</tr>
		<!-- END: ADDITIONAL -->
		<tr id="addtr">
			<td class="valid" colspan="6"><button name="addoption" id="addoption" type="button">{PHP.L.Add}</button></td>
		</tr>
	</table>

	<h2>{PHP.L.Settings}</h2>
	<table class="cells">
		<tr>
			<td class="coltop" style="width:75%;">{PHP.L.Parameter}</td>
			<td class="coltop" style="width:15%;">{PHP.L.Value}</td>
			<td class="coltop" style="width:10%;">{PHP.L.Reset}</td>
		</tr>
		<tr>
			<td>{PHP.L.cfg_syncpagination.0}:</td>
			<td class="centerall"><span id="syncpag"> &nbsp; </span></td>
			<td class="centerall">{PHP.R.icon_reset}</td>
		</tr>
		<tr>
			<td class="valid" colspan="3"><button type="submit">{PHP.L.Update}</button></td>
		</tr>
	</table>

	<p class="small">* {PHP.L.Newsautocutdesc}</p>
	<p class="small">** {PHP.L.Template_help}</p>
</div>

<!-- END: MAIN -->