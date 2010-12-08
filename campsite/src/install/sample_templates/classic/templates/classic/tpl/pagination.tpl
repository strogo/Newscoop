<div id="pagination">
{{ if $sf->current_list->at_end && $sf->current_list->count > $sf->current_list->length }}
    {{ if $sf->current_list->has_previous_elements }}
      <a href="{{ uri options="previous_items template classic/search-result.tpl" }}">Previous</a>
    {{ else }}
      Previous
    {{ /if }}
    |
    {{ if $sf->current_list->has_next_elements }}
      <a href="{{ uri options="next_items template classic/search-result.tpl" }}">Next</a>
    {{ else }}
      Next
    {{ /if }}
{{ /if }}
</div>