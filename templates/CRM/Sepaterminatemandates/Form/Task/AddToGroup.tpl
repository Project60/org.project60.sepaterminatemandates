{crmScope extensionKey='org.project60.sepaterminatemandates'}
  <div class="crm-submit-buttons">
      {include file="CRM/common/formButtons.tpl" location="top"}
  </div>

  <h3>{ts}Add to group{/ts}</h3>
{if ($status)}
  <div class="messages status no-popup">
      {$status}
  </div>
{/if}
<div class="crm-block crm-form-block crm-contact-task-addtogroup-form-block">
  <table class="form-layout">
    <tr><td>{$form.group_option.html}</td></tr>
    <tr id="id_existing_group">
      <td>
        <table class="form-layout">
          <tr><td class="label">{$form.group_id.label}<span class="crm-marker">*</span></td><td>{$form.group_id.html}</td></tr>
        </table>
      </td>
    </tr>
    <tr id="id_new_group" class="html-adjust">
      <td>
        <table class="form-layout">
          <tr class="crm-contact-task-addtogroup-form-block-title">
            <td class="label">{$form.title.label}<span class="crm-marker">*</span></td>
            <td>{$form.title.html}</td>
          <tr>
          <tr class="crm-contact-task-addtogroup-form-block-description">
            <td class="label">{$form.description.label}</td>
            <td>{$form.description.html}</td></tr>
            {if $form.group_type}
              <tr class="crm-contact-task-addtogroup-form-block-group_type">
                <td class="label">{$form.group_type.label}</td>
                <td>{$form.group_type.html}</td>
              </tr>
            {/if}
        </table>
      </td>
    </tr>
  </table>
</div>

<div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="bottom"}
</div>

{literal}
  <script type="text/javascript">
    showElements();
    function showElements() {
      if ( document.getElementsByName('group_option')[0].checked ) {
        cj('#id_existing_group').show();
        cj('#id_new_group').hide();
      } else {
        cj('#id_new_group').show();
        cj('#id_existing_group').hide();
      }
    }
  </script>
{/literal}
{/crmScope}
