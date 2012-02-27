$(function(){



$('a.delete').click(function(){
  return confirm('确认删除?此操作不能还原!');
});

$('a.cancel').click(function(){
  history.go(-1);
  return false;
});

$('a.save').click(function(){
  $('form').submit();

  return false;
});

$(':checkbox.checkAll').click(function(){
  if (this.checked) {
    $('input[name="'+$(this).attr('rel')+'"]').checkbox('off');
  } else {
    $('input[name="'+$(this).attr('rel')+'"]').checkbox('on');
  }
});


$.fn.extend({
  checkbox: function(action){
    action = action || 'toggle';
    return this.each(function(){
      if (action == 'toggle') {
        $(this).click();
      } else if (action == 'on') {
        if (this.checked == false) {
          $(this).click();
        }
      } else if (action == 'off') {
        if (this.checked) {
          $(this).click();
        }
      }
    });
  }
});

// checkbox
$('input:checkbox:not([rel="switch"])').each(function(){
  $(this).hide();
  var self = this,
      ui_class = "vm ico_checkbox";

  if (this.checked) {
    ui_class = ui_class+'_select';
  }

  var ui = $('<img src="Templates/'+__THEME__+'/images/i.gif" class="'+ui_class+'">');
  
  if ($(this).is(':disabled')) {
    ui.attr('class', ui_class+'_disable');
  } else {
    $(this).click(function(){
      var t = !this.checked;
      if (t) {
        ui.removeClass("ico_checkbox").addClass("ico_checkbox_select");
      } else {
        ui.removeClass("ico_checkbox_select").addClass('ico_checkbox');
      }
    });
    
    ui.click(function(){
      $(self).click();
    });

  }

  $(this).after(ui);

});

$('input:checkbox[rel="switch"]').each(function(){
  $(this).hide();
  var self = this,
      ui_class = "onoff";
  if (this.checked) {
    ui_class = ui_class+'_on';
  } else {
    ui_class = ui_class+'_off';
  }
  var ui = $('<div class="'+ui_class+'"><span></span></div>');

  if (!$(this).is(':disabled')) {

    $(this).click(function(){
      var t = !this.checked;

      if (t) {
        ui.removeClass("onoff_off").addClass("onoff_on");
      } else {
        ui.removeClass("onoff_on").addClass('onoff_off');
      }
    });
    
    ui.click(function(){
      $(self).click();
    });

  }

  $(this).after(ui);
});
// checkbox

$("input:radio").each(function(){
  $(this).hide();

  var self = this,
      ui_class = "vm ico_radio",
      name = this.name;

  if (this.checked) {
    ui_class = ui_class+'_select';
  }

  var ui = $('<img src="Templates/'+__THEME__+'/images/i.gif" class="'+ui_class+'">');

  if ($(this).is(':disabled')) {
    ui.attr('class', ui_class+'_disable');
  } else {

    jQuery.data(this, 'ui', ui);
    $(this).click(function(){
      var t = !this.checked;
      $('input:radio[name='+name+']').each(function(){
        jQuery.data(this, 'ui').removeClass('ico_radio_select').addClass('ico_radio');
      });
      ui.removeClass("ico_radio").addClass("ico_radio_select");
    });

    ui.click(function(){
      $(self).click();
    });
  }
  $(this).after(ui);
});


$('select').each(function(i){
  $(this).hide();
  var value = $(this).val();
  var ui = $('<div class="select"></div>');
  ui.css('z-index', 999 - i);

  var selected = $('<span class="select_selected"></span>'),
      arrow = $('<span class="select_btn"></span>'),
      op = $('<ul class="select_option"></ul>');

  var self = this;
  var max = '';
  $('option', $(this)).each(function(i){
    opt = $('<li></li>');
    jQuery.data(opt[0], 'value', this.value);
    if ($(this).text().length > max.length) {
      max = $(this).text();
    }
    opt.html('<p>'+$(this).text()+'</p>');
    op.append(opt);
    if (value == this.value) {
      value = $(this).text();
    }
  });
  selected.html(max);
  //selected.html(value);
  ui.append(selected).append(arrow);
  op.hide();
  ui.append(op);
  
  $("li", op).hover(
    function () {
      $(this).addClass("hover");
    },
    function () {
      $(this).removeClass("hover");
    }
  );

  $("li", op).click(function(){
    var text = $(this).text();
    selected.html(text);
    op.hide();
    $('body').unbind('click');
    $(self).val(jQuery.data(this, 'value')).change();
  });

  selected.click(function(){
    op.slideDown('fast', function(){
      $('body').click(function(e){
          var target = $(e.target);
          if (!target.is('li.hover')) {
            op.hide();
            $('body').unbind('click');
          }
      });
    });
  });

  arrow.click(function(){
    selected.click();
  });

  $(this).after(ui);
  selected.width(op.width()-20);
  selected.html(value);
  //;
});

});
