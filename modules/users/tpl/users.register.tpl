<!-- BEGIN: MAIN -->
<div class="block">
    <!-- IF {PHP.cfg.homebreadcrumb} -->
    <div class="breadcrumbs">{USERS_REGISTER_BREADCRUMBS}</div>
    <!-- ENDIF -->
    <h2 class="users">{USERS_REGISTER_TITLE}</h2>
    {FILE "{PHP.cfg.themes_dir}/{PHP.usr.theme}/warnings.tpl"}
    <form id="user-register" name="register" action="{USERS_REGISTER_SEND}" method="post" enctype="multipart/form-data">
        <table class="list">
            <tr>
                <td class="width30">{PHP.L.Username}:</td>
                <td class="width70">{USERS_REGISTER_USER} *</td>
            </tr>
            <tr>
                <td>{PHP.L.users_validemail}:</td>
                <td>
                    {USERS_REGISTER_EMAIL} *
                    <p class="small">{PHP.L.users_validemailhint}</p>
                </td>
            </tr>
            <tr>
                <td>{PHP.L.Password}:</td>
                <td>{USERS_REGISTER_PASSWORD} *</td>
            </tr>
            <tr>
                <td>{PHP.L.users_confirmpass}:</td>
                <td>{USERS_REGISTER_PASSWORDREPEAT} *</td>
            </tr>
            <!-- IF {PHP.cfg.captchamain|cot_plugin_active($this)} -->
            <tr>
                <td>{USERS_REGISTER_VERIFY_IMG}</td>
                <td>{USERS_REGISTER_VERIFY_INPUT} *</td>
            </tr>
            <!-- ENDIF -->
            <tr>
                <td colspan="2" class="valid">
                    <button type="submit">{PHP.L.Submit}</button>
                </td>
            </tr>
        </table>
    </form>
</div>
<!-- END: MAIN -->