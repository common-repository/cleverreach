var haet_cleverreach = haet_cleverreach || {};

// ********************************************
// Serialize form builder
haet_cleverreach.serialize_form_fields = function () {
    var $ = jQuery;
    var used_ids = $('#haet_cleverreach_formfields_used').sortable("toArray");
    var used = new Array();
    for (var i = 0; i < used_ids.length; i++) {
        var $attribute = $('#' + used_ids[i]);
        var type = $attribute.data('type');
        var label;
        if (type == 'description')
            label = $attribute.find('.field-description textarea').val();
        else label = $attribute.find('.field-label input').val();
        var required = $attribute.find('.field-required input').is(':checked');
        used.push({
            'field': $attribute.data('key'),
            'label': label,
            'type': type,
            'options': ($attribute.hasClass('type-gender') ? $attribute.find('.field-options textarea').val() : ''),
            'attribute_name': $attribute.find('.attribute-name').html(),
            'required': required
        });
    }
    var available_ids = $('#haet_cleverreach_formfields_available').sortable("toArray");
    var available = new Array();
    for (var i = 0; i < available_ids.length; i++) {
        var $attribute = $('#' + available_ids[i]);
        var type = $attribute.data('type');
        var label;
        if (type == 'description')
            label = $attribute.find('.field-description textarea').val();
        else label = $attribute.find('.field-label input').val();
        available.push({
            'field': $attribute.data('key'),
            'label': label,
            'type': type,
            'options': ($attribute.hasClass('type-gender') ? $attribute.find('.field-options textarea').val() : ''),
            'attribute_name': $attribute.find('.attribute-name').html()
        });
    }
    $('input[name="cleverreach_newsletter_settings[form_attributes_used]"]').val(JSON.stringify(used));
    $('input[name="cleverreach_newsletter_settings[form_attributes_available]"]').val(JSON.stringify(available));
}
// END: Serialize form builder
// ********************************************

// ********************************************
// UNSerialize form builder
haet_cleverreach.unserialize_form_fields = function () {
    var $ = jQuery;
    var used = $('input[name="cleverreach_newsletter_settings[form_attributes_used]"]').val();
    if (used.length > 0)
        used = JSON.parse(used);
    var available = $('input[name="cleverreach_newsletter_settings[form_attributes_available]"]').val();
    if (available.length > 0)
        available = JSON.parse(available);
    for (var i = 0; i < used.length; i++) {
        $('#haet_cleverreach_formfields_used').append(haet_cleverreach.get_attribute_sortable_html(used[i]));
    }
    for (var i = 0; i < available.length; i++) {
        $('#haet_cleverreach_formfields_available').append(haet_cleverreach.get_attribute_sortable_html(available[i]));
    }
}
// END: UNSerialize form builder
// ********************************************

// ********************************************
// get html code for form builder element
haet_cleverreach.get_attribute_sortable_html = function (attribute) {
    var $ = jQuery;
    var html =
        '<li id="cleverreach-attribute-' + attribute.field + '" data-key="' + attribute.field + '" data-type="' + attribute.type + '" class="attribute clearfix type-' + attribute.type + '">' +
        '<span class="attribute-name">' + attribute.attribute_name +
        '</span>';
    if (attribute.type == 'text' || attribute.type == 'email' || attribute.type == 'submit' || attribute.type == 'gender' || attribute.type == 'policy_confirm' || attribute.type == 'number' || attribute.type == 'date') {
        html +=
            '<div class="field-label">' +
            '<label>' + haet_cr_ajax.translations.label + '</label>' +
            '<input type="text" value="' + attribute.label.replace(/\"/g, "'") + '">' +
            '</div>';
    }


    if (attribute.type == 'description') {
        html +=
            '<div class="field-description">' +
            '<label>' + haet_cr_ajax.translations.text + '</label>' +
            '<textarea>' + attribute.label + '</textarea>' +
            '</div>';
    }

    if (attribute.type == 'gender') {
        html +=
            '<div class="field-options">' +
            '<label>' + haet_cr_ajax.translations.available_options + '</label>' +
            '<textarea>' + attribute.options + '</textarea>' +
            '</div>';
    }
    if (attribute.type == 'text' || attribute.type == 'email' || attribute.type == 'gender' || attribute.type == 'policy_confirm' || attribute.type == 'number' || attribute.type == 'date') {
        html +=
            '<div class="field-required">' +
            '<input type="checkbox" name="' + attribute.field + '-required" id="' + attribute.field + '-required" value="1" ' + (attribute.type == 'email' || attribute.type == 'policy_confirm' ? 'checked disabled' : (attribute.required ? 'checked' : '')) + '>' +
            '<label for="' + attribute.field + '-required">' + haet_cr_ajax.translations.required + '</label>' +
            '</div>';
    }

    html += '</li>';
    return html;
}
// END: get html code for form builder element
// ********************************************


jQuery(document).ready(function ($) {

    $('#haet_cleverreach_formfields_available, #haet_cleverreach_formfields_used').sortable({
        connectWith: '.connected-sortable',
        stop: function (event, ui) {
            haet_cleverreach.serialize_form_fields();
        }
    });

    $('li.attribute input, li.attribute textarea').on('change', function () {
        haet_cleverreach.serialize_form_fields();
    });

    $( "#cleverreach_newsletter_builder_form" ).submit(function( event ) {
        haet_cleverreach.serialize_form_fields();
    });

    if ($('#haet_cleverreach_formfields_available, #haet_cleverreach_formfields_used').length) {
        if ($('#haet_cleverreach_formfields_available li.attribute').length) {
            $('input[name="cleverreach_newsletter_settings[form_attributes_used]"]').val('');
            $('input[name="cleverreach_newsletter_settings[form_attributes_available]"]').val('');
        }
        haet_cleverreach.unserialize_form_fields();
    }

    $('input[name="cleverreach_newsletter_settings[show_comments_section_checkbox]"]').change(function () {
        var disabled = true;
        if ($('input[name="cleverreach_newsletter_settings[show_comments_section_checkbox]"]:checked').val() == "1")
            disabled = false;


        $('input[name="cleverreach_newsletter_settings[caption_for_comments_section_checkbox]"]').prop('disabled', disabled);
        $('select[name^="cleverreach_newsletter_settings[selected_group_and_form"]').prop('disabled', disabled);
        $('select[name^="cleverreach_newsletter_settings[selected_name_attribute"]').prop('disabled', disabled);
        $('input[name="cleverreach_newsletter_settings[show_comments_section_checkbox_default]"]').prop('disabled', disabled);

    });
    $('input[name="cleverreach_newsletter_settings[show_comments_section_checkbox]"]').change();

    $('select[name^="cleverreach_newsletter_settings[selected_group_and_form"]').change(function () {
        $('select[name^="cleverreach_newsletter_settings[selected_name_attribute"]').prop('disabled', true);
        $('.list-change-notice').slideDown(400);
    });

}); 


