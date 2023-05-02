<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Withdrawal</title>
    <link rel="icon" href="../assets/images/cooperative_icon.png" sizes="16x16">
    <link rel="stylesheet" href="../styles/app.css">
    <link rel="stylesheet" href="../styles/register.css">
    <link rel="stylesheet" href="../styles/loan_request.css">
    <link rel="stylesheet" href="../styles/modal.css">
    <link rel="stylesheet" href="../styles/loader.css">
    <link rel="stylesheet" href="../styles/loan_repayment.css">
    <script async type="module" src="../script/withdrawal.js"></script>
</head>

<body>
    <section class="hero loan_request_hero">
        <div class="nav_bar">
            <img src="../assets/images/9375592_avatars_accounts_man_male_people_icon.png" alt="user_avatar">
            <li class="fullname user_fullname"></li>
            <li class="fullname">
                <a class="home_link" href="../dir/dashboard.html">dashboard</a>
            </li>
        </div>
    </section>
    
    <form action="" class="loan_request_form form">

        <label for="title" class="form_title">my cooperative</label>

        <div class="amount_deposited">
            <div class="minor_deposit">Account balance</div>
            <div class="amount_deposit">&#8358; 2323243.00</div>
        </div>


        <label for="withdrawal_amount" class="label withdrawal_amount_label">Amount</label>
        <input type="text" name="" id="withdrawal_amount" class="input withdrawal_amount_inp" placeholder="Please enter withdrawal amount">
        
        <label for="withdrawal_acount" class="label withdrawal_account_label">Account number</label>
        <input type="text" name="account_number" id="" class="input withdrawal_account_inp" placeholder="Please enter withdrawal account number">
        
        <button type="submit" class="btn submit_btn">Withdrawal</button>
        <footer class="message">message
            <div class="form_loader loan_request_loader"></div>
        </footer>

    </form>

    <div class="notifier_wall">
        <div class="notifier">
            <div class="top_bar"></div>
        </div>
    </div>

</body>
</html>