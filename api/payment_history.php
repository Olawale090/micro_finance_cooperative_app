<?php 
    // include "config_enum.php";
    include "dto.php";
    session_start();

    interface Ipayments{
        public function payment_props();
        public function database_connection();
        public function payments();
    }

    class payment_history extends user_cache implements Ipayments{

        public function payment_props(){
            $this->host= new mysqli(server_config::host->value,server_config::username->value,server_config::password->value,server_config::db_name->value);
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

        public function payments(){
            /* @loan payments
            @account deposits */
        }


    }


?>