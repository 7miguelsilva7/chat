// USERS SECTION

<div class="bg-white">

//-----swching between users section and recent conversation section by alpine Js-------
        <div class="messages-box" wire:ignore x-show.transition.in="tab === 'recent'">
          <button :class="{ 'active': tab === 'groups' }" @click="tab = 'all'">
           <img class="float-right"  src="{{asset('icons/icons/group.png')}}" width="40" > 
         </button>
//-----End swching between users section and recent conversation section by alpine Js-------
      // Conversations
        <div class="list-group users rounded-0" >
            @foreach($conversations as $conversation)
            
            //---- If I started the chat betwwen us show me the avatar of the other user
            
               @if(Auth::user()->id == $conversation->first_user)
            <a class="list-group-item  list-group-item-action   text-white rounded-0" wire:click="$emit('selected_users',{{$conversation->second_user}})" >
              <div class="media ">
                <img src="{{Voyager::image($conversation->seecond_user->avatar)}}"  alt="user" width="50" class="rounded-circle">
                <div class="media-body ml-4 user" >
                  <div class="d-flex align-items-center justify-content-between mb-1">
                    <h6 class="mb-0" style="color: black">{{$conversation->seecond_user->name}}</h6><small class="small font-weight-bold" style="color: black"></small>
                  </div>
                  <p class="badge badge-danger {{$conversation->second_user}}" style="color: black" id="{{'user'.$conversation->first_user}}" >
                    {{\App\Message::where('statu','unreaded')->where('to_user',Auth::user()->id)->where('from_user',$conversation->seecond_user->id)->count()}}
                  </p>
                </div>
              </div>
            </a>
            //---- If I was not the one who started the chat betwwen us show me the avatar of the other userwho did it
              @else
            <div class="list-group-item list-group-item-action  text-white rounded-0  user3" wire:click="$emit('selected_users',{{$conversation->first_user}})" >
              <div class="media " >
                <img src="{{Voyager::image($conversation->fiirst_user->avatar)}}"  alt="user" width="50" class="rounded-circle">
                <div class="media-body ml-4 user">
                  <div class="d-flex align-items-center justify-content-between mb-1" >
                    <h6 class="mb-0" style="color: black">{{$conversation->fiirst_user->name}}</h6><small class="small font-weight-bold" style="color: black"></small>
                  </div>
                  <p class="badge badge-danger {{$conversation->first_user}}" style="color: black"  id="{{'user.'.$conversation->first_user}}">
                    {{\App\Message::where('statu','unreaded')->where('to_user',Auth::user()->id)->where('from_user',$conversation->fiirst_user->id)->count()}}</p>
                </div>
              </div>
              <p></p>
            </div>
              @endif
            @endforeach
          </div>
        </div>
        // users section
      <div class="messages-box list-group users rounded-0" wire:ignore x-show.transition.in="tab === 'all'">
           <button class="btn" :class="{ 'active': tab === 'recent' }"  @click="tab = 'recent'" style="border:none;">
           <img class="float-right"  src="{{asset('icons/icons/back.png')}}" width="40" > 
         </button>
           
            @foreach($users as $user)
            <a class="list-group-item  list-group-item-action   text-white rounded-0" wire:click="$emit('selected_users',{{$user->id}})" >
              <div class="media ">
                <img src="{{Voyager::image($user->avatar)}}"  alt="user" width="50" class="rounded-circle">
                <div class="media-body ml-4 user" >
                  <div class="d-flex align-items-center justify-content-between mb-1">
                    <h6 class="mb-0" style="color: black">{{$user->name}}</h6><small class="small font-weight-bold" style="color: black"></small>
                  </div>
                </div>
              </div>
            </a>
            @endforeach
          </div>
      </div>
    </div>
    //------------------------------------------
    
    
    <!-- Chat Box-->
    <div class="col-7 px-0">
 <div class="py-5 chat-box bg-white messages " id="messages">
        <!-- Messages-->
 
       <div class="messages-boxx" id="messages-boxx"  >
        @foreach($messages as $message)                  
          @if(Auth::user()->id == $message->from_user)
        <!-- Reciever Message-->
        <div class="media w-50 ml-auto mb-3">
          <div class="media-body">
            <div class="bg-primary rounded py-2 px-3 mb-2">
              <p class="text-small mb-0 text-white">{{$message->body}}</p>
            </div>
            <p class="small text-muted">{{$message->created_at}}</p>
          </div>
        </div>
        
          @else
        <!-- Sender Message-->
        <div class="media w-50 mb-3">
          <img src="{{Voyager::image($message->froom_user->avatar)}}" alt="user" width="50" class="rounded-circle">
          <div class="media-body ml-3">
            <div class="bg-light rounded py-2 px-3 mb-2">
              <p class="text-small mb-0 text-muted">{{$message->body}}</p>
            </div>
            <p class="small text-muted">{{$message->created_at}}</p>
          </div>
        </div>
         @endif
           
       @endforeach
      </div>    
    </div>
         <div class="bg-light">
        
        <div class="input-group">
          <input type="text" placeholder="Type a message" aria-describedby="button-addon2" class="form-control rounded-0 border-0 py-4 bg-light" wire:model='message'>
          <div class="input-group-append">
             <button id="button-addon2" type="submit" class="btn btn-link" wire:click='send'> <i class="fa fa-paper-plane" ></i></button>
          </div>
                  </div>
      </div>
     

    </div>
          