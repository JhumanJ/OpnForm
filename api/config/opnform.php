<?php

return [
    'admin_emails' => explode(',', env('ADMIN_EMAILS') ?? ''),
    'moderator_emails' => explode(',', env('MODERATOR_EMAILS') ?? ''),
    'template_editor_emails' => explode(',', env('TEMPLATE_EDITOR_EMAILS') ?? ''),
    'extra_pro_users_emails' => explode(',', env('EXTRA_PRO_USERS_EMAILS') ?? ''),
];
