$(function() {
  $("#nav-login-button").click(function(event) {
    event.preventDefault();
    var username = $("#username").val();
    var password = $("#password").val();

    $.post('/login', { username: username, password: password}, function(response) {
      console.log(response);
      response = jQuery.parseJSON(response);
      if (response !== 'password' && response !== 'username') {
        $("#nav-login").html("<small class='text-muted'>Now signed in as <a href='/user/" + response['id'] + "'>" + response['name'] + "</small>");
        $(".nav-ul").append("<li class='nav-li'><a href=\"/event_creator/{{session['user'].getId}}\">Create event</a></li>")
      } else {
        $("#nav-login").addClass("has-danger");
        $("#error-text").text("That's not a valid " + response + ". Try again.");
      }
    });
  });

  $("#add-attendee-button").click(function(event) {
    event.preventDefault();

    var name = $("#attendee-name").val();
    var email = $("#attendee-email").val();
    var action = $("#attendee-form").attr('action');

    $.post(action, {name: name, email: email}, function(response) {
      $("#attendees-to-invite").html(response);
    });
  });
});
