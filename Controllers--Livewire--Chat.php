<?php

namespace App\Http\Livewire\Forum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use Livewire\Component;
use  App\Message;
use App\User;
class Chat extends Component
{   
   use WithPagination;

	protected $listeners = ['selected_users', 'selected_group', 'loadMore'];


	public $message, $chat_id, $unreadedMessages, $search, $group_id , $paginate_var=10;

  
  // get user unreaded messages
        public function mount()
        {
               	$this->unreadedMessages = Auth::user()->unreadedMessages()->count();
           
        }
        public function render()
    {
  // get user unreaded messages     
    	 $unreadedMessages = $this->unreadedMessages;
 // select the user I want to chat with
    	 $id = $this->chat_id;
// get selected user Info ---------------------------
    	 if (isset($this->chat_id)) {
    	 	$chat_user = User::find($id);
    	 }
    	 else {
    	 	$chat_user = '';
    	 }
//--------------------------------

// get All user to select one of them
    	 $users= User::all();
// My id
    	$user_id = Auth::user()->id;
//------- Haldeling Messages between me and the selected user
          // counting
      $messages_count = Message::where('from_user',$user_id)
                         ->where(  'to_user',$id)
                         ->orWhere('from_user',$id)
                         ->where('to_user',$user_id)
                         ->count();
        // showing a special numer of messages firstly , then I can show more by scrolling to top
    	$messages = Message::where('from_user',$user_id)
    	                   ->where(  'to_user',$id)
    	                   ->orWhere('from_user',$id)
    	                   ->where('to_user',$user_id)
                         ->skip($messages_count - $this->paginate_var)
                         ->take($this->paginate_var)
                         ->get();
 // showing Recent conversations I have
$conversations = \App\Conversation::where('first_user',Auth::user()->id)
                                 ->orWhere('second_user',Auth::user()->id)
                                 ->orderBy('last_message_time','desc')
                                 ->get();
 return view('livewire.forum.chat',compact('messages','id','users','chat_user','unreadedMessages','conversations','paginate_var'));
    }
    
 // selecting the user I want to chat with by clicking his avatar 
     public function selected_users($id)
          {
             $this->chat_id = $id;
       //marking our messaeges as readed
             Message::where('statu','unreaded')
                    ->where('to_user',Auth::user()->id)
                    ->where('from_user',$id)
                    ->update(array('statu'=>'readed'));
       // number of messages Iwant to show
             $this->paginate_var = 10;
       // scrollng to bottom
             $this->emit('scroll');
          }

// sending messages
          public function send($id)
    {
            //---
    	  $message = new Message;
        $message->body = $this->message;
        $message->from_user = Auth::user()->id;
        $message->to_user = $this->chat_id;
        $message->save();
           //--
            
 //----------------------------Conversation------------------------    
           //check if there is an old conversation between us
        $conv_old = \App\Conversation::where('first_user',$message->from_user)
                          ->where('second_user',$message->to_user)
                          ->orWhere('first_user',$message->to_user)
                          ->where('second_user',$message->from_user)
                          ->get()->first();
          // if there is an old convesation  between as , just link it to this message 
      if($conv_old)
{
   $conversation = \App\Conversation::find($conv_old->id);
   $conversation->last_message_time = $message->created_at;
   $conversation->save();
   $message->conversation_id = $conv_old->id;
   $message->save();

}
// else create a conversation and store our ids in it 
else
{
	$conversation = new \App\Conversation;
	$conversation->first_user = $message->from_user;
	$conversation->second_user =$message->to_user;
	$conversation->last_message_time = $message->created_at;
  $conversation->save();
  $message->conversation_id = $conversation->id;
  $message->save();
}
 //---------------------------- End Conversation------------------------ 
 // -------------------------------Event-----------------
            //get Unreaded messages 
        $recivedUnreadedMessages =Message::where('statu','unreaded')
                                         ->where('from_user',Auth::user()->id)
                                         ->where('to_user', $this->chat_id)
                                         ->count();
    	$this->message = '';
      $chat_user = User::find($this->chat_id);
            //sending event with message content and the user Isend it to , and the numb of the Unreaded Messages
     event(new \App\Events\Chat($this->chat_id,$message,$recivedUnreadedMessages,$chat_user));
    }

// Load more then 10 messaeges by scrolling to top
   public function loadMore()
       {
          $this->paginate_var = $this->paginate_var + 10;
          $this->emit('load');
       }
}
