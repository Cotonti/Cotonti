<!-- BEGIN: FOOTER -->



</div>
<div id="messagebar">
				<a href="{PHP|cot_url('users','m=profile')}" class="strong">{PHP.usr.name} </a>
				<!-- IF {PHP.out.notices} -->
				| {PHP.out.notices}
				<!-- ENDIF -->
				<!-- IF {PHP.usr.messages} > 0 -->
				| <a href="{PHP|cot_url('pm')}" class="button">
					{PHP.L.home_newpms}: {PHP.usr.messages}
				</a>
				<!-- ENDIF -->
</div>
<div id="footer">
	<a href="http://cotonti.com" target="_blank" title="Cotonti {PHP.cfg.version}"><img src="{PHP.cfg.themes_dir}/admin/priori/img/cotonti.png" alt="Cotonti{PHP.cfg.version}" /></a>
</div>
{FOOTER_RC}
</body>
</html>
<!-- END: FOOTER -->