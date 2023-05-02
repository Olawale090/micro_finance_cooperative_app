<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>loan request</title>
    <link rel="icon" href="../assets/images/cooperative_icon.png" sizes="16x16">
    <link rel="stylesheet" href="../styles/app.css">
    <link rel="stylesheet" href="../styles/register.css">
    <link rel="stylesheet" href="../styles/loan_request.css">
    <link rel="stylesheet" href="../styles/modal.css">
    <link rel="stylesheet" href="../styles/loader.css">
    <script src="../script/loan_request.js" async></script>
</head>

<body>
    <section class="hero loan_request_hero">
        <div class="nav_bar">
            <img src="../assets/images/9375592_avatars_accounts_man_male_people_icon.png" alt="user_avatar">
            <li class="fullname username">full name</li>
            <li class="fullname">
                <a class="home_link" href="./dashboard.php">dashboard</a>
            </li>
        </div>
    </section>
    
    <form action="" class="loan_request_form form">

        <label for="title" class="form_title">my cooperative</label>
        
        <label for="bank_name" class="label bank_name"> Amount request </label>
        <input type="bank_name" name="loan_amount" id="" class="input amount_inp" placeholder="Please enter bank">

        <label for="dob" class="label dob">Loan duration</label>
        <select class="input duration_inp" name="loan_duration" id="">
            <option value="30">30 days</option>
            <option value="60">60 days</option>
            <option value="90">90 days</option>
        </select>
        
        <button type="submit" class="btn submit_btn">Get loan</button>
        <footer class="message">
            <div class="message_text"> message</div>
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