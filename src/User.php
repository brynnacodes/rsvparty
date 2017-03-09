<?php
    class User {
        private $name;
        private $password;
        private $email;
        private $id;

        function __construct($name, $password, $email, $id = null)
        {
            $this->name = $name;
            $this->password = $password;
            $this->email = $email;
            $this->id = $id;
        }

        function setEmail($email)
        {
            $this->email = $email;
        }

        function getEmail()
        {
            return $this->email;
        }

        function setName($name)
        {
            $this->name = $name;
        }

        function getName()
        {
            return $this->name;
        }

        function setPassword($password)
        {
            $this->password = $password;
        }

        function getPassword()
        {
            return $this->password;
        }

        function getId()
        {
            return $this->id;
        }

        function logIn($password)
        {
            if ($password == $this->getPassword()) {
                $_SESSION['attendee'] = [];
                $_SESSION['user'] = $this;
                return ['name' => $this->getName(), 'id' => $this->getId()];
            } else {
                return "password";
            }
        }

        function logOut()
        {
            $_SESSION['user'] = [];
        }

        function save()
        {
            $save = $GLOBALS['DB']->prepare("INSERT INTO users (name, password, email) VALUES (:name, :password, :email);");
            $save->execute([':name' => $this->getName(), ':password' => $this->getPassword(), ':email' => $this->getEmail()]);
            $this->id = $GLOBALS['DB']->lastInsertId();
        }

        function update($new_name, $new_password)
        {
            $this->setName($new_name);
            $this->setPassword($new_password);
            $update = $GLOBALS['DB']->prepare("UPDATE users SET name = :name, password = :password, email = :email WHERE id = :id;");
            $update->execute([':name' => $this->getName(), ':password' => $this->getPassword(), ':email' => $this->getEmail(), ':id' => $this->getId()]);
        }

        function delete()
        {
            $GLOBALS['DB']->exec("DELETE FROM users WHERE id = {$this->getId()};");
        }

        static function alreadyExists($user_name)
        {
            $found_user = User::findByUsername($user_name);
            if ($found_user != []) {
                return true;
            } else {
                return false;
            }
        }

        function getEvents()
        {
            $events = [];
            $returned_events = $GLOBALS['DB']->query("SELECT * FROM events WHERE user_id = '{$this->getId()}';");
            if ($returned_events) {
                $events = $returned_events->fetchAll(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'Event', ['user_id','name', 'date_time', 'description', 'location', 'guest_key','id']);
            }
            else {
                return false;
            }
            return $events;
        }

        static function find($id)
        {
            $returned_user = $GLOBALS['DB']->query("SELECT * FROM users WHERE id = {$id};");
            $user = $returned_user->fetchAll(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'User', ['name', 'password', 'email', 'id']);
            return $user[0];
        }

        static function findByUsername($name)
        {
            $returned_user = $GLOBALS['DB']->query("SELECT * FROM users WHERE name = '{$name}';");
            $user = $returned_user->fetchAll(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'User', ['name', 'password', 'email', 'id']);
            if ($user) {
                return $user[0];
            } else {
                return [];
            }
        }

        static function getAll()
        {
            $returned_users = $GLOBALS['DB']->query("SELECT * FROM users;");
            if ($returned_users) {
                $users = $returned_users->fetchAll(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'User', ['name', 'password', 'email', 'id']);
            } else {
                $users = [];
            }
            return $users;
        }

        static function deleteAll()
        {
            $GLOBALS['DB']->exec('DELETE FROM users;');
        }
    }


?>
