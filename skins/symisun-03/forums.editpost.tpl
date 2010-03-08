<!-- BEGIN: MAIN -->

			<div id="left" class="whitee" style="margin-right:23px;">

				<h2>{PHP.L.Edit} {PHP.L.Post}: {PHP.ft_title} @ {PHP.fs_title}</h2>

				<form action="{FORUMS_EDITPOST_SEND}" method="post">

				<div id="right" style="margin:0 0 0 18px;">

					<h3>{PHP.L.Publish}</h3>
					<div class="box padding15">
						<input type="submit" value="{PHP.L.Update}" class="submit" />
					</div>

					&nbsp;

				</div>

				<p class="breadcrumb">{PHP.skinlang.list.bread}: {FORUMS_EDITPOST_PAGETITLE}</p>

				<p class="details">{FORUMS_EDITPOST_SUBTITLE}</p>

				<!-- BEGIN: FORUMS_NEWTOPIC_ERROR -->
				<p class="error">{FORUMS_POSTS_EDITPOST_ERROR_BODY}</p>
				<!-- END: FORUMS_NEWTOPIC_ERROR -->

				<!-- IF {FORUMS_EDITPOST_TOP_TAGS} -->
				<fieldset>
					<legend>{PHP.skinlang.pageadd.basic}</legend>
				<!-- ENDIF -->
					<!-- BEGIN: FORUMS_EDITPOST_FIRSTPOST -->
					<div><label>{PHP.L.Title}</label>{FORUMS_EDITPOST_TOPICTITTLE}</div>
					<div><label>{PHP.L.Description}</label>{FORUMS_EDITPOST_TOPICDESCRIPTION}</div>
					<!-- END: FORUMS_EDITPOST_FIRSTPOST -->
					<!-- BEGIN: FORUMS_NEWTOPIC_TAGS -->
					<div>
						<label>{FORUMS_EDITPOST_TOP_TAGS}</label>
						{FORUMS_EDITPOST_FORM_TAGS} &nbsp; <span class="hint">{FORUMS_EDITPOST_TOP_TAGS_HINT}</span>
					</div>
					<!-- END: FORUMS_NEWTOPIC_TAGS -->
				<!-- IF {FORUMS_EDITPOST_TOP_TAGS} -->
				</fieldset>
				<!-- ENDIF -->

				<fieldset>
					<legend>{PHP.L.Text}</legend>
					<div style="padding:0 10px; margin-top:-15px" class="pageadd">
						<div style="width:100%;">{FORUMS_EDITPOST_TEXTBOXER}</div>
					</div>
				</fieldset>

				<!-- BEGIN: POLL -->
				<fieldset>
					<legend>{PHP.L.Poll}</legend>
					<div>
						<label>{PHP.L.Edit}</label>
						<input type="text" class="text" name="poll_text" value="{EDIT_POLL_TEXT}" size="64" maxlength="255" />
					</div>
					<div><label>{PHP.L.Options}</label>{EDIT_POLL_OPTIONS}</div>
					<div><label>{PHP.L.polls_multiple}</label>{EDIT_POLL_MULTIPLE}</div>
					<!-- BEGIN: EDIT -->
					<div><label>{PHP.L.Close}</label>{EDIT_POLL_CLOSE}</div>
					<div><label>{PHP.L.Reset}</label>{EDIT_POLL_RESET}</div>
					<div><label>{PHP.L.Delete}</label>{EDIT_POLL_DELETE}</div>
					<!-- END: EDIT -->
				</fieldset>
				<!-- END: POLL -->

			</form>

			</div>

		</div>

	</div>

	<br class="clear" />

<!-- END: MAIN -->