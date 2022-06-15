<?php
    namespace App\Functions;

    use App\Classes\Stylist;
    use App\Models\Stylist as StylistDB;
    use App\Models\Stylist_info;
    use App\Models\Stylist_area;
    use App\Models\Stylist_service;
    use App\Models\Stylist_freetime;
    use Illuminate\Http\Request;
    use Illuminate\Support\Carbon;
    use Illuminate\Support\Facades\DB;

    class StylistUserFunction{
        function __construct()
        {
            
        }
        //TODO:バリョテション
        //サインアップで入力したデータをデータベースに挿入する
        function signup(Request &$request){
            $stylistDB = new StylistDB();
            $stylist_infoDB = new Stylist_info();
            $stylistDB->email = $request->email;
            $stylistDB->password = $request->password;
            $stylist_infoDB->name = $request->name;
            $stylist_infoDB->address = $request->address;
            $year = $request->year;
            $month = $request->month;
            $day = $request->day;
            $stylist_infoDB->birthday = $year."-".$month."-".$day;
            $stylist_infoDB->sex = (int)$request->sex;
            $stylist_infoDB->phone = $request->phone;
            $stylist_infoDB->post = $request->post;
            $icon_path = $request->file("icon")->store("image");
            $stylist_infoDB->icon = $icon_path;
            $stylist_infoDB->save();
            $stylistDB->save();       
        }
        //TODO:バリョテション
        //サインインし、成功なら、データベースから取った情報をセッションに保存する
        function signin(Request &$request){
            $stylist_id = DB::table("stylists")->where("email","=",$request->email)->where("password","=",$request->password)->value("id");
            if($stylist_id){
                $this->data2session($stylist_id);
                // var_dump();
            }
        }
        //TODO:バリョテション
        //スタイリスト情報更新
        function info_update(Request &$request){
            $new_info = [
                'name' => $request->name,
                'phone' => $request->phone,
                'post' => $request->post,
                'address' => $request->address
            ];
            if($request->file('icon')){
                $new_info['icon'] = $request->file('icon')->store('image');
            }
            DB::table('stylist_infos')->where('id',$request->id)->update($new_info);
            $this->data2session($request->id);
        }
        //スタイリストの情報をセッションに保存する
        function data2session($stylist_id){
            $email =  DB::table("stylists")->where("id","=",$stylist_id)->value("email");
            $stylist_info = DB::table("stylist_infos")->where("id","=",$stylist_id)->first();
            $stylist = new Stylist($stylist_id,$stylist_info->name,$email,$stylist_info->address,$stylist_info->birthday,$stylist_info->sex==0?"女性":"男性",$stylist_info->phone,$stylist_info->post,$stylist_info->icon);
            session(["stylist"=>serialize($stylist)]);
        }
        //スタイリストの活動地域をデータベースから取得する
        function get_service_area(){
            $stylist = unserialize(session()->get("stylist"));
            $service_area_DB = DB::table('stylist_areas')->where('stylist_id','=',$stylist->getId())->pluck('area');
            $service_area = [];
            foreach($service_area_DB as $area){
                $service_area[] = $area;
            }
            return $service_area;
        }
        //スタイリストの活動地域をデータベースに追加する
        function insert_area(Request &$request){
            $area_DB = new Stylist_area();
            $stylist = unserialize(session()->get("stylist"));
            $area_DB->area = $request->area;
            $area_DB->stylist_id = $stylist->getId();
            $area_DB->save();
        }
        //スタイリストの活動地域を削除する
        function delete_area(Request &$request){
            $stylist = unserialize(session()->get("stylist"));
            DB::table('stylist_areas')->where('stylist_id','=',$stylist->getId())->where('area','=',$request->area)->delete();
        }
        //スタイリストのサービスをデータベースから取得する
        function get_service_menu(){
            $stylist = unserialize(session()->get("stylist"));
            $service_DB = DB::table('stylist_services')->where('stylist_id','=',$stylist->getId())->pluck('service');
            $service = [];
            foreach($service_DB as $s){
                $service[] = $s;
            }
            return $service;
        }
        //スタイリストのサービスをデータベースに追加する
        function insert_service(Request &$request){
            $service_DB = new Stylist_service();
            $stylist = unserialize(session()->get("stylist"));
            $service_DB->service = $request->service;
            $service_DB->stylist_id = $stylist->getId();
            $service_DB->save();
        }
        //スタイリストのサービスを削除する
        function delete_service(Request &$request){
            $stylist = unserialize(session()->get("stylist"));
            DB::table('stylist_services')->where('stylist_id','=',$stylist->getId())->where('service','=',$request->service)->delete();
        }
        //スタイリストのトップページにこれからの予約リと予約可能時間をデータベースから取得し、画面で表示する
        function top(){
            $stylist = unserialize(session()->get("stylist"));
            $reserve_list = DB::table('stylist_histories')->where("stylist_id","=",$stylist->getId())->where("end_time",">=",date("Y-m-d H:i:s"))->get();
            $freetime_list = DB::table('stylist_freetimes')->where("stylist_id","=",$stylist->getId())->where("end_time",">=",date("Y-m-d H:i:s"))->get();
            $status = DB::table('stylists')->where('id','=',$stylist->getId())->value('exist');
            return [$reserve_list,$freetime_list,$status];
        }
        //予約の詳細を取得する
        function reserve_detail($id){
            $stylist = unserialize(session()->get("stylist"));
            $reserve = DB::table('stylist_histories')->where("stylist_id","=",$stylist->getId())->where("id","=",$id)->first();
            return $reserve;
        }
        //予約リストを取得する
        function reserve(){
            $stylist = unserialize(session()->get("stylist"));
            $reserve_list = DB::table('stylist_histories')->where("stylist_id","=",$stylist->getId())->get();
            return $reserve_list;
        }        
        //TODO:バリョテション
        //予約可能時間を追加する
        function freetime_DB(Request &$request){
            $stylist = unserialize(session()->get("stylist"));
            $freetime = new Stylist_freetime();
            $freetime->stylist_id = $stylist->getId();            
            $freetime->start_time = Carbon::parse($request->start_time)->format('Y-m-d H:i:s');
            $freetime->end_time = Carbon::parse($request->end_time)->format('Y-m-d H:i:s');
            $freetime->save();
        }
        //予約可能の時間の削除
        function delete_freetime_DB(Request &$request){
            $stylist = unserialize(session()->get("stylist"));
            DB::table('stylist_freetimes')->where('id', '=', $request->id)->where('stylist_id','=',$stylist->getId())->delete();
        }
        //予約可能状態の切り替え
        function change_status(Request &$request){
            $stylist = unserialize(session()->get("stylist"));
            $status = DB::table('stylists')->where('id','=',$stylist->getId())->update(['exist'=>$request->status]);
        }
    }