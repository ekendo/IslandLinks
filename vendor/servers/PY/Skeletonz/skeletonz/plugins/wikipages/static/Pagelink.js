/**
  Pagelink controller
  **/
var EC_Pagelink = {
  update: function (elm, page_link_id) {
    if(elm.value != "null") {
      var d = getRequest('siteedit/pageLinkUpdate');
      
      var req_done = function (res_txt) {
        var section = "";
        try {
          var old_href = elm.previousSibling.previousSibling.firstChild.href.toString();
          var split_dash = old_href.split("#");
          if(isDefined(split_dash[1]))
            section = "#" + split_dash[1];
          elm.previousSibling.previousSibling.firstChild.href = res_txt + section;
        }
        catch(e) {
        }
      };

      d.addCallback(req_done);
      d.sendReq({'page_link_id': page_link_id, 'new_page_id': elm.value, 'current_pid': PageOracle.getPageId()});
    }
    else
      alert("This wasn't saved since this item isn't a page!");
  }
}
