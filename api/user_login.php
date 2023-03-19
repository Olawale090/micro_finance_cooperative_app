<?php
    include "config_enum.php";
    session_start();

    class login{

        public function login_props(){
            $this->host = new mysqli(server_config::host->value,server_config::username->value,server_config::password->value,server_config::db_name->value);
            $this->bvn = mysqli_real_escape_string($this->host,$_GET['bvn']);
            $this->password = mysqli_real_escape_string($this->host,$_GET['password']);
        }

        public function database_connection (){
            if(mysqli_connect_errno()){
                echo json_encode(
                    [   "success"=>false,
                        "error"=>[
                            "message"=>"Internal server error, connection failed",
                            "status"=> 500
                        ]
                    ]
                );
            }
        }

        public function user_login (){
           
            $bvn = strip_tags($this->bvn);
            $password = md5(strip_tags($this->password));

            if(!empty($bvn) && !empty($this->password)){

                $query = "SELECT * FROM user_registration 
                          WHERE bvn = '$bvn' AND password = '$password';
                          ";

                $passed_query = mysqli_query($this->host,$query,MYSQLI_USE_RESULT);
                $data = $passed_query->fetch_assoc();

                if($data){

                    // caching of user data
                    $_SESSION["NAME"] = $data["fullname"];
                    $_SESSION["ACCOUNT_NUMBER"]=$data["account_number"];
                    $_SESSION["BVN"] = $data["bvn"];
                    $_SESSION["BANK"] = $data["bank_name"];
                    $_SESSION["DOB"] = $data["dob"];

                    echo json_encode([
                        "success"=>true,
                        "data"=>[
                            "message"=>"Login successful"
                        ],
                        "status"=>200
                    ]);

                }
                
                if(is_null($data)){
                    echo json_encode([
                        "success"=>false,
                        "data"=>[
                            "message"=>"Account does not exist, please register."
                        ],
                        "status"=>200
                    ]);
            
                }

            }
            
            if(empty($bvn) || empty($password)){
                echo json_encode([
                    "success"=>false,
                    "data"=>[
                        "message"=>"Please fill the empty field(s)"
                    ],
                    "status"=>200
                ]);
            }
                
        
        }
    }

    $user_login = new login();
    $user_login->login_props();
    $user_login->database_connection();
    $user_login->user_login();
?>