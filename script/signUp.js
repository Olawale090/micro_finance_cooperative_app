"use strict"; 

const user_register = function (){
    this.fullname = document.querySelector(".fullname_inp");
    this.account_number = document.querySelector(".account_inp");
    this.bank = document.querySelector(".bank_inp");
    this.bvn = document.querySelector(".bvn_inp");
    this.password = document.querySelector(".password_inp");
    this.dob = document.querySelector(".dob_inp");
    this.submitBtn = document.querySelector(".submit_btn");
    this.messageBox = document.querySelector(".message");
    this.messageText = document.querySelector(".message_text");
    this.loader = document.querySelector(".register_loader");
}

user_register.prototype = {
    createUserAccount(){
        this.submitBtn.addEventListener("click",(e)=>{
            e.preventDefault();

            let form = {
                fullname:this.fullname.value,
                account_number:this.account_number.value,
                bank: this.bank.value,
                bvn: this.bvn.value,
                password: this.password.value,
                dob: this.dob.value
            }

            console.log(form);

            const xhr = new XMLHttpRequest();
            xhr.open('POST',`../api/user_registration.php`,true);
            xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded');

            let params = `fullname=${this.fullname.value}&account_number=${this.account_number.value}&bank=${this.bank.value}&bvn=${this.bvn.value}&password=${this.password.value}&dob=${this.dob.value}`;

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
                        window.open("../index.php","_self")
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

const register = new user_register();
register.createUserAccount();

