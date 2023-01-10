{crmScope extensionKey='org.project60.sepaterminatemandates'}
  <div class="crm-submit-buttons">
      {include file="CRM/common/formButtons.tpl" location="top"}
  </div>

  <h3>{ts}Terminate mandates{/ts}</h3>
{if ($status)}
  <div class="messages status no-popup">
      {$status}
  </div>
{/if}
  <div class="crm-block crm-form-block crm-terminatemandates-configuration-block">
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

  <div class="crm-submit-buttons">
      {include file="CRM/common/formButtons.tpl" location="bottom"}
  </div>
{/crmScope}
