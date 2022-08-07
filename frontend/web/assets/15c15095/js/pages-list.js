$(function() {
    var $checkboxes = $('input[name="pagesToDelete[]"]'),
        $deleteSelectedForm = $('.delete-selected'),
        $deleteSelectedBtn = $deleteSelectedForm.find('.delete-selected-btn');

    $checkboxes.on('change ifChanged', function() {
        $deleteSelectedForm.toggleClass('hidden', $checkboxes.filter(':checked').length <= 0);
    });

    $deleteSelectedBtn.on('click', function() {
        var confirmMessage = $deleteSelectedForm.data('confirm-message') || 'Are you sure you want to delete selected pages?';

        if (confirm(confirmMessage)) {
            $deleteSelectedForm.find('input[name="pagesToDelete[]"]').remove();
            $deleteSelectedForm.append($checkboxes.clone());
            $deleteSelectedForm.submit();
        }
    });
});