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

            $query = "SELECT account_name as name,
                        account_number as account,
                        SUM(amount) as total_deposit,
                        (SELECT SUM(outstanding_loan_amount) FROM get_loan WHERE bvn = '$bvn' AND account_status='$owe_loan' ) AS total_loans,
                        (SELECT SUM(amount) FROM user_deposit WHERE bvn = '$bvn') - 
                        (SELECT SUM(outstanding_loan_amount) FROM get_loan WHERE bvn = '$bvn' AND account_status = '$owe_loan') AS current_savings,
                        (SELECT account_status FROM cooperative_app.get_loan WHERE bvn = '$bvn' AND account_status = '$owe_loan') AS current_account_get_loan_status,
                        (SELECT payment_deadline FROM cooperative_app.get_loan WHERE bvn = '$bvn' AND account_status = '$owe_loan') AS loan_deadline,
                        (SELECT account_status FROM cooperative_app.pay_loan WHERE bvn = '$bvn') AS pay_loan_account_status
                    
                        FROM cooperative_app.user_deposit
                        WHERE bvn = '$bvn';
                     ";

            $pass_query = mysqli_query($this->host,$query,MYSQLI_USE_RESULT);
            $data = $pass_query->fetch_assoc();

            
            if (is_null($data["current_savings"]>0)){
                $_SESSION["CURRENT_SAVINGS"] = 0; 
            }else{
                $_SESSION["CURRENT_SAVINGS"] = $data["current_savings"];
            }

            mysqli_free_result($pass_query);

            if(is_null($data)){
                echo json_encode(
                    [
                        "success"=>false,
                        "data"=>[
                            "fullname"=>$data["name"],
                            "account_number"=>$data["account"],
                            "default_deposit"=>dashboard_config::INITIAL_ACCOUNT_DEPOSIT->value
                        ],
                        "error"=>[
                            "message"=>"Deposit records not found",
                            "status"=>400
                        ]
                    ]
                );

                
            }
            
            if(!is_null($data["name"])){

                echo json_encode(
                    [
                        "success"=>true,
                        "data"=>[
                            "fullname"=>$data["name"],
                            "account_number"=>$data["account"],
                            "bvn"=> $this->user_bvn,
                            "total_deposit"=>$data["total_deposit"],
                            "total_loans"=>$data["total_loans"],
                            "initial_savings"=>dashboard_config::INITIAL_ACCOUNT_DEPOSIT->value,
                            "current_savings"=>$data["current_savings"],
                            "loan_deadline"=>$data["loan_deadline"],
                            "account_status"=>$data["current_account_get_loan_status"]
                        ],

                        "message"=>"Deposit records found successfuly",
                        "status"=>200
                    ]
                );
                
            }

        }

    }

    $dashboard = new dashboard();
    $dashboard->board_props();
    $dashboard->database_connection();
    $dashboard->user_dashboard();
    // $dashboard->data_transfer();

?>