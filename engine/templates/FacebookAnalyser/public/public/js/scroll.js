document.createElement('main');
$(document).ready(function(){
  var doctitle = $('title').text();
  window.onpopstate = function(event) {
      if (event.state == null){
        $('#content').empty();
        return false;
      }
      var link = event.state['load'];
      var hash_removed = link.substring(0, link.indexOf('#'));
      var hash = link.substring(link.indexOf('#'), link.length);
      console.log('Retrieving ' + hash_removed);
      $.get(hash_removed, function(data){
        $('#content').empty();
        $('#content').append(data);
        $('#ajax_content').css("opacity", 0);
        $('#ajax_content').show();
        $('#ajax_content').animate({ opacity: 1}, 1200);
        $('a').each(function(){
          if ($(this).attr('href') == link){
            document.title = $(this).text() + ' - ' + doctitle;
          }
        });
      });
      window.scrollTo(event.state['scroll'],0);
  };
  $('a').click(function(e){
    var link = $(this).attr('href');
    var title = $(this).text();
    if (link.indexOf('#') > 0){
      var hash_removed = link.substring(0, link.indexOf('#'));
      var hash = link.substring(link.indexOf('#'), link.length);
      console.log('Retrieving ' + hash_removed);
      $.get(hash_removed, function(data){
        $('#content').empty();
        history.pushState({load: link, scroll: $(document).scrollTop()}, doctitle, link);
        document.title = title + " - " + doctitle;
        $('#content').append(data);
        $('#ajax_content').css("opacity", 0);
        $('#ajax_content').show();
        $('#ajax_content').animate({ opacity: 1}, 1200);
        $('html, body').animate({
          scrollTop: $(hash).offset().top
        }, 1000);
        console.log('Done!');
      });
      e.preventDefault();
    }
  });
});