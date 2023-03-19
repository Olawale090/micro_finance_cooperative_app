"use strict"; 

const user_login = function (){
    this.bvn = document.querySelector(".bvn_inp");
    this.password = document.querySelector(".password_inp");
    this.submitBtn = document.querySelector(".submit_btn");
    this.messageBox = document.querySelector(".message");
    this.messageText = document.querySelector(".message_text");
    this.loader = document.querySelector(".login_loader");
}

user_login.prototype = {
    loadUserData(){
        this.submitBtn.addEventListener("click",(e)=>{
            e.preventDefault();

            const xhr = new XMLHttpRequest();
            xhr.open('GET',`http://localhost/cooperative_app/api/user_login.php?bvn=${this.bvn.value}&password=${this.password.value}`,true);
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

                    if (response.success === true && response.data.message === 'Login successful') {
                        this.messageText.innerHTML = response.data.message;
                        this.messageText.style.color = "#50eb7f";
 
                        window.open("http://localhost/cooperative_app/dir/dashboard.html","_self")
                    }

                    if(response.success === false){
                        this.messageText.innerHTML = response.data.message;
                    }
                    
                    console.log(response);
                }
                
            }

            xhr.send();

        },false);
        
    }
};

const login = new user_login();
login.loadUserData();

