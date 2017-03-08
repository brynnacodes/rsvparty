<?php

    /**
    * @backupGlobals disabled
    * @backupStaticAttributes disabled
    */
    require_once "src/Attendee.php";
    require_once "src/Task.php";

    $server = 'mysql:host=localhost:8889;dbname=rsvparty_test';
    $username = 'root';
    $password = 'root';
    $DB = new PDO($server, $username, $password);


    class AttendeeTest extends PHPUnit_Framework_TestCase
    {

        protected function tearDown()
        {
            Event::deleteAll();
            Attendee::deleteAll();
            Task::deleteAll();
            User::deleteAll();
        }

        function test_save()
        {
            $name = 'Geoff';
            $email = 'geoff@email.biz';
            $event_id = '2';
            $test_attendee = new Attendee($name, $email, $event_id);

            $test_attendee->save();

            $result = Attendee::getAll();

            $this->assertEquals($test_attendee, $result[0]);
        }

        function test_getAll()
        {
            $name = 'Geoff';
            $email = 'geoff@email.biz';
            $event_id = '2';
            $test_attendee = new Attendee($name, $email, $event_id);
            $test_attendee->save();

            $name = 'Dave';
            $email = 'dave@email.co.au';
            $event_id = '4';
            $test_attendee2 = new Attendee($name, $email, $event_id);
            $test_attendee2->save();

            $result = Attendee::getAll();

            $this->assertEquals([$test_attendee, $test_attendee2], $result);
        }

        function test_deleteAll()
        {
            $name = 'Geoff';
            $email = 'geoff@email.biz';
            $event_id = '2';
            $test_attendee = new Attendee($name, $email, $event_id);
            $test_attendee->save();

            $name = 'Dave';
            $email = 'dave@email.co.au';
            $event_id = '4';
            $test_attendee2 = new Attendee($name, $email, $event_id);
            $test_attendee2->save();

            Attendee::deleteAll();
            $result = Attendee::getAll();

            $this->assertEquals([], $result);
        }

        function test_update()
        {
            $name = 'Geoff';
            $email = 'geoff@email.biz';
            $event_id = '2';
            $test_attendee = new Attendee($name, $email, $event_id);
            $test_attendee->save();

            $new_name = 'Tim';
            $test_attendee->update($new_name);
            $result = Attendee::getAll();

            $this->assertEquals($new_name, $result[0]->getName());
        }

        function test_updateRsvp()
        {
            $name = 'Geoff';
            $email = 'geoff@email.biz';
            $event_id = '2';
            $test_attendee = new Attendee($name, $email, $event_id);
            $test_attendee->save();

            $new_rsvp = 1;
            $test_attendee->updateRsvp($new_rsvp);
            $result = Attendee::getAll();

            $this->assertEquals($new_rsvp, $result[0]->getRsvp());
        }

        function test_delete()
        {
            $name = 'Geoff';
            $email = 'geoff@email.biz';
            $event_id = '2';
            $test_attendee = new Attendee($name, $email, $event_id);
            $test_attendee->save();

            $name = 'Dave';
            $email = 'dave@email.co.au';
            $event_id = '4';
            $test_attendee2 = new Attendee($name, $email, $event_id);
            $test_attendee2->save();

            $test_attendee->delete();
            $result = Attendee::getAll();

            $this->assertEquals([$test_attendee2], $result);
        }

        function test_addTask()
        {
            $name = 'Geoff';
            $email = 'geoff@email.biz';
            $event_id = '2';
            $test_attendee = new Attendee($name, $email, $event_id);
            $test_attendee->save();

            $name = 'task';
            $description = 'things';
            $event_id = '4';
            $test_task = new Task($name, $description, $event_id);
            $test_task->save();

            $test_attendee->addTask($test_task->getId());
            $result = $test_attendee->getTasks();

            $this->assertEquals([$test_task], $result);
        }

        function test_getTasks()
        {
            $name = 'Geoff';
            $email = 'geoff@email.biz';
            $event_id = '2';
            $test_attendee = new Attendee($name, $email, $event_id);
            $test_attendee->save();

            $name = 'task';
            $description = 'things';
            $event_id = '4';
            $test_task = new Task($name, $description, $event_id);
            $test_task->save();

            $name = 'tisk';
            $description = 'thungs';
            $event_id = '2';
            $test_task2 = new Task($name, $description, $event_id);
            $test_task2->save();

            $test_attendee->addTask($test_task->getId());
            $test_attendee->addTask($test_task2->getId());
            $result = $test_attendee->getTasks();

            $this->assertEquals([$test_task, $test_task2], $result);
        }
    }
?>
