AdminOrder.prototype.sendQuoteEmail = function(url){
    new Ajax.Request(url, {
        onSuccess: function(response) {
            alert(response.responseJSON);
        }
      });
};