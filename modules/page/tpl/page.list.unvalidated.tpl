<!-- BEGIN: MAIN -->
<div class="col3-2 first">
    <div class="block">
        <h2 class="folder">{LIST_CAT_TITLE}</h2>
        {FILE "{PHP.cfg.themes_dir}/{PHP.usr.theme}/warnings.tpl"}
        <table>
            <thead>
            <tr>
                <th>{PHP.L.Title}</th>
                <th>{PHP.L.Status}</th>
                <th>{PHP.L.Edit}</th>
            </tr>
            </thead>
            <tbody>
            <!-- BEGIN: LIST_ROW -->
            <tr>
                <td><strong>{LIST_ROW_TITLE}</strong></td>
                <td>{LIST_ROW_LOCAL_STATUS}</td>
                <td>{LIST_ROW_ADMIN_EDIT}</td>
            </tr>
            <!-- END: LIST_ROW -->
            </tbody>
        </table>
    </div>
    <!-- IF {LIST_PAGINATION} -->
    <p class="paging clear">
        <span>{PHP.L.Page} {LIST_CURRENT_PAGE} {PHP.L.Of} {LIST_TOTAL_PAGES}</span>
        {LIST_PREVIOUS_PAGE}{LIST_PAGINATION}{LIST_NEXT_PAGE}
    </p>
    <!-- ENDIF -->
</div>

<div class="col3-1">
    <!-- IF {PHP.usr.auth_write} -->
    <div class="block">
        <h2 class="admin">{PHP.L.Admin}</h2>
        <ul class="bullets">
            <!-- IF {PHP.usr.isadmin} -->
            <li><a href="{PHP|cot_url('admin')}">{PHP.L.Adminpanel}</a></li>
            <!-- ENDIF -->
            <li>{LIST_SUBMIT_NEW_PAGE}</li>
        </ul>
    </div>
    <!-- ENDIF -->
    <div class="block">
        <h2 class="tags">{PHP.L.Tags}</h2>
        {LIST_TAG_CLOUD}
    </div>
    {FILE "{PHP.cfg.themes_dir}/{PHP.theme}/inc/contact.tpl"}
</div>
<!-- END: MAIN -->