String.prototype.replaceAll = function(search, replacement) {
    var target = this;
    return target.replace(new RegExp(search, 'g'), replacement);
};

function collapseElement($element, height = 0) {
    $element.attr('style', `height: ${height}px;`);
    $element.removeClass('collapse');
    $element.addClass('collapsing');
    if ($element.hasClass('in')){
        $element.removeClass('in');
        $element.attr('aria-expanded', false);
        setTimeout(() => {
          $element.addClass('collapse');
          $element.removeClass('collapsing');
        }, 300);
    }
    else {
        $element.attr('aria-expanded', true);
        setTimeout(() => {
          $element.addClass('collapse');
          $element.addClass('in');
          $element.attr('style', '');
          $element.removeClass('collapsing');
        }, 300);
    }
}