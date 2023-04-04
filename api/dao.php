<?php 
    
    include "dto.php";
    interface Idao{
        public function dao_props();
        public function database_connection();
        public function dao_loan_payment();
    }

    class dao_loan_payment extends user_cache implements Idao{

        public function dao_props(){
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


        public function dao_loan_payment(){

            $bvn = $this->user_bvn;

            $user_account_manager_query = " SELECT * 
                                            FROM user_account_manager 
                                            WHERE bvn= '$bvn';
                                          ";

            $user_account_manager_proc = mysqli_query($this->host,$user_account_manager_query,MYSQLI_USE_RESULT);
            $user_account_manager_data = $user_account_manager_proc->fetch_assoc();
 
            mysqli_free_result($user_account_manager_proc);

            if(is_null($user_account_manager_data["account_name"])){
                echo json_encode([
                    "success"=>false,
                    "error"=> [
                        "message"=>"User deposit and loan record not found"
                    ],
                    "status"=>200
                ]);
            }

            if(!is_null($user_account_manager_data["account_name"])){
                echo json_encode([
                    "success"=>true,
                    "data"=> [
                        "fullname"=>$user_account_manager_data["account_name"],
                        "amount_saved"=>$user_account_manager_data["amount_saved"],
                        "amount_owed"=>$user_account_manager_data["amount_owed"],
                        "account_status"=>$user_account_manager_data["account_status"]
                    ],
                    "message"=>"user data found succesfuly",
                    "status"=>200
                ]);
            }


        }

    }

    $payments = new dao_loan_payment();
    $payments->dao_props();
    $payments->database_connection();
    $payments->dao_loan_payment();
    

?>