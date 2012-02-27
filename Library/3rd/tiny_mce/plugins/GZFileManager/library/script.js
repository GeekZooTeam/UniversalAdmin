
tinyMCEPopup.requireLangPack();


var realTime = {
  _lang : {},
  lang : 'auto',
  fixTime : null
};

realTime._lang['en'] = {
  month : ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'Aguest', 'September', 'October', 'November','December'],
  week : ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
  secend : '%1 second ago',
  secends : '%1 seconds ago',
  minute : '%1 minute ago',
  minutes : '%1 minutes ago',
  hour : '%1 hour ago',
  hours : '%1 hours ago',
  yesterday : 'yesterday at %1',
  lastweek : '%1 at %2',
  longago : '%1 %2, %3 at %4'
};

realTime._lang['zh-cn'] = {
  month : ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],
  week : ['星期日', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六'],
  secend : '%1 秒前',
  secends : '%1 秒前',
  minute : '%1 分钟前',
  minutes : '%1 分钟前',
  hour : '%1 小时前',
  hours : '%1 小时前',
  yesterday : '昨日%1',
  lastweek : '%1%2',
  longago : '%1%2日, %3 %4'
};

realTime.outPut = function(time) {

  function sprintf() {
    var args = arguments;
    if (args.length == 1) {
      return args[0];
    }
    var pattern = new RegExp("%([1-"+args.length+"])", "g");
    return args[0].replace(pattern, function(match, index) {
      return args[index];
    });
  }

  if (this.lang == 'auto') {
    this.lang = navigator.browserLanguage || navigator.language;
    this.lang = this.lang.toLowerCase();
  }

  var lang = this._lang[this.lang] || this._lang['en'];

  var m = lang.month,
      w = lang.week,
      d = new Date();

  time = parseInt(time);

  var delta = (this.fixTime || parseInt(d.getTime() / 1000)) - time;
  // if the time is below 0 then set 0
  delta = delta > 0 ? delta : 0;
  
  if (delta < 3600 * 24) {
    if (delta < 60) {// less then 1 minute
      if (delta > 1) {
        delta = sprintf(lang.secends, delta);
      } else {
        delta = sprintf(lang.secend, delta);
      }
    } else if (delta < 3600) {// less then 1 hour
      delta = parseInt(delta / 60);
      if (delta > 1) {
        delta = sprintf(lang.minutes, delta);
      } else {
        delta = sprintf(lang.minute, delta);
      }
    } else {// less then 1 day
      delta = parseInt(delta / 3600);
      if (delta > 1) {
        delta = sprintf(lang.hours, delta);
      } else {
        delta = sprintf(lang.hour, delta);
      }
    }
  } else {
    d.setTime(time * 1000);
    var t = [parseInt(d.getHours()), parseInt(d.getMinutes())];
    var alpha = t[0] > 12 ? (t[0] - 12)+':'+t[1]+' pm' : t[0]+':'+t[1]+' am';
    if (delta < 3600 * 48) {// less then 2 days
      delta = sprintf(lang.yesterday, alpha);
    } else if (delta < 3600 * 24 * 7) {// less then 1 week
      delta = sprintf(lang.lastweek, w[d.getDay()], alpha);
    } else {// more then 1 week
      delta = sprintf(lang.longago, m[d.getMonth()], d.getDate(), d.getFullYear(), alpha);
    }
  }

  return delta;
};

realTime.lang = tinyMCE.activeEditor.settings.language;

function insert(text) {
  var ext = text.split('.').pop().toLowerCase();

  if (ext == 'jpg' || ext == 'gif' || ext == 'png' || ext == 'bmp') {
    tinyMCEPopup.editor.execCommand('mceInsertContent', false, '<img src="'+text+'" />');
  } else {
    var name = text.split('/').pop();
    tinyMCEPopup.editor.execCommand('mceInsertContent', false, '<a href="'+text+'">'+name+'</a>');
  }
  
	tinyMCEPopup.close();
}

function sizeFormat(value, option) {
  var value = parseInt(value);
  var option = option || {};
  option.dec = option.dec || 2;
  option.prefixArr = option.prefixArr || [" B", " KB", " MB", " GB", " TB"];
  option.convertUnits = option.convertUnits || 1024;

  while (value > option.convertUnits) {
    value /= option.convertUnits;
    option.prefixArr.shift();
  }

  return (Math.round(value * Math.pow(10, option.dec)) / Math.pow(10, option.dec)) + option.prefixArr.shift();
}

