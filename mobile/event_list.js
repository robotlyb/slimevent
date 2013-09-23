//var get_events_url = "http://192.168.1.104/~kjlmfe/slimevent/m/find?";
var get_events_url = "http://event.hit.edu.cn/m/find?";
var param = [];
param['category'] = "all";
param['region'] = "all";
param['range'] = "week";
param['page'] = 0;
param['order'] = 'post';
param['by'] = 'desc';
param['every_page'] = 5;

var item_template_sameday = "" 
  + "<li>"
  +   "<h2><a href='#'>[{category}] {title}</a></h2>"
  +   "<div class='info'>"
  +   "<ul>"
  +     "<li>时间: {begin_date} ({begin_weekday}) {begin_time} - {end_time} ({begin_remain_day})</li>"
  +     "<li>地点: {region} {addr}</li>"
  +     "<li>发起: <a href='#'>{organizer}</a></li> "
  +   "</ul>"
  +   "</div>"
  + "</li>";

var item_template_diffday = "" 
  + "<li>"
  +   "<h2><a href='#'>[{category}] {title}</a></h2>"
  +   "<div class='info'>"
  +   "<ul>"
  +     "<li></i>开始时间: {begin_date} ({begin_weekday}) {begin_time} ({begin_remain_day})</li>"
  +     "<li></i>结束时间: {end_date} ({end_weekday}) {end_time} ({end_remain_day})</li>"
  +     "<li></i>地点: {region} {addr}</li>"
  +     "<li></i>发起: <a href='#'>{organizer}</a></li> "
  +   "</ul>"
  +   "</div>"
  + "</li>";

function substitute (str, obj) {
  /*
    if (!(Object.prototype.toString.call(str) === '[object String]')) {
        return '';
    }
    if(!(Object.prototype.toString.call(obj) === '[object Object]' && 'isPrototypeOf' in obj)) {
        return str;
    }
    */
  return str.replace(/\{([^{}]+)\}/g, function(match, key) {
    var value = obj[key];
    return ( value !== undefined) ? ''+value :'';
  });
}
function getEvents(type) {
  url = get_events_url;
  for(var k in param) {
    url += k + '=' + param[k] + "&&";
  }
  $.getJSON(url, function(data){
    //console.log(data);
    var html = "",
        btn = $(".more");
    for(var i = 0; i < data.length; i++) {
      if(data[i].begin_date == data[i].end_date) {
        html += substitute(item_template_sameday, data[i]);
      } else {
        html += substitute(item_template_diffday, data[i]);
      }
    }
    if(type == "more") {
      if(html != "") {
        $('.event-list').append(html);
        btn.text("摸摸我，加载更多");
        btn.removeClass("loading");
      } else {
        btn.text("别摸我了，没有了");
      }
    } else {
      $('.event-list').html(html);
      if(html != "") {
        btn.text("摸摸我，加载更多");
        btn.removeClass("loading");
      } else {
        btn.addClass("loading");
        btn.text("无结果");
      }
    }
  });
}

function bindEvent() {
  $(".more").bind('click', function() {
    var btn = $(".more");
    if(btn.hasClass("loading")) {
      return false;
    } 
    param['page']++;
    btn.addClass("loading");
    btn.text("正在努力获取数据...");
    getEvents("more");
  });
  $(".events-nav-item a").bind('click', function() {
    var p = $(this).parent();
    var btn = $(".more");
    if(p.hasClass("current")) {
      return false;
    }
    btn.addClass("loading");
    btn.text("正在努力获取数据...");
    $(this).parents("ul").children("li").removeClass("current");
    p.addClass("current");
    var data = $(this).attr('href').split('-');
    param[data[0]] = data[1];
    param['page'] = 0;
    getEvents("new");
    return false;
  });
}

$(function() {
  bindEvent();
  getEvents("new");
});

