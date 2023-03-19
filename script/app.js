/***
 * web worker for different fetchings
 */

const workerHandler = function(){
    if(window.Worker){
        let myWorker = new Worker("../script/worker.js");
        let message = {
            addThis:{
                num1:1,
                num2:2
            }
        };

        myWorker.postMessage(message);

        myWorker.onmessage = function(e){
            console.dir(e.data.result);
        }
    }
};

workerHandler();