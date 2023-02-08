<?php
    try {

        $connection = new PDO("mysql:localhost; dbname:pdo_test","root","");
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "connection established";

    } catch (PDOException $th) {
        throw $th;
        echo $th->getMessage();
    }

    class checker 
    {
        public static $surname = "moses";
        public $name = "olawale";
        private $school = "lautech";
        protected $matric_number = "132331";

    }
    
    $x = new checker();
    // echo "<br> {$x}";
    // echo "<br> {$x->name}";
    // echo "<br> {$x->school}";
    // echo "<br> {$x->matric_number}";
    // var_dump(new checker());

    // var_dump($_SERVER);

    // echo getenv(my_name);

?>