<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Users\UserRepositoryinterface;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepositoryinterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = DB::table('users')->where('role', '>', 0)->get();

        return view('admin.pages.users.index', compact('users'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = $this->userRepository->find($id);
        if (!$user) {
            return redirect('/admin/users/index');
        }

        return view('admin.pages.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = $this->userRepository->find($id);
        if (!$user || empty($user)) {
            return redirect('/admin/users/list');
        }

        $param = $request->all();
        if ($param['role'] == 0) {
            return redirect()->back();
        }
        $tempPassword = Hash::make($param['password']);
        $param['password'] = $tempPassword;

        $update = $this->userRepository->update($param, $id);
        if ($update) {
            return redirect('/admin/users/index');
        }

        return redirect('/admin/users/index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $user = $this->userRepository->find($id);
        if (!$user) {
            return redirect()->back();
        }

        $destroy = $this->userRepository->delete($id);
        if ($destroy) {
            return redirect('/admin/users/index');
        }

        return redirect('/admin/users/index');
    }
}
