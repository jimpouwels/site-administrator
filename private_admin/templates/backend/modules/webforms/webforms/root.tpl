<div class="content_left_column">
	{$list}
</div>
{if isset($metadata_editor)}
	<div class="content_right_column">
		{$metadata_editor}
		{$webform_editor}
	</div>
{/if}

<form id="add_form_hidden" class="displaynone" method="post" action="{$backend_base_url}">
	<input id="add_webform_action" name="add_webform_action" type="hidden" value="" />
</form>
