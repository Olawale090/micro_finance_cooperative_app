<?php 
    // include "config_enum.php";
    include "dto.php";

    interface Iwithdrawal{
        public function withdrawal_props();
        public function database_connection();
        public function account_withdrawal();
    }

    class withdrawal extends user_cache implements Iwithdrawal {

        public function withdrawal_props(){
            $this->host= new mysqli("sql309.epizy.com","epiz_33892097","o73zshWSjR","epiz_33892097_cooperative_app");
            // $this->host = new mysqli(server_config::host->value,server_config::username->value,server_config::password->value,server_config::db_name->value);
            $this->amount = mysqli_real_escape_string($this->host,$_POST["amount"]);
            $this->account_number = mysqli_real_escape_string($this->host,$_POST["account_number"]);
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


        public function account_withdrawal (){

            if(!empty($this->amount) && !empty($this->account_number) ){
                
                /***
                 * checking user maximum withdrawable amount and validation of transaction
                
                 */

                $amount = strip_tags($this->amount); 
                $account_number = strip_tags($this->account_number);
                $bvn = $this->user_bvn;

                $query ="   SELECT SUM(amount) AS total_deposits,
                            (SELECT SUM(outstanding_loan_amount) FROM get_loan WHERE bvn = $bvn) AS total_loans,
                            ((SELECT SUM(amount) FROM user_deposit WHERE bvn = $bvn) - (SELECT SUM(outstanding_loan_amount) FROM get_loan WHERE bvn = $bvn) ) AS current_savings,
                            (CASE 
                                WHEN (SELECT SUM(amount) FROM user_deposit WHERE bvn = $bvn) > (SELECT SUM(outstanding_loan_amount) FROM get_loan WHERE bvn = $bvn)  
                                THEN TRUE
                                ELSE FALSE
                                END
                            )  AS loan_payable
                            FROM user_deposit
                            WHERE bvn = $bvn;
                        ";

                $pass_query = mysqli_query($this->host,$query,MYSQLI_USE_RESULT);
                $data = $pass_query->fetch_assoc();
                mysqli_free_result($pass_query);

                $current_savings = $data["current_savings"];

                if($current_savings > $amount){
                    $query = "INSERT INTO withdrawal(account_name,account_number,bvn,withdrawal_amount) 
                              VALUES ('$this->user_name','$account_number','$this->user_bvn','$amount')";
                    $pass_query = mysqli_query($this->host,$query,MYSQLI_USE_RESULT);

                    if($pass_query){

                        echo json_encode([
                            "success"=>true,
                            "data"=>[
                                "message"=>"Money withdrawal succesful"
                            ],
                            "status"=>200
                        ]);

                    }

                    if($pass_query === false){
                        echo json_encode([
                            "success"=>false,
                            "data"=>[
                                "message"=>"Money withdrawal failed, please try again or contact customer support"
                            ],
                            "status"=>200
                        ]);
                    }

                    // MPH, MBChB, FACRRM, MBA, FRACGP, FACTM, DPH, DTH, MMED (FAM MED), DHSM, FIAM

//                     30 Queen Elizabeth Drive
// Dysart, QLD 4745 Australia
                }

                if($current_savings < $amount){
                     echo json_encode([
                            "success"=>false,
                            "error"=>[
                                "message"=>"The maximum amount you can withdraw is ".$current_savings
                            ],
                            "status"=>200
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