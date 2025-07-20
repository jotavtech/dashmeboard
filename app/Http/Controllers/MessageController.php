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
        
        $messages = Message::where('to_user_id', $user->id)
            ->orWhere('from_user_id', $user->id)
            ->with(['fromUser', 'toUser'])
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
                      $q->where('from_user_id', $user->id)
                        ->orWhere('to_user_id', $user->id);
                  });
        })->with(['fromUser', 'toUser'])->firstOrFail();
        
        // Marcar como lida se for destinatário
        if ($message->to_user_id === $user->id && !$message->is_read) {
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
            'from_user_id' => $user->id,
            'to_user_id' => $request->to_user_id,
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
                $q->where('from_user_id', $user->id)
                  ->where('to_user_id', $otherUser->id);
            })->orWhere(function($q) use ($user, $otherUser) {
                $q->where('from_user_id', $otherUser->id)
                  ->where('to_user_id', $user->id);
            });
        })->with(['fromUser', 'toUser'])
          ->orderBy('created_at', 'asc')
          ->get();
        
        // Marcar mensagens recebidas como lidas
        Message::where('from_user_id', $otherUser->id)
            ->where('to_user_id', $user->id)
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
        $count = Message::where('to_user_id', $user->id)
            ->where('is_read', false)
            ->count();
        
        return response()->json(['count' => $count]);
    }
}
