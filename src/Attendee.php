<?php


    class Attendee {

        private $name;
        private $email;
        private $event_id;
        private $rsvp;
        private $id;

        function __construct($name, $email, $event_id, $rsvp = 0, $id = null)
        {
            $this->name = $name;
            $this->email = $email;
            $this->event_id = $event_id;
            $this->rsvp = $rsvp;
            $this->id = $id;
        }

        function getName()
        {
            return $this->name;
        }

        function setName($new_name)
        {
            $this->name = $new_name;
        }

        function getEmail()
        {
            return $this->email;
        }

        function setEmail($new_email)
        {
            $this->email = $new_email;
        }

        function getEventId()
        {
            return $this->event_id;
        }

        function setRsvp($rsvp)
        {
            $this->rsvp = $rsvp;
        }

        function getRsvp()
        {
            return $this->rsvp;
        }

        function getId()
        {
            return $this->id;
        }

        function save()
        {
            $save = $GLOBALS['DB']->prepare("INSERT INTO attendees (name, email, event_id, rsvp) VALUES (:name, :email, :event_id, :rsvp);");
            $save->execute([':name' => $this->getName(), ':email' => $this->getEmail(), ':event_id' => $this->getEventId(), ':rsvp' => $this->getRsvp()]);
            $this->id = $GLOBALS['DB']->lastInsertId();
        }

        function update($new_name)
        {
            $this->setName($new_name);
            $update = $GLOBALS['DB']->prepare("UPDATE attendees SET name = :name WHERE id = :id;");
            $update->execute([':name' => $this->getName(), ':id' => $this->getId()]);
        }

        function updateEmail($new_email)
        {
            $this->setEmail($new_email);
            $update = $GLOBALS['DB']->prepare("UPDATE attendees SET email = :email WHERE id = :id;");
            $update->execute([':email' => $this->getEmail(), ':id' => $this->getId()]);
        }

        function updateRsvp($new_rsvp)
        {
            $this->setRsvp($new_rsvp);
            $update = $GLOBALS['DB']->prepare("UPDATE attendees SET rsvp = :rsvp WHERE id = :id;");
            $update->execute([':rsvp' => $this->getRsvp(), ':id' => $this->getId()]);
        }

        function delete()
        {
            $GLOBALS['DB']->exec("DELETE FROM attendees WHERE id = {$this->getId()};");
        }

        static function getAll()
        {
            $returned_attendees = $GLOBALS['DB']->query("SELECT * FROM attendees;");
            if ($returned_attendees) {
                $attendees = $returned_attendees->fetchAll(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'Attendee', ['name', 'email', 'event_id', 'rsvp', 'id']);
            } else {
                $attendees = [];
            }
            return $attendees;
        }

        static function deleteAll()
        {
            $GLOBALS['DB']->exec("DELETE FROM attendees;");
        }

        static function find($id)
        {
            $returned_attendee = $GLOBALS['DB']->query("SELECT * FROM attendees WHERE id = {$id};");
            $attendee = $returned_attendee->fetchAll(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'Attendee', ['name', 'email', 'event_id', 'rsvp', 'id']);
            return $attendee[0];
        }

    }

?>
