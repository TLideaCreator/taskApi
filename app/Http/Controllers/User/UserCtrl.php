<?php


namespace App\Http\Controllers\User;


use App\Format\UserFormat;
use App\Http\Controllers\ApiCtrl;
use App\Methods\FormatMethod;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Input;

class UserCtrl extends ApiCtrl
{

    /**
     * UserCtrl constructor.
     */
    public function __construct()
    {
        $this->formatTransfer = new UserFormat();
    }

    public function createUser(Request $request)
    {
        $user = $request->user;
        if(empty($user->is_admin)){
            abort(403);
        }

        $name = Input::get('name',null);
        $phone = Input::get('phone', null);
        $pwd = Input::get('pwd', null);
        $email = Input::get('email', null);
        $avatar = Input::get('avatar', null);
        $isAdmin = Input::get('admin', 0);

        if(is_null($name)){
            $this->notFound404('name');
        }
        if(!FormatMethod::matchPhone($phone)){
            $this->notFound404('phone');
        }

        if(!FormatMethod::matchPassword($pwd)){
            $this->notFound404( 'pwd');
        }

        if(!FormatMethod::matchEmail($email)){
            $this->notFound404('email');
        }

        if($isAdmin !== 0 && $isAdmin !==1){
            $isAdmin = 0;
        }

        $phoneCount = User::where('phone', $phone)->count();
        if($phoneCount > 0){
            abort(425);
        }
        $emailCount = User::where('email', $email)->count();
        if($emailCount > 0){
            abort(426);
        }

        $user = User::create([
            'phone'=>$phone,
            'email'=>$email,
            'password'=>Crypt::encrypt($pwd),
            'nickname'=>$name,
            'avatar'=>$avatar,
            'is_admin'=>$isAdmin,
            'token'=>''
        ]);

        return $this->toJsonItem($user);
    }

    public function updateUser()
    {

    }

    public function deleteUser()
    {

    }
    public function getUserDetail()
    {

    }
    public function getUserList(Request $request)
    {
        $user = $request->user;
        if(empty($user->is_admin)){
            abort(403);
        }
        $searchKey = Input::get('searchKey',null);

        $page =  $this->validatePage(Input::get('page',null));
        $pageCount =  $this->validatePageCount(Input::get('per_page',null));

        $userQuery = User::where(function ($query) use ($searchKey){
            if(!is_null($searchKey)){
                $query->where('nickname', 'like', "%{$searchKey}%")
                ->orWhere('phone', 'like', "%{$searchKey}%")
                ->orWhere('email', 'like', "%{$searchKey}%");
            }
        });
        $total = $userQuery->count();
        $userList = $userQuery->skip(($page-1)*$pageCount)->take($pageCount)->get();
        return $this->toJsonArray($userList)->setMeta([
            'total'=>$total,
            'page'=>$page,
            'page_count'=>$pageCount
        ]);
    }


}