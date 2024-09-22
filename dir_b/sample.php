<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AdminInquiryController extends Controller
{
  public function showAll()
  {
    $inquiries = Inquiry::with(['user'])->orderBy('created_at', 'DESC')->paginate(10);
    return view('admin/inquiry/showAllPage', compact('inquiries'));
  }

  public function mailList()
  {
    $users = User::with(['role'])->whereHas('role', function ($query) {
      $query->where('role', '=', '0');
    })
      ->get();

    return view('admin/inquiry/mailList', compact('users'));
  }

  public function update(Request $request)
  {
    if (isset($request->deleteRole)) {
      $toAdminUsers = UserRole::where('inquiry_send', 1)->get();
      if ($toAdminUsers->count() == 1 && $toAdminUsers->first()->user_id == $request->deleteRole) {
        return redirect()->back()->with('status', 'To宛先は最低1人以上必要です');
      }
      $user = User::with(['role'])->find($request->deleteRole);
      $user->role->inquiry_send = 0;
      $user->role->save();

      return to_route('admin.inquiry.mailList')->with('status', '宛先から削除しました');
    } else if (isset($request->addRoleTo)) {
      $user = User::with(['role'])->find($request->addRoleTo);
      $user->role->inquiry_send = 1;
      $user->role->save();

      return to_route('admin.inquiry.mailList')->with('status', '宛先Toに追加しました');
    } else if (isset($request->addRoleCc)) {
      $user = User::with(['role'])->find($request->addRoleCc);
      $user->role->inquiry_send = 2;
      $user->role->save();

      return to_route('admin.inquiry.mailList')->with('status', '宛先Ccに追加しました');
    }
  }
}
