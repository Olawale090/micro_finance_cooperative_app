"use strict"; 

const loanPayment = function (){

    this.userFullname = document.querySelector(".user_fullname");
    this.userAmountSaved = document.querySelector(".amount_deposit");
    this.userAmountOwed = document.querySelector(".amount_owed");

    this.messageBox = document.querySelector(".message");
    this.messageText = document.querySelector(".message_text");
    this.loader = document.querySelector(".repayment_loader");
    this.notifier = document.querySelector(".notifier_wall");
    this.notifierMessage = document.querySelector(".notifier_message");

    this.paymentButton = document.querySelector(".payment_button");

}

loanPayment.prototype = {
    loadUserAccountData(){
        const xhr = new XMLHttpRequest();
        xhr.open('GET','../api/dao.php',true);
        xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded');

        xhr.onload = ()=>{

            if(xhr.status === 200){
                let response = JSON.parse(xhr.responseText);

                console.log(response);

                if(response.success === true){

                    if(response.data.account_status === "DEFAULT"){
                        this.userFullname.textContent = response.data.fullname;
                        this.userAmountSaved.innerHTML = "&#8358;" + response.data.amount_saved + ".00";
                        this.userAmountOwed.innerHTML = "&#8358;" + response.data.amount_owed + ".00";
                    }

                    if(response.data.account_status === "NOT DEFAULT" && response.data.first_loan_record !== undefined){
                        this.userFullname.textContent = response.data.fullname;
                        this.userAmountSaved.innerHTML = "&#8358;" + response.data.amount_saved + ".00";
                        this.userAmountOwed.innerHTML = "&#8358;" + response.data.amount_owed + ".00";
                    }

                    if(response.data.account_status === "NOT DEFAULT" && response.data.first_loan_record === undefined){
                        this.userFullname.innerHTML = response.data.fullname;
                        this.userAmountSaved.innerHTML = "&#8358;" + response.data.amount_saved + ".00";
                        this.userAmountOwed.innerHTML = "&#8358;" + 0+ ".00";
                    }
                    

                }

                if(response.success === false){
                    this.notifier.style.display = "flex";
                }
                
            }
            
        }

        xhr.send();

    },

    makeUserPayment(){
        this.paymentButton.addEventListener("click",(e)=>{
            e.preventDefault();

            const xhr = new XMLHttpRequest();
            xhr.open('POST','../api/loan_payments.php',true);
            xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded');

            xhr.onloadstart = ()=>{
                    this.messageBox.style.display = "flex"
                    this.loader.style.display = "flex";
                    this.messageText.style.display = "none";
                }

            xhr.onloadend = ()=>{
                this.loader.style.display = "none";
                this.messageText.style.display = "block"
            }

            xhr.onload = ()=>{

                if(xhr.status === 200){
                    let response = JSON.parse(xhr.responseText);

                    if(response.success === true){
                        this.messageText.innerHTML = response.data.message;
                        this.messageText.style.color = "#50eb7f";
                        window.open("../dir/dashboard.php","_self");

                    }

                    if(response.success === false){
                        this.notifier.style.display = "flex";
                        this.notifierMessage.innerHTML = response.error.message;
                    }
                    
                }
                
            }

            xhr.send();

        });
        
    }
};

const loanRepayment = new loanPayment();
loanRepayment.loadUserAccountData();
loanRepayment.makeUserPayment();

