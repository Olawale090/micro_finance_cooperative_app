"use strict"; 

const withdrawal = function (){

    this.userFullname = document.querySelector(".user_fullname");
    this.userAmountSaved = document.querySelector(".amount_deposit");

    this.withdrawalAmount = document.querySelector(".amount");
    this.receivingAccount = document.querySelector(".account_number");

    this.messageBox = document.querySelector(".message");
    this.messageText = document.querySelector(".message_text");
    this.loader = document.querySelector(".repayment_loader");
    this.notifier = document.querySelector(".notifier_wall");
    this.notifierMessage = document.querySelector(".notifier_message");

    this.paymentButton = document.querySelector(".payment_button");

}

withdrawal.prototype = {
    loadUserAccountData(){
        const xhr = new XMLHttpRequest();
        xhr.open('GET','http://localhost/cooperative_app/api/dao.php',true);
        xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded');

        xhr.onload = ()=>{

            if(xhr.status === 200){
                let response = JSON.parse(xhr.responseText);
                console.log(response);

                if(response.success === true){
        
                    this.userFullname.textContent = response.data.fullname;
                    this.userAmountSaved.innerHTML = "&#8358;" + response.data.amount_saved + ".00";

                }

                if(response.success === false){
                    this.notifier.style.display = "flex";
                }
                
            }
            
        }

        xhr.send();

    },

    makeWithdrawal(){
        this.paymentButton.addEventListener("click",(e)=>{
            e.preventDefault();

            const xhr = new XMLHttpRequest();
            xhr.open('POST','http://localhost/cooperative_app/api/withdrawal.php',true); // this is the ideal way
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
                        window.open("http://localhost/cooperative_app/dir/dashboard.html","_self");

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

const bank_withdrawal = new withdrawal();
bank_withdrawal.loadUserAccountData();
// bank_withdrawal.makeUserPayment();

