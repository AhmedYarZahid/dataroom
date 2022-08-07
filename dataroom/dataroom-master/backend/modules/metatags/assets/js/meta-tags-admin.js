$(function() {
    var $metaTagsDataInput = $('#metatags-data'),
        $metaTagsList = $('.metatags-list'),
        $form = $metaTagsList.closest('form'),
        $metaTagTemplate = $('#metatags-tpl'),
        metaTagsData;

    function loadExistingMetaTags() {
        try {
            metaTagsData = JSON.parse($metaTagsDataInput.val());

            if (!Array.isArray(metaTagsData)) {
                metaTagsData = [];
            }
        } catch (e) {
            metaTagsData = [];
        }

        metaTagsData.forEach(function(metaTagsItem) {
            var $metaTag = $($metaTagTemplate.html());

            $metaTag.find('.metatag-attr-name-input').val(metaTagsItem.attrName);
            $metaTag.find('.metatag-attr-value-input').val(metaTagsItem.attrValue);
            $metaTag.find('.metatag-content-input').val(metaTagsItem.content);

            $metaTagsList.append($metaTag);
        });
    }

    $('.add-meta-tag').on('click', function() {
        var $metaTag = $($metaTagTemplate.html()),
            metaTagHeight;

        $metaTagsList.append($metaTag);

        metaTagHeight = $metaTag.height();

        $metaTag.css({
            opacity: 0,
            height: 0
        });

        $metaTag.animate({
            opacity: 1,
            height: metaTagHeight
        }, 300);

        return false;
    });

    $metaTagsList.on('click', '.delete-meta-tag', function() {
        var $metaTag = $(this).closest('.metatag');

        $metaTag.animate({
            opacity: 0,
            height: 0
        }, 300, function() {
            $metaTag.remove();
        });

        return false;
    });

    $form.on('submit', function() {
        var data = [];

        $metaTagsList.children('.metatag').each(function() {
            var $this = $(this),
                attrName = String($this.find('.metatag-attr-name-input').val()).trim(),
                attrValue = String($this.find('.metatag-attr-value-input').val()).trim(),
                content = String($this.find('.metatag-content-input').val()).trim();

            if (attrName.length && attrValue.length && content.length) {
                data.push({
                    attrName: attrName,
                    attrValue: attrValue,
                    content: content
                });
            }
        });

        $metaTagsDataInput.val(JSON.stringify(data));
    });

    loadExistingMetaTags();
});