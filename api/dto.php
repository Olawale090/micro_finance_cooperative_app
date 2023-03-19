<?php

    include "config_enum.php";
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(E_ALL);
    
    session_start();

    interface cache{
        public function props();
        public function data_transfer();
    }

    abstract class user_cache implements cache
    {
        public function props(){
            $this->user_name = $_SESSION["NAME"];
            $this->user_bvn = $_SESSION["BVN"];
            $this->user_account_number = $_SESSION["ACCOUNT_NUMBER"];
            $this->user_bank = $_SESSION["BANK"];
            $this->user_dob = $_SESSION["DOB"];
            $this->initial_balance = dashboard_config::INITIAL_ACCOUNT_DEPOSIT->value;
            $this->deposit = $_SESSION["CURRENT_SAVINGS"];
        }

        public function data_transfer(){

            if(is_null($this->user_name) || is_null($this->user_bvn) || is_null($this->user_account_number) || is_null($this->user_bank) || is_null($this->user_dob)){

                echo json_encode([
                    "success"=>false,
                    "error"=>[
                        "message"=>"User data not available, page redirect required",
                        "status"=>400,
                        "redirect"=>true,
                    ]
                ]);
                
            } else{

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
                    "status"=>200
                ]);
            }
            
        }
       
    }
    

?>