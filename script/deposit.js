"use strict"; 

const user_deposit = function (){
    this.amount = document.querySelector(".amount_inp");
    
    this.submitBtn = document.querySelector(".submit_btn");
    this.messageBox = document.querySelector(".message");
    this.messageText = document.querySelector(".message_text");
    this.loader = document.querySelector(".deposit_loader");
}

user_deposit.prototype = {
    makeDeposit(){
        this.submitBtn.addEventListener("click",(e)=>{
            e.preventDefault();

            const xhr = new XMLHttpRequest();
            xhr.open('POST',`../api/deposit.php`,true);
            xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded');

            let params = `amount=${this.amount.value}`;

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

                    if (response.success === true) {
                        this.messageText.innerHTML = response.data.message;
                        this.messageText.style.color = "#50eb7f";
                        window.open("http://localhost/cooperative_app/dir/dashboard.html","_self")
                    }

                    if(response.success === false){
                        this.messageText.innerHTML = response.error.message;
                    }
                    
                }
                
            }

            xhr.send(params);

        },false);
        
    }
};

const deposit = new user_deposit();
deposit.makeDeposit();

