<?php

namespace App\Models;

use Mpociot\Versionable\Version as BaseVersion;

class Version extends BaseVersion
{
    public function getModel()
    {
        $model = parent::getModel();

        if (is_object($model) && method_exists($model, 'getCasts')) {
            foreach ($model->getCasts() as $attribute => $cast) {
                $value = $model->getAttribute($attribute);

                if (in_array($cast, ['array', 'json'], true)) {
                    if ($value === null || $value === '') {
                        $model->setAttribute($attribute, []);
                    } elseif (is_string($value)) {
                        $decoded = json_decode($value, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $model->setAttribute($attribute, $decoded ?? []);
                        }
                    }
                } elseif ($cast === 'object') {
                    if ($value === null || $value === '') {
                        $model->setAttribute($attribute, (object) []);
                    } elseif (is_string($value)) {
                        $decoded = json_decode($value);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $model->setAttribute($attribute, $decoded ?? (object) []);
                        }
                    }
                }
            }
        }

        return $model;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
