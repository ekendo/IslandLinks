/**
    Upload controller
    **/

//Global
var EC_Upload_Current = {
    'ident': null,
    'type': null,
    'ident_id': null,
    'new_html': null,
    'linkonly': null
}

var EC_Upload = {
    //
    // Different open window funcs
    //
    showUploadWindowIdent: function(handler, ident, type, linkonly) {
        EC_Upload_Current.ident = ident;
        var url = PageOracle.getBaseURL() + '/' + handler + '/showUpload?ftype=' + type + '&linkonly=' + linkonly;
        GB_show("Upload a new file", url, 250, 410);
    },

    showUploadWindowId: function(handler, id, type, linkonly) {
        var url = PageOracle.getBaseURL() + '/' + handler + '/showUpload?ftype=' + type + '&id=' + id+ '&linkonly=' + linkonly;
        GB_show("Change file", url, 250, 410);
    },

    deleteFile: function(elm, handler, id, type) {
        if(confirm("Are you sure you want to delete this file?")) {
            var d = getRequest(handler + '/deleteFile');
            d.addCallback( function(res_txt) {
                EC_Upload.updateAll(elm.parentNode, res_txt);
            });

            var img = AJS.$bytc("img", null, elm.parentNode)[0];
            var linkonly = 0;
            //If the image is image_link, then it's link only
            if(img.src.indexOf('static_plugin/upload/image_link') != -1)
                linkonly = 1;

            d.sendReq({'file_id': id, 'ftype': type, 'linkonly': linkonly});
        }
    },

    updateAll: function(elm, new_inner_html) {
        var all_elmenets = getElementsByTagAndClassName("span", elm.className);
        for(var i=0; i < all_elmenets.length; i++) {
            if(elm.id == all_elmenets[i].id) {
                all_elmenets[i].innerHTML = new_inner_html;
            }
        }
    },

    // Called from outside
    setData: function(i, i_id, t, n) {
        EC_Upload_Current.ident = i;
        EC_Upload_Current.ident_id = i_id;
        EC_Upload_Current.type = t;
        EC_Upload_Current.new_html = n;
        AJS.callLater(EC_Upload.changeUploadObject, 100);
    },

    changeUploadObject: function() {
        var elm;
        if(EC_Upload_Current.type == "skimg")
            elm = AJS.$("skimg_" + EC_Upload_Current.ident_id);
        else if(EC_Upload_Current.type == "skfile")
            elm = AJS.$("skfile_" + EC_Upload_Current.ident_id);
        else
            elm = AJS.$("personnelimage_" + EC_Upload_Current.ident_id);

        if(elm != null) {
            IS_OBJ_CHANGED = true;
            EC_Upload.updateAll(elm, EC_Upload_Current.new_html);
        }
    }
}
