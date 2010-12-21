<!-- BEGIN: MAIN -->
<!-- BEGIN: MASSMOVETOPICS_MOVE_DONE -->
	<div class="error">{PHP.L.adm_done}</div>
<!-- END: MASSMOVETOPICS_MOVE_DONE -->
	<form id="massmovetopics" action="{MASSMOVETOPICS_FORM_URL}" method="post">
	<div class="block">
		<h2 class="forums">Mass move topics in forums</h2>
		<table class="cells">
			<tr>
				<td class="width30">Move all the topics and posts from the section:</td>
				<td class="width70">
					<select name="sourceid">
<!-- BEGIN: MASSMOVETOPICS_SELECT_SOURCE -->
						<option value="{MASSMOVETOPICS_SELECT_SOURCE_FS_ID}">{MASSMOVETOPICS_SELECT_SOURCE_NAME}</option>
<!-- END: MASSMOVETOPICS_SELECT_SOURCE -->
					</select>
				</td>
			<tr>
				<td>... into the section:</td>
				<td>
					<select name="targetid">
<!-- BEGIN: MASSMOVETOPICS_SELECT_TARGET -->
						<option value="{MASSMOVETOPICS_SELECT_TARGET_FS_ID}">{MASSMOVETOPICS_SELECT_TARGET_NAME}</option>
<!-- END: MASSMOVETOPICS_SELECT_TARGET -->
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="2" class="valid"><button type="submit" class="submit">{PHP.L.Move}</button></td>
			</tr>
		</table>
	</div>
	</form>
<!-- END: MAIN -->