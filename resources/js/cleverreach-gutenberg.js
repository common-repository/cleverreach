;(function () {
    const {Fragment, createElement: el} = wp.element;
    const {BlockControls, InspectorControls, createBlock} = wp.blocks;

    wp.blocks.registerBlockType('cleverreach/block', {
        title: 'CleverReach',
        icon: 'email',
        category: 'common',
        description: haet_cr_ajax.translations.description,
        attributes: {},
        edit({attributes, isSelected, setAttributes}) {
            $.post(haet_cr_ajax.ajax_url, {'action': 'cleverreach_preview_form'}, function (response) {
                console.log(response);
                $('.cleverreach-preview-container').html(response);
            });


            return [
                isSelected && el(
                    InspectorControls,
                    {},
                    el('a', {
                        href: '/wp-admin/admin.php?page=cleverreach-forms',
                        target: '_blank'
                    }, haet_cr_ajax.translations.editform),
                ),
                el('div', {class: 'cleverreach-preview-container'}, haet_cr_ajax.translations.loading)
            ];
        },
        save() {
            return el('div', {}, '[cleverreach_signup]');

        }
    });

})
();
