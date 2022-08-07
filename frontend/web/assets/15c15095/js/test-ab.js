$(function () {
    $('#test-ab-form').on('submit', function () {
        var versionA = $('.revisions-item:has(.rev-from-to)').eq(0).data() || {},
            versionB = $('.revisions-item:has(.rev-from-to)').eq(1).data() || {};

        $('#revisionAID').val(versionA.revisionId);
        $('#revisionBID').val(versionB.revisionId);
    });
});