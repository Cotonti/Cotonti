<!-- BEGIN: MAIN -->

<form action="admin.php?m=other&p=userimages&a=add" method="post">
<table class="cells">
	<thead>
		<tr>
			<th>{PHP.L.Code}</th>
			<th style="width:15%;">{PHP.L.Width}</th>
			<th style="width:15%;">{PHP.L.Height}</th>
			<th style="width:25%;">{PHP.L.CropRatio}</th>
			<th style="width:5%;"></th>
		</tr>
	</thead>
	<tbody>
		<!-- BEGIN: USERIMG_LIST -->
		<tr>
			<td>{CODE}</td>
			<td>{WIDTH} px</td>
			<td>{HEIGHT} px</td>
			<td>{CROP}</td>
			<td>{REMOVE}</td>
		</tr>
		<!-- END: USERIMG_LIST -->
		<tr>
			<td><strong>{PHP.L.AddNew}:</strong> <input type="text" name="userimg_code" size="40" /></td>
			<td><input type="text" name="userimg_width" size="10" /> px</td>
			<td><input type="text" name="userimg_height" size="10" /> px</td>
			<td><select name="userimg_crop">
				<option value="">{PHP.L.CropNone}</option>
				<option value="fit">{PHP.L.CropFit}</option>
				<option value="1:1">1:1</option>
				<option value="1:2">1:2</option>
				<option value="2:3">2:3</option>
				<option value="3:4">3:4</option>
				<option value="4:5">4:5</option>
				<option value="5:6">5:6</option>
				<option value="2:1">2:1</option>
				<option value="3:2">3:2</option>
				<option value="4:3">4:3</option>
				<option value="5:4">5:4</option>
				<option value="6:5">6:5</option>
				<option value="16:9">16:9</option>
				<option value="16:10">16:10</option>
			</select></td>
			<td><button type="submit">{PHP.L.Add}</button></td>
		<tr>
	</tbody>
</table>
</form>

<!-- END: MAIN -->