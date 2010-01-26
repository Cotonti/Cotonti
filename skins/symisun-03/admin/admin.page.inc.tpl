<!-- BEGIN: PAGE -->
		<ul>
			<li>
				<a href="{ADMIN_PAGE_URL_CONFIG}">{PHP.L.Configuration} : <img src="images/admin/config.gif" alt="" /></a>
			</li>
			<li>
<!-- IF {PHP.lincif_page} -->
				<a href="{ADMIN_PAGE_URL_ADD}">{PHP.L.addnewentry}</a>
<!-- ELSE -->
				{PHP.L.addnewentry}
<!-- ENDIF -->
			</li>
			<li>
<!-- IF {PHP.lincif_page} -->
				<a href="{ADMIN_PAGE_URL_QUEUE}">{PHP.L.adm_valqueue} : {ADMIN_PAGE_QUEUE}</a>
<!-- ELSE -->
				{PHP.L.adm_valqueue} : {ADMIN_PAGE_QUEUE}
<!-- ENDIF -->
			</li>
			<li>
<!-- IF {PHP.lincif_conf} -->
				<a href="{ADMIN_PAGE_URL_STRUCTURE}">{PHP.L.adm_structure}</a>
<!-- ELSE -->
				{PHP.L.adm_structure}
<!-- ENDIF -->
			</li>
			<li>
<!-- IF {PHP.lincif_conf} -->
				<a href="{ADMIN_PAGE_URL_EXTRAFIELDS}">{PHP.L.adm_extrafields_desc}</a>
<!-- ELSE -->
				{PHP.L.adm_extrafields_desc}
<!-- ENDIF -->
			</li>
			<li>
<!-- IF {PHP.lincif_conf} -->
				<a href="{ADMIN_PAGE_URL_CATORDER}">{PHP.L.adm_sortingorder}</a>
<!-- ELSE -->
				{PHP.L.adm_sortingorder}
<!-- ENDIF -->
			</li>
			<li>
				{PHP.L.Pages} : {ADMIN_PAGE_TOTALDBPAGES} (<a href="{ADMIN_PAGE_URL_LIST_ALL}">{PHP.L.adm_showall}</a>)
			</li>
		</ul>
<!-- END: PAGE -->