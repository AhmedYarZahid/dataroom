$(function() {
    // Toggles a boolean property
    $('.boolean-toggler > span').on('click', function() {
        var $this = $(this),
            data = $this.parent().data();

        $.getJSON(data.url, function() {
            $this.toggleClass('glyphicon-remove glyphicon-ok');
            $this.toggleClass('text-danger text-success');
        }, function() {
            alert('A database error occurred, please reload the page and try again');
        })
    });
});