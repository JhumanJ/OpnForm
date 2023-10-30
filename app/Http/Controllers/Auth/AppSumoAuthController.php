<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\Workspaces\WorkspaceAlreadyExisting;
use App\Exceptions\Workspaces\WorkspaceLimit;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AppSumoAuthController extends Controller
{
    use AuthenticatesUsers;

    public function handleCallback(Request $request)
    {
        ray($request->all());
        $this->validate($request, [
            'code' => 'required',
        ]);
        dd('ok');

        try {
            $workspace = $this->retrieveAccessToken($request->code);
        } catch (ClientException $exception) {

            // Permission issue: owner does not have full access to shared page
            if (Str::of($exception->getMessage())->contains('User does not have edit access to record')) {
                Log::error('Notion connection permission Error', [
                    'exception_msg' => $exception->getMessage(),
                    'response' => $exception->getResponse(),
                    'exception' => $exception,
                    'user' => Auth::user()->id,
                ]);

                return $this->callbackResponse([
                    'type' => 'error',
                    'message' => 'You do not have full access to the Notion pages you selected. Please make sure you have the right permissions.'
                ]);
            }

            Log::error('Error while connecting to notion', [
                'exception_msg' => $exception->getMessage(),
                'exception' => $exception,
            ]);

            return $this->callbackResponse([
                'type' => 'error',
                'message' => 'Error while connecting with Notion. Please try again!',
            ]);
        } catch (WorkspaceAlreadyExisting $exception) {
            // TODO: notify workspace owner
            return $this->callbackResponse([
                'type' => 'error',
                'upgrade' => true,
                'message' => 'workspace_already_existing',
                'owner' => $exception->getOwner(),
            ]);
        } catch (WorkspaceLimit $exception) {
            return $this->callbackResponse([
                'type' => 'error',
                'upgrade' => true,
                'retry' => false,
                'message' => 'You are only allowed to connect 1 Notion workspace. Please upgrade your subscription to the Enterprise plan  before adding more workspaces.',
            ]);
        }

        return $this->callbackResponse([
            'type' => 'success',
            'workspace' => $workspace,
        ]);
    }

    private function retrieveAccessToken(string $requestCode): Workspace
    {
        $baseUrl = 'https://api.notion.com/' . config('notion.version') . '/oauth/';
        $client = new Client([
            'base_uri' => $baseUrl,
        ]);

        $response = $client->post('token', [
            'form_params' => [
                'grant_type' => 'authorization_code',
                'code' => $requestCode,
                'redirect_uri' => route('notion.callback'),
            ],
            'auth' => [
                config('notion.client_id'),
                config('notion.client_secret'),
            ],
        ]);

        $body = (string)$response->getBody();
        $body = json_decode($body, true);

        return $this->findOrCreateWorkspace($body);
    }

    private function findOrCreateWorkspace(array $workspaceData): Workspace
    {
        // Check user's workspaces
        if ($workspace = Auth::user()->workspaces()
            ->where(function ($query) use ($workspaceData) {
                return $query->where('name', $workspaceData['workspace_name'])
                    ->orWhere('notion_workspace_id', $workspaceData['workspace_id']);
            })
            ->first()) {
            $workspace->update([
                'name' => utf8_encode($workspaceData['workspace_name']),
                'icon' => utf8_encode($workspaceData['workspace_icon'] ?? ucfirst($workspaceData['workspace_name'][0])),
                'bot_id' => $workspaceData['bot_id'],
                'notion_workspace_id' => $workspaceData['workspace_id'],
            ]);
        } // Check other existing workspaces
        elseif ($workspace = Workspace::where('notion_workspace_id', $workspaceData['workspace_id'])->first()) {
            $this->checkCanConnectWorkspace($workspace);
            $workspace->update([
                'name' => utf8_encode($workspaceData['workspace_name']),
                'icon' => utf8_encode($workspaceData['workspace_icon'] ?? ucfirst($workspaceData['workspace_name'][0])),
                'bot_id' => $workspaceData['bot_id'],
                'notion_workspace_id' => $workspaceData['workspace_id'],
            ]);
        } // New workspace, create it
        else {
            $this->checkCanConnectWorkspace();
            $workspace = Workspace::create([
                'bot_id' => $workspaceData['bot_id'],
                'notion_workspace_id' => $workspaceData['workspace_id'],
                'name' => utf8_encode($workspaceData['workspace_name']),
                'icon' => utf8_encode($workspaceData['workspace_icon'] ?? ucfirst($workspaceData['workspace_name'][0])),
            ]);
        }

        // Add relation with user
        Auth::user()->workspaces()->sync([
            $workspace->id => [
                'access_token' => $workspaceData['access_token'],
                'is_owner' => true,
            ],
        ], false);

        return $workspace;
    }

    private function checkCanConnectWorkspace($workspace = null)
    {
        $user = Auth::user();

        if ($workspace && $workspace->haveOpenedGates()) {
            return;
        }

        // If user has enterprise subscription, can do everything
        if ($user->has_enterprise_subscription) {
            return;
        }

        // If user doens't have enterprise
        if ($user->workspaces()->count() > 0) {
            throw new WorkspaceLimit();
        } else {
            // User has room for new workspace
            if ($workspace && $workspace->is_enterprise) {
                return;
            } elseif ($workspace) {
                // User has room, but workspace not enterprise
                throw new WorkspaceAlreadyExisting($workspace);
            }
        }
    }

    private function callbackResponse(array $result)
    {
        return view('notion.callback', [
            'result' => array_merge($result, [
                'source' => 'notion_tools',
            ]),
        ]);
    }
}
