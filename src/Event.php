<?php
    class Event {

        private $user_id;
        private $name;
        private $date_time;
        private $description;
        private $location;
        private $guest_key;
        private $id;

        function __construct($user_id, $name, $date_time, $description, $location, $guest_key = null, $id=null)
        {
            $this->user_id = $user_id;
            $this->name = $name;
            $this->date_time = $date_time;
            $this->description = $description;
            $this->location = $location;
            $this->id = $id;
            if ($guest_key == null) {
                $this->generateKey();
            } else {
                $this->guest_key = $guest_key;
            }
        }

        protected function generateKey()
        {
            $alph = "0123456789abcdefghijklmnopqrstuvwxyz";
            $key = $alph[mt_rand(0, 35)] . $alph[mt_rand(0, 35)] . $alph[mt_rand(0, 35)] . $alph[mt_rand(0, 35)] . $alph[mt_rand(0, 35)];
            $test = $GLOBALS['DB']->query("SELECT * FROM users WHERE guest_key = {$key};");
            if ($test) {
                $this->generateKey();
            } else {
                $this->guest_key = $key;
            }
        }

        function getGuestKey()
        {
            return $this->guest_key;
        }

        function getUserId()
        {
            return $this->user_id;
        }

        function getName()
        {
            return $this->name;
        }

        function setName($new_name)
        {
            $this->name = $new_name;
        }

        function getDateTime()
        {
            return $this->date_time;
        }

        function setDateTime($new_date_time)
        {
            $this->date_time = $new_date_time;
        }

        function getDescription()
        {
            return $this->description;
        }

        function setDescription($new_description)
        {
            $this->description = $new_description;
        }

        function getLocation()
        {
            return $this->location;
        }

        function setLocation($new_location)
        {
            $this->location = $new_location;
        }

        function getId()
        {
            return $this->id;
        }

        function save()
        {
            $exec = $GLOBALS['DB']->prepare("INSERT INTO events (user_id, name, date_time, description, location, guest_key) VALUES (:user_id, :name, :date_time, :description, :location, :guest_key);");
            $exec->execute([':user_id' => $this->getUserId(), ':name' => $this->getName(), ':date_time' => $this->getDateTime(), ':description' => $this->getDescription(), ':location' => $this->getLocation(), ':guest_key' =>
            $this->getGuestKey()]);
            $this->id = $GLOBALS['DB']->lastInsertId();
        }

        function updateName($new_name)
        {
            $exec = $GLOBALS['DB']->prepare("UPDATE events SET name = :name WHERE id = :id;");
            $exec->execute([':name' => $new_name, ':id' =>$this->getId()]);
            $this->setName($new_name);
        }

        function updateDateTime($new_date_time)
        {
            $exec = $GLOBALS['DB']->prepare("UPDATE events SET date_time = :date_time WHERE id = :id;");
            $exec->execute([':date_time' => $new_date_time, ':id' =>$this->getId()]);
            $this->setDateTime($new_date_time);
        }

        function updateDescription($new_description)
        {
            $exec = $GLOBALS['DB']->prepare("UPDATE events SET description = :description WHERE id = :id;");
            $exec->execute([':description' => $new_description, ':id' =>$this->getId()]);
            $this->setDescription($new_description);
        }

        function updateLocation($new_location)
        {
            $exec = $GLOBALS['DB']->prepare("UPDATE events SET location = :location WHERE id = :id;");
            $exec->execute([':location' => $new_location, ':id' =>$this->getId()]);
            $this->setLocation($new_location);
        }

        function delete()
        {
            $GLOBALS['DB']->exec(
            "DELETE FROM events WHERE id = {$this->getid()};");
        }

        function sendInvites($attendees_array, $subject, $message, $user_email)
        {
            $headers = 'From: ' . $user_email . '\r\n'. 'Reply-To: ' . $user_email;
            foreach ($attendees_array as $attendee) {
                $message = "<!DOCTYPE html>
                <html>
                <head>
                <meta charset='UTF-8'>
                </head>
                <body>
                <div>
                    <p>Hello " . $attendee->getName() . "!</p>
                    <p>" . $message . "</p>
                    <p>Click <a href='/event/" . $this->getGuestKey() . "/" . $attendee->getId() . "'>here</a> to RSVP to the event.</p>
                    <p>See you there!</p>
                </div>
                </body>
                </html>";
                mail($attendee->getEmail(), $subject, $message, $headers);
            }
        }

        function getAttendees()
        {
            $returned_attendees = $GLOBALS['DB']->query("SELECT * FROM attendees WHERE event_id = {$this->getId()};");
            if ($returned_attendees) {
                $attendees = $returned_attendees->fetchAll(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'Attendee', ['name', 'email', 'event_id', 'rsvp', 'id']);
            } else {
                $attendees = [];
            }
            return $attendees;
        }

        static function getAll()
        {
            $returned_events = $GLOBALS['DB']->query("SELECT * FROM events;");
            $events = [];
            foreach ($returned_events as $event) {
                $user_id = $event['user_id'];
                $name = $event['name'];
                $date_time = $event['date_time'];
                $description = $event['description'];
                $location = $event['location'];
                $guest_key = $event['guest_key'];
                $id = $event['id'];
                $new_event = new Event ($user_id, $name, $date_time, $description, $location, $guest_key, $id);
                array_push($events, $new_event);
            }
            return $events;
        }

        static function deleteAll()
        {
            $GLOBALS['DB']->exec("DELETE FROM events;");
        }

        static function find($id)
        {
            $returned_event = $GLOBALS['DB']->query("SELECT * FROM events WHERE id = {$id};");
            $event = $returned_event->fetchAll(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'Event', ['user_id', 'name', 'date_time', 'description', 'location', 'guest_key', 'id']);
            return $event[0];
        }

        static function findByKey($guest_key)
        {
            $returned_event = $GLOBALS['DB']->query("SELECT * FROM events WHERE guest_key = '{$guest_key}';");
            $event = $returned_event->fetchAll(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'Event',  ['user_id', 'name', 'date_time', 'description', 'location', 'guest_key', 'id']);
            return $event[0];
        }

    }

?>
