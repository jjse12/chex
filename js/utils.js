Number.prototype.toMoney = function toMoney() {
  return "US$ " + this.toFixed(2);
};

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

const constantWeightsAsc = {
  'Aún sin Seguimiento': new Date('2055-01-01'),
  'Pendiente': new Date('2054-01-01'),
  'No Recibido': new Date('2053-01-01'),
  'Sin Especificar': new Date('2052-01-01'),
};

const constantWeightsDesc = {
  'Aún sin Seguimiento': new Date('1970-01-01'),
  'Pendiente': new Date('1980-01-01'),
  'No Recibido': new Date('1990-01-01'),
  'Sin Especificar': new Date('2000-01-01'),
};

function sortddmmyyyyDate(desc, a, b){
    const strA = $(a).data('sorting-date');
    const strB = $(b).data('sorting-date');
    let dateA, dateB;
    if (strA.length !== 10) dateA = desc ? constantWeightsDesc[strA] : constantWeightsAsc[strA];
    else {
      const arrA = $(a).data('sorting-date').split('/');
      dateA = new Date(arrA[2], arrA[1], arrA[0]);
    }
  if (strB.length !== 10) dateB = desc ? constantWeightsDesc[strB] : constantWeightsAsc[strB];
    else {
      const arrB = $(b).data('sorting-date').split('/');
      dateB = new Date(arrB[2], arrB[1], arrB[0]);
    }

    if (desc){
        return dateA > dateB ? -1 : 1;
    }

    return dateA < dateB ? -1 : 1;
}

function sortDateTime(desc, a, b){
  const dateA = new Date(a);
  const dateB = new Date(b);
  if (desc){
    return dateA > dateB ? -1 : 1;
  }

  return dateA < dateB ? -1 : 1;
}
