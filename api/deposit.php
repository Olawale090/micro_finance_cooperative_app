<?php 
    // include "config_enum.php";
    include "dto.php";

    interface Ideposit{
        public function deposit_props();
        public function database_connection();
    }

    class account_deposit extends user_cache implements Ideposit {

        public function deposit_props(){
            $this->host = new mysqli(server_config::host->value,server_config::username->value,server_config::password->value,server_config::db_name->value);
            $this->amount = mysqli_real_escape_string($this->host,$_POST["amount"]);
            user_cache::props();
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


        public function make_deposit (){

            if(!empty($this->amount)){

                $amount = strip_tags($this->amount); 
                $query = "INSERT INTO user_deposit(account_name,bank_name,amount,account_number,bvn) 
                          VALUES ('$this->user_name','$this->user_bank','$amount','$this->user_account_number','$this->user_bvn')";

                $pass_query = mysqli_query($this->host,$query,MYSQLI_USE_RESULT);

                if($pass_query){
                    echo json_encode([
                        "success"=>true,
                        "data"=>[
                            "message"=>"Payment successful",
                            "status"=>200
                        ]
                    ]);
                }else{
                    echo json_encode([
                        "success"=>false,
                        "error"=>[
                            "message"=>"Account deposit successful",
                            "status"=>200
                        ]
                    ]);
                }
            }else{
                echo json_encode(
                    [
                        "success"=>false,
                        "error"=>[
                            "message"=>"Please fill the empty field(s)",
                            "status"=>200
                        ]
                    ]);
            }

        }

    }


    $deposit = new account_deposit();
    $deposit->deposit_props();
    $deposit->database_connection();
    $deposit->make_deposit();

?>