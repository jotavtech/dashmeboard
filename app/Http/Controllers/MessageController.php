<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $messages = Message::where('receiver_id', $user->id)
            ->orWhere('sender_id', $user->id)
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('messages.index', compact('messages'));
    }

    public function show($id)
    {
        $user = Auth::user();
        
        $message = Message::where(function($query) use ($user, $id) {
            $query->where('id', $id)
                  ->where(function($q) use ($user) {
                      $q->where('sender_id', $user->id)
                        ->orWhere('receiver_id', $user->id);
                  });
        })->with(['sender', 'receiver'])->firstOrFail();
        
        // Marcar como lida se for destinatário
        if ($message->receiver_id === $user->id && !$message->is_read) {
            $message->update([
                'is_read' => true,
                'read_at' => now()
            ]);
        }
        
        return view('messages.show', compact('message'));
    }

    public function create($username = null)
    {
        $recipient = null;
        if ($username) {
            $recipient = User::whereHas('profile', function($query) use ($username) {
                $query->where('username', $username);
            })->first();
        }
        
        return view('messages.create', compact('recipient'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'to_user_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000'
        ]);
        
        $user = Auth::user();
        
        // Verificar se não está enviando para si mesmo
        if ($user->id == $request->to_user_id) {
            return back()->withErrors(['message' => 'Você não pode enviar mensagem para si mesmo.']);
        }
        
        Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $request->to_user_id,
            'message' => $request->message
        ]);
        
        return redirect()->route('messages.index')
            ->with('success', 'Mensagem enviada com sucesso!');
    }

    public function conversation($username)
    {
        $user = Auth::user();
        $otherUser = User::whereHas('profile', function($query) use ($username) {
            $query->where('username', $username);
        })->firstOrFail();
        
        $messages = Message::where(function($query) use ($user, $otherUser) {
            $query->where(function($q) use ($user, $otherUser) {
                $q->where('sender_id', $user->id)
                  ->where('receiver_id', $otherUser->id);
            })->orWhere(function($q) use ($user, $otherUser) {
                $q->where('sender_id', $otherUser->id)
                  ->where('receiver_id', $user->id);
            });
        })->with(['sender', 'receiver'])
          ->orderBy('created_at', 'asc')
          ->get();
        
        // Marcar mensagens recebidas como lidas
        Message::where('sender_id', $otherUser->id)
            ->where('receiver_id', $user->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);
        
        return view('messages.conversation', compact('messages', 'otherUser'));
    }

    public function unreadCount()
    {
        $user = Auth::user();
        $count = Message::where('receiver_id', $user->id)
            ->where('is_read', false)
            ->count();
        
        return response()->json(['count' => $count]);
    }

    public function searchUsers(Request $request)
    {
        $query = $request->get('q');
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['users' => []]);
        }
        
        if (empty($query)) {
            return response()->json(['users' => []]);
        }
        
        $users = User::where('id', '!=', $user->id) // Excluir o usuário atual
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%")
                  ->orWhereHas('profile', function($profileQuery) use ($query) {
                      $profileQuery->where('username', 'like', "%{$query}%")
                                  ->orWhere('profession', 'like', "%{$query}%")
                                  ->orWhere('bio', 'like', "%{$query}%");
                  });
            })
            ->with('profile')
            ->limit(10)
            ->get()
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'username' => $user->profile ? $user->profile->username : null,
                    'profession' => $user->profile ? $user->profile->profession : null,
                    'profile_image' => $user->profile ? $user->profile->profile_image_url : null,
                ];
            });
        
        return response()->json(['users' => $users]);
    }



    public function conversations()
    {
        $user = Auth::user();
        
        // Buscar todas as conversas do usuário
        $conversations = Message::select('*')
            ->where(function($query) use ($user) {
                $query->where('sender_id', $user->id)
                      ->orWhere('receiver_id', $user->id);
            })
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function($message) use ($user) {
                // Agrupar por o outro usuário da conversa
                return $message->sender_id === $user->id ? $message->receiver_id : $message->sender_id;
            })
            ->map(function($messages) use ($user) {
                $latestMessage = $messages->first();
                $otherUser = $latestMessage->sender_id === $user->id ? $latestMessage->receiver : $latestMessage->sender;
                
                return [
                    'user' => $otherUser,
                    'latest_message' => $latestMessage,
                    'unread_count' => $messages->where('receiver_id', $user->id)->where('is_read', false)->count(),
                    'total_messages' => $messages->count(),
                    'last_activity' => $latestMessage->created_at
                ];
            })
            ->sortByDesc('last_activity')
            ->values();
        
        return view('messages.conversations', compact('conversations'));
    }
}
