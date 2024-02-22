<select class="block-selector" multiple="multiple" size="10" name="select_blocks_{$context_id}[]">    {if count($blocks_to_select) > 0}        {foreach from=$blocks_to_select item=block}            <option value="{$block.id}">{$block.title}</option>        {/foreach}    {else}        <option value="-1"></option>    {/if}</select><table class="selected-blocks" cellpadding="3px" cellspacing="0">    <colgroup width="150px"></colgroup>    <colgroup width="100px"></colgroup>    <colgroup width="50px"></colgroup>    <thead>    <tr>        <th>{$text_resources.selected_blocks_header}</th>        <th>{$text_resources.selected_blocks_position}</th>        <th>{$text_resources.selected_blocks_delete}</th>    </tr>    </thead>    <tbody>    {if count($selected_blocks) > 0}        {foreach from=$selected_blocks item=selected_block}            {assign var='class' value=''}            {if !$selected_block.published}                {assign var='class' value=' class="unpublished"'}            {/if}            <tr>                <td{$class}>{$selected_block.title}</td>                <td>{$selected_block.position_name}</td>                <td class="delete_column">{$selected_block.delete_field}</td>            </tr>        {/foreach}    {else}        <tr>            <td><em>{$text_resources.no_selected_blocks_found_message}</em></td>        </tr>    {/if}    </tbody></table>