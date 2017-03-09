<?php
    date_default_timezone_set("America/Los_Angeles");
    require_once __DIR__."/../vendor/autoload.php";
    require_once __DIR__."/../src/Attendee.php";
    require_once __DIR__."/../src/Event.php";
    require_once __DIR__."/../src/User.php";

    session_start();
    if (empty($_SESSION['user'])) {
        $_SESSION['user'] = [];
        $_SESSION['attendee'] = [];
    }

    $app = new Silex\Application();
    $app->register(new Silex\Provider\TwigServiceProvider(), ["twig.path" => __DIR__."/../views"]);

    use Symfony\Component\HttpFoundation\Request;
    Request::enableHttpMethodParameterOverride();

    $app['debug'] = true;

    $server = 'mysql:host=localhost:8889;dbname=rsvparty';
    $username = 'root';
    $password = 'root';
    $DB = new PDO($server, $username, $password);

    $app->get('/', function() use($app) {

    return $app['twig']->render('root.html.twig', ['session' => $_SESSION]);
    });

    $app->get('/about', function() use ($app) {
        return $app['twig']->render('about.html.twig', ['session' => $_SESSION]);
    });

    $app->get('/error', function() use($app) {
        $result = 'hello';
        return $app["twig"]->render("error.html.twig", ['result' => $result, 'session' => $_SESSION]);
    });

    $app->get('/user/{id}', function($id) use($app) {
        $user = User::find($id);
        $events = $user->getEvents();
        return $app['twig']->render("user_profile.html.twig", ['user' => $user, 'events' => $events, 'session' => $_SESSION]);
    });

    $app->post('/login', function() {
        $user = User::findByUsername($_POST['username']);
        if ($user) {
            $response = $user->logIn($_POST['password']);
            return json_encode($response);
        } else {
            return json_encode("username");
        }
    });

    $app->post('/sign_out/{id}', function ($id) use ($app) {
        $user = User::find($id);
        $user->logOut();
        return $app->redirect('/');
    });

    $app->get('/event_creator/{id}', function($id) use($app) {
        $user = User::find($id);
        return $app['twig']->render('create_event.html.twig', ['user' => $user, 'session' => $_SESSION]);
    });

    $app->post('/create_event', function() use ($app) {
        $user_id = $_POST['user_id'];
        $name = $_POST['name'];
        $date_time = $_POST['date'] . " " . $_POST['time'];
        $description = $_POST['description'];
        $location = $_POST['location'];
        $new_event = new Event ($user_id, $name, $date_time, $description, $location);
        $new_event->save();
        $id = $new_event->getId();
        return $app->redirect('/event_page/'.$id);
    });

    $app->get('/event_page/{id}', function($id) use ($app) {

        $event = Event::find($id);
        $attendees = $event->getAttendees();
        $user = User::find($event->getUserId());
        $key = 'AIzaSyCxVtVkvIYvgnBsEUQ9eKpOHKPQuJOjrBM';
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($event->getLocation())."&key=AIzaSyCxVtVkvIYvgnBsEUQ9eKpOHKPQuJOjrBM";

        $lat_long = json_decode(file_get_contents($url));
        $lat = $lat_long->results[0]->geometry->location->lat;
        $long = $lat_long->results[0]->geometry->location->lng;
        return $app['twig']->render('event_page.html.twig', ['attendees' => $attendees, 'event' => $event, 'lat' => $lat, 'long' => $long, 'key' => $key, 'session' => $_SESSION]);
        });

    $app->post('/send_invites/{id}', function($id) use ($app) {
        $event = Event::find($id);
        $attendees = $event->getAttendees();
        $subject = $_POST['subject-line'];
        $message = $_POST['personal-message'];
        $user_email = $_SESSION['user']->getEmail();
        $event->sendInvites($attendees, $subject, $message, $user_email);
        foreach ($attendees as $attendee) {
            $attendee->updateEmail('sent');
        }
        return $app->redirect('/event_page/'.$id);
    });

    $app->patch('/event_page/{id}/editdate_time', function($id) use ($app) {
        $event = Event::find($id);
        $new_date = $_POST['date'];
        $event->updateDateTime($new_date);
        return $app->redirect('/event_page/'.$id);
    });

    $app->patch('/event_page/{id}/edit_location', function($id) use ($app) {
        $event = Event::find($id);
        $new_location = $_POST['location'];
        $event->updateLocation($new_location);
        return $app->redirect('/event_page/'.$id);
    });

    $app->patch('/event_page/{id}/edit_description', function($id) use ($app) {
        $event = Event::find($id);
        $new_description = $_POST['description'];
        $event->updateDescription($new_description);
        return $app->redirect('/event_page/'.$id);
    });

    $app->post('add_attendee/{id}', function($id) use ($app) {
        $event = Event::find($id);
        $name = $_POST['name'];
        $email = $_POST['email'];
        $new_attendee = new Attendee($name, $email, $id);
        $new_attendee->save();
        $attendees = $event->getAttendees();
        return $app['twig']->render('attendee_list.html.twig', ['attendees' => $attendees]);
    });

    $app->get('/event_page/guest/{guest_key}/{id}', function($guest_key, $id) use ($app) {
        $event = Event::findByKey($guest_key);
        $attendees = Attendee::getAll();
        $attendee = Attendee::find($id);
        $users = User::getAll();
        $key = 'AIzaSyCxVtVkvIYvgnBsEUQ9eKpOHKPQuJOjrBM';
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($event->getLocation())."&key=AIzaSyCxVtVkvIYvgnBsEUQ9eKpOHKPQuJOjrBM";

        $lat_long = json_decode(file_get_contents($url));
        $lat = $lat_long->results[0]->geometry->location->lat;
        $long = $lat_long->results[0]->geometry->location->lng;

        return $app['twig']->render('event_page_guest.html.twig', ['attendees' => $attendees, 'attendee' => $attendee, 'event' => $event, 'lat' => $lat, 'long' => $long, 'key' => $key, 'users' => $users, 'session' => $_SESSION]);
    });

    $app->post('/event_page/guest/{guest_key}/{id}/rsvp', function($guest_key, $id) use ($app) {
        $event = Event::findByKey($guest_key);
        $attendees = Attendee::getAll();
        $attendee = Attendee::find($id);
        $users = User::getAll();
        $rsvp = $_POST['rsvp'];
        $attendee->setRsvp($rsvp);
        $key = 'AIzaSyCxVtVkvIYvgnBsEUQ9eKpOHKPQuJOjrBM';
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($event->getLocation())."&key=AIzaSyCxVtVkvIYvgnBsEUQ9eKpOHKPQuJOjrBM";

        $lat_long = json_decode(file_get_contents($url));
        $lat = $lat_long->results[0]->geometry->location->lat;
        $long = $lat_long->results[0]->geometry->location->lng;

        return $app->redirect('/event_page/guest/{guest_key}/{id}');
    });

    $app->patch('/event_page/editname/{id}', function($id) use ($app) {
        $new_name = $_POST['new_name'];
        updateName($new_name);
        $event = Event::findAll();
        return $app->redirect('/event_page/'.$id);
    });

    $app->get('/sign-up', function() use ($app) {
        return $app['twig']->render('sign_up.html.twig', ['session' => $_SESSION]);
    });

    $app->post("/create_account", function() use ($app) {
        $user_name = $_POST['user_name'];
        $user_password = $_POST['user_password'];
        $confirm_password = $_POST['confirm_password'];
        $user_email = $_POST['user_email'];

        if ($user_password != $confirm_password) {
            return $app['twig']->render('sign_up.html.twig', ['error' => 'Those passwords don\'t match', 'user' => $_SESSION['user']]);
        } elseif (User::alreadyExists($user_name)) {
            return $app['twig']->render('sign_up.html.twig', ['error' => 'That username already exists.', 'user' => $_SESSION['user']]);
        } else {
            $new_user = new User($user_name, $user_password, $user_email);
            $new_user->save();
            return $app->redirect('/user/'.$new_user->getId());
        }
    });

    return $app;
?>
