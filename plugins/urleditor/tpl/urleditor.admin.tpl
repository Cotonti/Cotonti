<!-- BEGIN: MAIN -->
		<h2>{PHP.L.adm_urls_rules}</h2>
		{FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/warnings.tpl"}
<!-- BEGIN: HTA -->
		<h3>{PHP.L.adm_urls_your} {ADMIN_URLS_CONF_NAME}</h3>
		<pre class="code">{ADMIN_URLS_HTA}</pre>
<!-- END: HTA -->
	<!-- IF {PHP.cfg.jquery} -->
	<script type="text/javascript" src="js/jquery.tablednd.min.js"></script>
	<script type="text/javascript">
		$(document).ready(function(){
			$("#rules").tableDnD({});
			$("#rule_new").hide();
			$("#add_rule").show();
		});
		var ruleCount = 0;
		function addRule()
		{
			var object = $('#rule_new').clone().attr("id", 'rule_'+ruleCount).show();
			$('#rules_top').after(object);
			$("#rules").tableDnDUpdate();
			ruleCount++;
			return false;
		}
	</script>
	<!-- ENDIF -->
	<style type="text/css">tr.tDnD_whileDrag td{background-color:yellow;}</style>
	<form name="add_url" id="add_url" action="{ADMIN_URLS_FORM_URL}" method="post" class="ajax">
		<table id="rules" class="cells">
			<tr id="rules_top" class="nodrop nodrag">
				<td class="coltop width15">{PHP.L.adm_urls_area}</td>
				<td class="coltop width35">{PHP.L.adm_urls_parameters}</td>
				<td class="coltop width35">{PHP.L.adm_urls_format}</td>
				<td class="coltop width15">{PHP.L.Delete}</td>
			</tr>
<!-- BEGIN: ROW -->
			<tr id="rule_{ADMIN_URLS_ROW_I}">
				<td class="centerall">{ADMIN_URLS_ROW_AREAS}</td>
				<td class="centerall">{ADMIN_URLS_ROW_PARTS1}</td>
				<td class="centerall">{ADMIN_URLS_ROW_PARTS2}</td>
				<td class="centerall">
					<a title="{PHP.L.Delete}" href="#" onclick="$(this).parent().parent().remove();return false;">{PHP.L.Delete}</a>
				</td>
			</tr>
<!-- END: ROW -->
			<tr id="rule_new">
				<td class="centerall">{ADMIN_URLS_ROW_AREAS}</td>
				<td class="centerall">{ADMIN_URLS_ROW_PARTS1}</td>
				<td class="centerall">{ADMIN_URLS_ROW_PARTS2}</td>
				<td class="centerall">
					<a title="{PHP.L.Delete}" href="#" onclick="$(this).parent().parent().remove();return false;">{PHP.L.Delete}</a>
				</td>
			</tr>
			<tr id="add_rule" style="display:none;" class="nodrop nodrag">
				<td colspan="4">
					<script type="text/javascript">
						ruleCount = {ADMIN_URLS_II};
					</script>
					<a href="#" onclick="return addRule()"><strong>{PHP.L.adm_urls_new}</strong></a>
				</td>
			</tr>
			<!-- IF {PHP.htaccess} -->
			<tr>
				<td>
					<input type="checkbox" name="htaccess" /> <strong>{PHP.L.adm_urls_htaccess}</strong><br />
				</td>
				<td>
					{PHP.L.adm_urls_custom_htaccess}:
				</td>
				<td colspan="2">
					<textarea name="custom_htaccess" rows="8" cols="80">{ADMIN_URLS_CUSTOM_HTACCESS}</textarea>
				</td>
			</tr>
			<!-- ENDIF -->
			<tr>
				<td class="valid" colspan="4">
					<input type="submit" value="{PHP.L.adm_urls_save}" />
				</td>
			</tr>
		</table>
		</form>
<!-- END: MAIN -->