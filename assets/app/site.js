$('.cms-modules-menu li.subitems > a').on('click', function(e) {
    $(this).parent().toggleClass('open');
    return false;
});
