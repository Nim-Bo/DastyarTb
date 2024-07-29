<?php

echo 'Ok';
include('index.php');

if(is_dir('data/banners')){
    
    $bannercounts = fcount('data/banners', "banner*.json");
    $o = 0;
    for($i=1;$i <= 999;$i++){
        
        if(file_exists("data/banners/banner$i.json")){
            $o = $o + 1;
            $bannerdata = json_decode(file_get_contents("data/banners/banner$i.json"), true);
            $rbtime = $bannerdata['rbtime'];
            $sndtime = $bannerdata['start'];
            $timestamp = time();
            $time = date('H:i');
            
            if($timestamp >= $sndtime and $bannerdata['banners'] == null){
                
                $chat_id = $bannerdata['chat_id'];
                $msg_id = $bannerdata['msg_id'];
                $chlist = file_get_contents('data/chlist.txt');
                $chids = explode(',', $chlist);
                if(!file_exists('data/chcounts.txt')){
                    $chcounts = 0;
                }else{
                    $chcounts = file_get_contents('data/chcounts.txt');
                }
                foreach($chids as $chid){
                    
                    if($chid != null){
                        
                        $for = bot('CopyMessage',[
                            'chat_id'=>$chid,
                            'from_chat_id'=>$chat_id,
                            'message_id'=>$msg_id,
                            ]);
                            
                        if($for->ok == true){
                            
                            $mid = $for->result->message_id;
                            $bannerdata['banners']["$chid"] = "$mid";
                            file_put_contents("data/banners/banner$i.json", json_encode($bannerdata));
                            
                        }
                    
                    }
                    
                }
                
                if($rbtime == 'never'){
                    unlink("data/banners/banner$i.json");
                }
                
            }
            
            elseif($rbtime <= $timestamp and $rbtime != 'never'){
                $bannerscounts = count($bannerdata['banners']);
                $banners = $bannerdata['banners'];
                for($j=0;$j <= $bannerscounts;$j++){
                    $chids = array_keys($banners);
                    $chid = $chids[$j];
                    $mid = $bannerdata['banners']["$chid"];
                    $del = bot('DeleteMessage',[
                        'chat_id' => $chid,
                        'message_id' => $mid,
                        ]);
                }
                
                $replies = $bannerdata['replies'];
                if($replies != null){
                    
                    $repliescount = count($bannerdata['replies']);
                    for($j=0;$j <= $repliescount +1;$j++){
                        $chids = array_keys($replies);
                        $chid = $chids[$j];
                        $mid = $bannerdata['replies']["$chid"];
                        bot('DeleteMessage',[
                            'chat_id' => $chid,
                            'message_id' => $mid,
                            ]);
                    }
                    
                }
                
                if(file_exists("data/banners/banner$i.json")){
                    
                    $counts = fcount('data/banners', "banner*.json");
                    unlink("data/banners/banner$i.json");
                    if(!file_exists("data/banners/banner$i.json")){
                        
                        $counts = fcount('data/banners', "banner*.json");
                        if($counts == 0){
                            rrmdir('data/banners');
                        }
                    }
                }
            }

            $rep_rbtime = $bannerdata['rep_rbtime'];
            $rep_sndtime = $bannerdata['rep_start'];

            if($timestamp >= $rep_sndtime and $bannerdata['replies'] == null){
                
                $rep_chat_id = $bannerdata['rep_chat_id'];
                $rep_msg_id = $bannerdata['rep_msg_id'];
                $bannercounts = count($bannerdata['banners']);
                $banners = $bannerdata['banners'];
                for($m=0;$m <= $bannercounts - 1;$m++){
                    $chids = array_keys($banners);
                    $chid = $chids[$m];
                    $mid = $bannerdata['banners']["$chid"];
                    if($markup == null){
                        $for = bot('CopyMessage',[
                            'chat_id' => $chid,
                            'from_chat_id' => $rep_chat_id,
                            'message_id' => $rep_msg_id,
                            'reply_to_message_id' => $mid,
                            ]);
                    }else{
                        $for = bot('CopyMessage',[
                            'chat_id' => $chid,
                            'from_chat_id' => $rep_chat_id,
                            'message_id' => $rep_msg_id,
                            'reply_to_message_id' => $mid,
                            'reply_markup' => json_encode([
                                'inline_keyboard' => $markup
                                ])
                            ]);
                    }
                        
                    if($for->ok == true){
                        
                        $mid = $for->result->message_id;
                        $bannerdata['replies']["$chid"] = "$mid";
                        /*$bannerdata['replychat'] = "$chat_id";
                        $bannerdata['replyid'] = "$msg_id";
                        if($markup != null){
                            $markup2 = json_encode($markup);
                            $bannerdata['markup'] = "$markup2";
                        }*/
                        file_put_contents("data/banners/banner$i.json", json_encode($bannerdata));
                        
                    }
                    
                }
                
            }
            
            elseif($rep_rbtime <= $timestamp and $rep_rbtime != 'never'){
                
                $replies = $bannerdata['replies'];
                if($replies != null){
                    
                    $repliescount = count($bannerdata['replies']);
                    for($j=0;$j <= $repliescount +1;$j++){
                        $chids = array_keys($replies);
                        $chid = $chids[$j];
                        $mid = $bannerdata['replies']["$chid"];
                        bot('DeleteMessage',[
                            'chat_id' => $chid,
                            'message_id' => $mid,
                            ]);
                    }
                    
                }
                
                $bannerdata['replies'] = null;
                file_put_contents("data/banners/banner$i.json", json_encode($bannerdata));

            }

        }
        
        if($o == $bannercounts){
            break;
        }
    }
    
}