<?php

    /**
    * @backupGlobals disabled
    * @backupStaticAttributes disabled
    */
    require_once 'src/Event.php';

    $server = 'mysql:host=localhost:8889;dbname=';
    $username = 'root';
    $password = 'root';
    $DB = new PDO($server, $username, $password);

    class EventTest extends PHPUnit_Framework_TestCase
    {
        function test_source_function() {
            $input = ' ';
            $test_source = new Source;

            $result = $test_source->test_function();

            $this->assertEquals(1, $result);
        }
    }

?>
