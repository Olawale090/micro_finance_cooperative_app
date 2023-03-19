<?php 

    include "dto.php";
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(E_ALL);
    interface Iloan_payment{
        public function loan_payment_props();
        public function database_connection();
        public function user_loan_payment();
    }

    class user_loan_payment extends user_cache implements Iloan_payment{

        public function loan_payment_props(){
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

        public function user_loan_payment(){

            $bvn = $this->user_bvn;

            $query = "SELECT account_status 
                      FROM get_loan 
                      WHERE account_status = 'DEFAULT' AND bvn = '$bvn'
                     ";

            $check_loan_status = mysqli_query($this->host,$query,MYSQLI_USE_RESULT);
            $status = $check_loan_status->fetch_assoc();
            mysqli_free_result($check_loan_status);

            if(is_null($status["account_status"])){
                echo json_encode([
                    "success"=>false,
                    "error"=> [
                        "message"=>"You're not defaulting"
                    ],
                    "status"=>400
                ]);
            }

            if(!is_null($status["account_status"])){

                $query ="SELECT SUM(amount) AS total_deposits,
                        (SELECT SUM(outstanding_loan_amount) FROM get_loan WHERE bvn = $bvn) AS total_loans,
                        ((SELECT SUM(amount) FROM user_deposit WHERE bvn = $bvn) - (SELECT SUM(outstanding_loan_amount) FROM get_loan WHERE bvn = $bvn) ) AS current_savings,
                        (CASE 
                            WHEN (SELECT SUM(amount) FROM user_deposit WHERE bvn = $bvn) > (SELECT SUM(outstanding_loan_amount) FROM get_loan WHERE bvn = $bvn)  
                            THEN TRUE
                            ELSE FALSE
                            END
                        )  AS loan_payable,
                        (SELECT outstanding_loan_amount FROM get_loan WHERE bvn = $bvn AND account_status = 'DEFAULT' ORDER BY id DESC LIMIT 1)AS amount_owed
                        FROM user_deposit
                        WHERE bvn = $bvn;
                    ";

                $pass_query = mysqli_query($this->host,$query,MYSQLI_USE_RESULT);
                $data = $pass_query->fetch_assoc();
                mysqli_free_result($pass_query);

                if(is_null($data)){
                    echo json_encode([
                        "success"=>false,
                        "error"=> [
                            "message"=>"User deposit and loan record not found"
                        ],
                        "status"=>200
                    ]);
                }

                if(!is_null($data)){

                    if($data["total_deposits"]>$data["total_loans"]){
    
                        $fullname = $this->user_name;
                        $account_number = $this->user_account_number;
                        $bank = $this->user_bank;
                        $dob = $this->user_dob;
                        $balance = $this->initial_balance;
                        $deposit = $this->deposit;

                        $amount_owed = $data["amount_owed"];
                        $amount_paid = $data["amount_owed"];
                        
                        $account_status = user_acccount_status::CUSTOMER_NOT_OWING->value;
                        $today = date("Y-m-d H:i:s");
    
                        $query = "INSERT INTO pay_loan (fullname,account_number,amount_owed,amount_paid,account_status,bvn) 
                                  VALUES('$fullname','$account_number','$amount_owed','$amount_paid','$account_status','$bvn');
                                   ";
    
                        $save_payment = mysqli_query($this->host,$query,MYSQLI_USE_RESULT); 

                        if($save_payment){
    
                            $loan_paid = loan_payment_status::LOAN_PAID->value;
                            $query = "  UPDATE get_loan 
                                        SET account_status = '$account_status',
                                            loan_payment_status = '$loan_paid',
                                            loan_payment_date = '$today'
                                        WHERE bvn = '$bvn' AND account_status = 'DEFAULT';
                                    ";
    
                            $pass_payment = mysqli_query($this->host,$query,MYSQLI_USE_RESULT);
                            $current_savings = $data["current_savings"];
    
                            if($pass_payment){

                                $account_manager_query = "SELECT * FROM user_account_manager WHERE bvn = $bvn;";
                                $proc_manager_query = mysqli_query($this->host,$account_manager_query,MYSQLI_USE_RESULT);
                                $manager_data = $proc_manager_query->fetch_assoc();
                                $owe_loan = user_acccount_status::CUSTOMER_OWING->value;
                                mysqli_free_result($proc_manager_query);

                                if(is_null($manager_data["account_name"])){

                                    $query = "SELECT account_name AS account_name,
                                                account_number AS account_number,
                                                SUM(amount) AS total_deposits_recorded,
                                                (SELECT amount FROM user_deposit WHERE bvn = '$bvn' ORDER BY id DESC LIMIT 1) AS last_deposit, 
                                                (SELECT SUM(outstanding_loan_amount) FROM get_loan WHERE bvn = '$bvn' AND account_status='$owe_loan' ORDER BY id DESC LIMIT 1) AS total_outstanding_loans,
                                                (SELECT SUM(amount) FROM user_deposit WHERE bvn = '$bvn') - 
                                                (SELECT SUM(outstanding_loan_amount) FROM get_loan WHERE bvn = '$bvn') AS current_savings,
                                                (SELECT account_status FROM cooperative_app.get_loan WHERE bvn = '$bvn' ORDER BY id DESC LIMIT 1) AS current_account_get_loan_status,
                                                (SELECT maximum_loan_capacity FROM cooperative_app.get_loan WHERE bvn = '$bvn' ORDER BY id DESC LIMIT 1) AS maximum_loan_capacity,
                                                (SELECT payment_deadline FROM cooperative_app.get_loan WHERE bvn = '$bvn' ORDER BY id DESC LIMIT 1) AS loan_deadline,
                                                (SELECT account_status FROM cooperative_app.pay_loan WHERE bvn = '$bvn' ORDER BY id LIMIT 1) AS pay_loan_account_status
                                                FROM cooperative_app.user_deposit
                                                WHERE bvn = '$bvn';
                                         ";

                                    $pass_query = mysqli_query($this->host,$query,MYSQLI_USE_RESULT);
                                    $data = $pass_query->fetch_assoc();
                                    mysqli_free_result($pass_query);

                                    if(!is_null($data["account_name"])){

                                        $amount_saved = $data["current_savings"];
                                        $amount_owed = $data["total_outstanding_loans"];
                                        $payment_deadline = $data["loan_deadline"];
                                        $loan_status = $data["current_account_get_loan_status"];
                                        $maximum_loan_capacity = $data["maximum_loan_capacity"];
    
                                        $acc_mng_query = " INSERT INTO user_account_manager(account_name, account_number, bvn, bank_name, amount_saved, amount_owed,payment_deadline,maximum_loan_capacity,account_status) 
                                                           VALUES ('$fullname','$account_number','$bvn','$bank','$amount_saved','$amount_owed','$payment_deadline','$maximum_loan_capacity','$loan_status')
                                                        ";
        
                                        $acc_mng_deposit = mysqli_query($this->host,$acc_mng_query,MYSQLI_USE_RESULT);
    
                                        if($acc_mng_deposit){
    
                                            echo json_encode([
                                                "success"=>true,
                                                "data"=>[
                                                    "message"=> "Loan payment successful"
                                                ],
                                                "status"=>200
                                            ]);
    
                                        }else{
                                            
                                            echo json_encode([
                                                "success"=>false,
                                                "error"=>[
                                                    "message"=> "Loan payment failed"
                                                ],
                                                "status"=>200
                                            ]);
    
                                        }
                                    }else {
                                        echo json_encode([
                                            "success"=>false,
                                            "error"=>[
                                                "message"=> "Required deposit records not found, please contact customer support"
                                            ],
                                            "status"=>200
                                        ]);
                                    }

                                }

                                if(!is_null($manager_data["account_name"])){

                                    $loan_cap_query =" SELECT payment_deadline, maximum_loan_capacity  
                                                       FROM cooperative_app.get_loan
                                                       WHERE bvn = '$bvn' 
                                                       ORDER BY id DESC
                                                       LIMIT 1
                                                    ";
                                    
                                    $deadline_loan_cap_proc = mysqli_query($this->host,$loan_cap_query,MYSQLI_STORE_RESULT);
                                    $deadline_loan_cap_data  = $deadline_loan_cap_proc->fetch_assoc();
                                    mysqli_free_result($deadline_loan_cap_proc);

                                    if(is_null($deadline_loan_cap_data["payment_deadline"])){
                                        echo json_encode([
                                            "success"=>false,
                                            "error"=>[
                                                "message"=>"user payment deadline not found"
                                            ],
                                            "status"=>200
                                        ]);
                                    }

                                    if(!is_null($deadline_loan_cap_data["payment_deadline"])){

                                        $not_owing_loan = user_acccount_status::CUSTOMER_NOT_OWING->value;
                                        $loan_deadline = $deadline_loan_cap_data["payment_deadline"];
                                        $maximum_loan_capacity = $deadline_loan_cap_data["maximum_loan_capacity"];

                                        $update_manager_query = "  UPDATE user_account_manager 
                                                                    SET amount_saved = '$current_savings',
                                                                        amount_owed = '$amount_owed',
                                                                        payment_deadline = '$loan_deadline',
                                                                        maximum_loan_capacity = '$maximum_loan_capacity',
                                                                        account_status = '$not_owing_loan'
                                                                    WHERE bvn = '$bvn';
                                                                ";
    
                                        $update_manager_data = mysqli_query($this->host,$update_manager_query,MYSQLI_USE_RESULT);
                                    
                                        if($update_manager_data){
                                            echo json_encode([
                                                "success"=>true,
                                                "data"=>[
                                                    "message"=>"Loan payment successful"
                                                ],
                                                "status"=>200
                                            ]);
                                        }else{
                                            echo json_encode([
                                                "success"=>false,
                                                "error"=>[
                                                    "message"=>"Loan payment failed, cannot update user account manager"
                                                ],
                                                "status"=>200
                                            ]);
                                        }
                                        
                                    }
    
                                }
                                
                            }
    
                            
                        }else{
                            echo json_encode([
                                "success"=>false,
                                "error"=>[
                                    "message"=> "Loan payment failed"
                                ],
                                "status"=>200
                            ]);
                        }
    
                        
                    }else {
                        echo json_encode([
                            "success"=>false,
                            "error"=>[
                                "message"=> "Please deposit more fund to pay your loan"
                            ],
                            "status"=>200
                        ]);
                    }
                    
                }


            }

           
            
                
        }

    }

    $payments = new user_loan_payment();
    $payments->loan_payment_props();
    $payments->database_connection();
    $payments->user_loan_payment();
    

?>