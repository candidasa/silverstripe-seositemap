<% if isList %>
<form name="input" action="{$Link}" method="post">
	<input type="submit" value="View Tree" />
</form>
<% else %>
<form name="input" action="{$Link}?list=1" method="post">
	<input type="submit" value="View List" />
</form>
<% end_if %>
$Sitetree