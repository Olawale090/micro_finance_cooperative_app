<?php 

    include "dto.php";

    class user_log_out{

        public function __construct(){
            /**
             * properties for future references
             */
        }

        public function log_out(){
            $clear_login = session_destroy();
            if($clear_login){
                echo json_encode([
                    "success"=>true,
                    "message"=>"User account logged out",
                    "status"=>200
                ]);
            }else{
                echo json_encode([
                    "success"=>false,
                    "message"=>"User account failed to log out, please try again",
                    "status"=>200
                ]);
            }
        }

    }

    $log_out = new user_log_out();
    $log_out->log_out();

?>