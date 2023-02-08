<?php
    // include "config_enum.php";
    include "dto.php";
    
    interface Iloan {
        public function loan_req_props();
        public function database_connection();
        public function make_loan_request();
    }

    class user_loan_request extends user_cache implements Iloan{
        public function loan_req_props(){
            $this->host = new mysqli(server_config::host->value,server_config::username->value,server_config::password->value,server_config::db_name->value);
            $this->loan_amount = mysqli_real_escape_string($this->host,$_POST["loan_amount"]);
            $this->loan_duration = mysqli_real_escape_string($this->host,$_POST["loan_duration"]);
            $this->loan_interest = loan_request_config::AVG_LOAN_INTEREST->value;
            $this->SIMPLE_INTEREST = $this->loan_interest/100*$this->loan_duration/$this->loan_duration*$this->loan_amount;
            $this->outstanding_amount = $this->loan_amount+$this->SIMPLE_INTEREST;
            user_cache::props();

            $this->MAXIMUM_LOAN_AMOUNT=$this->deposit*100/0.15;

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

        public function make_loan_request(){
    
            if(!empty($this->loan_amount) && !empty($this->loan_duration)){

                $amount = strip_tags($this->loan_amount);
                $duration = strip_tags($this->loan_duration);
                $bvn = $this->user_bvn;

                $query = "  SELECT account_name,
                                    account_number,
                                    bank_name,
                                    amount AS amount_first_deposited,
                                    transaction_date AS first_transaction_date,
                                    DATEDIFF(NOW(),transaction_date) AS transaction_start_days,
                                    SUM(amount) AS total_deposit
                            FROM user_deposit
                            WHERE bvn = '$bvn'
                            LIMIT 1;
                         ";

                $pass_query = mysqli_query($this->host,$query,MYSQLI_USE_RESULT);
                $data = $pass_query->fetch_assoc();

                mysqli_free_result($pass_query);

                if($data["transaction_start_days"] >= 90){
                    
                    if($data["total_deposit"] >= loan_request_config::MIN_NET_DEPOSIT->value){

                        $query ="  SELECT outstanding_loan_amount,
                                          account_status   
                                    FROM get_loan
                                    WHERE bvn = '$bvn'
                                    LIMIT 1;
                                ";

                        $passer = mysqli_query($this->host,$query,MYSQLI_USE_RESULT);
                        $data = $passer->fetch_assoc();

                        mysqli_free_result($passer); 

                        echo $data["account_status"]. " : status";

                        if(!is_null($data)){

                            if($data["account_status"] == user_acccount_status::CUSTOMER_OWING->value){
                                echo json_encode([
                                    "success"=>false,
                                    "data"=>[
                                        "message"=>"Please pay up your outstanding loan to get another one",
                                        "loan_status"=>loan_status::LOAN_FAILED,
                                        "status"=>500
                                    ]
                                ]);
                            }
                                
                        }


                        if(is_null($data["account_status"])){

                            $username = $this->user_name;
                            $bvn = $this->user_bvn;
                            $account_number = $this->user_account_number;
                            $user_bank = $this->user_bank;
                            $amount = $this->loan_amount;
                            $duration = $this->loan_duration;
                            $interest = $this->SIMPLE_INTEREST;
                            $available_loan_amount = $this->MAXIMUM_LOAN_AMOUNT;
                            $loan_granted = loan_status::LOAN_GRANTED->value;
                            $account_status = user_account_status::CUSTOMER_OWING->value;
                            $outstanding_loan = $this->outstanding_amount;

                            $query = "SELECT DATE_ADD(NOW(), INTERVAL $duration DAY)) AS loan_payback_date";
                            $date_query = mysqli_query($this->host,$query,MYSQLI_USE_RESULT);
                            $data = $data_query->fetch_assoc();

                            mysqli_free_result($date_query);

                            $loan_payback_date = $data["loan_payback_date"];

                            if(!is_null($loan_payback_date)){
                                try {
                                        $add_loan_query = " INSERT INTO get_loan (fullname,account_number,amount_request,bvn,bank_name,loan_interest,loan_duration,loan_status,maximum_loan_capacity,outstanding_loan_amount,account_status,payment_deadline) 
                                                            VALUES ('$username','$account_number','$amount','$bvn','$user_bank','$interest','$duration','$loan_granted','$available_loan_amount','$outstanding_loan','$account_status','$loan_payback_date')
                                                        ";

                                        $pass_query = mysqli_query($this->host,$add_loan_query,MYSQLI_USE_RESULT);

                                        if($pass_query){

                                            echo json_encode([
                                                "success"=>true,
                                                "data"=>[
                                                    "message"=>"Loan granted successfuly",
                                                    "loan_status"=>loan_status::LOAN_GRANTED,
                                                    "status"=>200
                                                ]
                                            ]);
                
                                        }else{
                                            echo json_encode([
                                                "success"=>false,
                                                "data"=>[
                                                    "message"=>"Loan request failed, please try again",
                                                    "loan_status"=>loan_status::LOAN_FAILED,
                                                    "status"=>500
                                                ]
                                            ]);
                                        }

                                } catch (Exception $e) {
                                    echo "SQL RUNNING ERROR: ".$e;
                                }
                            }

                        }else{
                            echo json_encode([
                                "success"=>false,
                                "error"=>[
                                    "message"=> "Please pay your outstanding loan, thank you",
                                    "loan_status"=>loan_status::LOAN_DECLINED,
                                    "status"=>200
                                ]
                            ]);
                        }




                    }else{
                        echo json_encode([
                            "success"=>true,
                            "data"=>[
                                "message"=>"You're not eligible for loan, inadequate account deposit",
                                "loan_status"=>loan_status::LOAN_PENDING,
                                "status"=>200
                            ]
                        ]);
                    }

                }else{
                    echo json_encode([
                        "success"=>false,
                        "error"=>[
                            "message"=> "Sorry you're not eligible for loan, ensure to make deposits to aid your loan requests ",
                            "loan_status"=>loan_status::LOAN_DECLINED,
                            "status"=>200
                        ]
                    ]);

                }
        }else{
            echo json_encode([
                "success"=>false,
                "error"=>[
                    "message"=> "Please fill the empty field(s)",
                    "status"=>200
                ]
            ]);
        }
    
    }
}

    $loan = new user_loan_request();
    $loan->loan_req_props();
    $loan->database_connection();
    $loan->make_loan_request();

?>