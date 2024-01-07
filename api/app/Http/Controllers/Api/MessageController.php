<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\MessageWithFriend as MessageWithFriendResource;
use App\Http\Resources\MessageConversation as MessageConversationResource;

class MessageController extends BaseApiController
{

    private $request;
    private $message;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request, Message $message)
    {
        $this->request = $request;
        $this->message = $message;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $userId = Auth::id();

            // Get all users except the authenticated user
            $users = User::where('id', '!=', $userId)->where('account_status',1)->get();
            
            $latestMessagesByUser = [];

            foreach ($users as $user) {
                $friendId = $user->id;

                // Query for the latest message where the authenticated user is either the sender or receiver
                $latestMessage = Message::where(function ($query) use ($userId, $friendId) {
                        $query->where('from', $userId)->where('to', $friendId);
                    })
                    ->orWhere(function ($query) use ($userId, $friendId) {
                        $query->where('from', $friendId)->where('to', $userId);
                    })
                    ->orderBy('created_at', 'desc')
                    ->first();

                if ($latestMessage) {
                    $latestMessagesByUser[$user->id] = [
                        'user' => $user,
                        'latest_message' => $latestMessage
                    ];
                }
            }
            
            $transformedData = collect($latestMessagesByUser)->map(function ($item) {
                return (object) [
                    'user' => (object) $item['user'],
                    'latest_message' => (object) $item['latest_message']
                ];
            });
            $response =  MessageWithFriendResource::collection($transformedData);
            return $this->sendSuccess($response, 'Messages retrive successfully.');

        } catch (ValidationException $exception) {
            // Handle validation errors
            return $this->sendValidationFail($exception);
        } catch (\Exception $exception) {
            // Handle other exceptions
            return $this->sendError('Failed to retrive messages.'.$exception->getMessage(), 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store()
    {
        // Start the database transaction
        DB::beginTransaction();
        
        try {
            $validatedData = $this->request->validate([
                'content' => 'required',
                'to' => 'required',
            ]);
            $composeMessage = [
                'content' => $this->request->content,
                'from' => Auth::user()->id,
                'to' => $this->request->to
            ];
            // compose mesage 
            $message = $this->message->create($composeMessage);

            //attached id to meesage and user own track
            $message->users()->attach(Auth::user()->id);
            // finish here 
            DB::commit();
            return $this->sendCreateSuccess($message, 'Message sent successfully.');

        } catch (ValidationException $exception) {
            // If any exception occurs, roll back the transaction
            DB::rollBack();
            // Handle validation errors
            return $this->sendValidationFail($exception);
        } catch (\Exception $exception) {
             // If any exception occurs, roll back the transaction
             DB::rollBack();
            // Handle other exceptions
            return $this->sendError('Failed to sent  Message.'.$exception->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $loggedInUserId = Auth::user()->id;
            $friendUserId = $id; 

            $conversations = Message::with(['sender', 'receiver'])
            ->where(function ($query) use ($loggedInUserId, $friendUserId) {
                $query->where('from', $loggedInUserId)->where('to', $friendUserId);
            })
            ->orWhere(function ($query) use ($loggedInUserId, $friendUserId) {
                $query->where('from', $friendUserId)->where('to', $loggedInUserId);
            })
            ->orderBy('created_at', 'asc')
            ->get();      

            $response =  MessageConversationResource::collection($conversations);
            return $this->sendSuccess($response, 'Messages retrive successfully.');
        } catch (ValidationException $exception) {
            // Handle validation errors
            return $this->sendValidationFail($exception);
        } catch (\Exception $exception) {
            // Handle other exceptions
            return $this->sendError('Failed to retrive messages.'.$exception->getMessage(), 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
       
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
