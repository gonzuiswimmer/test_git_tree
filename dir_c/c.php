<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SearchService;
use App\Models\User;
use App\Models\UserRole;
use App\Models\Department;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {

    return view('admin/top');
  }

  public function users(Request $request)
  {
    $users = SearchService::searchUser($request);
    $departments = Department::all();

    return view('admin/users/index', compact('users', 'departments'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    $departments = Department::all();

    return view('admin/users/create', [
      'departments' => $departments
    ]);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(RegisterRequest $request)
  {
    $user = User::create([
      'name' => $request->name,
      'email' => $request->email,
      'password' => Hash::make($request->password),
      'department_id' => $request->department_id,
      'beginner_flg' => $request->beginner_flg,
      'entry_date' => $request->entry_date,
      'gender' => $request->gender
    ]);

    UserProfile::create([
      'user_id' => $user->id,
      'blood_type' => 0
    ]);

    return to_route('admin.top')->with('status', '登録しました');
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    $user = User::find($id);
    return view('admin/users/show', compact('user'));
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    $user = User::find($id);
    $user_profile = UserProfile::where('user_id', $user->id)->first();
    $departments = Department::all();
    $followings = $user->followings()->orderBy('user_id')->get();
    $followers = $user->followers()->orderBy('followed_user_id')->get();

    return view('admin/users/edit', [
      'user' => $user,
      'user_profile' => $user_profile,
      'departments' => $departments,
      'followings' => $followings,
      'followers' => $followers,
    ]);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(AdminProfileUpdateRequest $request, $id)
  {
    $user = User::find($id);
    $user_profile = UserProfile::where('user_id', $user->id)->first();

    $user->name = $request->name;
    $user->department_id = $request->department_id;
    $user->beginner_flg = $request->beginner_flg;
    $user->email = $request->email;
    $user->entry_date = $request->entry_date;
    $user->gender = $request->gender;

    $user_profile->blood_type = $request->blood_type;
    $user_profile->birthday = $request->birthday;
    $user_profile->github_url = $request->github_url;
    $user_profile->qiita_url = $request->qiita_url;
    $user_profile->self_introduction = $request->self_introduction;


    $user->save();
    $user_profile->save();

    return redirect()->back()->with('status', '編集しました');
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    $user = User::find($id);
    $user->deleted_at = Carbon::now();

    $user->save();

    return to_route('admin.top')->with('status', '削除しました');
  }

  public function roles()
  {
    $users = User::whereHas('role', function ($query) {
      $query->where('role', '=', '0');
    })
      ->get();
    return view('admin/users/showRoles', compact('users'));
  }

  public function registerNewRole(Request $request)
  {
    $users = collect([]);
    $request->merge(['status' => 'working']);
    if (isset($request->name)) {
      $users = SearchService::searchUser($request);
    }

    return view('admin/users/registerRolePage', compact('users'));
  }

  public function storeNewRole($id)
  {
    $user = User::with(['role'])->find($id);
    if (!is_null($user->deleted_at) && is_null($user->role)) {
      $userRole = UserRole::create([
        'user_id' => $id,
        'role' => 0,
      ]);
      return to_route('admin.users.role')->with('status', '登録しました');
    } else if ($user->role->role === 0) {
      return to_route('admin.users.role')->with('status', '既に登録済みです');
    } else {
      return to_route('admin.users.role')->with('status', '登録できないユーザーです');
    }
  }
}
