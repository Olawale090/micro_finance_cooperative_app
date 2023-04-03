"use strict"; 

const user_dashboard = function (){
    this.userFullname = document.querySelector(".fullname");
    this.userAmountSaved = document.querySelector(".account_balance");
    this.userAmountOwed = document.querySelector(".amount_owed");
    this.eligibleLoan = document.querySelector(".loan_eligible");
    this.maximumEligibleLoan = document.querySelector(".maximum_loan");
    this.userAccountStatus = document.querySelector(".account_status");
    this.loanDeadline = document.querySelector(".payment_deadline");

    this.outstanding_loan_column = document.querySelector(".outstanding_payment_column");
    this.deadlineTitle = document.querySelector(".payment_deadline_title");
    this.amountOweTitle = document.querySelector(".amount_owed_title");

    this.loader = document.querySelector(".loader_wall");

}

user_dashboard.prototype = {
    loadUserData(){
        const xhr = new XMLHttpRequest();
        xhr.open('GET','http://mycooperative.epizy.com/api/dashboard.php',true);
        xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded');

        xhr.onloadstart = ()=>{
            this.loader.style.display = "flex";
        }

        xhr.onloadend = ()=>{
            this.loader.style.display = "none";
        }

        xhr.onload = ()=>{

            if(xhr.status === 200){
                let response = JSON.parse(xhr.responseText);

                if(response.data.fullname === undefined || response.data.fullname === null){
                    window.open("http://mycooperative.epizy.com/","_self");
                }

                if(response.success === true){

                    this.userFullname.textContent = response.data.fullname;
                    this.userAmountSaved.innerHTML = "&#8358;" + response.data.amount_saved + ".00";
                    this.userAmountOwed.innerHTML = "&#8358;" + response.data.amount_owed + ".00";
                    this.maximumEligibleLoan.innerHTML = "Maximum eligible loan: &#8358;"+response.data.maximum_loan_capacity + ".00";
                    this.userAccountStatus.textContent = "Account status: " + response.data.account_status;

                    if(response.data.account_status === "NOT DEFAULT"){
                        this.eligibleLoan.innerHTML = "Loan status: Eligible";
                        this.outstanding_loan_column.style.backgroundColor = "#8866EF";
                        this.outstanding_loan_column.style.backgroundImage = "url('../assets/images/undraw_savings_re_eq4w.svg')";
                        this.outstanding_loan_column.style.backgroundSize = "300px 200px";
                        this.loanDeadline.style.display = "none";
                        this.deadlineTitle.style.display = "none";
                        this.amountOweTitle.style.display = "none";
                        this.userAmountOwed.style.display = "none";

                    }
                    
                    if(response.data.account_status === "DEFAULT"){
                        this.eligibleLoan.innerHTML = "Loan status: Not Eligible";
                        this.loanDeadline.innerHTML = response.data.payment_deadline;
                        let deadline_date = response.data.payment_deadline.split(" ")[0].split("-");
                        this.loanDeadline.innerHTML = deadline_date[2] + "/" + deadline_date[1] + "/" +deadline_date[0];
                    }

                }
                
            }
            
        }

        xhr.send();

        }
};

const dashboard = new user_dashboard();
dashboard.loadUserData();


const log_out = function (){
    this.log_out_button = document.querySelector(".log_out");
}

log_out.prototype = {
    terminate_signin(){
        this.log_out_button.addEventListener('click',(e)=>{
            e.preventDefault();
            const xhr = new XMLHttpRequest();
            xhr.open('GET','http://mycooperative.epizy.com/cooperative_app/api/log_out.php',true);
            xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded');

           xhr.onload= ()=>{
            if(xhr.status === 200){
                let response = JSON.parse(xhr.responseText);
                console.warn(response);
                if(response.success === true){
                    window.open("http://mycooperative.epizy.com/","_self");
                }
                
            }
           }

           xhr.send();
        })
    }
};

let log_terminator = new log_out();
log_terminator.terminate_signin();



