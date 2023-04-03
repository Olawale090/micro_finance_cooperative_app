"use strict"; 

const userLoanRequest = function (){
    this.amount = document.querySelector(".amount_inp");
    this.duration = document.querySelector(".duration_inp");
    
    this.submitBtn = document.querySelector(".submit_btn");
    this.messageBox = document.querySelector(".message");
    this.messageText = document.querySelector(".message_text");
    this.loader = document.querySelector(".loan_request_loader");

    this.username = document.querySelector(".username");
}

userLoanRequest.prototype = {

    userData(){

        const xhr = new XMLHttpRequest();
        xhr.open('GET','http://mycooperative.epizy.com/api/dao.php',true);
        xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded');

        xhr.onload = ()=>{

            if(xhr.status === 200){
                let response = JSON.parse(xhr.responseText);

                if(response.success === true){

                    this.username.textContent = response.data.fullname;

                }

                if(response.success === false){
                    this.notifier.style.display = "flex";
                }
                
            }
            
        }

        xhr.send();

    },


    getLoan(){
        this.submitBtn.addEventListener("click",(e)=>{
            e.preventDefault();

            const xhr = new XMLHttpRequest();
            xhr.open('POST',`http://mycooperative.epizy.com/api/loan_request.php`,true);
            xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded');

            let params = `loan_amount=${this.amount.value}&loan_duration=${this.duration.value}`;

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
                        window.open("http://mycooperative.epizy.com/dir/dashboard.html","_self")
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

const loanRequest = new userLoanRequest();
loanRequest.userData()
loanRequest.getLoan();

