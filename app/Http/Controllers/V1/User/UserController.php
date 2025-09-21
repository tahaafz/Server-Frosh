<?php

namespace app\Http\Controllers\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\URL;

class UserController extends Controller
{
    public function invite()
    {
        return Url::signedRoute('invitation',['id'=>auth()->id()]);
    }
}
