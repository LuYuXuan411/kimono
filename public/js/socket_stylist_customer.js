let socket = io("192.168.10.209:3120");
let stylist_id = document.getElementById('stylist_id').value;
let customer_id = document.getElementById('customer_id').value;
let url = document.getElementById('url').value;
let csrf = document.getElementById('csrf').value;
let message_box = document.getElementById('message_box');
let msg = document.getElementById("message");
socket.emit('stylist_customer_join',customer_id);
msg.addEventListener('keyup',function(e){
    if(e.shiftKey){
        if(e.keyCode==13){
            sendMsg();
        }
    }
})
function sendMsg(){
    if(msg.value){
        socket.emit('stylist_customer_send',{stylist_id:stylist_id,customer_id:customer_id,message:msg.value});
        $.ajax({
            url:url,
            type:'POST',
            data:{"_token":csrf,stylist_id:stylist_id,customer_id:customer_id,message:msg.value,from:1},
            success:function(){
                console.log(1);
            },
            error:function(msg){
                console.log(msg);
            }
        })        
        msg.value = "";    
    }
}
socket.on('from_stylist',function(msg){
    make_message_stylist(msg);
})
socket.on('from_customer',function(msg){
    make_message_customer(msg);
})

function make_message_customer(message){
    let out_div = document.createElement("div");
    let inner_div = document.createElement("div");
    let pre = document.createElement("pre");
    out_div.classList.add("self");
    inner_div.classList.add("inner_div");
    pre.textContent = message;
    inner_div.appendChild(pre);
    out_div.appendChild(inner_div);    
    message_box.appendChild(out_div);
}
function make_message_stylist(message){
    let out_div = document.createElement("div");
    let inner_div = document.createElement("div");
    let pre = document.createElement("pre");
    out_div.classList.add("other_side");
    inner_div.classList.add("inner_div");
    pre.textContent = message;
    inner_div.appendChild(pre);
    out_div.appendChild(inner_div);    
    message_box.appendChild(out_div);
}