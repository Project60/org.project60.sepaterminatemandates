{crmScope extensionKey='org.project60.sepaterminatemandates'}
<div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="top"}
</div>
{if $action eq 8}
    {* Are you sure to delete form *}
  <h3>{ts}Delete Criteria for termination of mandates{/ts}</h3>
  <div class="crm-block crm-form-block">
    <div class="crm-section">{ts}Are you sure?{/ts}</div>
  </div>
{else}
  <h3>{ts}Search Criteria for mandate termination{/ts}</h3>
  <div class="crm-block crm-form-block">
    <div class="crm-section">
      <div class="label">{$form.description.label}</div>
      <div class="content">{$form.description.html}</div>
      <div class="clear"></div>
    </div>
    <div class="crm-section">
      <div class="label">{$form.is_active.label}</div>
      <div class="content">{$form.is_active.html}</div>
      <div class="clear"></div>
    </div>
  </div>
  <h3>{ts}Search Criteria{/ts}</h3>
  <div class="crm-block crm-form-block">
    <div class="crm-section">
      <div class="label">{ts}Cancelled contributions{/ts} <span class="crm-marker" title="{ts}This field is required.{/ts}">*</span></div>
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
  </div>
  <h3>{ts}Terminate mandate action{/ts}</h3>
  <div class="crm-block crm-form-block">
    <div class="help">
        {ts}Terminate the selected mandates please provide a reason for terminating those mandates
          and additionally specify the activity type and to who the activity should be assigned.
        {/ts}</div>
    <div class="crm-section">
      <div class="label">{$form.reason.label}</div>
      <div class="content">{$form.reason.html}</div>
      <div class="clear"></div>
    </div>
    <div class="crm-section">
      <div class="label">{$form.activity_type_id.label}</div>
      <div class="content">{$form.activity_type_id.html}</div>
      <div class="clear"></div>
    </div>
    <div class="crm-section">
      <div class="label">{$form.activity_status_id.label}</div>
      <div class="content">{$form.activity_status_id.html}</div>
      <div class="clear"></div>
    </div>
    <div class="crm-section">
      <div class="label">{$form.activity_assignee.label}</div>
      <div class="content">{$form.activity_assignee.html}</div>
      <div class="clear"></div>
    </div>
  </div>
{/if}
<div class="crm-submit-buttons">
  {include file="CRM/common/formButtons.tpl" location="bottom"}
</div>
{/crmScope}
