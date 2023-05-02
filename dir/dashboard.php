<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="icon" href="../assets/images/cooperative_icon.png" sizes="16x16">
    <link rel="stylesheet" href="../styles/app.css">
    <link rel="stylesheet" href="../styles/register.css">
    <link rel="stylesheet" href="../styles/loan_request.css">
    <link rel="stylesheet" href="../styles/payment_history.css">
    <link rel="stylesheet" href="../styles/loan_repayment.css">
    <link rel="stylesheet" href="../styles/dashboard.css">

    <script async type="module" src="../script/app.js" ></script>
    <script async type="module" src="../script/dashboard.js"></script>
</head>
<body>
    <section class="hero dashboard_hero">
        <div class="menu_parent">
            <div class="comp_name">my cooperative</div>
            <div class="nav_bar">
                <img src="../assets/images/9375592_avatars_accounts_man_male_people_icon.png" alt="user_avatar"  class="img_icon">
                <li class="fullname">full name</li>

                <button class="make_deposit">
                    <a class="deposit_link" href="deposit.php">Deposit</a>
                </button>

                <button class="payment_records">
                    <a class="payment_link" href="payment_history.php">Payment history</a>
                </button>

                <button class="log_out">
                    log out
                </button>

            </div>
        </div>

        <div class="data_board">
            <div class="amount_saved_column">
                <div class="board_title">
                    Account balance
                </div>
    
                <div class="board_amount account_balance">
                    
                </div>
            </div>

            <div class="outstanding_payment_column">
                <div class="board_title amount_owed_title">
                    Amount owed
                </div>
    
                <div class="board_amount amount_owed">
                    
                </div>

                <div class="payment_deadline_title">
                    Payment deadline
                </div>

                <div class="board_amount payment_deadline">
                    payment deadline
                </div>
            </div>
            
        </div>
        
    </section>

    <div class="activity_list">
        <list class="loan_activities">
            <div class="loan_text_parent account_status">
                
            </div>

            <div class="user_actions_parent">
                <button class="user_actions pay_loan_btn">
                    <a class="pay_loan_link" href="loan_repayment.php">Pay loan</a>
                </button>
            </div> 
        </list>

        <list class="loan_activities">
            <div class="loan_text_parent loan_eligible">
                
            </div>

            <div class="user_actions_parent">
                <button class="user_actions get_loan_btn">
                    <a class="get_loan_link" href="loan_request.php">Get loan </a> 
                </button>
            </div> 
        </list>

        <list class="loan_activities">
            <div class="loan_text_parent">
                <div class="loan_text maximum_loan">
                    Maximum eligible loan: Not available/&#8358;50,000.00
                </div> 
            </div>
            <div class="user_actions_parent">
                <button class="user_actions get_loan_btn">
                    <a class="get_loan_link" href="loan_request.php">Get loan </a>
                </button>
            </div>
        </list>

        <list class="loan_activities dashboard_info">
            Please ensure your bank account is active and make constant deposit to your cooperative account for higher 
            loan offers with lower interest.
        </list>

    </div>

    <div class="loader_wall">
        <div class="business_name">my cooperative</div>
        <div class="loader"></div>
    </div>
    
</body>
</html>