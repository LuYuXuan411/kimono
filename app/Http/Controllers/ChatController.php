<?php

namespace App\Http\Controllers;

use App\Functions\ChatFunction;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    //新しいメッセージをstylist_chatsに挿入する
    function insert_stylist(Request &$request){
        $chat_f = new ChatFunction();
        $chat_f->insert_stylist($request);
    }
    //スタイリストユーザが選択した顧客とのメッセージを取る    
    function stylist_user_get_message(Request &$request){
        $chat_f = new ChatFunction();
        $message_list = $chat_f->stylist_user_get_message($request);
        return $message_list;
    }

    function make_reserve(Request &$request){
        $chat_f = new ChatFunction();
        $url = $chat_f->make_reserve($request);
        return $url;
    }

}
