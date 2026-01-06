<?php

namespace App\Http\Controllers;

use App\Http\Requests\StartConversationRequest;
use App\Http\Requests\StoreMessageRequest;
use App\Models\Apartment;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
  
    public function startConversation(StartConversationRequest $request)
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();
            $apartment = Apartment::with('user')->findOrFail($request->apartment_id);
            if ($user->id === $apartment->user_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot start a conversation with yourself'
                ], 400);
            }
            $tenantId = $user->role === 'tenant' ? $user->id : $apartment->user_id;
            $ownerId = $user->role === 'rented' ? $user->id : $apartment->user_id;
            $conversation = Conversation::where('apartment_id', $request->apartment_id)
                ->where('tenant_id', $tenantId)
                ->where('owner_id', $ownerId)
                ->first();

            if (!$conversation) {
                $conversation = Conversation::create([
                    'tenant_id' => $tenantId,
                    'owner_id' => $ownerId,
                    'apartment_id' => $request->apartment_id,
                    'last_message_at' => now(),
                    'status' => 'active'
                ]);
            }
            $receiverId = ($user->id === $tenantId) ? $ownerId : $tenantId;
            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $user->id,
                'receiver_id' => $receiverId,
                'message' => $request->message,
                'read_at' => null
            ]);

            $conversation->update(['last_message_at' => now()]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Conversation started successfully',
                'data' => [
                    'conversation_id' => $conversation->id,
                    'message' => $message
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to start conversation: ' . $e->getMessage()
            ], 500);
        }
    }

    public function sendMessage(StoreMessageRequest $request)
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();
            $conversation = Conversation::with(['tenant', 'owner'])->findOrFail($request->conversation_id);
            if ($user->id !== $conversation->tenant_id && $user->id !== $conversation->owner_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not a participant in this conversation'
                ], 403);
            }
            $receiverId = ($user->id === $conversation->tenant_id) 
                ? $conversation->owner_id 
                : $conversation->tenant_id;

            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $user->id,
                'receiver_id' => $receiverId,
                'message' => $request->message,
                'read_at' => null
            ]);

            $conversation->update(['last_message_at' => now()]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Message sent successfully',
                'data' => $message
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to send message: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getConversations(Request $request)
    {
        $user = Auth::user();
        
        $conversations = Conversation::with(['apartment', 'tenant', 'owner', 'latestMessage'])
            ->where('tenant_id', $user->id)
            ->orWhere('owner_id', $user->id)
            ->orderBy('last_message_at', 'desc')
            ->get()
            ->map(function ($conversation) use ($user) {
                $otherUser = ($conversation->tenant_id === $user->id) 
                    ? $conversation->owner 
                    : $conversation->tenant;
                $unreadCount = $conversation->unreadMessagesCount($user->id);

                return [
                    'id' => $conversation->id,
                    'apartment' => [
                        'id' => $conversation->apartment->id,
                        'site' => $conversation->apartment->site,
                        'city' => $conversation->apartment->city,
                        'image' => $conversation->apartment->image
                    ],
                    'other_user' => [
                        'id' => $otherUser->id,
                        'name' => $otherUser->first_name . ' ' . $otherUser->last_name,
                        'role' => $otherUser->role,
                        'phone' => $otherUser->phone
                    ],
                    'last_message' => $conversation->latestMessage ? [
                        'message' => $conversation->latestMessage->message,
                        'sender_id' => $conversation->latestMessage->sender_id,
                        'created_at' => $conversation->latestMessage->created_at->format('Y-m-d H:i:s'),
                        'is_read' => !is_null($conversation->latestMessage->read_at)
                    ] : null,
                    'unread_count' => $unreadCount,
                    'last_message_at' => $conversation->last_message_at,
                    'status' => $conversation->status
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $conversations
        ]);
    }
    public function getMessages($conversationId, Request $request)
    {
        $user = Auth::user();
        
        $conversation = Conversation::with(['apartment', 'tenant', 'owner'])
            ->where('id', $conversationId)
            ->where(function ($query) use ($user) {
                $query->where('tenant_id', $user->id)
                      ->orWhere('owner_id', $user->id);
            })
            ->first();

        if (!$conversation) {
            return response()->json([
                'success' => false,
                'message' => 'Conversation not found'
            ], 404);
        }

        Message::where('conversation_id', $conversationId)
            ->where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = Message::with(['sender', 'receiver'])
            ->where('conversation_id', $conversationId)
            ->orderBy('created_at', 'asc')
            ->paginate($request->get('per_page', 50));
        $otherUser = ($conversation->tenant_id === $user->id) 
            ? $conversation->owner 
            : $conversation->tenant;

        return response()->json([
            'success' => true,
            'data' => [
                'conversation_id' => $conversation->id,
                'apartment' => [
                    'id' => $conversation->apartment->id,
                    'site' => $conversation->apartment->site,
                    'city' => $conversation->apartment->city
                ],
                'other_user' => [
                    'id' => $otherUser->id,
                    'name' => $otherUser->first_name . ' ' . $otherUser->last_name,
                    'role' => $otherUser->role
                ],
                'messages' => $messages->items(),
                'pagination' => [
                    'current_page' => $messages->currentPage(),
                    'last_page' => $messages->lastPage(),
                    'per_page' => $messages->perPage(),
                    'total' => $messages->total()
                ]
            ]
        ]);
    }

    public function getUnreadCount()
    {
        $user = Auth::user();
        
        $unreadCount = Message::where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'unread_count' => $unreadCount
            ]
        ]);
    }

    public function deleteConversation($conversationId)
    {
        $user = Auth::user();
        
        $conversation = Conversation::where('id', $conversationId)
            ->where(function ($query) use ($user) {
                $query->where('tenant_id', $user->id)
                      ->orWhere('owner_id', $user->id);
            })
            ->first();

        if (!$conversation) {
            return response()->json([
                'success' => false,
                'message' => 'Conversation not found'
            ], 404);
        }

        Message::where('conversation_id', $conversationId)->delete();
        $conversation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Conversation deleted successfully'
        ]);
    }

public function searchMessages(Request $request)
{
    $request->validate([
        'query' => 'required|string|min:2',
        'conversation_id' => 'nullable|exists:conversations,id'
    ]);

    $user = Auth::user();
    
    $searchTerm = $request->input('query');
    $searchTerm = trim($searchTerm);
    
    $messagesQuery = Message::with(['sender', 'conversation.apartment'])
        ->whereHas('conversation', function ($q) use ($user) {
            $q->where('tenant_id', $user->id)
              ->orWhere('owner_id', $user->id);
        })
        ->where(function ($query) use ($searchTerm) {
            $query->where('message', 'LIKE', '%' . $searchTerm . '%');
            $words = explode(' ', $searchTerm);
            if (count($words) > 1) {
                foreach ($words as $word) {
                    if (strlen($word) >= 2) {
                        $query->orWhere('message', 'LIKE', '%' . trim($word) . '%');
                    }
                }
            }
            $query->orWhere('message', 'LIKE', '% ' . $searchTerm . ' %')  
                  ->orWhere('message', 'LIKE', $searchTerm . ' %')        
                  ->orWhere('message', 'LIKE', '% ' . $searchTerm);       
        });

    if ($request->has('conversation_id')) {
        $messagesQuery->where('conversation_id', $request->conversation_id);
    }

    $messages = $messagesQuery->orderBy('created_at', 'desc')
        ->paginate($request->get('per_page', 20));

    return response()->json([
        'success' => true,
        'data' => $messages
    ]);
}
}