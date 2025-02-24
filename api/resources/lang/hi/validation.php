<?php

return [
    'accepted' => ':attribute को स्वीकार किया जाना चाहिए।',
    'active_url' => ':attribute एक मान्य URL नहीं है।',
    'after' => ':attribute, :date के बाद की एक तारीख होनी चाहिए।',
    'after_or_equal' => ':attribute, :date के बाद या उसके बराबर की तारीख होनी चाहिए।',
    'alpha' => ':attribute में केवल अक्षर हो सकते हैं।',
    'alpha_dash' => ':attribute में केवल अक्षर, संख्या, डैश और अंडरस्कोर हो सकते हैं।',
    'alpha_num' => ':attribute में केवल अक्षर और संख्याएं हो सकती हैं।',
    'array' => ':attribute एक एरे होना चाहिए।',
    'before' => ':attribute, :date से पहले की एक तारीख होनी चाहिए।',
    'before_or_equal' => ':attribute, :date से पहले या उसके बराबर की तारीख होनी चाहिए।',
    'between' => [
        'numeric' => ':attribute, :min और :max के बीच होना चाहिए।',
        'file' => ':attribute, :min और :max किलोबाइट के बीच होना चाहिए।',
        'string' => ':attribute, :min और :max वर्णों के बीच होना चाहिए।',
        'array' => ':attribute, :min और :max आइटमों के बीच होना चाहिए।',
    ],
    'boolean' => ':attribute फील्ड सही या गलत होना चाहिए।',
    'confirmed' => ':attribute पुष्टिकरण मेल नहीं खा रहा है।',
    'date' => ':attribute एक मान्य तारीख नहीं है।',
    'date_equals' => ':attribute, :date के बराबर की तारीख होनी चाहिए।',
    'date_format' => ':attribute फॉर्मेट :format से मेल नहीं खा रहा है।',
    'different' => ':attribute और :other अलग होना चाहिए।',
    'digits' => ':attribute, :digits अंक होना चाहिए।',
    'digits_between' => ':attribute, :min और :max अंकों के बीच होना चाहिए।',
    'dimensions' => ':attribute की अमान्य छवि आयाम हैं।',
    'distinct' => ':attribute फील्ड का एक डुप्लिकेट मान है।',
    'email' => ':attribute एक मान्य ईमेल पता होना चाहिए।',
    'ends_with' => ':attribute निम्न में से किसी एक के साथ समाप्त होना चाहिए: :values',
    'exists' => 'चयनित :attribute अमान्य है।',
    'file' => ':attribute एक फ़ाइल होनी चाहिए।',
    'filled' => ':attribute फील्ड आवश्यक है।',
    'gt' => [
        'numeric' => ':attribute, :value से बड़ा होना चाहिए।',
        'file' => ':attribute, :value किलोबाइट से बड़ा होना चाहिए।',
        'string' => ':attribute, :value वर्णों से बड़ा होना चाहिए।',
        'array' => ':attribute, :value आइटमों से अधिक होना चाहिए।',
    ],
    'gte' => [
        'numeric' => ':attribute, :value से बड़ा या बराबर होना चाहिए।',
        'file' => ':attribute, :value किलोबाइट से बड़ा या बराबर होना चाहिए।',
        'string' => ':attribute, :value वर्णों से बड़ा या बराबर होना चाहिए।',
        'array' => ':attribute में :value आइटम या अधिक होने चाहिए।',
    ],
    'image' => ':attribute एक छवि होनी चाहिए।',
    'in' => 'चयनित :attribute अमान्य है।',
    'in_array' => ':attribute फील्ड, :other में मौजूद नहीं है।',
    'integer' => ':attribute एक पूर्णांक होना चाहिए।',
    'ip' => ':attribute एक मान्य IP पता होना चाहिए।',
    'ipv4' => ':attribute एक मान्य IPv4 पता होना चाहिए।',
    'ipv6' => ':attribute एक मान्य IPv6 पता होना चाहिए।',
    'json' => ':attribute एक मान्य JSON स्ट्रिंग होना चाहिए।',
    'lt' => [
        'numeric' => ':attribute, :value से छोटा होना चाहिए।',
        'file' => ':attribute, :value किलोबाइट से छोटा होना चाहिए।',
        'string' => ':attribute, :value वर्णों से छोटा होना चाहिए।',
        'array' => ':attribute, :value आइटमों से कम होना चाहिए।',
    ],
    'lte' => [
        'numeric' => ':attribute, :value से छोटा या बराबर होना चाहिए।',
        'file' => ':attribute, :value किलोबाइट से छोटा या बराबर होना चाहिए।',
        'string' => ':attribute, :value वर्णों से छोटा या बराबर होना चाहिए।',
        'array' => ':attribute में :value आइटम से अधिक नहीं होना चाहिए।',
    ],
    'max' => [
        'numeric' => ':attribute, :max से बड़ा नहीं हो सकता है।',
        'file' => ':attribute, :max किलोबाइट से बड़ा नहीं हो सकता है।',
        'string' => ':attribute, :max वर्णों से बड़ा नहीं हो सकता है।',
        'array' => ':attribute में :max से अधिक आइटम नहीं हो सकते हैं।',
    ],
    'mimes' => ':attribute एक प्रकार की फ़ाइल: :values होनी च��हिए।',
    'mimetypes' => ':attribute एक प्रकार की फ़ाइल: :values होनी चाहिए।',
    'min' => [
        'numeric' => ':attribute कम से कम :min होना चाहिए।',
        'file' => ':attribute कम से कम :min किलोबाइट होना चाहिए।',
        'string' => ':attribute कम से कम :min वर्ण होना चाहिए।',
        'array' => ':attribute में कम से कम :min आइटम होने चाहिए।',
    ],
    'multiple_of' => ':attribute, :value का गुणज होना चाहिए।',
    'not_in' => 'चयनित :attribute अमान्य है।',
    'not_regex' => ':attribute प्रारूप अमान्य है।',
    'numeric' => ':attribute एक संख्या होनी चाहिए।',
    'password' => 'पासवर्ड गलत है।',
    'present' => ':attribute फील्ड मौजूद होना चाहिए।',
    'regex' => ':attribute प्रारूप अमान्य है।',
    'required' => ':attribute फील्ड आवश्यक है।',
    'required_if' => ':attribute फील्ड आवश्यक है जब :other :value है।',
    'required_unless' => ':attribute फील्ड आवश्यक है जब तक :other, :values में नहीं है।',
    'required_with' => ':attribute फील्ड आवश्यक है जब :values मौजूद है।',
    'required_with_all' => ':attribute फील्ड आवश्यक है जब :values मौजूद हैं।',
    'required_without' => ':attribute फील्ड आवश्यक है जब :values मौजूद नहीं है।',
    'required_without_all' => ':attribute फील्ड आवश्यक है जब :values में से कोई भी मौजूद नहीं है।',
    'same' => ':attribute और :other मेल खाने चाहिए।',
    'size' => [
        'numeric' => ':attribute, :size होना चाहिए।',
        'file' => ':attribute, :size किलोबाइट होना चाहिए।',
        'string' => ':attribute, :size वर्ण होना चाहिए।',
        'array' => ':attribute में :size आइटम होने चाहिए।',
    ],
    'starts_with' => ':attribute निम्न में से किसी एक से शुरू होना चाहिए: :values',
    'string' => ':attribute एक स्ट्रिंग हो��ी चाहिए।',
    'timezone' => ':attribute एक मान्य क्षेत्र होना चाहिए।',
    'unique' => ':attribute पहले से ही लिया गया है।',
    'uploaded' => ':attribute अपलोड करने में विफल रहा।',
    'url' => ':attribute प्रारूप अमान्य है।',
    'uuid' => ':attribute एक मान्य UUID होना चाहिए।',

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'कस्टम-संदेश',
        ],
    ],

    'attributes' => [],

    'invalid_json' => 'अमान्य इनपुट। कृपया सुधारें और पुनः प्रयास करें।',
    'invalid_captcha' => 'अमान्य कैप्चा। कृपया दिखाएं कि आप एक बॉट नहीं हैं।',
    'complete_captcha' => 'कृपया कैप्चा भरें।',
    'yes' => 'है',
    'no' => 'नहीं',
];
