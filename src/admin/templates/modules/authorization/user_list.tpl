<div class="user_tree">
    {if count($users) > 0}
        <ul>
            {foreach from=$users item=user}
                {if $user.is_current}
                    <strong>
                {/if}
                <li class="user_list_item">
                    <a title="{$user.fullname}" href="{$backend_base_url}&user={$user.id}">{$user.fullname}</a>
                </li>
                {if $user.is_current}
                    </strong>
                {/if}
            {/foreach}
        </ul>
    {/if}
</div>
