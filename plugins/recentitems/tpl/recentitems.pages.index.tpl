<!-- BEGIN: MAIN -->

<table class="cells">

    <tr>
        <td class="coltop">
        {PHP.L.Date}</td>
        <td class="coltop">
        {PHP.L.Category}</td>
    </tr>

    <!-- BEGIN: PAGE_ROW -->

    <tr>


        <td class="{PAGE_ROW_ODDEVEN}">
            {PAGE_ROW_DATE}
        </td>


        <td class="centerall {PAGE_ROW_ODDEVEN}">
            {PAGE_ROW_CATPATH_SHORT}
        </td>
    </tr>
    <tr>
        <td colspan="2" class="{PAGE_ROW_ODDEVEN}">
            <span style="float:right">{PAGE_ROW_COMMENTS}</span>{PAGE_ROW_SHORTTITLE}
        </td>


    </tr>

    <!-- END: PAGE_ROW -->
    <!-- BEGIN: NO_PAGES_FOUND -->
    <tr>
        <td colspan="2">
            <div class="error">{PHP.L.Rec_forum_nonew}</div>
        </td>
    </tr>
    <!-- END: NO_PAGES_FOUND -->
</table>


<!-- END: MAIN -->