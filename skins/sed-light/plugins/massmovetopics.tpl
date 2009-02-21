<!-- BEGIN: MAIN -->
<!-- BEGIN: MASSMOVETOPICS_MOVE_DONE -->
	<div class="error">{PHP.L.adm_done}</div>
<!-- END: MASSMOVETOPICS_MOVE_DONE -->
	<form id="massmovetopics" action="{MASSMOVETOPICS_FORM_URL}" method="post">
		Move all the topics and posts from the section :
		<select name="sourceid">
<!-- BEGIN: MASSMOVETOPICS_SELECT_SOURCE -->
			<option value="{MASSMOVETOPICS_SELECT_SOURCE_FS_ID}">{MASSMOVETOPICS_SELECT_SOURCE_NAME}</option>
<!-- END: MASSMOVETOPICS_SELECT_SOURCE -->
		</select>
		<br />
		&nbsp;
		<br />
		... to the section :
		<select name="targetid">
<!-- BEGIN: MASSMOVETOPICS_SELECT_TARGET -->
			<option value="{MASSMOVETOPICS_SELECT_TARGET_FS_ID}">{MASSMOVETOPICS_SELECT_TARGET_NAME}</option>
<!-- END: MASSMOVETOPICS_SELECT_TARGET -->
		</select>
		<br />
		&nbsp;
		<br />
		<input type="submit" class="submit" value="{PHP.L.Move}" />
	</form>
<!-- END: MAIN -->