$(function(){

  //$('.container').width($(document).width()-30);

// new_folder
$('#new_folder').click(function(){
    $('.bag').remove();
    var add = $('<input type="button" value="new" />');
    var td = $('<td colspan="3"></td>');
    var tr = $('<tr class="bag"></tr>');
    var input = $('<input type="text" class="text" name="folder_name" value="" />');
    td.append(input).append(' ').append(add);
    tr.append(td);
    add.click(function(){
        if (input.val()) {
            var form = $('form')[0];
            add.val('waiting...');
            add.unbind('click').attr('disabled','disabled');
            form.action = 'index.php?d='+d+'&a=new_folder&private_key='+private_key;
            form.submit();
        } else {
            notice('请输入目录名字');
        }
    });

    $('thead').prepend(tr);
});

function notice(msg) {
    var notice = $('<div class="notice">'+msg+'</div>');
    $('.container').prepend(notice);
    var timer = setInterval(function(){
        notice.hide('slow', function(){
            $(this).remove();
            clearInterval(timer);
        });
    }, 3000);
}

// new file
$('#new_file').click(function(){
    $('.bag').remove();
    var add = $('<input type="button" value="upload" />');
    var td = $('<td colspan="3"></td>');
    var tr = $('<tr class="bag"></tr>');
    var input = $('<input type="file" name="file_array" value="" />');
    td.append(input).append(' ').append(add);
    tr.append(td);
    add.click(function(){
        if (input.val()) {
            var form = $('form')[0];
            add.val('waiting...');
            add.unbind('click').attr('disabled','disabled');
            form.action = 'index.php?d='+d+'&a=new_file&private_key='+private_key;
            form.submit();
            
        } else {
            notice('请选择文件');
        }
    });

    $('thead').prepend(tr);
});

if (info != '') {
    notice(info);
}

$('.realtime').each(function(){
    var ctime = $(this).html();
    $(this).html(realTime.outPut(ctime));
});

$('.size_format').each(function(){
    var size = $(this).html();
    $(this).html(sizeFormat(size));
});

$('.folder_info').hover(function(){
    var dir = $(this).find('a').html();
    var del = $('<span style="float:right;padding-top:2px;"><img src="library/famfamfam/action_stop.gif" /></span>');
    del.click(function() {
        if (confirm(tinyMCEPopup.editor.getLang('GZFileManager.folder_delete')+dir+' ?')) {
            if (confirm(tinyMCEPopup.editor.getLang('GZFileManager.confirm_again'))) {
                del.unbind('click');
                location.href = '?d='+d+'/'+dir+'&a=delete&private_key='+private_key;
            }
        }
    });
    $(this).prepend(del);
},function(){
    $(this).parent().parent().find('span').remove();
});

$('.file_info').hover(function(){
    var filename = $(this).find('a').html();
    var url = $(this).find('a').attr('href');
    var htm = '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"\
            width="110"\
            height="14"\
            id="clippy" style="display:inline;">\
    <param name="movie" value="library/clippy.swf"/>\
    <param name="allowScriptAccess" value="always" />\
    <param name="quality" value="high" />\
    <param name="scale" value="noscale" />\
    <param NAME="FlashVars" value="text='+url+'">\
    <param name="bgcolor" value="#eeeeee">\
    <embed src="library/clippy.swf"\
           width="110"\
           height="14"\
           name="clippy"\
           quality="high"\
           allowScriptAccess="always"\
           type="application/x-shockwave-flash"\
           pluginspage="http://www.macromedia.com/go/getflashplayer"\
           FlashVars="text='+url+'"\
           bgcolor="#eeeeee" /></object>';
    $(this).append('<span style="padding-left:5px;">'+htm+'</span>');
    var del = $('<span style="float:right;padding-top:2px;"><img src="library/famfamfam/action_stop.gif" /></span>');
    del.click(function() {
        if (confirm(tinyMCEPopup.editor.getLang('GZFileManager.file_delete')+filename+' ?')) {
            del.unbind('click');
            location.href = '?d='+d+'/'+filename+'&a=delete&private_key='+private_key;
        }
    });
    $(this).prepend(del);
}, function(){
    $(this).parent().parent().find('span').remove();
});


$('tr').hover(function(){
    $(this).css('background', '#eee');
},function(){
    $(this).parent().find('tr').css('background', '#fff');
});


});