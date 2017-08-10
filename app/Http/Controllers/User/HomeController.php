<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
    	$data['course']						                =       \App\Course::paginate(8);
    	return view('pages.interface.home', compact('data'));
    }

    public function create_feedback()
    {
        return view('pages.interface.feedback');
    }

    public function feedbackstore(Request $request)
    {
        $validname = \Validator::make($request->all(), [//->Memanggil class Validator dan mengambil semua data inputan
            'feedback_text'                                 => 'required|string|min:2'
        ]);

        if ($validname->fails()) {
           return \Redirect::back()->with('err_msg', $validname->errors()->all() )->withInput($request->all());
        }

        $feedback                                           =       new \App\Feedback;
        $feedback->feedback_text                            =       $request->feedback_text;
        $feedback->save();
        return \Redirect::to('home')->with('sc_msg', 'Feedback successfuly send');

    }

    public function subscribe()
    {
        return view('pages.interface.subscribe');
    }

       public function subscribestore(Request $request)
    {
        $validname = \Validator::make($request->all(), [//->Memanggil class Validator dan mengambil semua data inputan
            'email'                                         => 'required|string|min:2|unique:subscribes'
        ]);

        if ($validname->fails()) {
           return \Redirect::back()->with('err_msg', $validname->errors()->all() )->withInput($request->all());
        }

        $subscribe                                          =       new \App\Subscribe;
        $subscribe->email                                   =       $request->email;
        $subscribe->save();
        return \Redirect::to('home')->with('sc_msg', 'Successfuly subscribe');

    }

    public function detail(Request $request,$id)
    {
    	$data['playlist']						            =	    \App\Playlist::where('course_id',$id)->get();
    	return view('pages.interface.list_playlist', compact('data'));
    }

    public function watch(Request $request,$id)
    {
        $validname = \Validator::make($request->all(), [//->Memanggil class Validator dan mengambil semua data inputan
            'playlist_name'                         => 'required|string|min:2|unique:watches'
        ]);

        $validip = \Validator::make($request->all(), [//->Memanggil class Validator dan mengambil semua data inputan
            'playlist_name'                         => 'required|ip|unique:watches'
        ]);

        if (($validname->fails())&&($validip->fails())) {

            $data['id']                                     =       $id; 
            $data['video']                                  =       \App\Playlist::where('id',$id)->get();
            $data['playlist']                               =       \App\Playlist::where('course_id',$request->course_id)->get();
            $data['comment']                                =       \App\Comment::where(['playlist_id'=>$id,'is_blocked'=>0])->orderBy('id','desc')->paginate(5);
            return view('pages.interface.video_list', compact('data'));  
        }else{
            if (Auth::check()) {
            $input                                          =       new \App\Watch;
            $input->playlist_name                           =       $request->playlist_name;
            $input->ip                                      =       $request->ip();
            $input->save();

            $data['id']                                     =       $id;
            $data['video']                                  =       \App\Playlist::where('id',$id)->get();
            $data['playlist']                               =       \App\Playlist::where('course_id',$request->course_id)->get();
            $data['comment']                                =       \App\Comment::where(['playlist_id'=>$id,'is_blocked'=>0])->orderBy('id','desc')->paginate(5); 
             return view('pages.interface.video_list', compact('data'));  

            }else{
            $data['id']                                     =       $id; 
            $data['video']                                  =       \App\Playlist::where('id',$id)->get();
            $data['playlist']                               =       \App\Playlist::where('course_id',$request->course_id)->get();
            $data['comment']                                =       \App\Comment::where(['playlist_id'=>$id,'is_blocked'=>0])->orderBy('id','desc')->paginate(5);
            return view('pages.interface.video_list', compact('data'));
            }
        }
    }
    public function comment(Request $request)
    {
        $comment                                            =       new \App\Comment;
        $comment->comment_text                              =       $request->comment_text;
        $comment->playlist_id                               =       $request->playlist_id;
        $comment->user_id                                   =       $request->user_id;
        $comment->save();
        return \Redirect::back();
    }

    public function daftar()
    {
        return view('pages.interface.userdaftar');
    }

    public function daftarstore(Request $request)
    {
         $validname = \Validator::make($request->all(), [//->Memanggil class Validator dan mengambil semua data inputan
            'username'                                 => 'required|string|max:50|min:2|unique:users',
            'email'                                    => 'required|email|min:2|unique:users',
            'password'                                 => 'required|string|max:50|min:8',
            'user_github'                                 => 'required|string|max:50|min:2|unique:users',
        ]);

        if ($validname->fails()) {
           return \Redirect::back()->with('err_msg', $validname->errors()->all() )->withInput($request->all());
        }

        $daftar                                           =       new \App\User;
        $daftar->username                                 =       $request->username;
        $daftar->email                                    =       $request->email;
        $daftar->password                                 =       bcrypt($request->password);
        $daftar->user_github                              =       $request->user_github;
        $daftar->role_id                                  =       3;
        $daftar->save();
        return \Redirect::to('home')->with('sc_msg', 'Successfuly Register');
    }
    public function userlog()
    {
        return view('pages.interface.userlog');
    }
    public function userdo(Request $request)
    {
        $dataLogin                                      = $request->only('email', 'password');

        if(\Auth::attempt($dataLogin))
            return \Redirect::to('home')->with('sc_msg', 'Welcome '.Auth::user()->username);

        return \Redirect::to('login')
                ->with('err_msg', 'Login Failed, Username or Password Wrong')
                ->withInput($dataLogin);
    }
    public function userout()
    {
        \Auth::logout();

        return \Redirect::to('userlog')
                ->with('sc_msg', 'Logout Successfuly');
    }
}
