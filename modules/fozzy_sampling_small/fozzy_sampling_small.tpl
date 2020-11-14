<div id = "{if $new_client == 1}new_client{else}old_client{/if}" class="col-xs-12 clearfix" style="padding: 0;">
<form action="/addgift" method="post">
<input type="hidden" name = "id_client" value="{$new_client}">
<input type="hidden" name = "id_cart" value="{$id_cart}">
<input id="submit_smile" type="submit" value="">
</form>
</div>


