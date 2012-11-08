<!-- BEGIN: MAIN -->
<script src="{PHP.cfg.plugins_dir}/news/js/news.admin.js" type="text/javascript"></script>
<div id="catgenerator"> <div style="display:none">{MAINCATEGORY}</div>
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
		<tr class="newscat">
			<td>
				<input type="text" class="text cay" name="cay" value="{ADDCATEGORY}" size="32" maxlength="255" />
			</td>
			<td><input type="text" class="text cac" name="cac" value="{ADDCOUNT}" size="3" maxlength="255" /></td>
			<td><input type="text" class="text cam" name="cam" value="{ADDCUT}" size="4" maxlength="4" /></td>
			<td class="cat_desc"><span class="cag">&nbsp;</span></td>
			<td class="cat_desc"><span class="caf">&nbsp;</span></td>
			<td colspan="2" class="cat_exists" style="color:red; display:none;">{PHP.L.Newscat_exists}</td>
			<td><button name="deloption" class="deloption" type="button"  style="display:none">{PHP.L.Delete}</button></td>
		</tr>
		<!-- END: ADDITIONAL -->
		<tr id="addtr">
			<td class="valid" colspan="6"><button name="addoption" id="addoption" type="button">{PHP.L.Add}</button></td>
		</tr>
	</table>
	<p class="small">* {PHP.L.Newsautocutdesc}</p>
	<p class="small">** {PHP.L.Template_help}</p><br />
</div>

<!-- END: MAIN -->