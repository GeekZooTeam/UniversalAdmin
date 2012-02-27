function real_time(time) {
    var m = ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],
        w = ['星期日', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六'],
        d = new Date();

    time = parseInt(time);

    var delta = parseInt(d.getTime() / 1000) - time;

    if (delta < 3600 * 24) {
        if (delta < 60) {
            delta += '秒';
        } else if (delta < 3600) {
            delta = parseInt(delta / 60);
            delta += '分钟';
        } else {
            delta = parseInt(delta / 3600);
            delta += '小时';
        }
        delta += '前';
    } else {
        d.setTime(time * 1000);
        var t = [parseInt(d.getHours()), parseInt(d.getMinutes())];
        var alpha = t[0] > 12 ? (t[0] - 12) + ':' + t[1] + ' pm' : t[0] + ':' + t[1] + ' am';
        if (delta < 3600 * 48) {
            delta = '昨日' + alpha;
        } else if (delta < 3600 * 24 * 7) {
            delta = w[d.getDay()] + alpha;
        } else {
            delta = m[d.getMonth()] + d.getDate() + '日, ' + d.getFullYear() + ' ' + alpha;
        }
    }

    return delta;
}


$(function(){

  $('a.delete').live('click', function(){
    return confirm('确定删除?此操作不能回退');
  });

  $('span.real_time').html(function(i, html){
    return real_time(html);
  });

});