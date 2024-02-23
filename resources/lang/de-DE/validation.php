<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => ':attribute muss akzeptiert werden.',
    'active_url' => ':attribute ist keine valide URL.',
    'after' => ':attribute muss ein Datum nach :date sein.',
    'after_or_equal' => ':attribute muss ein Datum gleich oder nach :date sein.',
    'alpha' => ':attribute soll nur Buchstaben enthalten.',
    'alpha_dash' => ':attribute soll nur Buchstaben, Zahlen, Stiche und Unterstiche enthalten.',
    'alpha_num' => ':attribute soll nur Buchstaben und Zahlen enthalten.',
    'array' => ':attribute muss ein Auflistung sein.',
    'before' => ':attribute muss ein Daten vor :date sein.',
    'before_or_equal' => ':attribute muss ein Daten gelich oder vor :date sein.',
    'between' => [
        'numeric' => ':attribute muss zwischen :min und :max sein.',
        'file' => ':attribute muss zwischen :min und :max kilobytes sein.',
        'string' => ':attribute muss zwischen :min und :max Zeichen lang sein.',
        'array' => ':attribute muss zwischen :min und :max Elemente enthalten.',
    ],
    'boolean' => ':attribute Feld muss wahr oder falsche sein.',
    'confirmed' => ':attribute Bestätigung stimmt nicht überein.',
    'date' => ':attribute ist kein valides Datum.',
    'date_equals' => ':attribute muss ein Datum gleich :date sein.',
    'date_format' => ':attribute entspricht nicht dem Format :format.',
    'different' => ':attribute und :other müssen sich unterscheiden.',
    'digits' => ':attribute muss :digits Ziffern lang sein.',
    'digits_between' => ':attribute muss zwischen :min und :max Ziffern lang sein.',
    'dimensions' => ':attribute besitzt eine invalide Bilddimension.',
    'distinct' => ':attribute Feld hat einen doppelten Wert.',
    'email' => ':attribute muss eine valide E-Mail Adresse sein.',
    'ends_with' => ':attribute muss eine der folgenden Endungen besitzen: :values.',
    'exists' => 'Das ausgewählte :attribute is invalid.',
    'file' => ':attribute muss eine Datei sein.',
    'filled' => 'Das :attribute Feld muss einen Wert enthalten.',
    'gt' => [
        'numeric' => ':attribute muss größer als :value sein.',
        'file' => ':attribute muss größer als :value kilobytes sein.',
        'string' => ':attribute muss mehr als :value Zeichen lang sein.',
        'array' => ':attribute muss mehr als :value Elemente enthalten.',
    ],
    'gte' => [
        'numeric' => ':attribute muss größer oder gleich :value sein.',
        'file' => ':attribute muss größer oder gleich :value kilobytes sein.',
        'string' => ':attribute muss mindestens :value Zeichen lang sein.',
        'array' => ':attribute muss mindestens :value Elemente enthalten.',
    ],
    'image' => ':attribute muss ein Bild sein.',
    'in' => 'Das ausgewählte :attribute ist invalide.',
    'in_array' => 'Das :attribute Feld existiert nicht in :other.',
    'integer' => ':attribute muss eine Ganzzahl sein.',
    'ip' => ':attribute muss eine valide IP Adresse sein.',
    'ipv4' => ':attribute muss eine valide IPv4 Adresse sein.',
    'ipv6' => ':attribute muss eien valide IPv6 Adresse sein.',
    'json' => ':attribute muss ein valider JSON sein.',
    'lt' => [
        'numeric' => ':attribute muss weniger als :value sein.',
        'file' => ':attribute muss weniger als :value kilobytes haben.',
        'string' => ':attribute muss weniger als :value Zeichen haben.',
        'array' => ':attribute muss weniger als :value Elemente enthalten.',
    ],
    'lte' => [
        'numeric' => ':attribute darf höchstens :value sein.',
        'file' => ':attribute darf höchstens :value kilobytes groß sein.',
        'string' => ':attribute darf höchstens :value Zeichen lang sein.',
        'array' => ':attribute darf höchstens :value Elemente enthalten.',
    ],
    'max' => [
        'numeric' => ':attribute darf nicht größer als :max sein.',
        'file' => ':attribute darf nicht größer als :max kilobytes seins.',
        'string' => ':attribute darf mehr als :max Zeichen lang sein.',
        'array' => ':attribute darf nicht mehr als :max Elemente enthalten.',
    ],
    'mimes' => ':attribute muss eine Datei mit einem der folgenden Typen sein: :values.',
    'mimetypes' => ':attribute muss eine Datie mit einem der folgenden Typens ein: :values.',
    'min' => [
        'numeric' => ':attribute muss mindestens :min sein.',
        'file' => ':attribute muss mindestens :min kilobytes groß sein.',
        'string' => ':attribute muss mindestens :min Zeichen lang sein.',
        'array' => ':attribute muss mindestens :min Element enthalten.',
    ],
    'multiple_of' => ':attribute muss eine Vielfaches von :value sein',
    'not_in' => 'Das ausgewählte :attribute ist invalide.',
    'numeric' => ':attribute muss eine Zahl sein.',
    'password' => 'Das Passwort ist falsch.',
    'present' => ':attribute muss vorhanden sein.',
    'regex' => ':attribute entspricht nicht dem Format.',
    'required' => 'Das Feld :attribute ist verpflichtend.',
    'required_if' => 'Das :attribute Feld ist verpflichtend wenn :other gleich :value ist.',
    'required_unless' => 'Das :attribute Feld ist verpflichtend sofern :other nicht in :values enthalten ist.',
    'required_with' => 'Das :attribute Feld ist verpflichtend wenn :values vorhanden ist.',
    'required_with_all' => 'Das :attribute Feld ist verpflichtend wenn alle :values vorhanden sind.',
    'required_without' => 'Das :attribute Feld ist verpflichtend wenn :values nicht vorhanden ist.',
    'required_without_all' => 'Das :attribute Feld ist verpflichtend wenn keines der folgenden :values vorhanden ist.',
    'same' => ':attribute und :other müssen übereinstimmen .',
    'size' => [
        'numeric' => ':attribute muss :size sein.',
        'file' => ':attribute muss :size kilobytes groß sein.',
        'string' => ':attribute muss :size Zeichen lang sein.',
        'array' => ':attribute muss :size Elemente enthalten.',
    ],
    'starts_with' => ':attribute muss einen der folgenden Anfänge haben: :values.',
    'string' => ':attribute muss eine Zeichenkette sein.',
    'timezone' => ':attribute muss eine valide Zeitzone sein.',
    'unique' => ':attribute wird bereits verwendet.',
    'uploaded' => ':attribute konnte nicht hochgeladen werden.',
    'url' => ':attribute besitzt ein invalides Format.',
    'uuid' => ':attribute muss eine valide UUID sein.',

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
    'custom' => [],

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

    'attributes' => [
        'name' => 'Name',
        'username' => 'Nutzername',
        'email' => 'E-Mail Adresse',
        'first_name' => 'Vorname',
        'last_name' => 'Nachname',
        'password' => 'Passwort',
        'password_confirmation' => 'Passwort bestätigen',
        'city' => 'Stadt',
        'country' => 'Land',
        'address' => 'Adresse',
        'phone' => 'Telefon',
        'mobile' => 'Handynummer',
        'age' => 'Alter',
        'sex' => 'Geschlecht',
        'gender' => 'Geschlecht',
        'year' => 'Jahr',
        'month' => 'Monat',
        'day' => 'Tag',
        'hour' => 'Stunde',
        'minute' => 'Minute',
        'second' => 'Sekunde',
        'title' => 'Titel',
        'content' => 'Inhalt',
        'body' => 'Inhalt',
        'description' => 'Beschreibung',
        'excerpt' => 'Ausschnitt',
        'date' => 'Datum',
        'time' => 'Zeit',
        'subject' => 'Betreff',
        'message' => 'Nachricht',
    ],

];
