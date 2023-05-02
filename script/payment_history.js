"use strict"; 

const paymentHistory = function (){
    this.user_name = document.querySelector(".fullname_holder")
    this.data_bearer = document.querySelector(".table_data_bearer");

}

paymentHistory.prototype = {
    loadPaymentRecords(){
        window.addEventListener("load",(e)=>{
            e.preventDefault();

            const xhr = new XMLHttpRequest();
            xhr.open('GET',`../api/payment_history.php`,true);
            xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded');

            xhr.onload = ()=>{
                if(xhr.status === 200){
                    let response = JSON.parse(xhr.responseText);

                    if (response.success === true) {

                        this.user_name.textContent = response.data.payments[0].fullname;
                        let id = 1;
                        
                        for (const i in response.data.payments) {

                            this.data_bearer.innerHTML += 
                            `<tr class="data_block">
                                <th class="serial_no">${id++}</th>
                                <th class="fullname"> ${response.data.payments[i].fullname}</th>
                                <th class="account_number">${response.data.payments[i].account_number}</th>
                                <th class="amount_paid">&#8358;${response.data.payments[i].amount_paid}</th>
                                <th class="date">${response.data.payments[i].payment_date}</th>
                            </tr> `;
                            
                        }
                        
                    }

                    if(response.success === false){
                        this.messageText.innerHTML = response.error.message;
                    }
                    
                }
                
            }

            xhr.send();

        },false);
        
    }
};

const allPayments = new paymentHistory();
allPayments.loadPaymentRecords();

