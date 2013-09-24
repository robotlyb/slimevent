function bindEvent() {
  $('.loading-img').bind('click', function() {

    var self = $(this),
        img = new Image();

    if(self.hasClass('loading')) {
      return;
    }

    img.src = self.attr('data-src');
    self.addClass("loading");
    self.text("海报马上出来，亲稍等...");
    img.onload = function() {
      $('.event-pic').html(img);
    }

  });
}
$(function() {
  bindEvent();
});
