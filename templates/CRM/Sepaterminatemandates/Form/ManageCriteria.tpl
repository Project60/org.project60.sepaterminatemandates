{crmScope extensionKey='org.project60.sepaterminatemandates'}
<div class="crm-content-block">
  <div class="action-link">
    <a class="button" href="{crmURL p="civicrm/terminatemandate/edit" q="reset=1&action=add" }">
      <i class="crm-i fa-plus-circle">&nbsp;</i>
        {ts}Add Criteria to terminate mandates{/ts}
    </a>
  </div>

  <div class="clear"></div>
  <div class="crm-results-block">
    <div class="crm-search-results">
      {if !empty($rows)}
      <table class="selector row-highlight">
        <thead class="sticky">
        <tr>
          <th scope="col" >{ts}Description{/ts}</th>
          <th scope="col" >{ts}Is active{/ts}</th>
          <th>&nbsp;</th>
        </tr>
        </thead>
          {foreach from=$rows item=row}
            <tr>
              <td>{$row.description}</td>
              <td>
                <span>
                {if $row.is_active eq 1}
                  <a href="{crmURL p='civicrm/terminatemandate/edit' q="reset=1&action=disable&id=`$row.id`"}" class="" title="{ts}Disable{/ts}">{ts}Enabled{/ts}</a>
                {else}
                  <a href="{crmURL p='civicrm/terminatemandate/edit' q="reset=1&action=enable&id=`$row.id`"}" class="" title="{ts}Enable{/ts}">{ts}Disabled{/ts}</a>
                {/if}
                </span>
              </td>
              <td>
                <span>
                  <a href="{crmURL p='civicrm/terminatemandate/edit' q="reset=1&action=update&id=`$row.id`"}" class="action-item crm-hover-button" title="{ts}Edit Criteria{/ts}">{ts}Edit{/ts}</a>
                  <a href="{crmURL p='civicrm/terminatemandate/edit' q="reset=1&action=delete&id=`$row.id`"}" class="action-item crm-hover-button" title="{ts}Delete Criteria{/ts}">{ts}Delete{/ts}</a>
                </span>
              </td>
            </tr>
          {/foreach}
      </table>
      {else}
        <div class="messages status no-popup"><i aria-hidden="true" class="crm-i fa-info-circle"></i>&nbsp;{ts}No criteria found{/ts}</div>
      {/if}
    </div>
  </div>
</div>
{/crmScope}
