<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function index()
    {
        $profiles = Profile::where('is_public', true)
            ->with('user')
            ->paginate(12);
        
        return view('profiles.index', compact('profiles'));
    }

    public function show($username)
    {
        $profile = Profile::where('username', $username)
            ->where('is_public', true)
            ->with('user')
            ->firstOrFail();
        
        return view('profiles.show', compact('profile'));
    }

    public function edit()
    {
        $user = Auth::user();
        $profile = $user->profile;
        
        if (!$profile) {
            $profile = new Profile();
        }
        
        return view('profiles.edit', compact('profile'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'username' => 'required|string|max:255|unique:profiles,username,' . ($user->profile ? $user->profile->id : ''),
            'bio' => 'nullable|string|max:1000',
            'profession' => 'nullable|string|max:255',
            'mood' => 'nullable|string|max:255',
            'public_agenda' => 'nullable|string|max:1000',
            'private_agenda' => 'nullable|string|max:1000',
            'daily_music' => 'nullable|string|max:255',
            'fortune_cookie_message' => 'nullable|string|max:500',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_public' => 'boolean'
        ]);

        $profile = $user->profile;
        
        if (!$profile) {
            $profile = new Profile();
            $profile->user_id = $user->id;
        }

        $profile->username = $request->username;
        $profile->bio = $request->bio;
        $profile->profession = $request->profession;
        $profile->mood = $request->mood;
        $profile->public_agenda = $request->public_agenda;
        $profile->private_agenda = $request->private_agenda;
        $profile->daily_music = $request->daily_music;
        $profile->fortune_cookie_message = $request->fortune_cookie_message;
        $profile->is_public = $request->has('is_public');

        // Upload de imagens
        if ($request->hasFile('profile_image')) {
            $image = $request->file('profile_image');
            $filename = 'profile_' . time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            
            // Upload para Cloudinary
            $cloudinary = \Cloudinary\Uploader::upload($image->getRealPath(), [
                'public_id' => $filename,
                'folder' => 'profiles'
            ]);
            
            $profile->profile_image = $cloudinary['public_id'];
        }

        if ($request->hasFile('background_image')) {
            $image = $request->file('background_image');
            $filename = 'background_' . time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            
            // Upload para Cloudinary
            $cloudinary = \Cloudinary\Uploader::upload($image->getRealPath(), [
                'public_id' => $filename,
                'folder' => 'backgrounds'
            ]);
            
            $profile->background_image = $cloudinary['public_id'];
        }

        $profile->save();

        return redirect()->route('profile.show', $profile->username)
            ->with('success', 'Perfil atualizado com sucesso!');
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        
        $profiles = Profile::where('is_public', true)
            ->where(function($q) use ($query) {
                $q->where('username', 'like', "%{$query}%")
                  ->orWhere('bio', 'like', "%{$query}%")
                  ->orWhere('profession', 'like', "%{$query}%")
                  ->orWhereHas('user', function($userQuery) use ($query) {
                      $userQuery->where('name', 'like', "%{$query}%");
                  });
            })
            ->with('user')
            ->paginate(12);
        
        return view('profiles.search', compact('profiles', 'query'));
    }

    public function myProfile()
    {
        $user = Auth::user();
        $profile = $user->profile;
        
        if (!$profile) {
            return redirect()->route('profile.edit')
                ->with('info', 'Crie seu perfil primeiro!');
        }
        
        return view('profiles.my-profile', compact('profile'));
    }
}
