<?php

namespace App\Http\Controllers\portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PagesController extends Controller
{
    public function index(){
      //  return view('portal.pages.jodot');

      return back()->with('error','Comming Soon');
    }
}
