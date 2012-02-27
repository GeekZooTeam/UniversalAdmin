$(document).ready(function(){

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

//checkbox
  $('input:checkbox').each(function(){
    $(this).hide();
    if ($(this).is(':disabled')) {
      var ui = $('<img src="Templates/'+__THEME__+'/images/i.png" class="checkbox_disabled">');
    } else {
      if (this.checked) {
        var ui = $('<img src="Templates/'+__THEME__+'/images/i.png" class="checkbox_checked">');
      } else {
        var ui = $('<img src="Templates/'+__THEME__+'/images/i.png" class="checkbox">');
      }
      var self = this;
      $(this).click(function(){
        var t = !this.checked;
        if (t) {
          ui.removeClass("checkbox").addClass("checkbox_checked");
        } else {
          ui.removeClass("checkbox_checked").addClass('checkbox');
        }
      });
      ui.click(function(){
        $(self).click();
      });
    }
    $(this).after(ui);
  });
//radio
  $("input:radio").each(function(){
    $(this).hide();
    var name = this.name;
    if ($(this).is(':disabled')) {
      var ui = $('<img src="Templates/'+__THEME__+'/images/i.png" class="radio_disabled">');
    } else {
      if (this.checked) {
        var ui = $('<img src="Templates/'+__THEME__+'/images/i.png" class="radio_checked">');
      } else {
        var ui = $('<img src="Templates/'+__THEME__+'/images/i.png" class="radio">');
      }
      var self = this;
      jQuery.data(this, 'ui', ui);
      $(this).click(function(){
        var t = !this.checked;
        $('input:radio[name='+name+']').each(function(){
          jQuery.data(this, 'ui').removeClass('radio_checked radio').addClass('radio');
        });
        ui.removeClass("radio").addClass("radio_checked");
      });
      ui.click(function(){
        $(self).click();
      });
    }
    $(this).after(ui);
  });
//select
  $('select').each(function(i){
    $(this).hide();
    var value = $(this).val();
    var ui = $('<div class="select"></div>');
    ui.css('z-index', 999 - i);
    var selected = $('<strong></strong>');
    var op = $('<ul></ul>');
    var self = this;
    $('option', $(this)).each(function(i){
      opt = $('<li></li>');
      jQuery.data(opt[0], 'value', this.value);
      opt.html($(this).text());
      op.append(opt);
      if (value == this.value) {
        value = $(this).text();
      }
    });
    selected.html(value);
    ui.append(selected);
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
      var text = $(this).html();
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

    $(this).after(ui);
  });

//tips
$(".tips .close").click(function(event){
    $(this).parent().parent(".tips").fadeOut(500);
   return false;
});

//table
$(".container thead th:last-child").css({"border-right":"none"});
$(".container tbody tr td:last-child").css({"border-right":"none"});
$(".container tbody tr:odd").css({"background":"#F5F5F5"});



$(".down").toggle(
  function () {
    $(this).removeClass("down");
    $(this).addClass("up");
  },
  function () {
    $(this).removeClass("up");
    $(this).addClass("down");
  }
);

});