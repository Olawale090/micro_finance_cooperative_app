<?php 

    // include "config_enum.php";
    include "dto.php";
    
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

            $query ="SELECT SUM(amount)AS total_deposits,
                        (SELECT SUM(outstanding_loan_amount) FROM get_loan WHERE bvn = $bvn) AS total_loans,
                        ((SELECT SUM(amount) FROM user_deposit WHERE bvn = $bvn) - (SELECT SUM(outstanding_loan_amount) FROM get_loan WHERE bvn = $bvn) )AS current_savings,
                        (CASE 
                            WHEN (SELECT SUM(amount) FROM user_deposit WHERE bvn = $bvn) > (SELECT SUM(outstanding_loan_amount) FROM get_loan WHERE bvn = $bvn)  
                            THEN TRUE
                            ELSE FALSE
                            END
                        )  AS loan_payable
                    FROM user_deposit
                    WHERE bvn = $bvn;";

            $pass_query = mysqli_query($this->host,$query,MYSQLI_USE_RESULT);
            $data = $pass_query->fetch_assoc();

            var_dump($data);

            mysqli_free_result($pass_query);

            if(is_null($data)){
                echo json_encode([
                    "success"=>false,
                    "error"=> [
                        "message"=>"User deposit record can't be found"
                    ],
                    "status"=>400
                ]);
            }

            if(!is_null($data)){

                if($data["current_savings"]>$data["total_loans"]){

                    $fullname = $this->user_name;
                    $account_number = $this->user_account_number;
                    $amount_owed = $data["total_loans"];
                    $amount_paid = $data["total_loans"];
                    $account_status = user_acccount_status::CUSTOMER_NOT_OWING->value;

                    $query = "INSERT INTO pay_loan (fullname,account_number,amount_owed,amount_paid,account_status) 
                               VALUES('$fullname','$account_number','$amount_owed','$amount_paid','$account_status');
                               ";

                    $save_payment = mysqli_query($this->host,$query,MYSQLI_STORE_RESULT);

                    mysqli_free_result($save_payment);

                    echo $save_payment;

                    if($save_payment){

                        $loan_paid = loan_payment_status::LOAN_PAID->value;

                        $query = "UPDATE get_loan 
                                    SET loan_payment_status = '$loan_paid'
                                    SET loan_payment_date = NOW()
                                    WHERE bvn = '$bvn';
                                    ";

                        $pass_payment = mysqli_query($this->host,$query,MYSQLI_USE_RESULT);

                        if($pass_payment){
                            echo json_encode([
                                "success"=>true,
                                "data"=>[
                                    "message"=> "Loan payment successful"
                                ],
                                "status"=>200
                            ]);
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

                    
                }
                
            }
                
        }

    }

    $payments = new user_loan_payment();
    $payments->loan_payment_props();
    $payments->database_connection();
    $payments->user_loan_payment();

?>