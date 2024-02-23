<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines – Norwegian Translation
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'Du må akseptere :attribute.',
    'active_url' => ':attribute er ikke en gyldig URL.',
    'after' => ':attribute må være en dato etter :date.',
    'after_or_equal' => ':attribute må være en dato etter eller lik :date.',
    'alpha' => ':attribute kan kun inneholde bokstaver.',
    'alpha_dash' => ':attribute kan kun inneholde bokstaver, tall, bindestreker og understreker.',
    'alpha_num' => ':attribute kan kun inneholde bokstaver og tall.',
    'array' => ':attribute må være en matrise.',
    'before' => ':attribute må være en dato før :date.',
    'before_or_equal' => ':attribute må være en dato før eller lik :date.',
    'between' => [
        'numeric' => ':attribute må være mellom :min og :max.',
        'file' => ':attribute må være mellom :min og :max kilobyte.',
        'string' => ':attribute må være mellom :min og :max tegn.',
        'array' => ':attribute må ha mellom :min og :max elementer.',
    ],
    'boolean' => ':attribute-feltet må være sant eller usant.',
    'confirmed' => ':attribute-bekreftelsen stemmer ikke.',
    'date' => ':attribute er ikke en gyldig dato.',
    'date_equals' => ':attribute må være en dato lik :date.',
    'date_format' => ':attribute samsvarer ikke med formatet :format.',
    'different' => ':attribute og :other må være forskjellige.',
    'digits' => ':attribute må være :digits siffer.',
    'digits_between' => ':attribute må være mellom :min og :max siffer.',
    'dimensions' => ':attribute har ugyldige bilde-dimensjoner.',
    'distinct' => ':attribute-feltet har en duplikatverdi.',
    'email' => ':attribute må være en gyldig e-postadresse.',
    'ends_with' => ':attribute må ende med en av følgende: :values.',
    'exists' => 'Det valgte :attribute er ugyldig.',
    'file' => ':attribute må være en fil.',
    'filled' => ':attribute-feltet må ha en verdi.',
    'gt' => [
        'numeric' => ':attribute må være større enn :value.',
        'file' => ':attribute må være større enn :value kilobyte.',
        'string' => ':attribute må være større enn :value tegn.',
        'array' => ':attribute må ha flere enn :value elementer.',
    ],
    'gte' => [
        'numeric' => ':attribute må være større enn eller lik :value.',
        'file' => ':attribute må være større enn eller lik :value kilobyte.',
        'string' => ':attribute må være større enn eller lik :value tegn.',
        'array' => ':attribute må ha :value elementer eller mer.',
    ],
    'image' => ':attribute må være et bilde.',
    'in' => 'Det valgte :attribute er ugyldig.',
    'in_array' => ':attribute-feltet eksisterer ikke i :other.',
    'integer' => ':attribute må være et heltall.',
    'ip' => ':attribute må være en gyldig IP-adresse.',
    'ipv4' => ':attribute må være en gyldig IPv4-adresse.',
    'ipv6' => ':attribute må være en gyldig IPv6-adresse.',
    'json' => ':attribute må være en gyldig JSON-streng.',
    'lt' => [
        'numeric' => ':attribute må være mindre enn :value.',
        'file' => ':attribute må være mindre enn :value kilobyte.',
        'string' => ':attribute må være mindre enn :value tegn.',
        'array' => ':attribute må ha færre enn :value elementer.',
    ],
    'lte' => [
        'numeric' => ':attribute må være mindre enn eller lik :value.',
        'file' => ':attribute må være mindre enn eller lik :value kilobyte.',
        'string' => ':attribute må være mindre enn eller lik :value tegn.',
        'array' => ':attribute må ikke ha flere enn :value elementer.',
    ],
    'max' => [
        'numeric' => ':attribute kan ikke være større enn :max.',
        'file' => ':attribute kan ikke være større enn :max kilobyte.',
        'string' => ':attribute kan ikke være større enn :max tegn.',
        'array' => ':attribute kan ikke ha flere enn :max elementer.',
    ],
    'mimes' => ':attribute må være en fil av typen: :values.',
    'mimetypes' => ':attribute må være en fil av typen: :values.',
    'min' => [
        'numeric' => ':attribute må være minst :min.',
        'file' => ':attribute må være minst :min kilobyte.',
        'string' => ':attribute må være minst :min tegn.',
        'array' => ':attribute må ha minst :min elementer.',
    ],
    'multiple_of' => ':attribute må være et multiplum av :value.',
    'not_in' => 'Det valgte :attribute er ugyldig.',
    'not_regex' => ':attribute-formatet er ugyldig.',
    'numeric' => ':attribute må være et tall.',
    'password' => 'Passordet er feil.',
    'present' => ':attribute-feltet må være til stede.',
    'regex' => ':attribute-formatet er ugyldig.',
    'required' => ':attribute-feltet er påkrevd.',
    'required_if' => ':attribute-feltet er påkrevd når :other er :value.',
    'required_unless' => ':attribute-feltet er påkrevd med mindre :other er i :values.',
    'required_with' => ':attribute-feltet er påkrevd når :values er til stede.',
    'required_with_all' => ':attribute-feltet er påkrevd når :values er til stede.',
    'required_without' => ':attribute-feltet er påkrevd når :values ikke er til stede.',
    'required_without_all' => ':attribute-feltet er påkrevd når ingen av :values er til stede.',
    'same' => ':attribute og :other må være like.',
    'size' => [
        'numeric' => ':attribute må være :size.',
        'file' => ':attribute må være :size kilobyte.',
        'string' => ':attribute må være :size tegn.',
        'array' => ':attribute må inneholde :size elementer.',
    ],
    'starts_with' => ':attribute må starte med en av følgende: :values.',
    'string' => ':attribute må være en tekststreng.',
    'timezone' => ':attribute må være en gyldig tidssone.',
    'unique' => ':attribute er allerede tatt.',
    'uploaded' => ':attribute kunne ikke lastes opp.',
    'url' => ':attribute-formatet er ugyldig.',
    'uuid' => ':attribute må være en gyldig UUID.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
