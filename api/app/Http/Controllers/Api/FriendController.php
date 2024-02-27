<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\FriendList as FriendListResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Api\BaseApiController;

class FriendController extends BaseApiController
{
    private $request;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $data  = $this->friends();
            $response =  FriendListResource::collection($data);
            return $this->sendSuccess($response, 'Friend list retrive successfully.');
        } catch (ValidationException $exception) {
            // Handle validation errors
            return $this->sendValidationFail($exception);
        } catch (\Exception $exception) {
            // Handle other exceptions
            return $this->sendError('Failed to retrive messages.'.$exception->getMessage(), 500);
        }
    }
    public function friends()
    {
        $loggedInUserId = auth()->id();

        return User::join('friends', function ($join) use ($loggedInUserId) {
            $join->on('users.id', '=', 'friends.user_id')
                    ->where('friends.confirm', 1)
                    ->where(function ($query) use ($loggedInUserId) {
                        $query->where('friends.friend_id', $loggedInUserId)
                            ->orWhere('friends.user_id', $loggedInUserId);
                    });
        })
        ->orWhere(function ($query) use ($loggedInUserId) {
            $query->join('friends as f2', function ($join) use ($loggedInUserId) {
                $join->on('users.id', '=', 'f2.friend_id')
                        ->where('f2.confirm', 1)
                        ->where('f2.user_id', $loggedInUserId);
            });
        })
        ->select('users.*')
        ->distinct()
        ->get();
    }
     /**
     * Display a listing of the resource.
     */
    public function request()
    {
        try {
            $data  = Auth::user()->friendRequests;
            $response =  FriendListResource::collection($data);
            return $this->sendSuccess($response, 'Friends requests retrive successfully.');
        } catch (ValidationException $exception) {
            // Handle validation errors
            return $this->sendValidationFail($exception);
        } catch (\Exception $exception) {
            // Handle other exceptions
            return $this->sendError('Failed to retrive request messages.'.$exception->getMessage(), 500);
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
     * Show the form for creating a new resource.
     */
    public function selected($id)
    {
        try {
            $user  = User::findOrfail($id);
            $data =  [
                'id' => $user->id,
                'full_name' => ucwords($user->fname).' '.ucwords($user->lname),
                'avatar' => $user->avatar,
                'online' => $user->online
            ];
            return $this->sendSuccess($data, 'Selected user retrive successfully.');
        } catch (ValidationException $exception) {
            // Handle validation errors
            return $this->sendValidationFail($exception);
        } catch (\Exception $exception) {
            // Handle other exceptions
            return $this->sendError('Failed to retrive request messages.'.$exception->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function add()
    {
        try {
           $this->request->validate([
                'add_to' => 'required',
            ]);
           DB::table('friends')->insert([
                'user_id' => Auth::user()->id,
                'friend_id' => $this->request->add_to,
                'sent_by' => Auth::user()->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
            return $this->sendCreateSuccess('', 'Friend request sent successfully.');

        } catch (ValidationException $exception) {
            // Handle validation errors
            return $this->sendValidationFail($exception);
        } catch (\Exception $exception) {
            // Handle other exceptions
            return $this->sendError('Failed to sent  request.'.$exception->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    

    /**
     * Update the specified resource in storage.
     */
    public function confirm()
    {
        try {
           $this->request->validate([
                'confirm_for' => 'required',
            ]);
            DB::table('friends')
            ->where('user_id', $this->request->confirm_for)
            ->where('sent_by', '!=', Auth::user()->id)
            ->where('friend_id', Auth::user()->id)
            ->update([
                'confirm' => 1,
                'accepted_at' =>  Carbon::now(),
            ]);
            return $this->sendCreateSuccess('', 'Friend request accepted successfully.');

        } catch (ValidationException $exception) {
            // Handle validation errors
            return $this->sendValidationFail($exception);
        } catch (\Exception $exception) {
            // Handle other exceptions
            return $this->sendError('Failed to accept  request.'.$exception->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function unfriend()
    {
        try {

           $this->request->validate([
                'unfriend_to' => 'required',
            ]);
            DB::table('friends')
            ->where(function ($query) {
                $query->where('user_id', Auth::user()->id)
                      ->where('friend_id', $this->request->unfriend_to);
            })
            ->orWhere(function ($query) {
                $query->where('friend_id', Auth::user()->id)
                      ->where('user_id', $this->request->unfriend_to);
            })
            ->delete();

            return $this->sendCreateSuccess('', 'Unfriend  successfully.');

        } catch (ValidationException $exception) {
            // Handle validation errors
            return $this->sendValidationFail($exception);
        } catch (\Exception $exception) {
            // Handle other exceptions
            return $this->sendError('Failed to unfriend.'.$exception->getMessage(), 500);
        }
    }
    public function youMayKnow() 
    {
        $loggedInUserId = auth()->id();

        $data =  User::whereNotIn('id', function ($query) use ($loggedInUserId) {
            // Select all user_ids and friend_ids from the 'friends' table related to the logged-in user
            $query->select('user_id')
                  ->from('friends')
                  ->where('user_id', $loggedInUserId)
                  ->orWhere('friend_id', $loggedInUserId)
                  ->union(
                      // Union with the inverse selection to cover both columns
                      $query->newQuery()
                            ->select('friend_id')
                            ->from('friends')
                            ->where('user_id', $loggedInUserId)
                            ->orWhere('friend_id', $loggedInUserId)
                  );
        })
        ->where('id', '!=', $loggedInUserId) // Exclude the logged-in user
        ->get();
        $response =  FriendListResource::collection($data);
        return $this->sendSuccess($response, 'People you may know retrive successfully.');
    }
}
