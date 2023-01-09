{crmScope extensionKey='org.project60.sepaterminatemandates'}
<div class="crm-form-block crm-search-form-block">
  <div class="crm-accordion-wrapper crm-advanced_search_form-accordion {if (!empty($rows))}collapsed{/if}">
    <div class="crm-accordion-header crm-master-accordion-header">
        {ts}Search Criteria{/ts}
    </div>
    <!-- /.crm-accordion-header -->
    <div class="crm-accordion-body">
      <div id="searchForm" class="form-item">
        <div class="crm-section">
          <div class="label">{ts}Cancelled contributions{/ts}</div>
          <div class="content">
            {$form.cancelled_contributions_qty.html}
            {ts}in the last{/ts}
            {$form.cancelled_contributions_months.html}
            {ts}months{/ts}
          </div>
          <div class="clear"></div>
        </div>
        <div class="crm-section">
          <div class="label">{$form.cancelled_contribution_successive.label}</div>
          <div class="content">
              {$form.cancelled_contribution_successive.html}
          </div>
          <div class="clear"></div>
        </div>
        <div class="crm-section">
          <div class="label">{$form.campaign_ids.label}</div>
          <div class="content">
              {$form.campaign_ids.html}
          </div>
          <div class="clear"></div>
        </div>
        <div class="crm-section">
          <div class="label">{$form.cancel_reasons.label}</div>
          <div class="content">
              {$form.cancel_reasons.html}
          </div>
          <div class="clear"></div>
        </div>
        <div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl" location="botton"}</div>
      </div>
    </div>
  </div>
</div>

{if (isset($rows) && !empty($rows))}
<div class="crm-content-block">
  <div class="crm-results-block">
    {if ($taskMetaData)}
      <div class="crm-search-tasks">{include file="CRM/common/searchResultTasks.tpl"}</div>
    {/if}
    {include file="CRM/common/pager.tpl" location="top"}

    <div class="crm-search-results">
      {if $taskMetaData}<a href="#" class="crm-selection-reset crm-hover-button"><i class="crm-i fa-times-circle-o"></i> {ts}Reset all selections{/ts}</a>{/if}
      <table class="selector row-highlight">
        <thead><tr>
          {if $taskMetaData}<th scope="col" title="Select Rows" style="{if $sticky_header}position: sticky; top: 35px;{/if}">{$form.toggleSelect.html}</th>{/if}
          <th scope="col" style="{if $sticky_header}position: sticky; top: 35px;{/if}">{ts}ID{/ts}</th>
          <th scope="col" style="{if $sticky_header}position: sticky; top: 35px;{/if}">{ts}Contact{/ts}</th>
          <th scope="col" style="{if $sticky_header}position: sticky; top: 35px;{/if}">{ts}Start date{/ts}</th>
          <th scope="col" style="{if $sticky_header}position: sticky; top: 35px;{/if}">{ts}Campaign{/ts}</th>
          <th scope="col" style="{if $sticky_header}position: sticky; top: 35px;{/if}">{ts}Reference{/ts}</th>
          <th scope="col" style="{if $sticky_header}position: sticky; top: 35px;{/if}">{ts 1=$cancelledContributionsMonths}Contributions (in last %1 months){/ts}</th>
          <th scope="col" style="">&nbsp;</th>
        </tr></thead>
        {foreach from=$rows item=row}
          <tr id='rowid{$row.id}' class="{cycle values="odd-row,even-row"}">
            {assign var=cbName value=$row.checkbox}
            {if $taskMetaData}<td>{$form.$cbName.html}</td>{/if}
            <td>{$row.id}</td>
            <td>{$row.contact}</td>
            <td>
              {$row.start_date|crmDate}
            </td>
            <td>
              {$row.campaign}
            </td>
            <td>
              {$row.reference}
            </td>
            <td>
              {foreach from=$row.contributions item=contribution name=contributionForEach}
                  <span style="{if $contribution.is_cancelled}color: red;{/if}">{$contribution.receive_date|crmDate} - {$contribution.status}</span> {if $contribution.cancel_reason}({$contribution.cancel_reason}){/if}
                  {if !$smarty.foreach.contributionForEach.last}<br />{/if}
              {/foreach}
            </td>
            <td>
                {if ($row.url)}
                  <a href="{$row.url}">
                      {$row.link_text}
                  </a>
                {/if}
            </td>
          </tr>
        {/foreach}

      </table>
    </div>

  {include file="CRM/common/pager.tpl" location="bottom"}

  </div>
</div>
{/if}
{/crmScope}
