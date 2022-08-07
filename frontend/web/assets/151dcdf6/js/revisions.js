$(function() {
    var $revisionsWrapper = $('.revisions-wrapper'),
        revisionsNum = $revisionsWrapper.data('revisionsNum');

    function init() {
        var $revisionItems = $('.revisions-item');

        $('[data-toggle="popover"]').popover();

        $('.rev-from-to').draggable({
            opacity: 0.5
        });

        $revisionItems.droppable({
            accept: '.rev-from-to',
            hoverClass: 'active',
            drop: function(e, ui) {
                var $this = $(this);

                if ($this.find('.rev-from-to').length === 0) {
                    $(ui.draggable).appendTo(this);

                    renderDiff();
                }
            }
        });

        $revisionItems.on('click', function() {
            var $this = $(this),
                thisIndex = $this.data('revisionIndex'),
                activeIndexFrom = getRevFromData().revisionIndex,
                activeIndexTo = getRevToData().revisionIndex;

            if ($this.find('.rev-from-to').length) {
                return;
            }

            if (Math.abs(thisIndex - activeIndexFrom) < Math.abs(thisIndex - activeIndexTo)) {
                $this.append(getRevFromElement().find('.rev-from-to'));
            } else {
                $this.append(getRevToElement().find('.rev-from-to'));
            }

            renderDiff();
        });

        $('.revisions-info .btn').on('click', function() {
            var $this = $(this);

            if ($this.hasClass('disabled')) {
                return;
            }

            if ($this.closest('.rev-from').length) {
                restoreRevision(getRevFromData());
            } else {
                restoreRevision(getRevToData());
            }
        });

        renderDiff();
    }

    function getRevFromElement() {
        return $('.revisions-item:has(.rev-from-to)').eq(0);
    }

    function getRevToElement() {
        return $('.revisions-item:has(.rev-from-to)').eq(1);
    }

    function getRevFromData() {
        return getRevFromElement().data();
    }

    function getRevToData() {
        return getRevToElement().data();
    }

    function restoreRevision(revisionData) {
        if (confirm('Are you sure you want to restore the revision made by '
            + revisionData.revisionUser + ' on ' + revisionData.revisionDate)
        ) {
            window.location.href = 'restore?id=' + revisionData.revisionId;
        }
    }

    function updateRevisionInfo($revInfo, revisionData) {
        $revInfo.find('.rev-user').html(revisionData.revisionUser);
        $revInfo.find('.rev-date').html(revisionData.revisionDate);

        $revInfo.find('.btn').toggleClass('disabled', revisionData.revisionIndex >= revisionsNum - 1);
    }

    function updateRevisionsInfo() {
        updateRevisionInfo($('.rev-from'), getRevFromData());
        updateRevisionInfo($('.rev-to'), getRevToData());
    }

    function getDiff(text1, text2) {
        var sm;

        text1 = difflib.stringAsLines($('<textarea/>').html(text1).text());
        text2 = difflib.stringAsLines($('<textarea/>').html(text2).text());

        sm = new difflib.SequenceMatcher(text1, text2);

        return diffview.buildView({
            baseTextLines: text1,
            newTextLines: text2,
            opcodes: sm.get_opcodes(),
            baseTextName: 'Rev 1',
            newTextName: 'Rev 2',
            contextSize: null,
            viewType: 2
        });
    }

    function applyPrettyDiff() {
        var $differences = $('.differences'),
            $headTh = $differences.find('thead th');

        $differences.prettyTextDiff({
            cleanup: true,
            originalContent: $headTh.eq(1).html(),
            changedContent: $headTh.eq(3).html(),
            diffContainer: $headTh.eq(3)
        });

        $differences.find('tr').each(function() {
            var $tr = $(this),
                $replace = $tr.children('.replace'),
                $replace1,
                $replace2;

            if ($replace.length !== 2) {
                return;
            }

            $replace1 = $replace.eq(0);
            $replace2 = $replace.eq(1);

            $replace1.addClass('rev-from');
            $replace2.addClass('rev-to');

            $tr.prettyTextDiff({
                cleanup: true,
                originalContent: $replace1.html(),
                changedContent: $replace2.html(),
                diffContainer: $replace2
            });

            $tr.prettyTextDiff({
                cleanup: true,
                originalContent: $replace2.html(),
                changedContent: $replace1.html(),
                diffContainer: $replace1
            });
        });
    }

    function renderDiff() {
        var $differences = $('.differences'),
            contentFrom = getRevFromElement().find('.page-content').html(),
            contentTo = getRevToElement().find('.page-content').html(),
            diff = getDiff(contentFrom, contentTo),
            $headTh;

        $differences.html(diff);

        $headTh = $differences.find('thead th');
        $headTh.eq(1).html(getRevFromData().revisionTitle);
        $headTh.eq(3).html(getRevToData().revisionTitle);

        updateRevisionsInfo();

        applyPrettyDiff();
    }

    init();
});