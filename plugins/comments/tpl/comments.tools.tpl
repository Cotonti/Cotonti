<!-- BEGIN: MAIN -->
<div class="block button-toolbar">
	<a href="{ADMIN_COMMENTS_CONFIG_URL}" class="button">{PHP.L.Configuration}</a>
</div>

{FILE "{PHP.cfg.system_dir}/admin/tpl/warnings.tpl"}

<div class="block">
	<table class="cells">
		<tr>
			<td class="coltop w-5">#</td>
			<td class="coltop w-5">{PHP.L.adm_area}</td>
			<td class="coltop w-5">{PHP.L.Code}</td>
			<td class="coltop w-15">{PHP.L.Author}</td>
			<td class="coltop w-15">{PHP.L.Date}</td>
			<td class="coltop w-35">{PHP.L.comments_comment}</td>
			<td class="coltop w-20">{PHP.L.Action}</td>
		</tr>
		<!-- BEGIN: ADMIN_COMMENTS_ROW -->
		<tr class="comment-row">
			<td class="textcenter">{ADMIN_COMMENTS_ITEM_ID}</td>
			<td class="textcenter">{ADMIN_COMMENTS_AREA}</td>
			<td class="textcenter">{ADMIN_COMMENTS_CODE}</td>
			<td class="textcenter">{ADMIN_COMMENTS_AUTHOR}</td>
			<td class="textcenter">{ADMIN_COMMENTS_DATE}</td>
			<td>
				<div class="comment-text closed">{ADMIN_COMMENTS_TEXT}</div>
				<div class="textright">
					<a href="#" class="comment-text-toggle" style="display:none">[+]</a>
				</div>
			</td>
			<td class="centerall action">
				<a href="{ADMIN_COMMENTS_URL}" class="button special">{PHP.L.Open}</a>
				<a href="{ADMIN_COMMENTS_ITEM_DEL_URL}" class="button ajax">{PHP.L.Delete}</a>
			</td>
		</tr>
		<!-- END: ADMIN_COMMENTS_ROW -->
		<!-- IF !{TOTAL_ENTRIES} -->
		<tr>
			<td class="centerall" colspan="7">{PHP.L.None}</td>
		</tr>
		<!-- ENDIF -->
	</table>
	<!-- IF {TOTAL_ENTRIES} -->
	<p class="paging">
		{PREVIOUS_PAGE}{PAGINATION}{NEXT_PAGE}
		<span>{PHP.L.Total}: {TOTAL_ENTRIES}, {PHP.L.Onpage}: {ENTRIES_ON_CURRENT_PAGE}</span>
	</p>
	<!-- ENDIF -->
</div>
<style>
	.comment-text {
		max-height: 1200px;
		transition: height ease-in-out 0.2s, max-height ease-in-out 0.2s;
		height: auto;
		overflow-y: hidden;
	}
	.comment-text.closed {
		max-height: 110px;
	}
	.comment-text-toggle {

	}
</style>
<script>
	setTimeout(function () {
		const toggles = document.querySelectorAll('.comment-text-toggle');
		for (let elem of toggles) {
			let textContainer = elem.closest('.comment-row').querySelector('.comment-text');
			if (textContainer.clientHeight < 110) {
				elem.remove();
				continue;
			}

			elem.style.display = '';

			elem.addEventListener('click', (e) => {
				e.preventDefault();
				console.log(e);
				if (textContainer.classList.contains('closed')) {
					e.target.innerHTML = '[-]';
				} else {
					e.target.innerHTML = '[+]';
				}
				textContainer.classList.toggle('closed');
			});
		}
	}, 500);
</script>
<!-- END: MAIN -->