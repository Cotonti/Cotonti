<!-- BEGIN: MAIN -->

<h2>

    <a href="forums.php">{PHP.L.Pages}</a>

</h2>
<table class="cells">

    <tr>
        <td colspan="2" class="coltop">
        {PHP.L.Pages} </td>
        <td class="coltop" style="width:176px;">
        {PHP.L.Owner}</td>
        <td class="coltop" style="width:56px;">
        {PHP.L.Comments}</td>
        <td class="coltop" style="width:86px;">
        {PHP.L.Ratings}</td>

    </tr>

    <!-- BEGIN: PAGE_ROW -->

    <tr>
        <td style="width:32px;" class="centerall {PAGE_ROW_ODDEVEN}">
            {PAGE_ROW_CATICON}
        </td>

        <td class="{PAGE_ROW_ODDEVEN}">
            <strong><a href="{PAGE_ROW_URL}">{PAGE_ROW_SHORTTITLE}</a></strong><br />
            <div class="desc">{PAGE_ROW_CATPATH}</div>
            <div class="desc">{PAGE_ROW_DESC}</div>
        </td>


        <td class="centerall {PAGE_ROW_ODDEVEN}">
{PAGE_ROW_DATE} : {PAGE_ROW_OWNER}
        </td>

        <td class="centerall {PAGE_ROW_ODDEVEN}">
            {PAGE_ROW_COMMENTS}
        </td>

        <td class="centerall {PAGE_ROW_ODDEVEN}">
            {PAGE_ROW_RATINGS}
        </td>

    </tr>

    <!-- END: PAGE_ROW -->
    <!-- BEGIN: NO_PAGES_FOUND -->
    <tr>
        <td colspan="5">
            <div class="error">{PHP.L.Rec_forum_nonew}</div>
        </td>
    </tr>
    <!-- END: NO_PAGES_FOUND -->
</table>


<!-- END: MAIN -->