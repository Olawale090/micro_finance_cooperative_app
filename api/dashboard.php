<?php 

    include "dto.php";

    interface user_board{
        public function board_props();
        public function database_connection();
        public function user_dashboard();
    }


    class dashboard extends user_cache implements user_board{
        
        public function board_props(){
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

        public function user_dashboard(){

            $bvn = $this->user_bvn;
            $owe_loan = user_acccount_status::CUSTOMER_OWING->value;

            $user_account_manager_query = " SELECT * 
                                            FROM user_account_manager 
                                            WHERE bvn= '$bvn';
                                          ";

            $user_account_manager_proc = mysqli_query($this->host,$user_account_manager_query,MYSQLI_USE_RESULT);
            $user_account_manager_data = $user_account_manager_proc->fetch_assoc();
 
            mysqli_free_result($user_account_manager_proc);

            
            $_SESSION["CURRENT_SAVINGS"] = $user_account_manager_data["amount_saved"];

            if(!is_null($user_account_manager_data["account_name"])){
                echo json_encode([
                        "success"=>true,
                        "data"=>[
                            "fullname"=>$user_account_manager_data["account_name"],
                            "account_number"=>$user_account_manager_data["account_number"],
                            "bvn"=> $user_account_manager_data["bvn"],
                            "bank_name"=>$user_account_manager_data["bank_name"],
                            "amount_saved"=>$user_account_manager_data["amount_saved"],
                            "amount_owed"=>$user_account_manager_data["amount_owed"],
                            "payment_deadline"=>$user_account_manager_data["payment_deadline"],
                            "initial_savings"=>dashboard_config::INITIAL_ACCOUNT_DEPOSIT->value,
                            "maximum_loan_capacity"=>$user_account_manager_data["maximum_loan_capacity"],
                            "account_status"=>$user_account_manager_data["account_status"]
                        ],

                        "message"=>"Deposit records found successfuly",
                        "status"=>200
                    ]
                );
            }

            if(is_null($user_account_manager_data["account_name"])){

                $query = "SELECT account_name AS name,
                        account_number AS account,
                        SUM(amount) AS total_deposit,
                        (SELECT amount FROM user_deposit WHERE bvn = '$bvn' ORDER BY id DESC LIMIT 1) AS last_deposit, 
                        (SELECT SUM(outstanding_loan_amount) FROM get_loan WHERE bvn = '$bvn' AND account_status='$owe_loan' ORDER BY account_status DESC LIMIT 1) AS total_loans,
                        (SELECT SUM(amount) FROM user_deposit WHERE bvn = '$bvn') - 
                        (SELECT SUM(outstanding_loan_amount) FROM get_loan WHERE bvn = '$bvn') AS current_savings,
                        (SELECT account_status FROM cooperative_app.get_loan WHERE bvn = '$bvn' AND account_status = '$owe_loan' ORDER BY account_status DESC LIMIT 1) AS current_account_get_loan_status,
                        (SELECT payment_deadline FROM cooperative_app.get_loan WHERE bvn = '$bvn' AND account_status = '$owe_loan' ORDER BY account_status DESC LIMIT 1) AS loan_deadline,
                        (SELECT account_status FROM cooperative_app.pay_loan WHERE bvn = '$bvn' ORDER BY account_status LIMIT 1) AS pay_loan_account_status
                    
                        FROM cooperative_app.user_deposit
                        WHERE bvn = '$bvn';
                     ";

                $pass_query = mysqli_query($this->host,$query,MYSQLI_USE_RESULT);
                $data = $pass_query->fetch_assoc();
    
                mysqli_free_result($pass_query);

                if(is_null($data["name"])){
                    echo json_encode([
                        "success"=>true,
                        "data"=>[
                            "fullname"=>$this->user_name,
                            "bvn"=>$this->user_bvn,
                            "account_number"=>$this->user_account_number,
                            "bank"=>$this->user_bank,
                            "dob"=>$this->user_dob,
                            "initial_balance"=>$this->initial_balance
                        ],
                        "message"=>"sufficient data not found please contact customer support",
                        "status"=>200
                    ]);

                }
            
                if(!is_null($data["name"])){

                    $_SESSION["CURRENT_SAVINGS"] = $data["current_savings"];

                    echo json_encode([
                            "success"=>true,
                            "data"=>[
                                "fullname"=>$data["name"],
                                "account_number"=>$data["account"],
                                "bvn"=> $this->user_bvn,
                                "total_deposit"=>$data["total_deposit"],
                                "recent_deposit"=>$data["last_deposit"],
                                "total_loans"=>$data["total_loans"],
                                "initial_savings"=>dashboard_config::INITIAL_ACCOUNT_DEPOSIT->value,
                                "current_savings"=>$data["current_savings"],
                                "loan_deadline"=>$data["loan_deadline"],
                                "account_status"=>$data["current_account_get_loan_status"]
                            ],

                            "message"=>"Deposit records found successfuly",
                            "status"=>200
                        ]);
                    
                }
            }

            

        }

    }

    $dashboard = new dashboard();
    $dashboard->board_props();
    $dashboard->database_connection();
    $dashboard->user_dashboard();

?>