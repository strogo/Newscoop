<div id="genericform">
{{ subscription_form type="by_section" total="Total" template="subscription.tpl" button_html_code="class=\"submitbutton\"" }}
<table class="userform">
  <tr>
    <th colspan="2">{{ if $sf->language->name == "English" }}Please fill in the following form in order to create the subscription.{{ else }}Por favor, rellene el siguiente formulario con el fin de crear la suscripción.{{ /if }}</th>
  </tr>
  <tr>
    <td colspan="2">
      {{ if $sf->language->name == "English" }}Subscription time:{{ else }}Suscripción tiempo:{{ /if }}
      {{ $sf->publication->subscription_time }}
      {{ $sf->publication->subscription_time_unit }}
    </td>
  </tr>
  <tr>
    <td colspan="2">
      {{ camp_select object="subscription" attribute="alllanguages" }}
      <span class="formtext">{{ if $sf->language->name == "English" }}Subscribe to all languages{{ else }}Suscríbete a todos los idiomas{{ /if }}</span>
    </td>
  </tr>
  <tr>
    <td>{{ if $sf->language->name == "English" }}Languages:{{ else }}Idiomas{{ /if }}</td>
    <td>{{ camp_select object="subscription" attribute="languages" }}
  </tr>
  <tr>
    <td colspan="2">{{ if $sf->language->name == "English" }}Sections:{{ else }}Secciones{{ /if }}</td>
  </tr>
  {{ list_sections }}
  <tr>
    <td colspan="2">
      {{ camp_select object="subscription" attribute="section" }}
      <input name="tx_subs{{ $sf->section->number }}" type="hidden" value="{{ $sf->publication->subscription_time }}">
      {{ $sf->section->name }}
    </td>
  </tr>
  {{ /list_sections }}
</table>
<div align="center">{{ /subscription_form }}</div>
</div>