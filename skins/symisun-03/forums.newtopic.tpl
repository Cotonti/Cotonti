<!-- BEGIN: MAIN -->

			<div id="left" class="whitee" style="margin-right:23px;">

				<h2>{PHP.skinlang.forumstopics.Newtopic} @ {PHP.fs_title}</h2>

				<form action="{FORUMS_NEWTOPIC_SEND}" method="post">

				<div id="right" style="margin:0 0 0 18px;">

					<!-- BEGIN: PRIVATE -->
					<h3>{PHP.L.Private}</h3>
					<div class="box padding15">
						<p>{PHP.L.Yes} {FORUMS_NEWTOPIC_ISPRIVATE}<br />
						<span class="hint">{PHP.skinlang.forumsnewtopic.privatetopic1}. {PHP.skinlang.forumsnewtopic.privatetopic2}</span></p>
					</div>
					<!-- END: PRIVATE -->

					<h3>{PHP.L.Publish}</h3>
					<div class="box padding15">
						<input type="submit" value="{PHP.L.Submit}" class="submit" />
					</div>

					&nbsp;

				</div>

				<p class="breadcrumb">{PHP.skinlang.list.bread}: {FORUMS_NEWTOPIC_PAGETITLE}</p>

				<p class="details">{FORUMS_NEWTOPIC_SUBTITLE}</p>

				<!-- BEGIN: FORUMS_NEWTOPIC_ERROR -->
				<p class="error">{FORUMS_NEWTOPIC_ERROR_BODY}</p>
				<!-- END: FORUMS_NEWTOPIC_ERROR -->

				<fieldset>
					<legend>{PHP.skinlang.pageadd.basic}</legend>
					<div><label>{PHP.L.Title}</label>{FORUMS_NEWTOPIC_TITLE}</div>
					<div><label>{PHP.L.Description}</label>{FORUMS_NEWTOPIC_DESC}</div>
					<!-- BEGIN: FORUMS_NEWTOPIC_TAGS -->
					<div>
						<label>{FORUMS_NEWTOPIC_TOP_TAGS}</label>
						{FORUMS_NEWTOPIC_FORM_TAGS} &nbsp; <span class="hint">{FORUMS_NEWTOPIC_TOP_TAGS_HINT}</span>
					</div>
					<!-- END: FORUMS_NEWTOPIC_TAGS -->
				</fieldset>

				<fieldset>
					<legend>{PHP.L.Text}</legend>
					<div style="padding:0 10px; margin-top:-15px" class="pageadd">
						<div style="width:100%;">{FORUMS_NEWTOPIC_TEXTBOXER}</div>
					</div>
				</fieldset>

				<!-- BEGIN: POLL -->
				<fieldset>
					<legend>{PHP.L.Poll}</legend>
					<div>
						<label>{PHP.L.Edit}</label>
						{EDIT_POLL_IDFIELD}{EDIT_POLL_TEXT}
					</div>
					<div><label>{PHP.L.Options}</label>{EDIT_POLL_OPTIONS}</div>
					<div>{EDIT_POLL_MULTIPLE}</div>
					<!-- BEGIN: EDIT -->
					<div>{EDIT_POLL_CLOSE}</div>
					<div>{EDIT_POLL_RESET}</div>
					<div>{EDIT_POLL_DELETE}</div>
					<!-- END: EDIT -->
				</fieldset>
				<!-- END: POLL -->

			</form>

			</div>

		</div>

	</div>

	<br class="clear" />

<!-- END: MAIN -->