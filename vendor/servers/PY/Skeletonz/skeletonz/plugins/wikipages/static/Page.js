var EC_Pages = {
  remove: function(span_elm, id) {
    Indicator.append(span_elm);
    var d = AJS.getRequest('pages/deletePage');
    d.addCallback( function (res_txt) {
      span_elm.parentNode.parentNode.innerHTML = res_txt;
    });
    d.sendReq({'id': id});
  }
}
