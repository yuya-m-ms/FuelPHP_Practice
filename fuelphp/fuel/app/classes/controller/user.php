<?php

class Controller_User extends Controller
{
    public function action_select($id)
    {
      if($id <= 0 || $id > 3){
          return "ユーザーは存在しません";
      }
      Session::set('user_id', $id);
      if(Session::get('user_id') == 1) $name = "田中太郎";
      if(Session::get('user_id') == 2) $name = "鈴木次郎";
      if(Session::get('user_id') == 3) $name = "山田花子";
      return $name . "に設定しました";
    }
}
