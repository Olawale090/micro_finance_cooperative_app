<?php 
    
    enum server_config:string{
        case host = "localhost";
        case username = "root";
        case password = "";
        case db_name = "cooperative_app";
    }

    enum dashboard_config:int{
        case INITIAL_ACCOUNT_DEPOSIT = 0;
    }

    enum user_acccount_status:string{
        case CUSTOMER_OWING = "DEFAULT";
        case CUSTOMER_NOT_OWING = "NOT DEFAULT";
    }

    enum loan_request_config:int{
        case AVG_LOAN_INTEREST = 15;
        case MIN_NET_DEPOSIT = 100000;
        case ACCOUNT_MIN_DAYS = 90;
    }

    enum loan_status:string{
        case LOAN_DECLINED = "DECLINED";
        case LOAN_GRANTED = "GRANTED";
        case LOAN_PENDING = "PENDING";
        case LOAN_FAILED = "FAILED";
    }

    enum loan_payment_status:string{
        case LOAN_PAID = "LOAND PAID";
        case LOAN_OUTSTANDING = "LOAN OUTSTANDING";
    }


?>