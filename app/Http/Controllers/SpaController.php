<?php

namespace App\Http\Controllers;

use App\Service\SeoMetaResolver;
use Illuminate\Http\Request;
class SpaController extends Controller
{
    /**
     * Get the SPA view.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        return view('spa',[
            'meta' => (new SeoMetaResolver($request))->getMetas(),
        ]);
    }
}
