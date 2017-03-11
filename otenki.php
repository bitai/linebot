<?php
/*
 * 入門向けスクリプト
 * 
 */
    $accessToken = '';
    
    //ユーザーからのメッセージ取得
    $json_string = file_get_contents('php://input');
    $jsonObj = json_decode($json_string);
    
    $type = $jsonObj->{"events"}[0]->{"message"}->{"type"};
    //メッセージ取得
    $text = $jsonObj->{"events"}[0]->{"message"}->{"text"};
    //ReplyToken取得
    $replyToken = $jsonObj->{"events"}[0]->{"replyToken"};
    
    //メッセージ以外のときは何も返さず終了
    if($type != "text"){
        exit;
    }

    if(strstr($text, '天気予報')){
        // Livedoor お天気APIに問い合わせ
        $response = file_get_contents(
                                      'http://weather.livedoor.com/forecast/webservice/json/v1?' .
                                      http_build_query($query)
                                      );
        $result = json_decode($response,true);
        /*foreach($result["forecasts"] as $r){
            $text .= $r['dateLabel']. 'の天気は'. $r['telop']. 'です。\n';
        }*/
        $text = $result["description"]["text"];
    }
    
    //返信データ作成
    $response_format_text = [
    "type" => "text",
    "text" => $text
    ];
    $post_data = [
    "replyToken" => $replyToken,
    "messages" => [$response_format_text]
    ];
    
    $ch = curl_init("https://api.line.me/v2/bot/message/reply");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                                               'Content-Type: application/json; charser=UTF-8',
                                               'Authorization: Bearer ' . $accessToken
                                               ));
    $result = curl_exec($ch);
    curl_close($ch);
?>
