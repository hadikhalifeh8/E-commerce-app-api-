<?php

namespace App\Http\Controllers\Api\pages;

use App\Http\Controllers\Controller;
use App\Models\cr;
use App\Models\PushNotification;
use App\Models\User;
use Illuminate\Http\Request;

class PushNotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $push_notifications = PushNotification::orderBy('created_at', 'desc')->get();
        return view('notification.index', compact('push_notifications'));
    }


    
    public function bulksend($usersid, $title, $message, $topic, $pageid, $pagename){
        $comment = new PushNotification();
        $user = User::find($usersid);
     
        $comment->users_id  = $usersid;
       $comment->title = "success";
       $comment->body = "The Order has been Approved";
    
        $comment->save();

        $url = 'https://fcm.googleapis.com/fcm/send';
        
        $dataArr = array('click_action' => 'FLUTTER_NOTIFICATION_CLICK','status'=>"done");
         $notification = array('title' =>$comment->title, 'body' => $comment->body, 'sound' => 'default', 'badge' => '1',);
        //$notification = array('title' =>"hi", 'body' => "how are you", 'topic'=> "users", 'pageid'=> $req->pageid, 'pagename'=> $req->pagename,'sound' => 'default', 'badge' => '1',);
       // $notification = array('title' =>"hi", 'body' => "how are you", 'topic'=> "users", 'pageid'=> "", 'pagename'=> "",'sound' => 'default', 'badge' => '1',);

        
        $arrayToSend = array('to' => '/topics/' . $topic, 'notification' => $notification, 'data' => $dataArr, 'priority'=>'high');
        $fields = json_encode ($arrayToSend);
        $headers = array (
            'Authorization: key=' . "AAAAnnJWTL4:APA91bEChQkuMmYhFVYGWcJ9t60PQDNvBb-Xq3gw4LJHAwXxNcKnEs39IE6K7cefBXUkpFgr59xsxe1w8AV0XfGrQkK0_6f6nO1ceXcwVEM-I5bPUhsNpEZctQ-ct80sutTpSLh627Io",
            'Content-Type: application/json'
        );
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_POST, true );
        curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $fields );
        $result = curl_exec ( $ch );
        //var_dump($result);
        curl_close ( $ch );
        return redirect()->back()->with('success', 'Notification Send successfully');
    }




 public function getNotification($usersid)
 {
    $user = User::find($usersid);

    $notification = PushNotification::where('users_id',$usersid)->orderBy('id', 'desc')->get();

    if($notification->isNotEmpty()) {
        return response()->json([
            'status' => 'success',
            'data' => $notification,
            ]);
    }else{
        return response()->json([
                           'status' => 'failure',
                            'data' => 'No notifications founded',
                        ]);
    }
 } 






    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('notification.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

  



 

 
}
