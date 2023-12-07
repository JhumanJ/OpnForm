<?php

namespace App\Http\Controllers;

use App\Models\Workspace;
use Illuminate\Http\Request;

class CaddyController extends Controller
{
    public function ask(Request $request)
    {
        $request->validate([
            'domain' => 'required|string',
        ]);
        // make sure domain is valid
        $domain = $request->input('domain');
        if (!preg_match('/^[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}$/', $domain)) {
            return $this->error([
                'success' => false,
                'message' => 'Invalid domain',
            ]);
        }

        \Log::info('Caddy request received',[
            'domain' => $domain,
        ]);

        if ($workspace = Workspace::whereJsonContains('custom_domains',$domain)->first()) {
            \Log::info('Caddy request successful',[
                'domain' => $domain,
                'workspace' => $workspace->id,
            ]);
            return $this->success([
                'success' => true,
                'message' => 'OK',
            ]);
        }

        \Log::info('Caddy request failed',[
            'domain' => $domain,
            'workspace' => $workspace?->id,
        ]);

        return $this->error([
            'success' => false,
            'message' => 'Unauthorized domain',
        ]);
    }
}
