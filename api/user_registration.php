<?php 
    include "config_enum.php";

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET,PUT,POST,DELETE,PATCH,OPTIONS");
    header("Access-Control-Allow-Headers: *");
    header("Access-Control-Allow-Headers: X-Requested-With");
    header('Content-Type: application/json');
    header('Access-Control-Max-Age: 1000');
    header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');

    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(E_ALL);
    
    session_start();

    interface Iregistration{
        public function reg_props();
        public function database_connection();
        public function user_registration();
    }

    class user_registration_form implements Iregistration{
        public function reg_props (){
            $this->host= new mysqli(server_config::host->value,server_config::username->value,server_config::password->value,server_config::db_name->value);
            $this->fullname = mysqli_real_escape_string($this->host,$_POST['fullname']);
            $this->account_number = mysqli_real_escape_string($this->host,$_POST['account_number']);
            $this->bank = mysqli_real_escape_string($this->host,$_POST['bank']);
            $this->bvn = mysqli_real_escape_string($this->host,$_POST['bvn']);
            $this->password = md5(mysqli_real_escape_string($this->host,$_POST['password']));
            $this->dob = mysqli_real_escape_string($this->host,$_POST['dob']);
        }

        public function database_connection(){
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

        public function user_registration (){

            $fullname = strip_tags($this->fullname);
            $account_number = strip_tags($this->account_number);
            $bank = strip_tags($this->bank);
            $bvn = strip_tags($this->bvn);
            $dob = strip_tags($this->dob);
            $password = $this->password;

            if( !empty($fullname) && !empty($account_number) && !empty($bank) && !empty($bvn) && !empty($this->password) && !empty($dob)){

                $query = "SELECT * FROM user_registration 
                          WHERE bvn = '$bvn';
                          ";

                $passed_query = mysqli_query($this->host,$query,MYSQLI_USE_RESULT);
                $data = $passed_query->fetch_assoc();

                if(!is_null($data["fullname"])){
                    echo json_encode([
                        "success"=>false,
                        "error"=>[
                            "message"=>"User account already exist"
                        ],
                        "status"=>200
                    ]);

                }
                
                if(is_null($data)){

                    $add_user_query = "INSERT INTO user_registration (fullname,account_number,bank_name,bvn,password,dob) 
                               VALUES('$fullname','$account_number','$bank','$bvn','$password','$dob')";

                    $pass_user_query = mysqli_query($this->host,$add_user_query,MYSQLI_USE_RESULT);

                    if($pass_user_query){
                        echo json_encode([
                            "success"=>true,
                            "data"=>[
                                "message"=>"Registration successful"
                            ],
                            "status"=>200
                        ]);
                    }else{
                        echo json_encode([
                            "success"=>false,
                            "error"=>[
                                "message"=>"Registration failed, please try again"
                            ],
                            "status"=>00
                        ]);
                    }
                }
            }else{
                echo json_encode([
                    "success"=>false,
                    "error"=>[
                        "message"=>"Please fill the empty field(s)"
                    ],
                    "status"=>200
                ]);
            }
                
        
        }
    }

    $user_account = new user_registration_form();
    $user_account->reg_props();
    $user_account->database_connection();
    $user_account->user_registration();

?>