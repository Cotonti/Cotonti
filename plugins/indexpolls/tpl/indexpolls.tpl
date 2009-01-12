<!-- BEGIN: POLL_VIEW -->
{POLL_FORM_BEGIN}
<table>
<!-- BEGIN: POLLTABLE -->
<tr><td><label>{POLL_INPUT}{POLL_OPTIONS}</label></td></tr>
<!-- END: POLLTABLE -->
<tr><td>{POLL_FORM_BUTTON}</td></tr></table>
{POLL_FORM_END}
<!-- END: POLL_VIEW -->

<!-- BEGIN: POLL_VIEW_VOTED -->
<table>
<!-- BEGIN: POLLTABLE -->
<tr><td>{POLL_OPTIONS}</td><td><div style="width:100px;"><div class="bar_back"><div class="bar_front" style="width:{POLL_PER}%;"></div></div></div></td><td>{POLL_PER}%</td><td>{POLL_COUNT}</td></tr>
<!-- END: POLLTABLE -->
</table>
<p>{POLL_VOTERS} {PHP.skinlang.polls.voterssince} {POLL_SINCE}</p>
<!-- END: POLL_VIEW_VOTED-->

<!-- BEGIN: POLL_VIEW_DISABLED -->
<table>
<!-- BEGIN: POLLTABLE -->
<tr><td>{POLL_OPTIONS}</td></tr>
<!-- END: POLLTABLE -->
<tr><td>{PHP.L.rat_registeredonly}</td></tr></table>
<!-- END: POLL_VIEW_DISABLED-->

