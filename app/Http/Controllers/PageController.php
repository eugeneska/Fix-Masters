<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PageController extends Controller
{
    public function home(): View
    {
        return view('pages.home');
    }

    public function quizDevice(): View
    {
        return view('pages.quiz.index');
    }

    public function quizProblem(): View
    {
        return view('pages.quiz.step-2');
    }

    public function quizBrand(): View
    {
        return view('pages.quiz.brand');
    }

    public function quizContact(): View
    {
        return view('pages.request');
    }

    public function thanks(): View
    {
        return view('pages.thanks');
    }

    public function privacy(): View
    {
        return view('pages.privacy');
    }
}
