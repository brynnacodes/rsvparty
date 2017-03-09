<?php
    /**
    * @backupGlobals disabled
    * @backupStaticAttributes disabled
    */
    require_once "src/User.php";

    $server = 'mysql:host=localhost:8889;dbname=rsvparty_test';
    $username = 'root';
    $password = 'root';
    $DB = new PDO($server, $username, $password);


    class UserTest extends PHPUnit_Framework_TestCase
    {
        protected function tearDown()
        {
            Event::deleteAll();
            Attendee::deleteAll();
            User::deleteAll();
            $_SESSION['user'] = [];
        }

        function test_save()
        {
            $name = 'Bob';
            $password = 'pass';
            $email = 'bob@email.com';
            $new_user = new User($name, $password, $email);
            $new_user->save();

            $result = User::getAll();

            $this->assertEquals($new_user, $result[0]);
        }

        function test_getAll()
        {
            $name = 'Bob';
            $password = 'pass';
            $email = 'bob@email.com';
            $new_user = new User($name, $password, $email);
            $new_user->save();

            $name2 = 'Bob2';
            $password2 = 'pass2';
            $email2 = 'bob2@email.com';
            $new_user2 = new User($name2, $password2, $email2);
            $new_user2->save();

            $result = User::getAll();

            $this->assertEquals([$new_user, $new_user2], $result);
        }

        function test_deleteAll()
        {
            $name = 'Bob';
            $password = 'pass';
            $email = 'bob@email.com';
            $new_user = new User($name, $password, $email);
            $new_user->save();

            $name2 = 'Bob2';
            $password2 = 'pass2';
            $email2 = 'bob2@email.com';
            $new_user2 = new User($name2, $password2, $email2);
            $new_user2->save();

            User::deleteAll();
            $result = User::getAll();

            $this->assertEquals([], $result);
        }

        function test_find()
        {
            $name = 'Bob';
            $password = 'pass';
            $email = 'bob@email.com';
            $new_user = new User($name, $password, $email);
            $new_user->save();

            $name2 = 'Bob2';
            $password2 = 'pass2';
            $email2 = 'bob2@email.com';
            $new_user2 = new User($name2, $password2, $email2);
            $new_user2->save();

            $result = User::find($new_user->getId());

            $this->assertEquals($new_user, $result);
        }

        function test_update()
        {
            $name = 'Bob';
            $password = 'pass';
            $email = 'bob@email.com';
            $new_user = new User($name, $password, $email);
            $new_user->save();

            $new_name = 'Dave';
            $new_user->update($new_name, $password);
            $result = User::getAll();

            $this->assertEquals($new_name, $result[0]->getName());
        }

        function test_getEvents()
        {
            $name = 'Bob';
            $password = 'pass';
            $email = 'bob@email.com';
            $new_user = new User($name, $password, $email);
            $new_user->save();

            $new_user_id = $new_user->getId();

            $event_name1 = 'Sausage Party';
            $date_time1 = '2017-04-05 00:12:12';
            $location1 = 'Portland, OR';
            $description1 = 'A sausage party, obviously.';
            $guest_key1 = '12345';
            $test_event1 = new Event($new_user_id, $event_name1, $date_time1, $location1, $description1, $guest_key1);
            $test_event1->save();

            $event_name2 = 'Taco Tuesday';
            $date_time2 = '2027-04-05 00:22:22';
            $location2 = 'Beaverton, OR';
            $description2 = "Do not come. Always come.";
            $guest_key2 = '22345';
            $test_event2 = new Event($new_user_id, $event_name2, $date_time2, $location2, $description2, $guest_key2);
            $test_event2->save();

            $result = $new_user->getEvents();
            $expected_results = [$test_event1, $test_event2];

            $this->assertEquals($expected_results, $result);

        }

        function test_delete()
        {
            $name = 'Bob';
            $password = 'pass';
            $email = 'bob@email.com';
            $new_user = new User($name, $password, $email);
            $new_user->save();

            $name2 = 'Bob2';
            $password2 = 'pass2';
            $email2 = 'bob2@email.com';
            $new_user2 = new User($name2, $password2, $email2);
            $new_user2->save();

            $new_user->delete();
            $result = User::getAll();

            $this->assertEquals([$new_user2], $result);
        }

        function test_logIn_success()
        {
            $name = 'Bob';
            $password = 'pass';
            $email = 'bob@email.com';
            $new_user = new User($name, $password, $email);
            $new_user->save();

            $new_user->logIn($password);
            $result = $_SESSION['user'];

            $this->assertEquals($new_user, $result);
        }

        function test_logIn_failure()
        {
            $name = 'Bob';
            $password = 'pass';
            $email = 'bob@email.com';
            $new_user = new User($name, $password, $email);
            $new_user->save();

            $new_user->logIn('wrong');
            $result = $_SESSION['user'];

            $this->assertEquals([], $result);
        }

        function test_findByUsername()
        {
            $name = 'Bob';
            $password = 'pass';
            $email = 'bob@email.com';
            $new_user = new User($name, $password, $email);
            $new_user->save();

            $name2 = 'Bob2';
            $password2 = 'pass2';
            $email2 = 'bob2@email.com';
            $new_user2 = new User($name2, $password2, $email2);
            $new_user2->save();

            $result = User::findByUsername($new_user->getName());

            $this->assertEquals($new_user, $result);
        }
    }

?>
