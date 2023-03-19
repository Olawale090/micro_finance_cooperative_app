<?php 
    // include "config_enum.php";
    include "dto.php";
    // session_start();

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
            $bvn = $this->user_bvn;
            $query = "SELECT * FROM pay_loan WHERE bvn = '$bvn'";
            $pass_query = mysqli_query($this->host,$query,MYSQLI_USE_RESULT);
            $all_payments = $pass_query->fetch_all(MYSQLI_ASSOC);
            
            if(!is_null($all_payments[0]["fullname"])){
                echo json_encode([
                    "success"=>true,
                    "data"=>[
                        "payments"=>$all_payments
                        
                    ],
                    "message"=>"payment history found successfuly",
                    "status"=>200
                ]);
                
            }else{
                echo json_encode([
                    "success"=>false,
                    "error"=>[
                        "message"=>"Loan history not found"
                    ],
                    "status"=>200
                ]);
            }

        }


    }

    $payment_hisiory = new payment_history();
    $payment_hisiory->payment_props(); 
    $payment_hisiory->database_connection(); 
    $payment_hisiory->payments(); 
?>