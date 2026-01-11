<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Helpers;
class ThemeController extends Controller
{
    public function setTheme($style)
    {
        Helpers::setStyle($style);
        return redirect()->back();
    }
}
