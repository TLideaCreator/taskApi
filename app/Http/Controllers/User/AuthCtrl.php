<?php


namespace App\Http\Controllers\User;


use App\Format\LoginFormat;
use App\Http\Controllers\ApiCtrl;
use App\Models\User;
use Faker\Provider\Uuid;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Input;

class AuthCtrl extends ApiCtrl
{

    /**
     * AuthCtrl constructor.
     */
    public function __construct()
    {
        $this->formatTransfer = new LoginFormat();
    }

    public function login()
    {
        $acct = Input::get('account', null);
        $pwd = Input::get('pwd', null);
        $user = User::where(function ($query) use ($acct, $pwd) {
            $query->where('phone', $acct)
                ->orWhere('email', $acct);
        })->first();
        if (empty($user)) {
            abort(203);
        }
        if ($pwd !== Crypt::decrypt($user->password)) {
            $this->notFound404('pwd');
        }
        $user->token = Uuid::uuid();
        $user->save();
        return $this->toJsonItem($user);
    }
}